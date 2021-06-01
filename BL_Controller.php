<?php

/**
 * Class CL_Controller
 * @category  PHP Inbuilt functions
 * @package   Controller
 * @author Tumuhimbise Godwin
 *@copyright Copyright (c) 2020-2021
 * @version   2.0
 */


/**
 * Class CL_Controller
 * Get access to commonly used inbuilt php functions to ease up your development cycle
 */

class BL_Controller
{
    public  $input; //Input class

    public  $server; //Server class

    public $cookie; //Cookie class

    public $session; //Session class

    public $password; //Password class

    public $notification; //Notification class

    public $verify; //Verify class

    public $model = [];

    public $unique_key; //Unique_key class

    public $file; //File class

    public $mail; //Mail Class

    public function __construct() {
        //parent::__construct();

        //Loading models
        //Input class
        $this->input = new Input();
        //Server class
        $this->server = new Server();
        //cookie class
        $this->cookie = new Cookies();
        //Session class
        $this->session = new Session();
        $this->model = new stdClass();
        $this->password = new Password();
        //Notification class
        $this->notification = new Notification();
        //Unique_key class
        $this->unique_key = new Unique_key();
        //File class
        $this->file = new File();

        //Mail class
        $this->mail = new Mail();
    }

    function remove_none_utf_char($string) {
        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $string);
    }

    function remove_special_chars($string) {
        $string = strip_tags($string);
        $string = preg_replace('/[^A-Za-z0-9. -]/', ' ', $string);
        // Replace sequences of spaces with hyphen
        $string = preg_replace('/  */', '-', $string);
        return $string;
    }

    function xss_clean($string) {
        $string = strip_tags($string);
        return $string;
    }

    function remove_numbers_from_string($string) {
        return preg_replace('/\d+/u', '', $string);
    }

    function replace_multiple_spaces($string) {
        return preg_replace('!\s+!', ' ', $string);
    }

}

/**
 * Class Server
 * All server global variable properties
 */
class Server
{
    public $remote_address;
    public $server_name;
    public $request_method;
    public $query_string;
    public $request_uri;
    public $document_root;
    public $http_refer;
    public $server_address;
    public $os;
    public $browser;

    function __construct()
    {
        $this->remote_address = $this->remote_addr();
        $this->server_name = $this->server_name();
        $this->request_method = $this->request_method();
        $this->query_string = $this->query_string();
        $this->request_uri = $this->request_uri();
        $this->document_root = $this->document_root();
        $this->http_refer = $this->http_referer();
        $this->server_address = $this->addr();
        $this->os = $this->getOS();
        $this->browser = $this->getBrowser();
    }

    private function request_method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function query_string()
    {
        return isset($_SERVER['QUERY_STRING']) ?? $_SERVER['QUERY_STRING'];
    }

    private function request_uri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    private function document_root()
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    private function server_name()
    {
        return $_SERVER['SERVER_NAME'];
    }
    private function http_referer()
    {
        if (isset($_SERVER['HTTP_REFERER']))
            return $_SERVER['HTTP_REFERER'];
        else return $this->server_name();
    }
    private function addr()
    {
        return isset($_SERVER['SERVER_ADDR']) ?? $_SERVER['SERVER_ADDR'];
    }

    private function remote_addr()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    private function getOS() {
        $user_agent = $_SERVER["HTTP_USER_AGENT"];
        $os_platform    =   "Unknown OS Platform";
        $os_array       =   array(
            '/windows nt 10/i'     =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($os_array as $regex => $value) {

            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }

        }

        return $os_platform;

    }
    private function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $ub = 'Unknown';
        $platform = 'Unknown';

