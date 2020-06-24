<?php

namespace App\Domain\Poll;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class PollQuestion
 * @package App\Domain\Poll
 * @property integer $id
 * @property string $type
 * @property string $question
 * @property Poll $poll
 * @property Collection $answers
 */
class PollQuestion extends Model
{
    protected $fillable = ['type','question','slug'];

    protected $hidden = ['id','poll_id'];

    public $timestamps = false;

    const MULTI = 'multi';
    const SINGLE = 'single';

    protected $with = ['answers'];

    public function answers()
    {
        return $this->hasMany('App\Domain\Poll\PollAnswer')->orderBy('answer');
    }
}
