<?php
namespace System\Http\Validate;
use System\Database\DB;

class Validate
{

    protected $validated = array();
    protected $password = '';

    /**
     * Validate user Input
     *
     * @param array $rules
     */
    public function __construct(array $rules)
    {
       return $this->validate($rules); 
    }


    public function validate(array $rules)
    {
        $_SESSION['errors'] = [];
        foreach($rules as $key => $inputRules)
        {
            $inputRules = explode('|', $inputRules);
            foreach($inputRules as $rule)
            {
                if(strpos($rule, ':') !== false)
                {
                    $rule = explode(':', $rule);
                    if(count($rule) > 2)
                    {
                        $this->{$rule[0]}($_POST[$key], $rule[1], $rule[2], str_replace('_', ' ', $key));
                    }else 
                    {
                        $this->{$rule[0]}($_POST[$key], $rule[1], str_replace('_', ' ', $key));
                    }
                }else {

                    $this->{$rule}($_POST[$key], str_replace('_', ' ', $key));
                }
            }
        }
        return $this->isValid();
    }


    protected function required(string $value, string $input = '')
    {
        if(empty(trim($value)))
        {
            $_SESSION['errors'][] = "The $input field must be filled";
        }else{
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = $value;
        }
    }

    protected function max(string $string, string $num, string $input = '')
    {
        $string = trim($string);
        if(strlen($string) > $num)
        {
            $_SESSION['errors'][] = "The $input($string) should not be greater than $num";
        }
        else{
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = $string;
        }
    }

    protected function min(string $string, string $num, string $input='')
    {
        $string = trim($string);
        if(strlen($string) < $num)
        {
            $_SESSION['errors'][] = "The $input($string) should not be less than $num.";
        }
        else{
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = $string;
        }
    }

    protected function regex(string $value, string $regex, string $input = '')
    {
        if(preg_match($regex, $value) === 0)
        {
            $_SESSION['errors'][] = "The submitted $input is invalid.";
        }
        else{
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = $value;
        }
    }

    protected function numeric($value, string $input = '')
    {
        if(!is_numeric($value))
        {
            $_SESSION['errors'][] = "The $input should be a number.";
        }
        else{
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = $value;
        }
    }

    
    protected function nullable($value, string $input = '')
    {
        if(empty(trim($value)))
        {
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = trim($value);
        }
    }


    protected function email($value, string $input = '')
    {
        if(!filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            $_SESSION['errors'][] = "The submitted email address is invalid.";
        }
        else
        {
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = trim($value);
        }
    }

    protected function url($value, string $input = '')
    {
        if(!filter_var($value, FILTER_VALIDATE_URL))
        {
            $_SESSION['errors'][] = "The submitted URL is invalid.";
        }
        else
        {
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = trim($value);
        }
    }

    protected function password1($value, string $input = '')
    {
        $this->password = $value;
    }


    protected function password2($value, string $input = '')
    {
        if(strcmp($this->password, $value) !== 0)
        {
            $_SESSION['errors'][] = "Passwords do not match.";
        }
        else
        {
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = trim($value);
        }
        $this->password = '';
    }


    protected function unique(string $value, string $table, $column, string $input = '')
    {
        if(DB::table($table)->where($column, $value)->exists())
        {
            $error = "The supplied " . str_replace('_', ' ', $input) . " is already 
            taken / in use";
            $_SESSION['errors'][] = $error;
        }
        else
        {
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = trim($value);
        }
    }


    protected function date(string $string, string $input='')
    {
        $string = str_replace('/', '-', $string);
        if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $string) === 0)
        {
            $_SESSION['errors'][] = "Invalid date format for $input";
        }
        else{
            $input = str_replace(' ', '_', $input);
            $this->validated[$input] = $string;
        }
    }

    /**
     * Get Validated User Input
     *
     * @return array
     */
    public function validated()
    {
        return $this->validated;
    }


    public function isValid()
    {
        if(!empty($_SESSION['errors']))
        {
            return redirect()->back()->withInput();
        }
        return $this;
    }
}