        $deviceType='Desktop';

        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$u_agent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($u_agent,0,4))){

            $deviceType='Mobile';

        }

        if($_SERVER['HTTP_USER_AGENT'] == 'Mozilla/5.0(iPad; U; CPU iPhone OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B314 Safari/531.21.10') {
            $deviceType='Tablet';
        }

        if(stristr($_SERVER['HTTP_USER_AGENT'], 'Mozilla/5.0(iPad;')) {
            $deviceType='Tablet';
        }
        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';

        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';

        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the user agent yes separately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'IE';
            $ub = "MSIE";

        } else if(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";

        } else if(preg_match('/Chrome/i',$u_agent) && (!preg_match('/Opera/i',$u_agent) && !preg_match('/OPR/i',$u_agent)))
        {
            $bname = 'Chrome';
            $ub = "Chrome";

        } else if(preg_match('/Safari/i',$u_agent) && (!preg_match('/Opera/i',$u_agent) && !preg_match('/OPR/i',$u_agent)))
        {
            $bname = 'Safari';
            $ub = "Safari";

        } else if(preg_match('/Opera/i',$u_agent) || preg_match('/OPR/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";

        } else if(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";

        } else if((isset($u_agent) && (strpos($u_agent, 'Trident') !== false || strpos($u_agent, 'MSIE') !== false)))
        {
            $bname = 'Internet Explorer';
            $ub = 'Internet Explorer';
        }


        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];

            } else {
                $version= @$matches['version'][1];
            }

        } else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return (object) array(
            'agent'     => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'os'        => $platform,
            'pattern'   => $pattern,
            'device'    => $deviceType
        );
    }
    /**
     *
     * @return string The current folder with a full url
     * eg http://bluefaces.tech/sample
     */
    public function site_url() {
        $base = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
        $base .= "://".$_SERVER['HTTP_HOST'];
        $base .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
        return $base;
    }

    /**
     * @static base_url
     * @return string The a full main url
     * eg http://bluefaces.tech/
     */
    public function base_url() {
        $base = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
        $base .= "://".$_SERVER['HTTP_HOST']."/";
        return $base;
    }
}

/**
 * Class Input
 * Common HTTP request methods and input filters
 */
class Input {

    function __construct()
    {

    }
    /**
     * Request Method
     * @return string
     */
    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get Request Method
     * @return bool
     */
    public function isGet()
    {
        return $this->getMethod() === 'get';
    }

    /**
     * POST Request Method
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() === 'post';
    }

    /**
     * Get values parsed in the HTTP POST request
     * @param string $string
     * @return array|false|mixed
     */
    public function post($string = '') {

        $string = trim($string);

        if (empty($string))
            return $_POST;

        if (isset($_POST[$string]))
            return $this->filter_string(strip_tags($_POST[$string]));

        else
            return false;
    }

    /**
     * Get values parsed in an HTTP GET request
     * @param string $string
     * @return array|false|string
     */
    public function get($string = '') {

        $string = trim($string);

        if (empty($string))
            return $_GET;

        if (isset($_GET[$string]))
            return strip_tags($this->filter_string($_REQUEST[$string]));

        else
            return false;
    }

    /**
     * Get the values parsed in the HTTP GET or POST methods
     * @param string $string
     * @return array|false|string
     */
    public function request($string = "") {
        if (empty($string)) {
            return $_REQUEST;
        }
        if (isset($_REQUEST[$string]))
            return strip_tags($this->filter_string($_REQUEST[$string]));
        else
            return false;
    }

    /**
     * Filter a string
     * @param $string
     * @return mixed
     */
    public function filter_string($string) {
        return filter_var($string, FILTER_SANITIZE_STRING);
    }

    /**
     * Filter an email and remove unwanted characters
     * @param $string
     * @return mixed
     */
    public function filter_email($string) {
        return filter_var($string, FILTER_SANITIZE_EMAIL);
    }

    /**
     * validate an email
     * @param $string
     * @return bool
     */
    public function validate_email($string) {
        $bool = false;
        if (filter_var($string, FILTER_VALIDATE_EMAIL)):
            $bool = true;
        endif;
        return $bool;
    }

    /**
     * Check if the url is valid
     * @param $url
     * @return bool
     */
    public function validate_url($url) {
        $bool = false;
        if(filter_var($url, FILTER_VALIDATE_URL)):
            $bool = true;
        endif;
        return $bool;
    }

    /**
     * Compare 2 strings
     * @param string $value1
     * @param string $value2
     * @return bool
     */
    public function compare($value1,  $value2) {
        $bool = false;
        if($value1 == $value2){
            $bool = true;
        }
        return $bool;
    }

