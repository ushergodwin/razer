<?php

use System\Template\Template;

Template::templateExtension(); //defaults to blade.php

Template::templatesPath($_SERVER['DOCUMENT_ROOT'].'/app/Templates/');

Template::setTemplateCaching(FALSE);
