#!/bin/php
<?php

ini_set('register_argc_argv', 0);
if (!isset($argc) || is_null($argc)) die('CLI only');

$upload_dir = dirname(dirname(__FILE__)). DIRECTORY_SEPARATOR . 'uploads';


// Apache could handle symlinked directories 
// but php -S can't and we want uniform developer and deploy experiences
// throughout all OS-es
/* 
$os = strtolower(php_uname('s')); 

if($os === 'darwin' || strpos($os, 'win') === false) {
  echo 'not win, won\'t move uploads dir';
  return; 
} 
*/
// Windows
switch($argv[1]){
    case 'pre':
      rename("wordpress/wp-content/uploads", "uploads");
      rename("wordpress/.htaccess", ".htaccess");
    break;

    case 'post':
      rename("uploads", "wordpress/wp-content/uploads");
      rename(".htaccess", "wordpress/.htaccess");
    break;
    default:
      throw new Exception('No argument or wrong argument:' . $argv[1]);
}