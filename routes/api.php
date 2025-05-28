<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;

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


Route::group(['prefix' => 'private'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::group(["prefix" => "protected", "middleware" => ['jwt.auth']], function (){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/event/image/upload', [EventController::class, 'uploadEventImage']);
    Route::post('/event/create', [EventController::class, 'createEvent']);
    Route::put('/event/update', [EventController::class, 'updateEvent']);
    Route::delete('/event/delete', [EventController::class, 'deleteEvent']);
    Route::get('/event', [EventController::class, 'getAllAdminEvents']);
    Route::get('/event/lastDate', [EventController::class, 'getLastDateEvents']);
    Route::post('/event/category/create', [EventController::class, 'createCategory']);
    Route::delete('/event/category/delete', [EventController::class, 'deleteCategory']);
    Route::get('/event/category', [EventController::class, 'fetchAllCategory']);


});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
