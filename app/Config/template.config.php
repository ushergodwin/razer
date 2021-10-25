<?php

use System\Template\Template;

Template::templateExtension(); //defaults to blade.php

Template::templatesPath(BASE_PATH.'/app/Templates/');
