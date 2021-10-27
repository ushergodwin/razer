<?php

namespace System\Database\Grammer;

use Illuminate\Support\Pluralizer;

class Grammer extends Pluralizer
{

    public static function decamelize(string $camelCase)
    {
      $camel=preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', "_".'$0', $camelCase));
      return Grammer::plural(strtolower($camel));
    }
}