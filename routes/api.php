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


    // auth
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

    // movie
    Route::get('movie/list', [\App\Http\Controllers\Api\MovieController::class, 'list']);
    Route::get('movie/{movie}/detail', [\App\Http\Controllers\Api\MovieController::class, 'show']);

    // pdf download
    Route::get('/movie/{movie}/pdf-download', [\App\Http\Controllers\Api\MovieController::class, 'downloadPdf']);

    Route::middleware('auth:api')->group(function () {

        // comment
        Route::post('comment/store', [\App\Http\Controllers\Api\CommentController::class, 'store']);

        // movie
        Route::apiResource('/movies', \App\Http\Controllers\Api\MovieController::class)->except('show');
    });