    /**
     * Check if the value in the variable is a number
     * @param $value
     * @return bool
     */
    public function isNaN($value) {
        return is_numeric($value);
    }

    /** Convert to number
     * @param $var
     * @return int
     */
    public function toNumber($var) {
        return (int)$var;
    }

    /**
     * Convert an array to object
     * @param $var
     * @return object
     */
    public function toObject($var) {
        return (object)$var;
    }

    /**
     * Convert an integer to string
     * @param $var
     * @return string
     */

    public function toString($var) {
        return strval($var);
    }

    /**
     * @param array $variable An array of variables to check
     * @return bool false if all parsed variables are empty and True otherwise if not empty
     */
    public function isNotEmpty(array $variable){
        $feed = false;
        $len = count($variable);
        for ($i = 0; $i < $len; $i++) {
            if (empty(trim($variable[$i]))) {
                $feed = true;
                break;
            }
        }
        return $feed;
    }
}

/**
 * Class Cookies
 * All cookie global variable properties
 */
class Cookies  {
    public $cookie;
    function __construct()
    {
        //parent::__construct();
        $this->cookie = (object)$this->get_cookie();
    }

    private function get_cookie() {
        return $_COOKIE;
    }
    public function set($cookie_name, $cookie_data) {
        setcookie($cookie_name, $cookie_data, time() + (86400 * 30 * 3), "/");
    }

    public function read($cookie_name = false) {
        if (isset($cookie_name))
            if (isset($_COOKIE[$cookie_name]))
                return $_COOKIE[$cookie_name];
            else
                return false;
        return $_COOKIE;

    }

    public function destroy($cookie = false) {
        if ($cookie)
            setcookie( $cookie, "", time()- 60, "/","", 0);
        else {
            if (isset($_SERVER['HTTP_COOKIE'])) {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    setcookie($name, '', time()-1000);
                    setcookie($name, '', time()-1000, '/');
                }
            }
        }
    }
}

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

/**
 * Class Session
 * All session global variable properties
 */
class Session
{
    public function __construct()
    {

    }

    /**
     * @param string $url
     * The url whether to redirect
     * @return void
     */
    public function redirect($url) {
        header("location:".$url);
        exit();
    }
    /**
     * Starts session
     * @return bool
     */
    public function start() {
        return session_start();
    }

    /**
     * @param string $url The location where to redirect a user after ending the session
     * Destroys all the session data registered on session set
     * @return void
     */
    public function end($url) {
        session_unset();
        session_destroy();
        $this->redirect($url);
    }
    /**
     * @param string $session_name The name of the session to register
     * @param array|string $session_data The data of the registered session
     * @return array|bool On successful registration of the session
     */
    public function create_session($session_name,  $session_data) {
        return $_SESSION[$session_name] = $session_data;
    }
    /**
     * @param string $session_name The name of the registered session
     * @return array|string An array or string of session data
     */
    public function session_data($session_name) {
        return $_SESSION[$session_name];
    }

}

/**
 * Class Notification
 * Bootstrap designed alters
 */
class Notification
{
    public function __construct()
    {

    }

    /**
     * @param string $message The success message notification
     * @return string
     */
    public function success($message) {
        return "<div class='alert alert-success'>
    <strong><i class='fas fa-check-circle text-success'></i></strong> {$message}
    <button type='button' class='close' data-dismiss='alert'>&times;</button>
</div>";
    }

    /**
     * @param string $message The failure message notification
     * @return string
     */
    public function failure($message) {
        return "<div class='alert alert-warning'>
    <strong><i class='fas fa-exclamation-triangle text-warning'></i></strong> {$message}
    <button type='button' class='close' data-dismiss='alert'>&times;</button>
</div>";
    }

    /**
     * @param string $message The message notification/information
     * @return string
     */
    public function info($message) {
        return "<div class='alert alert-info'>
    <strong><i class='fas fa-info-circle text-info'></i></strong> {$message}
    <button type='button' class='close' data-dismiss='alert'>&times;</button>
</div>";
    }

