<?php


namespace App\Services;


use App\Domain\Poll\Poll;
use App\Domain\Poll\PollAnswer;
use App\Domain\Poll\PollQuestion;
use App\Domain\Votes\VoteAnswer;
use App\Domain\Votes\VoteAnswerItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VoteService
{

    //TODO: just upload file and verify in ePUAP - more to do
    // 1. validate base64 document content vs database
    // 2. sync and (future async)
    public function verifyVoteFile(string $file_name) {
        if(!Storage::exists($file_name)) {
            throw ValidationException::withMessages(['file' => 'Brak pliku do walidacji']);
        } else {
            info("[VoteService] Verify file ${file_name}");
            $client = new \GuzzleHttp\Client(['cookies' => true,'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36',
            ]]);
            $response = $client->post(config('e-poll.upload_url'),
                [
                    'multipart' => [

                        [
                            'name'     => 'file',
                            'contents' => Storage::get($file_name),
                            'filename' => $file_name
                        ],

                    ]
                ]
            );
            $status = json_decode($response->getBody()->getContents());
            if($status->status == 'OK') {
            info('File uploaded succesfully');
                $verify_response = $client->get(config('e-poll.verify_url'));
                if($verify_response->getStatusCode() != 200) {
                    log()->error('Wrong answer status',[$status->status]);
                } else {
                    $verify_json = $verify_response->getBody()->getContents();
                }
            } else {
                log()->error('Wrong ststaus',[$status->status]);
            }

        }
    }


    public function saveVote(array $votes) {
        /** @var  $vote */
        $vote_answer = new VoteAnswer();
        $vote_answer->uuid = Str::uuid();
        $vote_answers_items = $this->validateVote($votes);

        return DB::transaction(function() use($vote_answer,$vote_answers_items) {
            $vote_answer->save();
            $vote_answer->vote_answer_items()->saveMany($vote_answers_items);
            return $vote_answer;
        });
    }

    protected function validateVote(array $votes) {

        /** @var Collection $error_bag */
        $error_bag = collect();
        $vote_answers_items = collect();
        foreach($votes as $vote) {
            if(key_exists('slug',$vote)){
                /** @var Poll $poll */
                $poll = Poll::query()->where('slug',$vote['slug'])->firstOr('*',function () use ($error_bag) {
                    $error_bag->put('vote.slug','Głosowanie nie istnieje');
                });
                if($poll->questions()->count() === count($vote['questions'])){
                    $vac = $this->validateQuestions($poll,collect($vote['questions']),$error_bag);
                    $vac->each(function (VoteAnswerItem $vai) use ($vote_answers_items,$poll) {
                        $vai['poll_id'] = $poll->id;
                        $vote_answers_items->push($vai);
                    });

                } else {
                    $error_bag->put('questions','Zla liczba pytan w głosowaniu');
                }
            } else {
                $error_bag->put('vote.slug','Brak parametru slug');
             }
        }
        if($error_bag->isEmpty()) {
            return $vote_answers_items;
        } else {
            throw ValidationException::withMessages($error_bag->toArray());
        }
    }

    private function validateQuestions(Poll $poll, Collection $vote_questions, Collection $errors)
    {
        $vai_collection = collect();
        $poll->questions->each(function (PollQuestion $question) use ($vote_questions,$errors,$vai_collection) {
            $key_index = $vote_questions->search(function ($item) use ($question) {
                return array_key_exists('slug',$item) && $item['slug'] === $question->slug;
            });
            if($key_index === false) {
                $errors->put('vote.questions','Nie znaleziono pytania '.$question->slug);
            } else {
                $vote_question = $vote_questions->get($key_index);
                //sprawdź czy ma odpowiedzi (pole)
                if(array_key_exists('answers',$vote_question)) {
                    $collection = $this->validateSingleQuestion($question, collect($vote_question['answers']), $errors);

                    $collection->each(function ($item) use($vai_collection) {
                        info('push',[$item]);
                        $vai_collection->push($item);
                    });

                } else {
                    $errors->put('vote.questions.answers','Brak odpowiedzi');
                }
            }
        });
        return $vai_collection;
    }

    private function validateSingleQuestion(PollQuestion $question, Collection $vote_answers, Collection $errors) : Collection
    {
        $vote_answers_items = collect();
        if($question->type === PollQuestion::SINGLE) {

            if($vote_answers->count() === 1) {
                $vote_answer = $vote_answers->first();
                $vai =$this->validateSingleAnswer($vote_answer, $question, $errors);
                if($vai != null) {
                    $vote_answers_items->add($vai);
                }

            } else {
                $errors->put('vote.questions.answers','Powinna być tylko jedna odpowiedz');
            }
        } else {

            $vote_answers->each(function ($vote_answer) use($question,$errors,$vote_answers_items) {
                $vai = $this->validateSingleAnswer($vote_answer, $question, $errors);
                if($vai != null) {
                    $vote_answers_items->add($vai);
                }

            });
        }
        return $vote_answers_items;

    }

    /**
     * @param $vote_answer
     * @param PollQuestion $question
     * @param Collection $errors
     *
     */
    private function validateSingleAnswer($vote_answer, PollQuestion $question, Collection $errors)
    {
        if (array_key_exists('answer', $vote_answer)) {
            $slug = $vote_answer['answer'];
            /** @var PollAnswer $poll_answer */
            $poll_answer = $question->answers->first(function (PollAnswer $answer) use ($slug) {

                return $answer->slug === $slug;
            });
            if ($poll_answer == null) {
                $errors->put('vote.questions.answers.answer', "Nie istnieje taka odpowiedz ${slug}");
            } else {
                $vote_answer_item = new VoteAnswerItem();
                $vote_answer_item['poll_question_id'] = $question->id;
                $vote_answer_item['poll_answer_id'] = $poll_answer->id;
                return $vote_answer_item;
            }
        } else {
            $errors->put('vote.questions.answers.slug', 'Brakuje \'answer\' przy odpowiedziach');
        }
    }

}
