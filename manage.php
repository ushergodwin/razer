<?php

use System\Database\Migrations\Migrations;


require_once __DIR__. '/system/Database/Migrations/Migrations.php';

Migrations::__init__(__DIR__);

//command line arguments

$shortopts  = "";
$shortopts .= "m:";  // Required value
$shortopts .= "e:";
$shortopts .= "d:";
$shortopts .= "v::"; // Optional value
$shortopts .= "h::";
$shortopts .= "i::";
$shortopts .= "export:";
$shortopts .= "migrate:";

$longopts  = array(
    "required:",     // Required value
    "optional::",    // Optional value
    "option",        // No value
    "opt",           // No value
);

$options = getopt($shortopts, $longopts);

// print_r($options);
// return;
 if(!empty($options['m'])) {
    switch (strtolower($options['m'])) {
        case 'igrate':
            Migrations::RunAll();
            break;
        case 'group':
            Migrations::config(['tables' => [0 => $options['m']]]);
            Migrations::groupMigrations();
            break;
        case 'clear':
            echo 'You are attempting to clear all migrations, Continue? [y/n] ';

            if (!in_array(trim(fgets(STDIN)), array('y', 'Y'))) {
    
                echo 'Operation terminated ' . "\n";
                exit;
            }
            echo "Continuing\n";
            Migrations::config(['tables' => [0 => $options['m']]]);
            Migrations::clearMigrations();
            break;
        case 'list':
            Migrations::config(['tables' => [0 => $options['m']]]);
            Migrations::listMigrations();
            break;
        default:
        Migrations::config(array('migration' => $options['m']));
        Migrations::RunSingle();       
    }
 }elseif(!empty($options['h'])) {
     echo "
        ########## Migrations Help Guide ##########
        Run a Specific Migration:  php manage.php -m migration_file_name
        Run all Migrations:  php manage.php -migrate
        List all Migrations:  php manage.php -m list
        Make migration for an entire database: php manage.phy -export
        Make migration for a specific table: php manage.php -e table_name
        Make migration for multiple tables: php manage.php -e table1,table2,table2..
        For more Info: php manage.php -info ";
 } elseif(!empty($options['i'])) {
     Migrations::README();
 }elseif(!empty($options['e'])) {
     $args = explode(',', $options['e']);
     Migrations::config(['tables' => $args]);
     if (count($args) === 1 and $options['e'] !== 'xport') {

        echo 'Is '.$args[0].' a table(1) or database(2)? [1/2] ';

        if (!in_array(trim(fgets(STDIN)), array('1'))) {

            echo 'Attempting to use the provided database ' . "\n";

            Migrations::config(['tables' => [0 => $options['e']]], false);
        }
        else {

            Migrations::config(['tables' => [0 => $options['e']]], true, true);
            echo 'If you have multiple tables to export, separate them with commas' . "\n";
        }
        echo 'Continuing' . "\n";
     }
     
     Migrations::exportDataForMigration();
 } elseif (!empty($options['v'])) {
    
    echo "Boosted Migrations Manager v".Migrations::$version;
 }  elseif (!empty($options['d'])) {
     Migrations::config(["table" => $options['d']]);
     Migrations::dropMigration();
 }
 else {
     echo "Sorry, I did not understand what you mean ";
 }