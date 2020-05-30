<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VoteAnswerItemResource extends JsonResource
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
            'glosowanie' => $this->poll->slug,
            'pytanie' => $this->poll_question->slug,
            'odpowiedz' => $this->poll_answer->slug

        ];
    }
}
