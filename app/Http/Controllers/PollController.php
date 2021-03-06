<?php

namespace App\Http\Controllers;

use App\Domain\Poll\Poll;
use App\Domain\Poll\PollAnswer;
use App\Domain\Poll\PollQuestion;
use App\Domain\Votes\VoteAnswer;
use App\Http\Resources\PollResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class PollController extends Controller
{
    public function getPolls() {
        return PollResource::collection(
            QueryBuilder::for(Poll::class)
                ->allowedFilters([
                    AllowedFilter::callback('status', function (Builder $query, $value) {
                        if ($value === Poll::STATUS_ACTIVE) {
                           $query->where([['from','<',Carbon::now()],['to','>',Carbon::now()]]);
                        } elseif ($value === Poll::STATUS_FINISHED) {
                            $query->where('to','<',Carbon::now());
                        } elseif ($value === Poll::STATUS_NOT_STARTED) {
                            $query->where('from','>',Carbon::now());
                        }
                    }),
                ])
                //->with('questions')
                ->get()
            //Poll::query()->where([['from','<',Carbon::now()],['to','>',Carbon::now()]])->get()
        );
    }

    public function getPoll(string $poll_slug) {
        return new PollResource(Poll::query()->where([['from','<',Carbon::now()],['to','>',Carbon::now()],['slug','=',$poll_slug]])->with('questions')->firstOrFail());
    }

    //public function getAllFinished() {
    //    return PollResource::collection(Poll::query()->where('to','<',Carbon::now())->get());
    //}

    public function getResults(string $poll_slug) {
        /** @var Poll $poll */
        $poll = Poll::query()->where('slug','=',$poll_slug)->firstOrFail();

        $raw_answer = DB::select('SELECT i.poll_question_id, i.poll_answer_id
            , COUNT(i.vote_answer_id) as count FROM vote_answer_items i RIGHT JOIN vote_answers a ON (i.vote_answer_id = a.id AND a.status = \''.VoteAnswer::STATUS_VERIFIED.'\') WHERE poll_id = '.$poll->id.' GROUP BY i.poll_question_id,i.poll_answer_id');
        /** @var Collection $answer */
         $answer = collect($raw_answer)->map(function ($ans) use ($poll) {
             /** @var PollQuestion $question */
             $question = $poll->questions->whereStrict('id',$ans->poll_question_id)->first();
             /** @var PollAnswer $answer */
             $answer = $question->answers->whereStrict('id',$ans->poll_answer_id)->first();
             $obj =  [
                 'poll' => $poll->name,
                 'poll_slug' => $poll->slug,
                 'question_slug' => $question->slug,
                 'question' => $question->question,
                 'answer' => $answer->answer,
                 'answer_slug' => $answer->slug,
                 'count' => $ans->count ];
             return $obj;
         });
        return response()->json($answer);
    }
}
