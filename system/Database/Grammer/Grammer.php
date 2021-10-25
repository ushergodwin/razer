<?php

namespace System\Database\Grammer;

class Grammer
{
    protected const CONSONANTS = 'b,c,d,f,g,h,j,k,l,m,n,p,q,r,s,t,v,w,x,y,z';

    public static function plural(string $word)
    {
        return self::convertToPluralForm($word);
    }

    public static function singular(string $word)
    {
        return self::convertToSingularForm($word);
    }

    protected static function convertToPluralForm(string $word)
    {
        $word_len = strlen($word);
        $last__two_letters = substr($word, $word_len - 2, 2);
        $second_last_letter = substr($last__two_letters, 0, 1);
        $last_letter = substr($last__two_letters, 1, 1);

        $consonants = explode(',', self::CONSONANTS);
        if(in_array($second_last_letter, $consonants) && strtolower($last_letter) === 'y')
        {
            $word = substr($word, 0, strlen($word) - 1);
            return $word .= "ies";
        }

        return $word .= 's';
    }

    protected static function convertToSingularForm(string $word)
    {
        $word_len = strlen($word);
        $last__three_letters = substr($word, $word_len - 3, 3);
        $last_letter = substr($word, $word_len - 1, 1);

        if(strtolower($last__three_letters) === 'ies')
        {
            return substr($word, 0, strlen($word) - 3) . 'y';
        }
        if(strtolower($last_letter) === 's')
        {
            $word = substr($word, 0, strlen($word) - 1);
        }
        return $word;
    }

    public static function decamelize(string $camelCase)
    {
      $camel=preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', "_".'$0', $camelCase));
      return Grammer::plural(strtolower($camel));
    }
}