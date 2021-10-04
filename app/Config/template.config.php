<?php

use System\Template\Template;

Template::templateExtension();
Template::cachePath($_SERVER['DOCUMENT_ROOT'].'/app/Templates/cache/');
Template::templatesPath($_SERVER['DOCUMENT_ROOT'].'/app/Templates/');
Template::setTemplateCaching(FALSE);
