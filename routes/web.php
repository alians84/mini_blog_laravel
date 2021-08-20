<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[PostController::class,'index'])
    ->name('blog.index');

Route::group([
    'as'=>'post.',//имя маршрута
    'prefix'=>'post',//префикс маршрута
//    'middleware'=>['auth']//один или несколько посредников
],function(){

    Route::get('index',[PostController::class,'index'])
        ->name('index');
    Route::get('search',[PostController::class,'search'])
        ->name('search');
    Route::get('create',[PostController::class,'create'])
        ->name('create');
    Route::post('store',[PostController::class,'store'])
        ->name('store');
    Route::get('show/{id}',[PostController::class,'show'])
        ->name('show');
    Route::get('edit/{id}',[PostController::class,'edit'])
        ->name('edit');
    Route::patch('update/{id}',[PostController::class,'update'])
        ->name('update');
    Route::delete('destroy/{id}',[PostController::class,'destroy'])
        ->name('destroy');
});




Auth::routes();
