<?php

use System\Controller\Route;

Route::get('', [Home::class, 'index']);
Route::get('test', [Home::class, 'testStaticTable']);
Route::dynamic('user', [Home::class, 'testDynamicUrl']);


