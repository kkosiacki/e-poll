<?php

namespace App\Http\Controllers;

use App\Domain\Votes\VoteAnswer;
use App\Http\Resources\VoteAnswerResource;
use App\Services\VoteService;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * @var VoteService
     */
    private $vote_service;


    /**
     * VerifyController constructor.
     */
    public function __construct(VoteService $vote_service)
    {
        $this->vote_service = $vote_service;
    }

    public function verify(Request $request) {
        $file_name =  $request->file('file')->store('to_verify');

        $this->vote_service->verifyVoteFile($file_name);
    }

    public function vote(Request $request) {

        $input = $request->validate(['votes' =>['required']]);
        /** @var VoteAnswer $vote */
        $vote = $this->vote_service->saveVote($input['votes']);
        $resource = new VoteAnswerResource($vote);
        return response($resource->toJson(JSON_PRETTY_PRINT))
            ->withHeaders([
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'attachment; filename="'.$vote->uuid.'".txt"'
            ]);
    }
}
