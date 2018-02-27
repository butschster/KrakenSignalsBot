<?php

Route::get('/logs', 'LogsController@index')->name('logs');
Route::get('/alerts', 'AlertsController@index')->name('alerts');
Route::get('/', 'HomeController@index');
Auth::routes();