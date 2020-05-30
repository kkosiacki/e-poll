<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoteAnswerItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_answer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vote_answer_id')->nullable(false);
            $table->unsignedInteger('poll_id')->nullable(false);
            $table->unsignedInteger('poll_question_id')->nullable(false);
            $table->unsignedInteger('poll_answer_id')->nullable(false);
            $table->foreign('vote_answer_id')->references('id')->on('vote_answers');
            $table->foreign('poll_id')->references('id')->on('polls');
            $table->foreign('poll_question_id')->references('id')->on('poll_questions');
            $table->foreign('poll_answer_id')->references('id')->on('poll_answers');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_answer_items');
    }
}
