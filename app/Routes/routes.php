<?php

use System\Controller\Route;

Route::To('', 'Home::index');
Route::To('test', 'Home::testStaticTable');
Route::Dynamic('user', 'Home::testDynamicUrl::$1');
