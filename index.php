<?php


if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

//session_set_cookie_params(360000,"/");

$lifetime=360000;
  session_start();
  setcookie(session_name(),session_id(),time()+$lifetime);
//session_start();
if (isset($_GET['ajax']) 
|| (isset($_GET['page']) && ($_GET['page']=='pdf_create' || $_GET['page']=='csv_create'
))

|| isset($_POST['received_message1']) || isset($_POST['access_query']) || isset($_POST['operating']))
$lm_did_header = true;
require( ABSPATH.'includes/lm-header.php' );


if(IOT_SUPPORT && isset($_POST['received_message1']) || isset($_POST['operating']))
    require( INCLUDES_PATH.'received_a_message.php' );
else if(ENTRY_ACCESS_CONTROL && isset($_POST['access_query']))
    require( INCLUDES_PATH.'access_query.php' );
else if (isset($_GET['ajax']))
    require( INCLUDES_PATH.'ajax_calls.php' );
else{
    require( PAGES_PATH.'lm-body.php' );
   if (isset($_GET['page']) && $_GET['page']!='csv_create')
    require( INCLUDES_PATH.'lm-footer.php' );

}
