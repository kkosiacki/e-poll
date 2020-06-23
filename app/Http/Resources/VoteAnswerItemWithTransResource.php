<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VoteAnswerItemWithTransResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       return [ 'poll' => $this->poll->name,
                'question' => $this->poll_question->question,
                'answer' => $this->poll_answer->answer
           ];
    }
}
