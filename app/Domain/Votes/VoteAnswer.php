<?php

namespace App\Domain\Votes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


/**
 * Class VoteAnswer
 * @package App\Domain\Votes
 * @property string $uuid
 * @property string $status
 * @property Collection $vote_answer_items
 */
class VoteAnswer extends Model
{
    const STATUS_CREATED = 'created';
    const STATUS_VERIFIED = 'verified';

    protected $hidden = ['id'];

    protected $with = ['vote_answer_items'];

    protected $attributes = [
        'status' => self::STATUS_CREATED,
    ];

    public function vote_answer_items()
    {
        return $this->hasMany('App\Domain\Votes\VoteAnswerItem');
    }

}
