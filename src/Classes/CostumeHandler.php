<?php

namespace Classes;

class CostumeHandler{

    public static function error_500( $errno = 0, $errstr ="", $errfile="", $errline ="", $errcontext = array()){  
        return view('errors/500', compact("errno", "errstr", 'errfile', 'errline', 'errcontext'));
    }

    public static function error_419(){
        return view("errors/419");
    }

    public static function error_404(){
        return view("errors/404");
    }
}