<?php

namespace Classes;

use Interfaces\IRequest;

class Request implements IRequest
{

  function __construct()
  {
    $this->bootstrapSelf();

  }

  private function bootstrapSelf()
  {
    foreach($_SERVER as $key => $value)
    {
      $this->{$this->toCamelCase($key)} = $value;
    }
  }

  private function toCamelCase($string)
  {
    $result = strtolower($string);
        
    preg_match_all('/_[a-z]/', $result, $matches);

    foreach($matches[0] as $match)
    {
        $c = str_replace('_', '', strtoupper($match));
        $result = str_replace($match, $c, $result);
    }

    return $result;
  }

  public function request_method(){
    return $this->requestMethod;
  }

  public function all()
  {
    if($this->requestMethod === "GET")
    {
      return;
    }


    if ($this->requestMethod == "POST")
    {

      $body = array();
      
      foreach($_POST as $key => $value)
      {
        $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
      }

      foreach($_FILES as $key => $value)
      {
        $body[$key] = new File($value);
      }

      return $body;
    }
  }

  public function get($key){
    if($this->requestMethod === "GET")
    {
      return;
    }


    if ($this->requestMethod == "POST")
    {

      if(isset($_POST[$key])){
        return  filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
      }

      if(isset($_FILES[$key])){
        return new File($_FILES[$key]);
      }

      return;
    }
  }
}