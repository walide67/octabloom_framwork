<?php

namespace Classes;

class File{
    private $src = __DIR__ . "/../storage/tmp/";
    private $file = null;
    private $filename = "";
    private $tmp = "";
    private $size = "";
    private $error = 0;
    private $type = "";
    private $generated_name = "";

    public function __construct($file)
    {
        $this->file = $file;
        $this -> filename = $this->file["name"];
        $this -> tmp = $this->file["tmp_name"];
        $this -> size = $this->file["size"];
        $this -> error = $this->file["error"];
        $this -> type = $this->file["type"];
    }

    public function get_error_message(){
        switch ($this->error()) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    private function generate_file_name(){
        $ext = substr($this -> filename, strrpos($this -> filename, '.')+1);
        return time() . "." . $ext;
    }

    public function set_distination($dist){
        $this->src = $dist;
    }

    public function get_distination(){
        return str_replace("/", DIRECTORY_SEPARATOR,  $this->src);
    }

    public function name(){
        return $this->filename;
    }

    public function tmp(){
        return $this->tmp;
    }

    public function size(){
        return $this->size;
    }

    public function error(){
        return $this->error;
    }

    public function type(){
        return $this->type;
    }

    public function get_generated_file_name(){
        return $this->generated_name;
    }

    public function upload(){
        $this->generated_name = $this->generate_file_name();

        if(move_uploaded_file($this -> tmp, $this->src . $this->generated_name)){
            return true;
        }

        return false;
    }


}