<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('quizzes', '\App\Http\Controllers\QuizController@getList');
    Route::get('quizzes/{id}', '\App\Http\Controllers\QuizController@getDetail');
    Route::get('quizzes/code/{code}', '\App\Http\Controllers\QuizController@getByCode');
    Route::post('quizzes/{code}/start', '\App\Http\Controllers\QuizController@startQuiz');
    Route::post('quizzes/{code}/submit', '\App\Http\Controllers\QuizController@submitQuiz');
});
