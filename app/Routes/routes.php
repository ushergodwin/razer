<?php

use System\Controller\Route;

Route::get('', 'Home::index');
Route::get('test', 'Home::testStaticTable');
Route::dynamic('user', 'Home::testDynamicUrl::$1');

Route::view('basic/route', 'basic', ["title" => 'Basic Route']);

Route::get('callback', function(){
    if ($_SERVER['REQUEST_METHOD'] !== "POST") {
        throw new Exception('not post');
    }
});

Route::get('basic/user', function() {
    Table('interns')->use('internship')
    ->between(["year_of_study" => [2,3]])
    ->get('print_r');
});
