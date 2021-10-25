<?php
namespace App\Models;

use System\Database\DB;
use System\Models\Model;

class testModel extends Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function allInterns() {
        return DB::table('interns')
            ->where(["email" => 'godwintumuhimbise96@gmail.com'], 'id DESC')
            ->get();
    }

    public function all() {
        return DB::table('staff')->get();
    }

    public function oneIntern() {
        return DB::table('staff')
            ->row('fname, lname')
            ->where(["username" => 'mbonye'])
            ->get();
    }

    public function insert() {
        $data = [
            "first_name" => "Usher",
            "last_name" => "The programmer",
            "program" => "PHP",
            "year_of_study" => "4",
            "gender" => "Unknown"
        ];

        return DB::table('interns', $data)->save();
    }

    public function update() {
        $data = [
            "first_name" => "Usher",
            "last_name" => "The programmer",
            "program" => "PHP",
            "year_of_study" => "4",
            "gender" => "Unknown"
        ];

        return DB::table('interns', $data)
            ->where(["email" => "test@gmail.com"])
            ->save();
    }

    public function NumberOfRecords() {
        return DB::table('interns')->where(["email" => "test@gmail.com"])->exists('year_of_study');
    }

    public function select() {
        return DB::table('interns')
                    ->use('internship')
                    ->select('first_name, last_name')
                    ->where(["id" => 9657])
                    ->whereOr(["email" => "test@gmail.com"])
                    ->get();
    }

    public function distinct() {
        return DB::table('interns')->distinct('email, first_name')->get();
    }

    public function messages() {
        return DB::table('messages')->initJoin()
                              ->join('staff', 'messages.sent_from', 'staff.email')
                              ->get();
    }

    public function diffrentDB() {
        return DB::table('interns')->use('internship')
                                    ->between(["year_of_study" => [2,3]])
                                    ->get();
    }

    public function testLike() {
        return DB::table('interns')->use('internship')
                                    ->initLike()
                                    ->like(["email" => '%00%'])
                                    ->likeOr(["id" => '%38%'])
                                    ->get();
    }
}