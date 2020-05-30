<?php

namespace App\Domain\Votes;

use Illuminate\Database\Eloquent\Model;

class VoteAnswerItem extends Model
{
    protected $hidden = ['id'];

    public $timestamps = false;

    public function poll_answer()
    {
        return $this->belongsTo('App\Domain\Poll\PollAnswer');
    }

    public function poll_question()
    {
        return $this->belongsTo('App\Domain\Poll\PollQuestion');
    }

    public function poll()
    {
        return $this->belongsTo('App\Domain\Poll\Poll');
    }

    public function vote_answer()
    {
        return $this->belongsTo('App\Domain\Votes\VoteAnswer');
    }
}
