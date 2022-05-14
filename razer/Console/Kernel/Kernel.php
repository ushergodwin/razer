<?php
namespace Razer\Console\Kernel;

use Razer\Controller\Make;
use Razer\Database\Grammer\Grammer;
use Razer\Database\Migrations\Migrations;
use Razer\Server\Server;

class Kernel 
{
    protected $options;

    public function __construct($base)
    {
        Migrations::__init__($base);
    }

    public function make($input)
    {
        $this->options = $input;
        return $this;
    }

    public function run()
    {
        if(!empty($this->options)) {
     
            switch ($this->options[1]) {
                case 'make:db':
                    if(isset($this->options[2]))
                    {
                        return Migrations::createDatabase($this->options[2]);
                    }
                    Migrations::createDatabase();
                    break;
        
                case ($this->options[1] === '--version' || $this->options[1] === "-v"):
                    echo "\e[0;32;40mv".Migrations::$version . "\e[0m";
                    break;
                
                case ($this->options[1] === '--help' || $this->options[1] === "-h"):
                    $f = './Razer/Database/Migrations/cli-help.txt';
                    $read = file_get_contents($f);
                    echo "\e[0;32;40m$read \e[0m";
                    break;
        
                    
                case ($this->options[1] === '--info' || $this->options[1] === "-i"):
                    Migrations::README();
                    break;
        
                case 'migrate':
                    if(isset($this->options[2]))
                    {
                        $file = explode('=', $this->options[2]);
                        Migrations::config([], true);
                        Migrations::RunAll(false, [], [$file[1]]);
                        return;
                    }
                    Migrations::RunAll();
                    break;
        
                case 'make:migration': 
                    Migrations::config(["table" => $this->options[2]]);
                    Migrations::makeMigration();
                    break;
        
                case 'migrate:refresh':
                    Migrations::rollBack(true);
                    break;
        
                case 'migrate:rollback':
                    Migrations::rollBack();
                    break;
        
                case 'migrate:group':
                    if(isset($this->options[2]))
                    {
                        switch (strtolower($this->options[2])) {
                            case '--run':
                                Migrations::RunGroupedMigrations();
                                break;
                        }
                        return;
                    }
                    Migrations::groupMigrations();
                    break;
        
                case 'migrate:status':
                    Migrations::listMigrations();
                    break;
        
                case 'migrate:modify':
                    Migrations::modifyMigrations();
                    break;
        
                case 'migrate:logs': 
                    if(isset($this->options[2]))
                    {
                        switch(strtolower($this->options[2]))
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
                    foreach(glob('Razer/Views/cache/' . '*') as $file) {
                        unlink($file);
                    }
                    echo "\e[0;32;40mCache cleared. \e[0m\n";
                    break;
        
                case 'make:controller':
                    Make::$path = getcwd();
                    if(!isset($this->options[3]))
                    {
                        Make::controller($this->options[2]);
                    }
                    if(isset($this->options[3]) && strtolower($this->options[3]) == '--resource')
                    {
                        Make::resourceController($this->options[2]);
                    }
                    break;
        
                case 'make:model': 
                    Make::$path = getcwd();
                    if(!isset($this->options[3]))
                    {
                        Make::model($this->options[2]);
                    }
                    if(isset($this->options[3]) && strtoupper($this->options[2]) == '-M')
                    {
                        Make::model($this->options[3]);
                        $table = "create_". Grammer::decamelize($this->options[3]) . "_table";
                        Migrations::config(["table" => $table]);
                        Migrations::makeMigration();
        
                    }
                    break;
                
                case 'serve':
                    Server::runDevelopment();
                    break;
                default:
                    echo "\e[0;33;40mSorry, I do not understand what you mean\n Use \e[0m\e[0;32;40mphp manage -h for help \e[0m";
            }
        }
    }

    public function __destruct()
    {
        $this->options = array();
    }
}