<?php
use App\Controller\BaseController;
use App\Models\testModel;

class Home extends BaseController
{
    public $model;


    public function __construct()
    {
        
        parent::__construct();
        $this->model = new testModel();
    }

    public function index() {
        $context = [
            'title' => 'PHASER PHP LIBRARY |  HOME',
            'alert' => $this->response->http('You have successfully installed the Phaser PHP Library')
        ];
        $this->render('index', $context);
    }
    
    public function testStaticTable() {
        //$affected = $this->model->update();
        $staff = array_to_object($this->model->testLike());
        $context = [
            "affected" => "0",
            "staff" => $staff
        ];

        $this->render('test', $context);
    }
}