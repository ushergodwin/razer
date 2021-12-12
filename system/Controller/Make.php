<?php
namespace System\Controller;

use System\Database\Grammer\Grammer;

class Make
{
    public static $path;

    public static function controller(string $name)
    {
        $namespace = "App\Controller";
        $filelocation = explode('/', $name);
        $filelocation_num = count($filelocation);
        $className = $name;
        if($filelocation_num > 1)
        {
            $className = $filelocation[$filelocation_num - 1];
            unset($filelocation[$filelocation_num - 1]);
            $namespace .= "\\".implode("\\",$filelocation);
            if(!file_exists(self::$path . "/app/Controller/".implode("/",$filelocation)."/"))
            {
                for($i = 0; $i < ($filelocation_num - 1); $i++)
                {
                    mkdir(self::$path . "/app/Controller/". $filelocation[$i]);
                }
            }
        }
        $path = self::$path . '/app/Controller/'. $name . ".php";
        $controller = "<?php \n namespace $namespace; \n use App\Controller\BaseController;\n\n class $className extends BaseController \n { \n\n }";
        $f = fopen($path, 'w+');
        if(!$f)
        {
            echo "\e[0;33;40mFailed to create controller:\e[0m $name\n";
            exit;
        }
        $write = fwrite($f, $controller);

        if(!$write)
        {
            echo "\e[0;33;40mFailed to create controller:\e[0m $name\n";
            exit;
        }

        fclose($f);
        echo "\e[0;32;40mCreated controller:\e[0m $name\n";
    }

    public static function model(string $name)
    {
        $namespace = "App\Models";
        $filelocation = explode('/', $name);
        $filelocation_num = count($filelocation);
        $className = $name;
        if($filelocation_num > 1)
        {
            $className = $filelocation[$filelocation_num - 1];
            unset($filelocation[$filelocation_num - 1]);
            $namespace .= "\\".implode("\\",$filelocation);
            if(!file_exists(self::$path . "/app/Models/".implode("/",$filelocation)."/"))
            {
                for($i = 0; $i < ($filelocation_num - 1); $i++)
                {
                    mkdir(self::$path . "/app/Models/". $filelocation[$i]);
                }
            }
        }
        $name = Grammer::singular($name);
        $path = self::$path . '/app/Models/'. $name . ".php";
        $controller = "<?php \n namespace $namespace; \n use System\Models\Model;\n\n class $className extends Model \n { \n\n }";
        $f = fopen($path, 'w+');
        if(!$f)
        {
            echo "\e[0;33;40mFailed to create Model:\e[0m $name\n";
            exit;
        }
        $write = fwrite($f, $controller);

        if(!$write)
        {
            echo "\e[0;33;40mFailed to create Model:\e[0m $name\n";
            exit;
        }

        fclose($f);
        echo "\e[0;32;40mCreated Model:\e[0m $name\n";
    }

    public static function resourceController(string $name)
    {
        $namespace = "App\Controller";
        $filelocation = explode('/', $name);
        $filelocation_num = count($filelocation);
        $className = $name;
        if($filelocation_num > 1)
        {
            $className = $filelocation[$filelocation_num - 1];
            unset($filelocation[$filelocation_num - 1]);
            $namespace .= "\\".implode("\\",$filelocation);
            if(!file_exists(self::$path . "/app/Controller/".implode("/",$filelocation)."/"))
            {
                for($i = 0; $i < ($filelocation_num - 1); $i++)
                {
                    mkdir(self::$path . "/app/Controller/". $filelocation[$i]);
                }
            }
        }

        $path = self::$path . '/app/Controller/'. $name . ".php";
        $controller = "<?php \n namespace $namespace; \n use App\Controller\BaseController;\n use \System\Http\Request\Request; \n\n class $className extends BaseController \n { \n ";
        $controller .= "\t\t/**\n\t\t* Display a listing of the resource.\n\t\t* @return \System\Http\Response\Response\n\t\t*/\n";
        $controller .= "\t\tpublic function index()\n\t\t{\n\t\t\t// \n\n \t\t}\n\n";
        $controller .= "\t\t/**\n\t\t* Show the form for creating a new resource.\n\t\t* @return \System\Http\Response\Response\n\t\t*/\n";
        $controller .= "\t\tpublic function create()\n\t\t{\n\t\t\t// \n \t\t}\n\n";
        $controller .= "\t\t/**\n\t\t* Store a newly created resource in storage.\n\t\t* @param \System\Http\Request\Request \$request\n\t\t* @return \System\Http\Response\Response\n\t\t*/\n";
        $controller .= "\t\tpublic function store(Request \$request)\n\t\t{\n\t\t\t// \n \t\t}\n\n";
        $controller .= "\t\t/**\n\t\t* Display the specified resource.\n\t\t* @param int|string \$id\n\t\t* @return \System\Http\Response\Response\n\t\t*/\n";
        $controller .= "\t\tpublic function show(\$id)\n\t\t{\n\t\t\t// \n\n \t\t}\n\n";
        $controller .= "\t\t/**\n\t\t* Show the form for editing the specified resource.\n\t\t* @param int|string \$id\n\t\t* @return \System\Http\Response\Response\n\t\t*/\n";
        $controller .= "\t\tpublic function edit(\$id)\n\t\t{\n\t\t\t// \n\n \t\t}\n\n";
        $controller .= "\t\t/**\n\t\t* Update the specified resource in storage.\n\t\t* @param \System\Http\Request\Request \$request\n\t\t* @return \System\Http\Response\Response\n\t\t*/\n";
        $controller .= "\t\tpublic function update(Request \$request)\n\t\t{\n\t\t\t// \n\n \t\t}\n\n";
        $controller .= "\t\t/**\n\t\t* Remove the specified resource from storage.\n\t\t* @param int|string \$id\n\t\t* @return \System\Http\Response\Response\n\t\t*/\n";
        $controller .= "\t\tpublic function destroy(\$id)\n\t\t{\n\t\t\t// \n\n \t\t}\n\n}";
        $f = fopen($path, 'w+');
        if(!$f)
        {
            echo "\e[0;33;40mFailed to create controller:\e[0m $name\n";
            exit;
        }
        $write = fwrite($f, $controller);

        if(!$write)
        {
            echo "\e[0;33;40mFailed to create controller:\e[0m $name\n";
            exit;
        }

        fclose($f);
        echo "\e[0;32;40mCreated controller:\e[0m $name\n";
    }
}
