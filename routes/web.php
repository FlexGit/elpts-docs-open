<?php

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

Route::post('/ajaxEmailConfirmCode', 'ElptsController@ajaxEmailConfirmCode')->name('ajaxEmailConfirmCode.post');
Route::post('/ajaxOgrnCheck', 'ElptsController@ajaxOgrnCheck')->name('ajaxOgrnCheck.post');
Route::post('/ajaxDocSave', 'ElptsController@ajaxDocSave')->name('ajaxDocSave.post');
Route::post('/ajaxDocSign', 'ElptsController@ajaxDocSign')->name('ajaxDocSign.post');

Route::get('/', 'ElptsController@index')->name('elpts.index');
Route::get('/{templates_id}/create', 'ElptsController@create')->name('elpts.create');
Route::post('/{templates_id}', 'ElptsController@store')->name('elpts.store');

Route::get('/file/{file}', 'ElptsController@file')->name('elpts.file');

Route::fallback(function () {
    abort(404);
});