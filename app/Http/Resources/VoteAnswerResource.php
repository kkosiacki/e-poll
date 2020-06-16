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
            'status' => $this->status,
            'epuap_podpis' => $this->signature_date,
            'stworzono' => $this->created_at,
            'zarejestrowano' => $this->when($this->updated_at->notEqualTo($this->created_at), $this->updated_at),
            'odpowiedzi' => VoteAnswerItemResource::collection($this->vote_answer_items)
        ];
    }
}
