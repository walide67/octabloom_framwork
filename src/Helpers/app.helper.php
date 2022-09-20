<?php

function autoload($className)
{
  $className = ltrim($className, "\\");
  $fileName  = '';
  $namespace = '';
  if ($lastNsPos = strripos($className, "\\")) {
    $namespace = substr($className, 0, $lastNsPos);
    $className = substr($className, $lastNsPos + 1);
    $fileName  = str_replace("\\", DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }
  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.class.php';

  require $fileName;
}


function base_url()
{
  if (!is_localhost()) {
    return sprintf(
      "%s://%s",
      isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
      $_SERVER['SERVER_NAME']
    );
  }

  return sprintf(
    "%s://%s/%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    env("APP_URL")
  );
}

function config($conf)
{
  $array = explode(".", $conf);
  if (count($array) == 2) {
    $config = require __DIR__ . "/../Config/" . $array[0] . ".php";
    if (array_key_exists($array[1], $config)) {
      return $config[$array[1]];
    }

    return null;
  }
  return null;
}

function view($view_file, $args = null)
{
  if (!is_null($args)) {
    foreach ($args as $key => $value) {
      $$key = $value;
    }
  }

  $view_file = str_replace(".", DIRECTORY_SEPARATOR, $view_file);
  $path = __DIR__ . "/../App/views/" . $view_file . ".php";
  if (is_readable($path)) {
    include_once $path;
  } else {
    return "view not found";
  }
}

function assets($file)
{
  echo trim(base_url(), '/') . "/assets/" . $file;
}

function env($key, $default = null)
{
  if (is_null($_ENV[$key])) {
    return $default;
  }

  return $_ENV[$key];
}

function app()
{
  return Classes\App::get_instance();
}

function is_localhost()
{
  if ($_SERVER['HTTP_HOST'] === "localhost" || $_SERVER['HTTP_HOST'] === "127.0.0.1") {
    return true;
  }

  return false;
}

function redirect($uri)
{
  header("Location:" . base_url() . $uri);
  exit();
}

function route($uri)
{
  return base_url() . $uri;
}

function redirect_back()
{
  header('Location: ' . $_SERVER['HTTP_REFERER']);
  exit;
}

function get_localhost_url($route)
{
  if (is_localhost()) {
    return str_replace("/" . env("APP_URL"), '', $route);
  }


  return $route;
}

function csrf_token()
{
  return $_SESSION['csrf_token'];
}

function set_session_message($key, $value)
{
  $_SESSION[$key] = trim($value);
}

function get_session_message($key)
{
  if (isset($_SESSION[$key])) {
    $message = $_SESSION[$key];
    unset($_SESSION[$key]);
    return $message;
  }
  return null;
}

function has_session_message($key)
{
  if (isset($_SESSION[$key])) {
    return true;
  }
  return false;
}

function getRecords($data)
{
  foreach ($data as $row) {
    yield $row;
  }
}
