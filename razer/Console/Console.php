<?php
namespace Razer\Console;

class Console {

    public function arguments()
    {
        global $argv;
        return $argv;
    }
}