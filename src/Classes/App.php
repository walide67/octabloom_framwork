<?php

namespace Classes;

use PDO;

class App
{

  private static $instance = null;

  private $router = null;

  private $conn;

  public function database_connection()
  {
    try{
    $dns = env("DB_DRIVER", "mysql") . ":host=" . env("DB_HOST", "localhost") . ":" . env("DB_PORT", "3306") . ";dbname=" . env("DB_DATABASE_NAME");

    $conn = new PDO($dns, env("DB_USERNAME", ""), env("DB_PASSWORD", ""), $this->get_pdo_options(env("APP_DEBUG", false)));

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //$conn->exec("set names utf8");
    $this->conn = $conn;

  } catch (\PDOException $e) {
    
   }
  }

  public function load_env_vars()
  {
    (new DotEnv(__DIR__ . '/../.env'))->load();
  }

  public function set_include_path()
  {
    $current_file = __FILE__;
    $path_array = explode(DIRECTORY_SEPARATOR, trim(dirname($current_file), DIRECTORY_SEPARATOR));

    array_pop($path_array);

    $path = implode(DIRECTORY_SEPARATOR, $path_array);

    set_include_path($path);
  }

  public function set_handler(){
    set_error_handler([CostumeHandler::class, "error_500"]);
  }

  public function load_routes()
  {

    require "src/routes/web.php";

  }

  public function csrf_token(){
    return isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : false;
  }

  public function add_assets_route()
  {
    $this->router = new Router(new Request);

    $assets_route = '/assets/:file';
    $this->router->get($assets_route, function ($request, $file_path) {

      $path = str_replace("/", DIRECTORY_SEPARATOR, __DIR__ . "/../App/assets/" . $file_path);

      if (is_readable($path)) {
        $stored_file = file_get_contents($path);

        $mime_type = mime_content_type($path);

        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ($ext === 'css') {
          $mime_type = "text/css";
        }

        if ($ext === "js") {
          $mime_type = "text/javascript";
        }

        header("Content-Type: " . $mime_type);

        return $stored_file;
      }
      return false;
    });
  }

  public static function get_instance()
  {

    if (is_null(self::$instance)) {
        self::$instance = new static();
    }
    return self::$instance;
  }

  public function router()
  {
    return $this->router;
  }

  public function request()
  {
    return $this->router()->request();
  }

  public function set_debug($debug_mode)
  {
    if ($debug_mode) {
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);
    } else {
      ini_set('display_errors', 0);
      ini_set('display_startup_errors', 0);
      error_reporting(0);
    }
  }

  private function get_pdo_options($debug_mode)
  {
    $options = array(
      PDO::MYSQL_ATTR_LOCAL_INFILE => true,
      PDO::ATTR_ERRMODE    => PDO::ERRMODE_SILENT,
      PDO::ATTR_PERSISTENT => false,
    );

    if ($debug_mode) {
      $options = array(
        PDO::MYSQL_ATTR_LOCAL_INFILE => true,
        PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => true,
      );
    }

    return $options;
  }

}
