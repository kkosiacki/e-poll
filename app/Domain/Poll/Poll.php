<?php

namespace App\Domain\Poll;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Psy\Util\Str;

/**
 * Class Poll
 * @package App\Domain\Poll
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property Carbon $from
 * @property Carbon $to
 * @property Collection $questions
 */
class Poll extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_FINISHED = 'finished';
    const STATUS_NOT_STARTED = 'not_started';

    protected $fillable = ['name','slug','from','to'];


    protected $hidden = ['id'];

//    protected $with = ['questions'];

    public function questions()
    {
        return $this->hasMany('App\Domain\Poll\PollQuestion');
    }

    public function getStatusAttribute()
    {
        /** @var Carbon $now */
        $now = Carbon::now();
        if($now->isBetween($this->from,$this->to)) {
            return Poll::STATUS_ACTIVE;
        }
        if($now->isAfter($this->to)) {
            return Poll::STATUS_FINISHED;
        }
        if($now->isBefore($this->from)) {
            return Poll::STATUS_NOT_STARTED;
        }
        info('WRONG STATUS',[$this]);
        return "UNDEFINED";
    }
}
