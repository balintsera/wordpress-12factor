<?php

if (!getenv('DATABASE_URL')) {
		throw new Exception('DATABASE_URL must be set');
}

$db = array_merge(parse_url(getenv('DATABASE_URL')));
define('DB_NAME',     substr($db['path'], 1));
define('DB_USER',     $db['user']);
define('DB_PASSWORD', $db['pass']);
define('DB_HOST',     $db['host'].':'.$db['port']);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
$table_prefix  = 'wp_';

define('AWS_ACCESS_KEY_ID', getenv('AWS_ACCESS_KEY_ID') ? : getenv('BUCKETEER_AWS_ACCESS_KEY_ID'));
define('AWS_SECRET_ACCESS_KEY', getenv('AWS_SECRET_ACCESS_KEY')?:getenv('BUCKETEER_AWS_SECRET_ACCESS_KEY'));


define('AUTH_KEY',         getenv('WORDPRESS_AUTH_KEY')        ?:'put your unique phrase here');
define('SECURE_AUTH_KEY',  getenv('WORDPRESS_SECURE_AUTH_KEY') ?:'put your unique phrase here');
define('LOGGED_IN_KEY',    getenv('WORDPRESS_LOGGED_IN_KEY')   ?:'put your unique phrase here');
define('NONCE_KEY',        getenv('WORDPRESS_NONCE_KEY')       ?:'put your unique phrase here');
define('AUTH_SALT',        getenv('WORDPRESS_AUTH_SALT')       ?:'put your unique phrase here');
define('SECURE_AUTH_SALT', getenv('WORDPRESS_SECURE_AUTH_SALT')?:'put your unique phrase here');
define('LOGGED_IN_SALT',   getenv('WORDPRESS_LOGGED_IN_SALT')  ?:'put your unique phrase here');
define('NONCE_SALT',       getenv('WORDPRESS_NONCE_SALT')      ?:'put your unique phrase here');

define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);
if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') $_SERVER['HTTPS'] = 'on';

define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', false); // this is correct - we don't want errors to go to debug.log, but to the default destination

define('DISALLOW_FILE_MODS', true);

define('DISABLE_WP_CRON', in_array(getenv('DISABLE_WP_CRON'), ['true', '1', 'yes'], true) ? true : false);

if(!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__) . '/wordpress/'); // should not be necessary

require_once(ABSPATH . 'wp-settings.php');

