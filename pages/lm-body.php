<?php
$valid_page=false; //see lm-footer.php for make the proper menu item toggled

if ((isset($_POST['username']) && isset($_POST['password']) && lm_auth($_POST['username'],$_POST['password'])) || (isset($_SESSION['logged']) && $_SESSION['logged']==1 && !isset($_GET['logout']))){

        if (lm_isset_str('page')!='pdf_create')
            {
            require_once( INCLUDES_PATH . 'left_panel.php' );
            require_once( INCLUDES_PATH . 'right_panel.php' );
            }
            
        if (isset($req_page)) //$req_page from lm-header.php
        {
        if (S7_SUPPORT && file_exists(USER_PAGES_PATH.$req_page.'.php')) 
            {require_once(USER_PAGES_PATH.$req_page.'.php' );$valid_page=true;}
            else { 
        
        if (isset($req_page)) //$req_page from lm-header.php
        {
            
                    switch($req_page){
                    case "workorders": require_once( PAGES_PATH.'workorders.php' );
                    if (!isset($_SESSION['workorder_status']))
                    $_SESSION['workorder_status']=1;
                    $valid_page=true;
                    break;
                    case "workrequests": require_once( PAGES_PATH.'workrequests.php' );$valid_page=true;break;
                    case "users": require_once( PAGES_PATH.'users.php' );$valid_page=true;break;
                    case "assets": require_once( PAGES_PATH.'assets.php' );$valid_page=true;break;
                    case "locations": require_once( PAGES_PATH.'locations.php' );$valid_page=true;break;
                    case "categories": require_once( PAGES_PATH.'categories.php' );$valid_page=true;break;
                    case "products": require_once( PAGES_PATH.'products.php' );$valid_page=true;break;
                    case "works": require_once( PAGES_PATH.'works.php' );$valid_page=true;break;
                    case "operators_works": require_once( PAGES_PATH.'operators_works.php' );$valid_page=true;break;
                    case "work_stats": require_once( PAGES_PATH.'work_stats.php' );$valid_page=true;break;
                    case "stock": require_once( PAGES_PATH.'stock.php' );$valid_page=true;break;
                    case "partners": require_once( PAGES_PATH.'partners.php' );$valid_page=true;break;
                    case "counters": require_once( PAGES_PATH.'counters.php' );$valid_page=true;break;
                    case "settings": require_once( PAGES_PATH.'settings.php' );$valid_page=true;break;
                    case "pdf_create": require_once( PAGES_PATH.'pdf_create.php' );$valid_page=true;break;
                    case "messages": require_once( PAGES_PATH.'messages.php' );$valid_page=true;break;
                    case "iot_intervals": require_once( PAGES_PATH.'iot_intervals.php' );$valid_page=true;break;
                    case "stock_movements":require_once( PAGES_PATH.'stock_movements.php' );$valid_page=true;break;
                    case "connections":require_once( PAGES_PATH.'connections.php' );$valid_page=true;break;
                    case "connection_types":require_once( PAGES_PATH.'connection_types.php' );$valid_page=true;break;
                    case "notifications":require_once( PAGES_PATH.'notifications.php' );$valid_page=true;break;
                    case "pinboard":require_once( PAGES_PATH.'pinboard.php' );$valid_page=true;break;
                    default: require_once( PAGES_PATH.'dashboard.php' );
                }
        }
            
        }

}else
require_once( PAGES_PATH.'dashboard.php' );

if (isset($_GET['logout'])){
require_once( PAGES_PATH.'bye.php' );
require_once( ABSPATH . 'pages/lm-login.php' );

}}
else
require_once( ABSPATH . 'pages/lm-login.php' );

