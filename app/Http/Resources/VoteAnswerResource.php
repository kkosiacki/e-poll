<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;



class VoteAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'status' => $this->status,
            'signature_date' => $this->signature_date,
            'created'=> $this->created_at,
            'uploaded' => $this->when($this->updated_at->notEqualTo($this->created_at), $this->updated_at),
            'answers' => VoteAnswerItemWithTransResource::collection($this->vote_answer_items)
        ];
    }
}
