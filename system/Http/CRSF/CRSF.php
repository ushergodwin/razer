<?php
namespace System\Http\CRSF;

class CRSF
{
    private function get_token_id() {
        if(isset($_SESSION['token_id'])) { 
                return $_SESSION['token_id'];
        }
        $token_id = '_token';
        $_SESSION['token_id'] = $token_id;
        return $token_id;
	}
	
	private function get_token() {
        if(isset($_SESSION['token_value'])) {
                return $_SESSION['token_value']; 
        }
        $token = hash('sha256', $this->random(500));
        $_SESSION['token_value'] = $token;
        return $token;
	}
	
	private function check_valid() {
        if(isset($_POST[$this->get_token_id()])) {
            if($_POST[$this->get_token_id()] == $this->get_token())
            {
                return 1;
            }
            return 2;
        }
        return 3;  
	}
	
	private function random($len) {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $byteLen = intval(($len / 2) + 1);
            $return = substr(bin2hex(openssl_random_pseudo_bytes($byteLen)), 0, $len);
        } elseif (@is_readable('/dev/urandom')) {
            $f=fopen('/dev/urandom', 'r');
            $urandom=fread($f, $len);
            fclose($f);
            $return = '';
        }
 
        if (empty($return)) {
            for ($i=0;$i<$len;++$i) {
                if (!isset($urandom)) {
                    if ($i%2==0) {
                        mt_srand(time()%2147 * 1000000 + (double)microtime() * 1000000);
                    }
                    $rand=48+mt_rand()%64;
                    } else {
                        $rand=48+ord($urandom[$i])%64;
                    }
 
                    if ($rand>57) $rand+=7;
                    if ($rand>90) $rand+=6;
  
                    if ($rand==123) $rand=52;
                    if ($rand==124) $rand=53;
                    $return.=chr($rand);
                }
        }
 
        return $return;
	}

    /**
     * Get the CRSF Token Id
     *
     * @return string
     */
    public static function crsfTokenId()
    {
        return (new self)->get_token_id();
    }

    /**
     * Get a CRSF Token value
     *
     * @return string
     */
    public static function crsfTokenValue()
    {
        return (new self)->get_token();
    }

    /**
     * Check the CRSF Token is valid
     *
     * @return int
     * 
     * 1 The CRSF TOKEN is set and valid
     * 
     * 2 The CRSF TOKEN is set but invalid
     * 
     * 3 The CRSF TOKEN is missing
     */
    public static function isValid()
    {
        return (new self)->check_valid();
    }
}
