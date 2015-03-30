<?php

Route::get('/', 'HomeController@index');


Route::resource('shoot','ScreenshotController');
