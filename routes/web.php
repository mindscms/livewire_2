<?php

use App\Http\Livewire\ShowPost;
use Illuminate\Support\Facades\Route;

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



Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/posts', function () {
        return view('posts');
    })->name('posts');

    Route::get('/{slug}', ShowPost::class)->name('show_post');

});


require __DIR__.'/auth.php';



Route::get('/', function () {
    return view('welcome');
});
