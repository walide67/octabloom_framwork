<?php use Classes\Logger; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="<?php assets("libs/bootstrap/css/bootstrap.min.css"); ?>">
  <link rel="stylesheet" type="text/css" href="<?php assets("libs/fontawesome/css/all.min.css"); ?>">
  <link rel="stylesheet" type="text/css" href="<?php assets("libs/datatables/datatables.min.css"); ?>">
  <link rel="stylesheet" type="text/css" href="<?php assets('css/style.css'); ?>">
  <title>Server error</title>
  <style>
    html,
    body {
      height: 100%;
      overflow: hidden;
      color: #6c757d !important;
    }

    .container {
      text-align: center;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      height: 100%;

    }

    .container h1 {
      font-size: 45px;
      font-weight: 600;
      font-family: sans-serif;
    }

    .container h3 {
      font-size: 26px;
    }
  </style>
</head>

<body>
  <div class="container">
    <?php 
    Logger::write("[ File $errfile : $errline ][Error no : $errno] : $errstr", LOGGER_ERROR);
    ?>
    <?php if (!env('APP_DEBUG', false) && $errno) {  ?>
      <div>
        <h1>Server Error</h1>
        <h3>500</h3>
        <?php exit; ?>
      </div>
    <?php } else { ?>
      <div>
        <h3><?php echo "#" . $errno . " - " . $errstr; ?></h3>
        <h3><?php echo "File : " . $errfile; ?></h3>
        <h3><?php echo "Line : " . $errline; ?></h3>
        <?php exit; ?>
      </div>
    <?php } ?>
  </div>

</body>

</html>