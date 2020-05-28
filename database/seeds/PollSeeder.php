<?php

use App\Domain\Poll\Poll;
use App\Domain\Poll\PollQuestion;
use Illuminate\Database\Seeder;

class PollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var Poll $poll1 */
        $poll1 = Poll::create(['slug' =>Str::slug('Glosowanie testowe'),'name' =>'Glosowanie testowe','from' => \Illuminate\Support\Carbon::minValue(),'to' => \Carbon\Carbon::maxValue()]);
        /** @var Poll $poll2 */
        $poll2 = Poll::create(['slug' =>Str::slug('Glosowanie testowe nie aktywne'),'name' =>'Glosowanie testowe nie aktywne','from' => \Illuminate\Support\Carbon::minValue(),'to' => \Carbon\Carbon::now()->addDays(-1)]);


        $poll1->questions()->create(['type' => PollQuestion::SINGLE,'question' => 'Kto zostanie prezydentem Polski','slug' => Str::slug('dasdas_fhdsuufds')])->answers()->createMany(
            [
                ['answer' => 'Adrian','slug' => Str::slug('Adrian')],
                ['answer' => 'Marian','slug' => Str::slug('Marian')],
                ['answer' => 'Obama','slug' => Str::slug('Obama')],
                ['answer' => 'Zaden','slug' => Str::slug('Zaden')]
            ]
        );
        $poll1->questions()->create(['type' => PollQuestion::MULTI,'question' => 'Ulubione knajpy','slug' => Str::slug('Ulubione knajpy')])->answers()->createMany(
            [
                ['answer' => 'Zakaski przekaski','slug' => Str::slug('Zakaski przekaski')],
                ['answer' => 'Zapiecek','slug' => Str::slug('Zapiecek')],
                ['answer' => 'U Magdy Gessler','slug' => Str::slug('U Magdy Gessler')],
                ['answer' => 'Nie jadam na miescie','slug' => Str::slug('Nie jadam na miescie')]
            ]
        );

        $poll2->questions()->create(['type' => PollQuestion::SINGLE,'question' => 'Kto zostanie mistrzem Polski','slug' => Str::slug('Kto zostanie mistrzem Polski')])->answers()->createMany(
            [
                ['answer' => 'Skra','slug' => Str::slug('Skra')],
                ['answer' => 'ZAKSA','slug' => Str::slug('ZAKSA')],
                ['answer' => 'Odra Opole','slug' => Str::slug('Odra Opole')],
                ['answer' => 'Berlin Racing Team','slug' => Str::slug('Berlin Racing Team')]
            ]
        );
    }
}
