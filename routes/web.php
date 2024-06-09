<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ToDoListController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::controller(ToDoListController::class)->prefix('lists')->group(function () {
        Route::get('', 'index')->name('todolist.index');
        Route::post('', 'store')->name('todolist.store');
        Route::get('{toDoList}/edit', 'edit')->name('todolist.edit');
        Route::delete('delete/{toDoList}', 'destroy')->name('todolist.destroy');
        Route::patch('{toDoList}', 'update')->name('todolist.update');
        Route::get('{toDoList}', 'show')->name('todolist.show');
    });

    Route::controller(TaskController::class)->prefix('lists/{toDoList}/tasks')->group(function () {
        Route::get('', 'index')->name('tasks.index');
        Route::post('', 'store')->name('tasks.store');
        Route::get('{task}/edit', 'edit')->name('tasks.edit');
        Route::delete('delete/{task}', 'destroy')->name('tasks.destroy');
        Route::patch('{task}', 'update')->name('tasks.update');
        Route::get('{task}', 'show')->name('tasks.show');
    });
});

