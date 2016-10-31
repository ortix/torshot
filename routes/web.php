<?php

Route::get('/test', function() {
   phpinfo();
});

Route::get('/', function () {
    return view('pages.home');
});
