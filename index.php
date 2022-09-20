<?php 


date_default_timezone_set('Africa/Algiers');

require 'vendor/autoload.php';

define('SESSION_TIMEOUT', config('app.session_timeout'));

session_start();

$app = app();

$app->load_env_vars();

$app->set_debug(env('APP_DEBUG', false));

$app->set_handler();

$app->database_connection();

$app->add_assets_route();

$app->load_routes();

$app->set_include_path();

if (isset($_SESSION['start']) && (time() - $_SESSION['start'] > SESSION_TIMEOUT)) {
  session_unset(); 
  session_destroy(); 
  set_session_message('login_error', 'session destroyed');
  redirect('/login');
}

$_SESSION['start'] = time();