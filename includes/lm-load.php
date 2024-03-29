<?php

/*
 * If lm-config.php and lm-settings.php exist in the Libremaint root load them. 
 *
 */
if ( file_exists( ABSPATH . 'config/lm-config.php') ) {
	require_once( ABSPATH . 'config/lm-config.php' );

} else{
lm_die("There is no lm-config.php in the ".ABSPATH.'/config/');

}
if ( file_exists( ABSPATH . 'config/lm-settings.php') ) {
	require_once( ABSPATH . 'config/lm-settings.php' );
} else{
lm_die("There is no lm-settings.php in the ".ABSPATH.'/config/');

}

	if ( LM_DEBUG ) {
		error_reporting( E_ALL );

		if ( LM_DEBUG_LOG ) {
			ini_set( 'log_errors', 1 );
			ini_set( 'error_log', ABSPATH. 'lm-debug.log' );
		}
	} else {
		error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	}
if (!isset($_SESSION["deps_has_checked"]))
    {// we are checking the dependencies 
    $deps="";
    if (version_compare(PHP_VERSION, REQ_MIN_PHP_VERSION) <= 0)
   $deps.="PHP version must be ".REQ_MIN_PHP_VERSION." or above! Now it is:".PHP_VERSION."<br/>";
       $i=0;
      foreach ($req_extensions as $req_extension){
     if(!extension_loaded($req_extension)){

      if ($i>0)
         $deps.=", ";
      $i++;
      $deps.="\"".$req_extension."\"";
     }}
     if ($i==1)
     $deps.=" php extension is missing. Please install it!"."<br/>";
     if ($i>1)   
     $deps.=" php extensions are missing. Please install them!"."<br/>";
     
     
     $i=0;
     foreach ($req_classes as $req_class){
     if(!class_exists($req_class) ){

      if ($i>0)
         $deps.=", ";
      $i++;
      $deps.="\"".$req_class."\"";
     }}
     if ($i==1)
     $deps.=" php class is missing"."<br/>";
     if ($i>1)   
     $deps.=" php classes are missing"."<br/>";
     
     
        if (!file_exists(TMP_PATH)) {
        mkdir(TMP_PATH, 0777, true);
        }
     if (!file_exists(INFO_PATH)) {
        mkdir(INFO_PATH, 0777, true);
        }
      if (!file_exists(INFO_THUMB_PATH)) {
        mkdir(INFO_THUMB_PATH, 0777, true);
       
        }  
 if ($deps=="")
 $_SESSION["deps_has_checked"]=1;
 else
 die($deps);
}
require(INCLUDES_PATH.SQL_DB."_db_class.php"); 

$dba = new DB();

$dba->set_db_settings(DATABASE, USERNAME, PASSWORD, HOST);
if (!$dba->connect())
lm_die("Database error. Does that exist?".$dba->err_msg);
$sql="SET names utf8";
$dba->Query($sql);


/*
$last="";
if (isset($priviliges)){
foreach ($priviliges as $p){
if ($priviliges!='break')
$_SESSION[$p]=1;
//
//
//

if ($p!="break" && 1==1){
$SQL="SHOW COLUMNS FROM `users` LIKE '".$p."'";
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($dba->affectedRows()==0){
$SQL="ALTER TABLE `libremaint`.`users` 
ADD COLUMN `".$p."` BIT(1) NOT NULL default 0";
if ($last=="")
$SQL.=" AFTER ".$last;
$dba->Query($SQL);
if (LM_DEBUG)
error_log($SQL,0);
}
//$SQL="UPDATE users SET `".$p."`=1";
//$res=$dba->Query($SQL);
}
//
//
$last=$p;
}

}

*/
