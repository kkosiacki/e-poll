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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('verify', 'VoteController@verifyVote');


Route::get('polls', 'PollController@getPolls');

Route::get('polls/{poll_slug}', 'PollController@getPoll');
Route::get('results/{poll_slug}', 'PollController@getResults');


// Route::post('votes','VoteController@vote');
Route::post('votes',function() {
    return abort(500, 'Cisza wyborcza');
};
Route::get('votes/{uuid}','VoteController@getVote');
