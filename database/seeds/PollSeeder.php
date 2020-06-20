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
        $poll1 = Poll::create(['slug' =>Str::slug('Prezydent Polski 2020'),'name' =>'Prezydent Polski 2020','from' => \Illuminate\Support\Carbon::minValue(),'to' => \Carbon\Carbon::maxValue()]);
        /** @var Poll $poll2 */
        // $poll2 = Poll::create(['slug' =>Str::slug('Glosowanie testowe nie aktywne'),'name' =>'Glosowanie testowe nie aktywne','from' => \Illuminate\Support\Carbon::minValue(),'to' => \Carbon\Carbon::now()->addDays(-1)]);
        // /** @var Poll $poll3 */
        // $poll3 = Poll::create(['slug' =>Str::slug('Glosowanie testowe3'),'name' =>'glosowanie testowe3','from' => \Illuminate\Support\Carbon::minValue(),'to' => \Carbon\Carbon::maxValue()]);
        /*
Robert BIEDROŃ
Krzysztof BOSAK
Andrzej Sebastian DUDA
Szymon Franciszek HOŁOWNIA
Marek JAKUBIAK
Władysław Marcin KOSINIAK-KAMYSZ
Mirosław Mariusz PIOTROWSKI
Paweł Jan TANAJNO
Rafał Kazimierz TRZASKOWSKI
Waldemar Włodzimierz WITKOWSKI
Stanisław Józek ŻÓŁTEK
*/


        $poll1->questions()->create(['type' => PollQuestion::SINGLE,'question' => 'Kto zostanie prezydentem Polski','slug' => Str::slug('dasdas_fhdsuufds')])->answers()->createMany(
            [
                ['answer' => 'Robert BIEDROŃ','slug' => Str::slug('Robert BIEDROŃ')],
                ['answer' => 'Krzysztof BOSAK','slug' => Str::slug('Krzysztof BOSAK')],
                ['answer' => 'Szymon Franciszek HOŁOWNIA','slug' => Str::slug('Szymon Franciszek HOŁOWNIA')],
                ['answer' => 'Marek JAKUBIAK','slug' => Str::slug('Marek JAKUBIAK')]
                ['answer' => 'Władysław Marcin KOSINIAK-KAMYSZ','slug' => Str::slug('Władysław Marcin KOSINIAK-KAMYSZ')]
                ['answer' => 'Mirosław Mariusz PIOTROWSKI','slug' => Str::slug('Mirosław Mariusz PIOTROWSKI')]
                ['answer' => 'Paweł Jan TANAJNO','slug' => Str::slug('Paweł Jan TANAJNO')]
                ['answer' => 'Rafał Kazimierz TRZASKOWSKI','slug' => Str::slug('Rafał Kazimierz TRZASKOWSKI')]
                ['answer' => 'Waldemar Włodzimierz WITKOWSKI','slug' => Str::slug('Waldemar Włodzimierz WITKOWSKI')]
                ['answer' => 'Stanisław Józek ŻÓŁTEK','slug' => Str::slug('Stanisław Józek ŻÓŁTEK')]
                ['answer' => 'Zaden','slug' => Str::slug('Zaden')]
            ]
        );
        // $poll1->questions()->create(['type' => PollQuestion::MULTI,'question' => 'Ulubione knajpy','slug' => Str::slug('Ulubione knajpy')])->answers()->createMany(
        //     [
        //         ['answer' => 'Zakaski przekaski','slug' => Str::slug('Zakaski przekaski')],
        //         ['answer' => 'Zapiecek','slug' => Str::slug('Zapiecek')],
        //         ['answer' => 'U Magdy Gessler','slug' => Str::slug('U Magdy Gessler')],
        //         ['answer' => 'Nie jadam na miescie','slug' => Str::slug('Nie jadam na miescie')]
        //     ]
        // );
        //     $poll1->questions()->create(['type' => PollQuestion::SINGLE,'question' => 'Który utwor jest najfajniejszy','slug' => Str::slug('Który utwor jest najfajniejszy')])->answers()->createMany(
        //          [
        //              ['answer' => 'Mój sokole','slug' => Str::slug('Mój sokole')],
        //              ['answer' => 'Mój jest ten kawałek podłogi','slug' => Str::slug('Mój jest ten kawałek podłogi')],
        //              ['answer' => 'Mój ból jest gorszy niż twój','slug' => Str::slug('Mój ból jest gorszy niż twój')],
        //              ['answer' => 'Meluzyna','slug' => Str::slug('Meluzyna')]
        //          ]
        // );

        // $poll2->questions()->create(['type' => PollQuestion::SINGLE,'question' => 'Kto zostanie mistrzem Polski','slug' => Str::slug('Kto zostanie mistrzem Polski')])->answers()->createMany(
        //     [
        //         ['answer' => 'Skra','slug' => Str::slug('Skra')],
        //         ['answer' => 'ZAKSA','slug' => Str::slug('ZAKSA')],
        //         ['answer' => 'Odra Opole','slug' => Str::slug('Odra Opole')],
        //         ['answer' => 'Berlin Racing Team','slug' => Str::slug('Berlin Racing Team')]
        //     ]
        // );
        // $poll3->questions()->create(['type' => PollQuestion::SINGLE,'question' => 'Jaki system operacyjny używasz w lapku swym z którym chodzisz po miastach i wsiach polski?','slug' => Str::slug('Jaki system operacyjny używasz w lapku swym z którym chodzisz po miastach i wsiach polski?')])->answers()->createMany(
        //     [
        //         ['answer' => 'Windows','slug' => Str::slug('Windows')],
        //         ['answer' => 'Inny gorszy','slug' => Str::slug('Inny gorszy')],
        //     ]
        // );

    }
}
