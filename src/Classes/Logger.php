<?php

namespace Classes;

define("LOGGER_INFO", 0);
define("LOGGER_ERROR", 1);

class Logger{

    public static function write($message, $type = LOGGER_INFO){
        $date = new \DateTime();
        $log_date = $date->format('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];

        $path_to_file = __DIR__ . "/../storage/logs/log-" . $date->format('Y-m-d') . ".log";

        if($type === LOGGER_ERROR){
            $path_to_file = __DIR__ . "/../storage/logs/error-" . $date->format('Y-m-d') . ".log";
        }

        $data = "[$log_date][$ip] : $message" . PHP_EOL;

        file_put_contents($path_to_file, $data, FILE_APPEND);
    }
}