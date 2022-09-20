<?php

use Classes\Security;

app()->router()->get('/captchag', function(){
  return Security::generate_captcha();
});

app()->router()->get('/', 'ExempleController@index');

return true;
