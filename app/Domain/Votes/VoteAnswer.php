<?php

namespace App\Domain\Votes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;


/**
 * Class VoteAnswer
 * @package App\Domain\Votes
 * @property string $uuid
 * @property string $status
 * @property string $pesel
 * @property string $first_name
 * @property string $last_name
 * @property Carbon $signature_date
 * @property string $file_name
 * @property Collection $vote_answer_items
 */
class VoteAnswer extends Model
{
    const STATUS_CREATED = 'created';
    const STATUS_VERIFIED = 'verified';
    const STATUS_OVERWRITTEN = 'overwritten';

    protected $hidden = ['id'];

    protected $with = ['vote_answer_items'];

    protected $dates = [
        'signature_date',
    ];

    protected $attributes = [
        'status' => self::STATUS_CREATED,
    ];

    public function vote_answer_items()
    {
        return $this->hasMany('App\Domain\Votes\VoteAnswerItem');
    }

}
