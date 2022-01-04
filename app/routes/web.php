<?php

use App\Controller\Home;
use System\Routes\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteProvider within the app. Now create something great!
|
*/
Route::get('/', [Home::class, 'index']);
