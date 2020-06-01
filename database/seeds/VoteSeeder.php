<?php

use Illuminate\Database\Seeder;

class VoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var \App\Domain\Votes\VoteAnswer $v */
        $v = \App\Domain\Votes\VoteAnswer::create(['status' => 'created','uuid' => 'ce71d34a-a7c1-4916-a434-10cbb2add6ab']);

        $v->vote_answer_items()->createMany([
           ['poll_id'  =>1,'poll_question_id' =>1,'poll_answer_id' => 1],
            ['poll_id'  =>1,'poll_question_id' =>2,'poll_answer_id' => 5],
            ['poll_id'  =>1,'poll_question_id' =>2,'poll_answer_id' => 6],
            ['poll_id'  =>1,'poll_question_id' =>2,'poll_answer_id' => 7],
            ['poll_id'  =>3,'poll_question_id' =>4,'poll_answer_id' => 13],
        ]);


        \App\Domain\Votes\VoteAnswer::create(['status' => \App\Domain\Votes\VoteAnswer::STATUS_VERIFIED,'uuid' => 'de71d34a-a7c1-4916-gdfgdf34-10cbb2add6ab'])->vote_answer_items()->createMany([
            ['poll_id'  =>1,'poll_question_id' =>1,'poll_answer_id' => 2],
            ['poll_id'  =>1,'poll_question_id' =>2,'poll_answer_id' => 5],
            ['poll_id'  =>1,'poll_question_id' =>2,'poll_answer_id' => 6],
            ['poll_id'  =>3,'poll_question_id' =>4,'poll_answer_id' => 14],
        ]);

        \App\Domain\Votes\VoteAnswer::create(['status' => \App\Domain\Votes\VoteAnswer::STATUS_VERIFIED,'uuid' => 'gdf-a7c1-4916-a434-10cbb2hgfdd6ab'])->vote_answer_items()->createMany([
            ['poll_id'  =>1,'poll_question_id' =>1,'poll_answer_id' => 3],
            ['poll_id'  =>1,'poll_question_id' =>2,'poll_answer_id' => 8],
            ['poll_id'  =>1,'poll_question_id' =>2,'poll_answer_id' => 6],
            ['poll_id'  =>1,'poll_question_id' =>2,'poll_answer_id' => 7],
            ['poll_id'  =>3,'poll_question_id' =>4,'poll_answer_id' => 14],
        ]);

    }
}
