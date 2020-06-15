<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('verify', 'VoteController@verifyVote');

//TODO: not allowed to create polls with slug active or finished
Route::get('polls/active', 'PollController@getAllActive');
Route::get('polls/finished', 'PollController@getAllFinished');
Route::get('polls/{poll_slug}', 'PollController@getPoll');
Route::get('results/{poll_slug}', 'PollController@getResults');


Route::post('votes','VoteController@vote');
