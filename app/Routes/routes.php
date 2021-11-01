<?php

use App\Controller\Home;
use System\Routes\Route;

Route::get('/', [Home::class, 'index']);

