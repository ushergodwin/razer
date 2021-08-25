<?php
namespace App\Controller;

use System\Controller\Controller;
use System\Template\Template;

class BaseController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    private function template_path() {
        return $_SERVER['DOCUMENT_ROOT'].'/app/templates/';
    }
    
    public function render(string $page = 'index', $context = []) {
        return Template::view($page, $context);
    }

    public static function decodeClass($class) {
        return ucfirst($class);
    }

}