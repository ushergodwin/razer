<?php

use System\Views\Template;

Template::templateExtension(); //defaults to blade.php

Template::templatesPath(BASE_PATH.'/app/views');
