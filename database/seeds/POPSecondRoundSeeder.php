<?php

use App\Domain\Poll\Poll;
use Illuminate\Database\Seeder;

class POPSecondRoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var Poll $poll1 */
        $poll1 = Poll::create(['slug' =>Str::slug('Prezydent Polski 2020 II tura'),'name' =>'Prezydent Polski 2020 II tura','from' => \Illuminate\Support\Carbon::minValue(),'to' => new \Illuminate\Support\Carbon("2020-07-12")]);

        $poll1->questions()->create(['type' => PollQuestion::SINGLE,'question' => 'Na kogo oddasz głos w drugiej turze wyborów prezydenckich 2020','slug' => Str::slug('Na kogo oddasz głos w drugiej turze')])->answers()->createMany(
            ['answer' => 'DUDA Andrzej Sebastian','slug' => Str::slug('DUDA Andrzej Sebastian')],
            ['answer' => 'TRZASKOWSKI Rafał Kazimierz','slug' => Str::slug('TRZASKOWSKI Rafał Kazimierz')],
            ['answer' => 'Żaden/nie głosuje','slug' => Str::slug('Żaden/nie głosuje')]
        );
    }
}
