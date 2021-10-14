<?php

use System\Controller\Route;
use System\HttpRequest\HttpRequest;

Route::get('', [Home::class, 'index']);
Route::get('test', [Home::class, 'testInsertAndUpdate']);
Route::dynamic('user', [Home::class, 'testDynamicUrl']);
Route::get('test2', function(HttpRequest $request){
    echo $request->q;
});


