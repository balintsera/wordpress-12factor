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
      moveIfExists("wordpress/wp-content/uploads", "uploads");
      moveIfExists("wordpress/.htaccess", ".htaccess");
    break;

    case 'post':
      moveIfExists("uploads", "wordpress/wp-content/uploads");
      moveIfExists(".htaccess", "wordpress/.htaccess");
      moveIfExists("src/languages", "wordpress/wp-content/languages");
    break;
    default:
      throw new Exception('No argument or wrong argument:' . $argv[1]);
}


function moveIfExists($target, $destination) {
  if (!file_exists($target)) {
    return;
  }
  
  rename($target, $destination);
}

function copyIfExists($target, $destination) {
   if (!file_exists($target)) {
    return;
  }

  shell_exec("cp -r $target $dest");
}