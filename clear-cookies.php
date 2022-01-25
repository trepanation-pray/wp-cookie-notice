<?php
  function clear_cookies() {
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time()-1000);
            setcookie($name, '', time()-1000, '/');
        }
    }
    echo 'done';
  }

  add_action( 'rest_api_init', function() {
  register_rest_route( 'cookie-control', '/clear-cookies', array(          
    'methods'  => 'GET',
    'callback' => 'clear_cookies' 
  ) ); 
  } );