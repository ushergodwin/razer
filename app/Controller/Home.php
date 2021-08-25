<?php
use App\Controller\BaseController;
use App\Models\Model;

class Home extends BaseController
{
    public $model;


    public function __construct()
    {
        
        parent::__construct();
        $this->model = new Model();
    }

    public function index() {
        $context = [
            'title' => 'PHASER PHP LIBRARY |  HOME',
            'alert' => $this->response->RowHttpResponse('You have successfully installed the Phaser PHP Library')
        ];
        $this->render('index', $context);
    }
    
    public function testStaticTable() {
        //$affected = $this->model->update();
        $context = [
            "affected" => "0",
            "staff" => print_r($this->model->testLike())
        ];

        $this->render('test', $context);
    }
}