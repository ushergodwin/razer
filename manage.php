<?php

use System\Database\Migrations\Migrations;

require_once __DIR__. '/system/Database/Migrations/Migrations.php';

Migrations::__init__(__DIR__);

//command line arguments

$options = $argv;

 if(!empty($options)) {
    switch ($options[1]) {
        case 'version':
            echo "Boosted Migrations Manager v".Migrations::$version;
            break;
        
        case 'help':
            echo "
            ########## Migrations Help Guide ##########
            RunAll a Specific Migration:  php manage.php migrate:file migration_file_name
            RunAll all Migrations:  php manage.php migrate
            List all Migrations:  php manage.php migrate:list
            Export migration for an entire database: php manage.phy export
            Migration a specific table(s): php manage.php migrate:table table1 table2 tablen
            Migration a different database: php manage.php migrate:db db_name
            Drop Migration: php manage.php migrate:rollback

            ######### Template ########################
            Clear Cache: php manage.php cache:clear
            For more Info: php manage.php info ";
            break;

            
        case 'info':
            Migrations::README();
            break;

        case 'migrate':
            if(isset($options[2]))
            {
                $file = explode('=', $options[2]);
                Migrations::config([], true);
                Migrations::RunAll(false, [], [$file[1]]);
                return;
            }
            Migrations::RunAll();
            break;

        case 'make:migration': 
            Migrations::config(["table" => $options[2]]);
            Migrations::makeMigration();
            break;

        case 'migrate:refresh':
            Migrations::rollBack(true);
            break;

        case 'migrate:rollback':
            Migrations::rollBack();
            break;

        case 'migrate:group':
            if(isset($options[2]))
            {
                switch (strtolower($options[2])) {
                    case '--run':
                        Migrations::RunGroupedMigrations();
                        break;
                }
                return;
            }
            Migrations::groupMigrations();
            break;

        case 'migrate:list':
            Migrations::listMigrations();
            break;

        case 'migrate:modify':
            Migrations::modifyMigrations();
            break;

        case 'migrate:logs': 
            if(isset($options[2]))
            {
                switch(strtolower($options[2]))
                {
                    case '--clear': 
                        Migrations::clearMigrationErrors();
                        break;
                }

                return;
            }
            Migrations::showMigrationErrors();
            break;

        case 'cache:clear': 
            echo "\e[0;33;40mClearing cache...\e[0m " . "\n";
            foreach(glob('system/Template/cache/' . '*') as $file) {
                unlink($file);
            }
            echo "\e[0;32;40mCache cleared. \e[0m\n";
            break;
        
        default:
            echo "\e[0;33;40mSorry, I did not understand what you mean \e[0m\n";
    }
}