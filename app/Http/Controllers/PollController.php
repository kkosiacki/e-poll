<?php

namespace App\Http\Controllers;

use App\Domain\Poll\Poll;
use App\Domain\Poll\PollAnswer;
use App\Domain\Poll\PollQuestion;
use App\Domain\Votes\VoteAnswer;
use App\Http\Resources\PollResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    public function getAllActive() {
        return PollResource::collection(Poll::query()->where([['from','<',Carbon::now()],['to','>',Carbon::now()]])->get());
    }

    public function getAllFinished() {
        return PollResource::collection(Poll::query()->where('to','<',Carbon::now())->get());
    }

    public function getResults(string $poll_slug) {
        /** @var Poll $poll */
        $poll = Poll::query()->where('slug','=',$poll_slug)->firstOrFail();

        $raw_answer = DB::select('SELECT i.poll_question_id, i.poll_answer_id
            , COUNT(i.vote_answer_id) as count FROM vote_answer_items i RIGHT JOIN vote_answers a ON (i.vote_answer_id = a.id AND a.status = \''.VoteAnswer::STATUS_VERIFIED.'\') WHERE poll_id = '.$poll->id.' GROUP BY i.poll_question_id,i.poll_answer_id');
         $answer = collect($raw_answer)->map(function ($ans) use ($poll) {
             /** @var PollQuestion $question */
             $question = $poll->questions->whereStrict('id',$ans->poll_question_id)->first();
             /** @var PollAnswer $answer */
             $answer = $question->answers->whereStrict('id',$ans->poll_answer_id)->first();
             $obj =  ['question_slut' => $question->slug, 'question' => $question->question, 'answer' => $answer->answer,'answer_slug' => $answer->slug, 'count' => $ans->count ];
             info('soung',$obj);
             return $obj;
         });
        return response()->json($answer);
    }
}
