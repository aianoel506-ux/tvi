<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

// FOR SYSTEM INFO
define('SYSTEM_NAME',		'Tiedra Ventures Portal');
define('SYSTEM_MSG_QRY',	'Error PHP: Query is not set yet, please check backend PHP!');
define('SYSTEM_MSG_DEFAULT', 'Error PHP: Query is not set yet, please check backend PHP!');

// DATABASE SETUP
define('SYSTEM_ONLINE', FALSE); // TRUE IF CONNECT DB TO LOCALHOST
define('SYSTEM_DEV_MODE', TRUE); // TRUE IF CONNECT DB TO LOCALHOST
define('SYSTEM_DEV_PORT', FALSE); // FALSE if default | add port if 3306 is not the mysql port
define('SYSTEM_GOOGLE_API', 'AIzaSyDqC5lmJR1TtWTnySj2psx8-3JynOFUyYE');
define('APP_URL', ($_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http') . "://{$_SERVER['SERVER_NAME']}".str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));

/* End of file constants.php */
/* Location: ./application/config/constants.php */