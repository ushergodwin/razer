<?php
namespace System\Database\Logs;
use PDOException;

trait DatabaseLogs
{
    protected function logError($error)
    {
        $root = $_SERVER['DOCUMENT_ROOT'];

        $f = fopen($root.'/database/logs/db-logs.txt', 'a+');
        $error = "[" . date("D d M Y H:i:s") . "] \t" . $error;
        $error .= "\n\n ----------------------------------------------------------------------- \n\n";

        if(strtolower(env('ENVIRONMENT')) === 'local')
        {
            self::clearDatabasLogs();
            fwrite($f, $error);
            fclose($f);
            return throw new PDOException($error);
        }
        fwrite($f, $error);
        return fclose($f);
    }

        /**
     * Display Database logs
     *
     * @return void
     */
    public static function showDatabaseLogs()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];

        $f = fopen($root.'/database/logs/db-logs.txt', 'r');

        $content = fread($f, filesize($root.'/database/logs/db-logs.txt'));
        d($content);
    }


    /**
     * Clear Database logs
     *
     * @return bool true on success or false on failure.
     */
    public static function clearDatabasLogs()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];

        $f = fopen($root.'/database/logs/db-logs.txt', 'w+');
        $error = "----------------------------------------------------------------------- \n\n";
        fwrite($f, $error);
        return fclose($f);
    }
}
