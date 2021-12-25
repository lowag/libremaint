<?php
//define( 'ABSPATH', dirname( __FILE__ ) . '/' );defined in index.php
define('REQ_MIN_PHP_VERSION','7.0.0');
define('SQL_DB','MYSQL');//MYSQL or MSSQL (case sensitive),but maybe there are some mysql specific queries...
define('SERVER_IP_ADDR','your_server_ip_or_name');
define( 'URL','http://'.SERVER_IP_ADDR.'/libremaint/' );
define('CSS_LOC', URL.'style/');
define('VENDORS_LOC', URL.'vendor/');
define('VENDORS_PATH', ABSPATH.'vendor/');
define('INCLUDES_PATH', ABSPATH.'includes/');
define('INCLUDES_LOC', URL.'includes/');
define('PAGES_LOC', URL.'pages/');
define('USER_PAGES_LOC', ABSPATH.'pages/user_pages/');
define('PAGES_PATH', ABSPATH.'pages/');
define('USER_PAGES_PATH', ABSPATH.'pages/user_pages/');
define('ASSETS_PATH', ABSPATH.'assets/');
define('TMP_PATH','/tmp/');

//you have to make your own telegram bot for this feature
//and dont forget to change the token you got in the 'send_message.py'
define('TELEGRAM_SENDSCRIPT_PATH', ABSPATH);


//files locations attached to works,assets,spares,...
define('INFO_LOC', URL.'info_files/');

define('INFO_PATH', ABSPATH.'info_files/');
define('LAST_BACKUP', ABSPATH.'last_backup');
define('INFO_THUMB_LOC', INFO_LOC.'thumbs/');
define('INFO_THUMB_PATH', INFO_PATH.'thumbs/');

define('ENTRY_ACCESS_CONTROL',1);
define('IOT_SUPPORT',1);
define('S7_SUPPORT',0);
define('TAM_TAI',1); //technical avaibility machine, technical avaibility infrastucture, used in report generation in work_stat_by_assets.php 
define('OPERATOR_NOTIFICATIONS_SUPPORT',1);
define('PINBOARD',1);
define('MESSAGE_TO_NOTIFIERS',ABSPATH.'msg_to_notifiers.txt');
$req_classes=array("Imagick");
$req_extensions=array("mysqli","gettext","gd");
//for sql auth:
define('DATABASE','your_database_name');
define('HOST','your_database_host');
define('USERNAME','your_database_username');
define('PASSWORD','your_database_password');

