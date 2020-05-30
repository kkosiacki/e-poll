<?php

namespace App\Domain\Poll;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PollAnswer
 * @package App\Domain\Poll
 * @property int $id
 * @property string $answer
 * @property string $slug
 */
class PollAnswer extends Model
{
    protected $fillable = ['answer','slug'];

    protected $hidden = ['id','poll_question_id'];

    public $timestamps = false;
}
