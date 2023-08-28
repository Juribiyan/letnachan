<?PHP
class Obj
    {
    var $variable_name;
    var $validator_string;
    var $error_string;
    }
define("E_VAL_REQUIRED_VALUE", "Пожалуйста, введите %s");
define("E_VAL_MAXLEN_EXCEEDED", "Поле %s превысило максимальную длину.");
define("E_VAL_MINLEN_CHECK_FAILED", "Пожалуйста, введите большую длину %d для %s");
class FormValidator
    {
    var $validator_array;
    var $error_hash;
    var $custom_validators;
    function FormValidator()
        {
        $this->validator_array   = array();
        $this->error_hash        = array();
        $this->custom_validators = array();
        }
    function addValidation($variable, $validator, $error)
        {
        $validator_obj                   = new Obj();
        $validator_obj->variable_name    = $variable;
        $validator_obj->validator_string = $validator;
        $validator_obj->error_string     = $error;
        array_push($this->validator_array, $validator_obj);
        }
    function GetErrors()
        {
        return $this->error_hash;
        }
    function ValidateForm()
        {
        $bret             = true;
        $error_string     = "";
        $error_to_display = "";
        if (strcmp($_SERVER['REQUEST_METHOD'], 'POST') == 0)
            {
            $form_variables = $_POST;
            }
        else
            {
            $form_variables = $_GET;
            }
        $vcount = count($this->validator_array);
        foreach ($this->validator_array as $val_obj)
            {
            if (!$this->ValidateObject($val_obj, $form_variables, $error_string))
                {
                $bret                                      = false;
                $this->error_hash[$val_obj->variable_name] = $error_string;
                }
            }
        if (true == $bret && count($this->custom_validators) > 0)
            {
            foreach ($this->custom_validators as $custom_val)
                {
                if (false == $custom_val->DoValidate($form_variables, $this->error_hash))
                    {
                    $bret = false;
                    }
                }
            }
        return $bret;
        }
    function ValidateObject($Object, $formvariables, &$error_string)
        {
        $bret          = true;
        $splitted      = explode("=", $Object->validator_string);
        $command       = $splitted[0];
        $command_value = '';
        if (isset($splitted[1]) && strlen($splitted[1]) > 0)
            {
            $command_value = $splitted[1];
            }
        $default_error_message = "";
        $input_value           = "";
        if (isset($formvariables[$Object->variable_name]))
            {
            $input_value = $formvariables[$Object->variable_name];
            }
        $bret = $this->ValidateCommand($command, $command_value, $input_value, $default_error_message, $Object->variable_name, $formvariables);
        if (false == $bret)
            {
            if (isset($Object->error_string) && strlen($Object->error_string) > 0)
                {
                $error_string = $Object->error_string;
                }
            else
                {
                $error_string = $default_error_message;
                }
            }
        return $bret;
        }
    function validate_req($input_value, &$default_error_message, $variable_name)
        {
        $bret = true;
        if (!isset($input_value) || strlen($input_value) <= 0)
            {
            $bret                  = false;
            $default_error_message = sprintf(E_VAL_REQUIRED_VALUE, $variable_name);
            }
        return $bret;
        }
    function validate_maxlen($input_value, $max_len, $variable_name, &$default_error_message)
        {
        $bret = true;
        if (isset($input_value))
            {
            $input_length = strlen($input_value);
            if ($input_length > $max_len)
                {
                $bret                  = false;
                $default_error_message = sprintf(E_VAL_MAXLEN_EXCEEDED, $variable_name);
                }
            }
        return $bret;
        }
    function validate_minlen($input_value, $min_len, $variable_name, &$default_error_message)
        {
        $bret = true;
        if (isset($input_value))
            {
            $input_length = strlen($input_value);
            if ($input_length < $min_len)
                {
                $bret                  = false;
                $default_error_message = sprintf(E_VAL_MINLEN_CHECK_FAILED, $min_len, $variable_name);
                }
            }
        return $bret;
        }
    function ValidateCommand($command, $command_value, $input_value, &$default_error_message, $variable_name, $formvariables)
        {
        $bret = true;
        switch ($command)
        {
            case 'req':
                {
                $bret = $this->validate_req($input_value, $default_error_message, $variable_name);
                break;
                }
            case 'maxlen':
                {
                $max_len = intval($command_value);
                $bret    = $this->validate_maxlen($input_value, $max_len, $variable_name, $default_error_message);
                break;
                }
            case 'minlen':
                {
                $min_len = intval($command_value);
                $bret    = $this->validate_minlen($input_value, $min_len, $variable_name, $default_error_message);
                break;
                }
        }
        return $bret;
        }
    }
?> 