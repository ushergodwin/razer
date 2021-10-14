<?php
use App\Controller\BaseController;
use App\Models\Interns;
use System\Database\DB;
use System\Database\Grammer\Grammer;

class Home extends BaseController
{
    public $model;


    public function __construct()
    {
        
        parent::__construct();
        // $this->model = new testModel();
    }

    public function index() {
        $context = [
            'title' => 'PHASER PHP LIBRARY |  HOME',
            'alert' => ''
        ];
        $data = DB::table('interns')->where('year_of_study', 2)->orderBy('id', 'DESC')->get();
        print_r($data);

        $data2 = DB::table('interns')->row()->where('email', 'rytah@gmail.com')->get();

        echo "<br/> <br/>";

        print_r($data2);

        echo "<br/> <br/>";

        $data3 = DB::table('interns')->where('email', 'rytah@gmail.com')->value('gender');
        echo $data3;

        echo "<br/> <br/>";
        $data4 = DB::table('interns')->range(5, 10)->get();

        print_r($data4);
        
        echo "<br/> <br/>";
        $data5 = DB::table('interns')->range(5, 10)->count();
        echo $data5;

        echo "<br/> <br/>";
        $data6 = DB::table('interns')->where('year_of_study', 4)->avg('id');
        echo $data6;

        echo "<br/> <br/>";

        $data7 = DB::use('tb_monitor', 'staff')
        ->join('appointments', 'staff.email', 'appointments.doctor_id')
        ->join('patients', 'appointments.patient_id', 'patients.email')
        ->where('appointments.doctor_id', 'godwintumuhimbise96@gmail.com')->get();
        echo "<br/> <br/>";
        print_r($data7);
        
        echo "<br/> <br/>";

        $data8 = DB::table('interns')
        ->like('last_name', '%Godwin')
        ->like('id', '%95%')
        ->get();

        print_r($data8);
        
        echo "<br/> <br/>";
        $data9 = DB::table('interns')
        ->between('year_of_study', 1, 3)
        ->between('id', 95701, 95706)
        ->orderBy('email')->get();

        print_r($data9);

        echo "<br/> Row Query <br/>";

        // $data10 = DB::table('interns')
        // ->unionJoin('admin')->get();
        // print_r($data10);

        $data11 = DB::query("SELECT * FROM interns WHERE gender = ?")
        ->bindings(["M"])->get();

        print_r($data11);

        // DB::showDatabaseLogs();
        $this->render('index', $context);
    }


    public function testInsertAndUpdate()
    {
        $context = [
            'title' => 'INSERT AND UPDATE',
            'alert' => ''
        ];

        $data = [
            'first_name' => 'Nyamwiza',
            'last_name' => 'Geradline',
            'password' => $this->password()->encrypt('geraldine')
        ];

        $multidata = [
            [
                'email' => 'admin17@mfabutech.com',
                'first_name' => 'Tumuhimbise',
                'last_name' => 'Godwin',
                'password' => $this->password()->encrypt('godwin')
            ],
            [
                'email' => 'admin18@mfabutech.com',
                'first_name' => 'Tumuhimbise',
                'last_name' => 'Godwin',
                'password' => $this->password()->encrypt('godwin')
            ],
            [
                'email' => 'admin19@mfabutech.com',
                'first_name' => 'Tumuhimbise',
                'last_name' => 'Godwin',
                'password' => $this->password()->encrypt('godwin')
            ]
        ];
            
        // if(DB::table('admin')->save($data)){
        //     echo DB::affectedRows();
        // }

        // if(DB::table('admin')->where('email', 'admin19@mfabutech.com')->update($data)){
        //     echo DB::affectedRows() . " rows Updated";
        // }

        // if(DB::table('admin')->where('email', 'admin19@mfabutech.com')->delete()){
        //     echo DB::affectedRows() . " row deleted";
        // }

        // echo DB::table('admin')->where('email', 'admin19@mfabutech.com')->doesNotExist();
        
        // $softDelete = DB::table('users')->where('phone_number1', '0756809525')->get();

        // print_r($softDelete);
        // print_r(Interns::find(95714)->delete());
        // print_r(Interns::all());
        
        // $interns = new Interns();
        // $interns->email = 'rytahpatience02@gmail.com';
        // $interns->phone = '+256752561974';
        // $interns->first_name = "Mbabazi";
        // $interns->last_name = "Shanice";
        // $interns->program = "p.6";
        // $interns->year_of_study = 1;
        // $interns->gender = 'F';
        // if($interns->save())
        // {
        //     echo $interns->lastId() . " saved successfully.";
        // }

        // echo Grammer::plural('boy') . "<br/>";
        // echo Grammer::singular('Products');
        // if(DB::use('phaser', 'users')->where('id', 3)->forceDelete())
        // {
        //     echo DB::affectedRows() . " parmanently deleted!";
        // }
        $this->render('test', $context);
    }
}