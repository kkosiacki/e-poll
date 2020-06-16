<?php

namespace App\Http\Resources;

use App\Domain\Votes\VoteAnswerItem;
use Illuminate\Http\Resources\Json\JsonResource;

class VoteResource extends JsonResource
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
          'id' => $this->uuid,
          'stworzono' => $this->created_at,
          'odpowiedzi' => VoteAnswerItemResource::collection($this->vote_answer_items)

        ];
    }
}
