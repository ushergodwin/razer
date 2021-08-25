<?php
namespace System\Uuid;

/**
 * Class Unique_Key
 * Unique, random numbers and strings
 */
class Uuid
{
    public function __construct()
    {

    }
    /**
     * @param string $prefix A string that should be put before the unique key
     * [optional]
     * @param bool $extra Determines whether to add more keys at the end of the key
     * Can be useful, for instance, if you generate identifiers simultaneously on several hosts that might happen to generate the identifier at the same microsecond.
     * With an empty prefix, the returned string will be 13 characters long. If extra is true, it will be 23 characters.
     * @return string A unique key
     */
    public function Uuid($prefix = '', $extra = false) {
        return uniqid($prefix, $extra);
    }



    /**
     * @param int $min The starting number when generating a random number
     * @param int $max The maximum number to end from
     * @param bool $more_entropy
     * [optional]
     * If set to true, random_number will add additional entropy (using the combined linear congruential generator) at the end of the return value, which should make the results more unique.
     * @return int A random number btn the supplied $min and $max
     * @throws Exception
     */
    public function randNumber($min, $max, $more_entropy = false) {
        $entropy = substr(uniqid("",true), 14, 8);
        $id = strval(rand($min, $max));
        $more_entropy ? $id .=".".$entropy : $id;
        return $id;
    }


    /**
     * @param string $case Allowed case of characters in a string
     * Allowed CASES are; a by default
     * a1, A, aA, A1, aA1
     * @param int $length Number of characters to be returned
     * @param bool $more_entropy
     * [optional]
     * If set to true, random_string will add additional entropy (using the combined linear congruential generator) at the end of the return value, which should make the results more unique.
     * @return string Random string
     */
    public function randString($case = "a", $length = 13, $more_entropy = false) {
        $entropy = substr(uniqid("",true), 14, 8);
        $rand_string = "";
        $allowed = "abcdefghijklmnopqrstuvwxyz";
        $rand_string = substr(str_shuffle($allowed), 0, $length);
        if ($case == "a1") {
            $allowed = "0123456789abcdefghijklmnopqrstuvwxyz";
            $rand_string = substr(str_shuffle($allowed), 0, $length);
        }
        if ($case == "A") {
            $allowed = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $rand_string = substr(str_shuffle($allowed), 0, $length);
        }
        if ($case == "aA") {
            $allowed = "AzByCxDwEvFuGtHsIrJqKpLoMnNmOlPkQjRiShTgUfVeWdXcYbZa";
            $rand_string = substr(str_shuffle($allowed), 0, $length);
        }
        if ($case == "A1") {
            $allowed = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $rand_string = substr(str_shuffle($allowed), 0, $length);
        }
        if ($case == "aA1") {
            $allowed = "0123456789AzByCxDwEvFuGtHsIrJqKpLoMnNmOlPkQjRiShTgUfVeWdXcYbZa";
            $rand_string = substr(str_shuffle($allowed), 0, $length);
        }
        $more_entropy == true ? $rand_string.=".".$entropy : $rand_string;
        return $rand_string;
    }
}
