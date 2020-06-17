<?php


namespace App\Services;


use App\Domain\Poll\Poll;
use App\Domain\Poll\PollAnswer;
use App\Domain\Poll\PollQuestion;
use App\Domain\Votes\VoteAnswer;
use App\Domain\Votes\VoteAnswerItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use stdClass;
use Opis\JsonSchema\{
    Validator, ValidationResult, ValidationError, Schema
};

class VoteService
{

    //TODO: just upload file and verify in ePUAP - more to do
    // 1. sync and (future async)
    public function verifyVoteFile(string $file_name) {
        if(!Storage::exists($file_name)) {
            throw ValidationException::withMessages(['file' => 'Brak pliku do walidacji']);
        } else {
            /** @var VoteAnswer $vote_answer */
            $vote_answer = $this->verifyFileContent(Storage::get($file_name));
            info('VoteAnswer found nad valid',[$vote_answer->uuid]);
            $epuap_answer = $this->veriifyInePUAP($file_name);
            if(VoteAnswer::query()->where('pesel',$epuap_answer->pesel)->exists()) {
                //TODO: check if the same vote_id -> then error...
                throw ValidationException::withMessages(['file' => 'Ten pesel juz glosowal']);
            } else {
                $vote_answer->pesel = $epuap_answer->pesel;
                $vote_answer->first_name = $epuap_answer->firstName;
                $vote_answer->last_name = $epuap_answer->lastName;
                $vote_answer->signature_date = $epuap_answer->signatureDate;
                $vote_answer->status = VoteAnswer::STATUS_VERIFIED;
                $vote_answer->update();
                return $vote_answer;
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


    protected function veriifyInePUAP(string $file_name) {

            info("[VoteService] veriifyInePUAP ${file_name}");
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
                    logger()->error('Wrong answer status',[$status->status]);
                    throw ValidationException::withMessages(['file' => 'Nieudana weryfikacja w ePUAP']);
                } else {
                    $response_string =$verify_response->getBody()->getContents();
                    info('response from epuap '.$response_string);
                     $json_string = json_decode($response_string);
                    if($json_string) {
                        //Always one element array
                        return head($json_string);
                    } else {
                        logger()->error('NIe udalo sie zjesonowac stringa',[$json_string]);
                        throw ValidationException::withMessages(['file' => 'Nieudana weryfikacja w ePUAP']);
                    }

                }
            } else {
                logger()->error('Wrong ststaus',[$status->status]);
                throw ValidationException::withMessages(['file' => 'Nieudana weryfikacja w ePUAP']);
            }


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
            $slug = (string) $vote_answer['answer'];
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

    private function verifyFileContent(string $content) :VoteAnswer
    {

        $epuap_xml = simplexml_load_string($content,"SimpleXMLElement",);
        $epuap_xml->registerXPathNamespace('wnio','http://epuap.gov.pl/fe-model-web/wzor_lokalny/EPUAP-----/podpisanyPlik/');
        $epuap_xml->registerXPathNamespace('str','http://crd.gov.pl/xml/schematy/struktura/2009/11/16/');
        $xpathArray = $epuap_xml->xpath('/wnio:Dokument/wnio:TrescDokumentu/str:Zalaczniki/str:Zalacznik/str:DaneZalacznika');
        if($xpathArray) {
            $content = json_decode(base64_decode((string) $xpathArray[0]));
            if($content) {
                $schema = Schema::fromJsonString(File::get(base_path('').'/vote_schema.json'));
                $validator = new Validator();

                /** @var ValidationResult $result */
                $result = $validator->schemaValidation($content, $schema);
                if($result->isValid()) {
                    return $this->ckeclVoteAnswers($content);
                }
            }
        }
        throw ValidationException::withMessages(['file' => 'Zly format pliku podpisu']);
    }

    private function ckeclVoteAnswers(StdClass $content) :VoteAnswer
    {
        info('fdsifsudh',[$content]);
        /** @var VoteAnswer  $vote_answer */
        $vote_answer = VoteAnswer::query()->where('uuid',$content->id)->firstOrFail();
        if(count($content->odpowiedzi) === $vote_answer->vote_answer_items()->count()) {
            foreach ($content->odpowiedzi as $vote_answer_item) {
                if(!$vote_answer->vote_answer_items->contains(function (VoteAnswerItem $item) use ($vote_answer_item) {
                    return $item->poll->slug === $vote_answer_item->glosowanie &&
                        $item->poll_question->slug === $vote_answer_item->pytanie &&
                        $item->poll_answer->slug === $vote_answer_item->odpowiedz;
                })){
                    throw ValidationException::withMessages(['file' => 'Zle dane w podpisanym pliku']);
                }
            }
            return $vote_answer;
        }
        throw ValidationException::withMessages(['file' => 'Zle dane w podpisanym pliku']);
    }

}
