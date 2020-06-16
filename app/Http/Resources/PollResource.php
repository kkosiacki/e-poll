<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PollResource extends JsonResource
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
            'slug' => $this->slug,
            'title' => $this->name,
            'status' => $this->status,
            'from' => $this->from,
            'to' => $this->to,
            'questions' => PollQuestionResource::collection($this->whenLoaded('questions')),

            ];
    }
}
