<?php

namespace Classes;

define('CSRF_TIMEOUT', config("app.csrf_token_timeout"));
define('BR_ATTEMPS_MAX', config("app.bruteforce_allowed_attemps"));
define('BR_ATTEMPS_MAX_TIME', config("app.bruteforce_block_time"));

class Security
{

  private static $string_length = 6;
  private static $permitted_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  private static $image_width = 100;
  private static $image_height = 50;

  private static $generated_string = "";

 public static function csrf_protection()
    {

        if (isset($_POST['csrf_token']) === false) {
            return false;
        }

        $token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : "";

        if ($token === "") {
            return false;
        }

        if ($_POST['csrf_token'] !== $token) {
            return false;
        }

        if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time'] > CSRF_TIMEOUT)) {
            unset($_SESSION['csrf_token']);
            return false;
          }

        unset($_SESSION['csrf_token']);
        return true;
    }

    public static function generate_csrf_token()
    {
        $token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : "";
        if (!$token) {
            $token = md5(uniqid());
            $_SESSION['csrf_token'] = $token;
            $_SESSION['csrf_token_time'] = time();
        }
    }

  public function check_captcha($input){
    return $input === self::$generated_string;
  }

  public static function get_captcha_img(){
    echo "<img src='". base_url() ."/captcha' alt='captcha'";
  }

  public static function generate_captcha()
  {
    $image = self::generate_background_image();
    self::$generated_string = self::generate_random_string();

    $black = imagecolorallocate($image, 0, 0, 0);
    $white = imagecolorallocate($image, 255, 255, 255);
    $textcolors = [$black, $white];

    $fonts_path = __DIR__ . '/../App/assets/fonts/';
    
    $fonts = [$fonts_path . 'acme/Acme-Regular.ttf', $fonts_path . 'ubuntu/Ubuntu-Regular.ttf', $fonts_path . 'merriweatherSans/MerriweatherSans-Regular.ttf'];
    
    for($i = 0; $i < self::$string_length; $i++) {
      $letter_space = 70/self::$string_length;
      $initial = 15;
      imagettftext($image, 20, rand(0, 15), $initial + $i*$letter_space, rand(20, 40), $textcolors[rand(0, 1)], $fonts[array_rand($fonts)], self::$generated_string[$i]);
    }
    
    header('Content-type: image/png');
    return imagepng($image);
    imagedestroy($image);
  }


  private static function generate_random_string()
  {
    $input_length = strlen(self::$permitted_chars);
    $random_string = '';
    for ($i = 0; $i < self::$string_length; $i++) {
      $random_character = self::$permitted_chars[random_int(0, $input_length - 1)];
      $random_string .= $random_character;
    }
    return $random_string;
  }

  private static function generate_background_image()
  {
    $image = imagecreatetruecolor(self::$image_width, self::$image_height);

    imageantialias($image, true);

    $colors = [];

    $red = rand(125, 175);
    $green = rand(125, 175);
    $blue = rand(125, 175);

    for ($i = 0; $i < 5; $i++) {
      $colors[] = imagecolorallocate($image, $red - 20 * $i, $green - 20 * $i, $blue - 20 * $i);
    }

    imagefill($image, 0, 0, $colors[0]);

    for ($i = 0; $i < 10; $i++) {
      imagesetthickness($image, rand(2, 10));
      $rect_color = $colors[rand(1, 4)];
      //imagerectangle($image, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $rect_color);
    }

    return $image;
  }


  public static function brute_force_protection(){
    $ip = str_replace('.', '', $_SERVER['REMOTE_ADDR']);
    if(!isset($_SESSION["bf_" . $ip])){
      $_SESSION["bf_" . $ip] = 1;
      $_SESSION["bf_" . $ip . "_time"] = time();
      return true;
    }

    if($_SESSION["bf_" . $ip] < BR_ATTEMPS_MAX){
      $_SESSION["bf_" . $ip] = intval($_SESSION["bf_" . $ip]) + 1;
      return true;
    }else{
      if(time() - $_SESSION["bf_" . $ip . "_time"] > BR_ATTEMPS_MAX_TIME){
        $_SESSION["bf_" . $ip . "_time"] = time();
        $_SESSION["bf_" . $ip] = 1;
        return true;
      }else{
        set_session_message("login_error", "You was blocked for ". BR_ATTEMPS_MAX_TIME / 60 ." minutes");
        return false;
      }
    }

    return true;

  }
}
