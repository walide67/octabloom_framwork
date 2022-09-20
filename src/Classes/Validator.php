<?php

namespace Classes;

class Validator{

    private static $messages = [
        "required" => "the @ field is required",
        "email" => "@ field value is not a valid email",
        "max" => "the @ field contains more than # characters",
        "min" => "the @ field contains less than # characters",
        "string" => "the @ field must be string",
        "regex_match" => "the @ field is not valid"
    ];

    public static function make($rules, $inputs){
        $rules = is_string($rules) ? explode("|", $rules) : $rules;
        $errors = array();
        foreach ($inputs as $input_name => $value) {
            if(key_exists($input_name, $rules)){
                $input_rules = explode("|", $rules[$input_name]);
                $error_message = "";
                foreach ($input_rules as $key => $rule) {
                    $rule = explode(":", $rule);
                    $rule_name = $rule[0];
                    array_shift($rule);
                    $params = $rule;
                    if(count($params) > 0){
                        $error_status = call_user_func_array([Validator::class, $rule_name], array_merge([$value], $params));
                    }else{
                        $error_status = call_user_func([Validator::class, $rule_name], $value);
                    }
                    if(!$error_status){
                        $error_message = self::prepare_message($rule_name, $input_name);
                        break;
                    } 
                }
                if($error_message !== ""){
                    $errors[$input_name] = $error_message;
                }
            }
        }

        if(count($errors) > 0){
            foreach ($errors as $input_name => $error) {
              set_session_message($input_name, $error);
            }
            redirect_back();
        }
        return true;
    }

    private static function required($input){
        return strlen(trim($input)) > 0;
    }

    private static function email($input){
        return filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    private static function max($input, $max){
        self::$messages["max"] = str_replace("#", $max, self::$messages["max"]);
        return strlen($input) <= intval($max);
    }

    private static function min($input, $min){
        self::$messages["min"] = str_replace("#", $min, self::$messages["min"]);
        return strlen($input) >= intval($min);
    }

    private static function string($input){
        return is_string($input);
    }

    private static function regex_match($input, $pattern){
        return preg_match($pattern, $input) === 1 ? true : false;
    }

    private static function messages($rule){
        return self::$messages[$rule];
    }

    private static function prepare_message($message_key, $input_name =""){
        $message = "";
        if(key_exists($message_key, self::$messages)){
            $message = self::$messages[$message_key];
            $message = str_replace("@", $input_name, $message);
            $message = str_replace("#", "", $message);
        }

        return $message;
    }
}