<?php

use System\Template\Template;

Template::templateExtension('.php');
Template::cachePath($_SERVER['DOCUMENT_ROOT'].'/app/templates/cache/');
Template::templatesPath($_SERVER['DOCUMENT_ROOT'].'/app/templates/');
Template::setTemplateCaching(FALSE);
