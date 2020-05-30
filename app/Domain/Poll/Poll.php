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
    protected $fillable = ['name','slug','from','to'];


    protected $hidden = ['id'];

    protected $with = ['questions'];

    public function questions()
    {
        return $this->hasMany('App\Domain\Poll\PollQuestion');
    }
}