    /**
     * @param string $message The danger/ fatal error message notification
     * @return string
     */
    public function danger($message) {
        return "<div class='alert alert-danger'>
    <strong><i class='fas fa-exclamation-triangle text-danger'></i></strong> {$message}
    <button type='button' class='close' data-dismiss='alert'>&times;</button>
</div>";
    }

}

/**
 * Class Unique_Key
 * Unique, random numbers and strings
 */
class Unique_Key
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
    public function unique_key($prefix = '', $extra = false) {
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
    public function random_number($min, $max, $more_entropy = false) {
        $entropy = substr(uniqid("",true), 14, 8);
        $id = rand($min, $max);
        $more_entropy == true ? $id .=".".$entropy : $id;
        return (int)$id;
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
    public function random_string($case = "a", $length = 13, $more_entropy = false) {
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

/**
 * Class File
 * Image upload and saving it to database
 */
class File
{
    public function __construct()
    {
    }

    /**
     * @param string $path The path where to upload the file
     * @param string $input_name The name of the input field
     * @return bool|string True if the file/image was uploaded, false if not and string in case of a user error
     */
    public function upload_image_in_bg($path, $input_name) {
        $root = $_SERVER['DOCUMENT_ROOT']."/";
        $target_dir = $root.$path."/";
        $target_file = $target_dir . basename($_FILES[$input_name]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES[$input_name]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            $uploadOk = 0;
            return "Sorry, file already exists.";
        }
        // Check file size
        if ($_FILES[$input_name]["size"] > 500000) {
            $uploadOk = 0;
            return "Sorry, your file is too large.";
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            $uploadOk = 0;
            return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            return "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
                return true;
            } else {
                return false;
            }
        }

    }

    /**
     * @param string $input_name The name of the input file
     * use it to save the image reference in the database
     * @return mixed
     */
    public function get_image_name($input_name) {
        return $_FILES[$input_name]["name"];
    }
}

class Mail {
    function __construct()
    {
    }

    public function send($to, $subject, $message, $name = "Dear User",  $from = "BLUEFACES <info@bluefaces.tech>") {
        $copy = date("Y");
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From:". $from. "\r\n";
        $body = "
                                <div class='row'>
                            <div class='col-12'>
                                <div class='card mt-2'>
                                <div class='card-header border-0 py-1'>
                                    <h3 class='card-title text-primary'>BLUEFACES RESPONSE</h3>                              
                                      </div>
                                         <div class='card-body'>
                                          <h4 class='card-title text-primary'>Hello, {$name}</h4>
                                          <div class='text-secondary'> {$message}</div>
                                          <footer class='page-footer font-small cyan darken-3 fixed-bottom'>
                                          <div class='footer-copyright text-center py-3'>© {$copy} Copyright:
                                        <a href='https://www.bluefaces.tech/'>BLUEFACES</a>
                                        </div>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                        </div>
        ";
        try {
            mail($to, $subject, $body, $headers);
            echo $this->mailSuccess();
        }
        catch(Exception $e){
            echo $this->mailFailed();
        }
    }
    private function mailSuccess(){
        return "<div class='alert alert-success alert-dismissible fade show'>
    <strong><i class='fas fa-check-circle'></i></strong>Message has been sent successfully!
    <button type='button' class='close' data-dismiss='alert'>&times;</button>
</div>";
    }
    private function mailFailed() {
        return "<div class='alert alert-danger alert-dismissible fade show'>
        <strong><i class='fas fa-exclamation-triangle'></i></strong> Oops, Message Not Sent, Please try again later!
        <button type='button' class='close' data-dismiss='alert'>&times;</button>
    </div>";
    }
    private function sent(){
        return "<div class='alert alert-success alert-dismissible fade show'>
    <strong><i class='fas fa-check-circle'></i></strong>A verification link has been sent to your email. Please click on it to verify your account and enjoy BLUEFACES services.
    <button type='button' class='close' data-dismiss='alert'>&times;</button>
</div>";
    }
    private function failed() {
        return "<div class='alert alert-danger alert-dismissible fade show'>
        <strong><i class='fas fa-exclamation-triangle'></i></strong> Oops, unexpected error occured while to trying to send you a verification link!!
        <button type='button' class='close' data-dismiss='alert'>&times;</button>
    </div>";
    }
    public function verifyEmail($to, $id, $code, $from = "BLUEFACES <info@bluefaces.tech>", $subject = "ACCOUNT VERIFICATION") {
        $copy = date("Y");
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From:". $from. "\r\n";
        $body = "
                                <div class='row'>
                            <div class='col-12'>
                                <div class='card mt-2'>
                                <div class='card-header border-0 py-1'>
                                    <h3 class='card-title text-primary'>VERIFY ACCOUNT</h3>                              
                                      </div>
                                         <div class='card-body'>
                                          <h4 class='card-title text-primary'>Hello, please verify your account to continue.</h4>
                                          <a href='verify?id={$id}&code={$code}' class='btn btn-primary'><h5>Verify Account </h5></a>
                                          <footer class='page-footer font-small cyan darken-3 fixed-bottom'>
                                          <div class='footer-copyright text-center py-3'>© {$copy} Copyright:
                                        <a href='https://www.bluefaces.tech/'>BLUEFACES</a>
                                        </div>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                        </div>
        ";
        try {
            mail($to, $subject, $body, $headers);
            echo $this->sent();
        }
        catch(Exception $e){
            echo $this->failed();
        }
    }
    private function resetRequestSent(){
        return "<div class='alert alert-success alert-dismissible fade show'>
    <strong><i class='fas fa-check-circle'></i></strong>A reset password link has been sent to your email. Please click on it to continue and reset your password.
    <button type='button' class='close' data-dismiss='alert'>&times;</button>
</div>";
    }
    private function resetRequestFailed() {
        return "<div class='alert alert-danger alert-dismissible fade show'>
        <strong><i class='fas fa-exclamation-triangle'></i></strong> Oops, Message Not Sent, Please try again later!
        <button type='button' class='close' data-dismiss='alert'>&times;</button>
    </div>";
    }
    public function sendRequest($to, $subject, $message, $name = "Dear User", $from = "BLUEFACES <info@bluefaces.tech>") {
        $copy = date("Y");
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From:". $from. "\r\n";
        $body = "
                                <div class='row'>
                            <div class='col-12'>
                                <div class='card mt-2'>
                                <div class='card-header border-0 py-1'>
                                    <h3 class='card-title text-primary'>PASSWORD RESET REQUEST</h3>                              
                                      </div>
                                         <div class='card-body'>
                                          <h4 class='card-title text-primary'>Hello, {$name}</h4>
                                          <div class='text-secondary'> {$message}</div>
                                          <footer class='page-footer font-small cyan darken-3 fixed-bottom'>
                                          <div class='footer-copyright text-center py-3'>© {$copy} Copyright:
                                        <a href='https://www.bluefaces.tech/'>BLUEFACES</a>
                                        </div>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                        </div>
        ";
        try {
            mail($to, $subject, $body, $headers);
            echo $this->resetRequestSent();
        }
        catch(Exception $e){
            echo $this->resetRequestFailed();
        }
    }
    public function notify($to, $subject, $message, $name = "Dear User",  $from = "BLUEFACES <info@bluefaces.tech>") {
        $copy = date("Y");
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From:". $from. "\r\n";
        $body = "
                                <div class='row'>
                            <div class='col-12'>
                                <div class='card mt-2'>
                                <div class='card-header border-0 py-1'>
                                    <h3 class='card-title text-primary'>BLUEFACES NOTIFICATION</h3>                              
                                      </div>
                                         <div class='card-body'>
                                          <h4 class='card-title text-primary'>Hi, {$name}</h4>
                                          <div class='text-secondary'> {$message}</div>
                                          <footer class='page-footer font-small cyan darken-3 fixed-bottom'>
                                          <div class='footer-copyright text-center py-3'>© {$copy} Copyright:
                                        <a href='https://www.bluefaces.tech/'>BLUEFACES</a>
                                        </div>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                        </div>
        ";
        try {
            mail($to, $subject, $body, $headers);
        }
        catch(Exception $e){
        }
    }
}

