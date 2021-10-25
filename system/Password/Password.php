<?php
namespace System\Password;

/**
 * Class Password
 * All password related activities
 */
class Password
{
    /**
     * @param $string
     * @return string 64 bit character long encrypted key
     */
    public function hash($string) {
        return password_hash($string, PASSWORD_DEFAULT);
    }
    /**
     * @param string $password The user's password
     * @param string $hash The Encrypted key obtained when hashing.
     * @return bool True if the password matches with the hash, false otherwise
     */
    public function verify($password, $hash) {
        
        if(password_verify($password, $hash))
        {
            return true;
        }elseif ($this->sha1($password) === $hash) {
            return true;
        }elseif ($this->md5($password) === $hash) {
            return true;
        }else {
            return false;
        }
    }
    /**
     * @param string $rowpassword String to be encrypted
     * @return string 42 bit character long encrypted key
     */
    public function sha1($rowpassword) {
        return sha1($rowpassword);
    }
    /**
     * @param string $rowpassword String to be encrypted
     * @return string 32 bit character long encrypted key
     */
    public function md5($rowpassword) {
        return md5($rowpassword);
    }
}

