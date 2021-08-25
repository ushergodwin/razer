<?php
namespace System\Password;

/**
 * Class Password
 * All password related activities
 */
class Password
{
    function __construct(){

    }

    /**
     * @param $string
     * @return string 64 bit character long encrypted key
     */
    public function encrypt($string) {
        return password_hash($string, PASSWORD_DEFAULT);
    }
    /**
     * @param string $password The user's password
     * @param string $hash The Encrypted key obtained when hashing.
     * @return bool True if the password matches with the hash and false if not
     */
    public function verify($password, $hash) {
        return password_verify($password, $hash);
    }
    /**
     * @param string $key String to be encrypted
     * @return string 42 bit character long encrypted key
     */
    public function encrypt_sh($key) {
        return sha1($key);
    }
    /**
     * @param string $key String to be encrypted
     * @return string 32 bit character long encrypted key
     */
    public function encrypt_md($key) {
        return md5($key);
    }
    /**
     * @param string $password The user's password
     * @param string $hash The Encrypted key obtained when hashing.
     * @return bool True if the password matches with the hash and false if not
     */
    public function decrypt_sh($password, $hash) {
        $bool = false;
        if ($this->encrypt_sh($password) == $hash):
            $bool = true;
        endif;
        return $bool;

    }
    /**
     * @param string $password The user's password
     * @param string $hash The Encrypted key obtained when hashing.
     * @return bool True if the password matches with the hash and false if not
     */
    public function decrypt_md($password, $hash) {
        $bool = false;
        if ($this->encrypt_md($password) == $hash):
            $bool = true;
        endif;
        return $bool;

    }
}

