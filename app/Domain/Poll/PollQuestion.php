<?php

namespace App\Domain\Poll;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PollQuestion
 * @package App\Domain\Poll
 * @property integer $id
 * @property string $type
 * @property string $question
 * @property Poll $poll
 */
class PollQuestion extends Model
{
    protected $fillable = ['type','question','slug'];

    protected $hidden = ['id'];

    const MULTI = 'multi';
    const SINGLE = 'single';

    protected $with = ['answers'];

    public function answers()
    {
        return $this->hasMany('App\Domain\Poll\PollAnswer');
    }
}
