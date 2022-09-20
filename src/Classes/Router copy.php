<?php

namespace Classes;

use Handler;
use Interfaces\IRequest;

class Router
{
  private $request;
  private $supportedHttpMethods = array(
    "GET",
    "POST"
  );

  function __construct(IRequest $request)
  {
    $this->request = $request;
  }

  public function request()
  {
    return $this->request;
  }

  ###################################################################################
  ###################################################################################

  function __call($name, $args)
  {

    list($uri, $method) = $args;
    if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {
      $this->invalidMethodHandler();
    }

    $route = rtrim(preg_replace("/:[a-z]+/", '', $uri), '/');

    $params_keys = $this->get_params_from_uri($uri, $route);
    
    $request_uri = trim(str_replace($this->request->appUrl, '', $this->request->requestUri), '/');


    if ($method instanceof \Closure) {

      $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
    }

    if (is_string($method)) {
      list($ctrClass, $function) = explode('@', $method);
      $controller = "App\Controllers\\" . $ctrClass;
      $obj = new $controller;
      $closure = (new \ReflectionMethod("App\Controllers\\" . $ctrClass, $function))->getClosure($obj);

      $this->{strtolower($name)}[$this->formatRoute($route)] = $closure;
    }
  }


  function get_params_from_uri($uri , $route){
    $params = array();
    $params_string = trim(str_replace($route, '', $uri), '/:');

    if($params_string !== ""){
      $params  = explode('/:', $params_string);
    }

    return $params;
  }



  /**
   * Removes trailing forward slashes from the right of the route.
   * @param route (string)
   */
  private function formatRoute($route)
  {

    $route  = get_localhost_url($route);

    $result = trim($route, '/');
    if ($result === '') {
      return '/';
    }
    return $result;
  }

  private function invalidMethodHandler()
  {
    header("{$this->request->serverProtocol} 405 Method Not Allowed");
  }

  private function defaultRequestHandler()
  {
    header("{$this->request->serverProtocol} 404 Not Found");
  }

  /**
   * Resolves a route
   */
  function resolve()
  {
    $methodDictionary = $this->{strtolower($this->request->requestMethod)};

    $this->request->requestUri  = get_localhost_url($this->request->requestUri);

    $uri = trim($this->request->requestUri, '/');

    $slash_position  = strrpos($uri, '/');
    if(strpos($uri, "assets") !== false){
      $slash_position  = strpos($uri, '/');
      
    }

    $route = ($slash_position) ? substr($uri,0, $slash_position) : $uri;


    if($route !== "assets"){
      $params = $slash_position ? explode('/', substr($uri, $slash_position + 1)) : array();
      
    }else{
      $params = array(substr($uri, $slash_position + 1));
    }

    $formatedRoute = $this->formatRoute($route);

    if($this->request()->request_method() === "GET"){
      Security::generate_csrf_token();
    }


    if($this->request()->request_method() === "POST"){

      if(!(Security::csrf_protection())){
        return CostumeHandler::error_419();
      }
    }

    if (!array_key_exists($formatedRoute, $methodDictionary)) {
      return CostumeHandler::error_404();
    } else {
      $method = $methodDictionary[$formatedRoute];
    }

    $reflection = new \ReflectionFunction($method);
    $arguments  = $reflection->getParameters();

    $args_num = count($arguments) > 0 ? count($arguments) - 1 : count($arguments);

    if(count($params) !== $args_num){
      return CostumeHandler::error_404();
    }

    if (is_null($method)) {
      $this->defaultRequestHandler();
      return;
    }

    echo call_user_func_array($method, array_merge(array($this->request), $params));
  }

  function __destruct()
  {
    $this->resolve();
  }
}
