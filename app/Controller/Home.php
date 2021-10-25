<?php
use App\Controller\BaseController;
use System\Http\Request;

class Home extends BaseController
{
    public function index() {

        $context = [
            'title' => 'PHASER PHP LIBRARY |  HOME',
        ];
        return render('index', $context);
    }
}