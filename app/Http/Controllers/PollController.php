<?php

namespace App\Http\Controllers;

use App\Domain\Poll\Poll;
use App\Http\Resources\PollResource;
use Illuminate\Support\Carbon;

class PollController extends Controller
{
    public function getAllActive() {
        return PollResource::collection(Poll::query()->where([['from','<',Carbon::now()],['to','>',Carbon::now()]])->get());
    }
}
