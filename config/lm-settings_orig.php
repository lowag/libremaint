<?php
//$valid_languages=array(gettext('English')=>'en',gettext('Hungarian')=>'hu');
$valid_languages=array(gettext('English')=>'en');
$lang = 'en';//default language
//this allows building main database as bilingual (assets,locations,products)
define('LANG2_AS_SECOND_LANG',0);
define('LANG1','en');
//define('LANG2','hu');

function valid($locale) {
global $valid_languages;
    return in_array($locale,$valid_languages);
}


if (isset($_GET['lang']) && valid($_GET['lang'])) {
    // the locale can be changed through the query-string
    $lang = $_GET['lang'];    //you should sanitize this!
    setcookie('lm_lang', $lang); //it's stored in a cookie so it can be reused
} else if (isset($_COOKIE['lm_lang']) && valid($_COOKIE['lm_lang'])) {
    // if the cookie is present instead, let's just keep it
    $lang = $_COOKIE['lm_lang']; //you should sanitize this!
} else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    // default: look for the languages the browser says the user accepts
    $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    array_walk($langs, function (&$lang) { $lang = strtr(strtok($lang, ';'), ['-' => '_']); });
    foreach ($langs as $browser_lang) {
        if (valid($browser_lang)) {
            $lang = $browser_lang;
            break;
        }
    }
}
 //setlocale(LC_ALL, 'hu_HU.utf8');
 putenv("LC_ALL={$lang}");
if ($lang=="hu"){
 setlocale(LC_MESSAGES, 'hu_HU.utf8');
$lang_date_format = 'Y-m-d';
 }
 else{
 $lang_date_format = 'd-m-Y';
setlocale(LC_MESSAGES, 'en_EN.utf8');
}
//setlocale(LC_ALL, 'hu_HU.UTF-8');
bindtextdomain('lm-main', ABSPATH.'/locales');
textdomain('lm-main');

bind_textdomain_codeset('lm-main', 'UTF-8');

if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)=='hu')
$lang_date_format_for_input = 'Y-m-d';
else
$lang_date_format_for_input = 'd-m-Y';



define( 'LOCALE','en');
if (LOCALE=='hu' || LOCALE=="jp")
define('FIRSTNAME_IS_FIRST',false);
else
define('FIRSTNAME_IS_FIRST',true);

define('LM_DEBUG',true);
define('LM_DEBUG_LOG',true);
define('ROWS_PER_PAGE',10);
define('MAX_INFO_FILE_SIZE',20000000);
define('DAYS_ALLOW_TO_MODIFY_WORKS',14);
define('WHAT_IS_IMPORTANT_NOW',1);
 

define('ACCEPTED_FILE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'text/plain',
    'application/pdf',
    'application/vnd.oasis.opendocument.text',
      'ods'
   ]);
   
   
 //if (!$_SESSION['logged'] || isset($_GET['param3']) && $_GET['param3']=='users'|| (isset($_POST['page']) && $_POST['page']=='users')){// we need this only if we haven't logged or want to see priviliges
 //if you want add more variable you should see lm-load.php
 $priviliges=array(
 "ADD_WORKORDER","SEE_WORKORDERS","SEE_WORKORDER_DETAIL","MODIFY_WORKORDER","DELETE_WORKORDER","ADD_FILE_TO_WORKORDER","SEE_FILE_OF_WORKORDER","SEE_CONF_FILE_OF_WORKORDER","DELETE_FILE_OF_WORKORDER","SEE_STATS_OF_WORKORDERS","ADD_PRODUCT_WORKORDER",
 "break",
 "ADD_WORK","SEE_WORKS","SEE_WORK_DETAIL","MODIFY_WORK","DELETE_WORK","ADD_FILE_TO_WORK","SEE_FILE_OF_WORK","SEE_CONF_FILE_OF_WORK","DELETE_FILE_OF_WORK","SEE_STATS_OF_WORKS",
 "break",
 "SEE_OPERATORS_WORKS","RECORD_OPERATOR_WORK","MODIFY_OPERATOR_WORK","DELETE_OPERATOR_WORK",
 "break",
 "ADD_WORKREQUEST","SEE_WORKREQUESTS","SEE_WORKREQUEST_DETAIL","MODIFY_WORKREQUEST","DELETE_WORKREQUEST","ADD_FILE_TO_WORKREQUEST","SEE_FILE_OF_WORKREQUEST","SEE_CONF_FILE_OF_WORKREQUEST","DELETE_FILE_OF_WORKREQUEST","SEE_STATS_OF_WORKREQUESTS",
 "break",
 "ADD_NOTIFICATION","MODIFY_NOTIFICATION","SEE_NOTIFICATIONS","SEE_NOTIFICATION_DETAILS","SEE_STATS_OF_NOTIFICATIONS","DELETE_NOTIFICATION",
 "break",
 "ADD_TO_PINBOARD","MODIFY_PIN","SEE_PINBOARD","UPLOAD_TO_PINBOARD","DELETE_FROM_PINBOARD","ADD_FILE_TO_PIN","SEE_FILE_OF_PIN",
 "break",
 "ADD_ASSET","SEE_ASSETS","SEE_ASSET_DETAIL","MODIFY_ASSET","DELETE_ASSET","ADD_FILE_TO_ASSET","SEE_FILE_OF_ASSET","SEE_CONF_FILE_OF_ASSET","DELETE_FILE_OF_ASSET","ADD_CONNECTION_TO_ASSET","SEE_CONNECTION_OF_ASSET",
 "break",
"ADD_LOCATION","SEE_LOCATIONS","SEE_LOCATION_DETAIL","MODIFY_LOCATION","DELETE_LOCATION","ADD_FILE_TO_LOCATION","SEE_FILE_OF_LOCATION","SEE_CONF_FILE_OF_LOCATION","DELETE_FILE_OF_LOCATION",
"break",
"ADD_USER","SEE_USERS","SEE_USER_DETAIL","MODIFY_USER","DELETE_USER","ADD_FILE_TO_USER","SEE_FILE_OF_USER","SEE_CONF_FILE_OF_USER","DELETE_FILE_OF_USER","SEE_STATS_OF_USERS",
"break",
"PUT_PRODUCT_INTO_STOCK","TAKE_PRODUCT_FROM_STOCK","DELETE_PRODUCT","SEE_STOCK","STOCK-TAKING","SEE_PRODUCT_MOVING","SEE_FILE_OF_PRODUCT_MOVING","ADD_FILE_TO_PRODUCT_MOVING",
"break",
"ADD_CATEGORY","SEE_CATEGORY","MODIFY_CATEGORY","DELETE_CATEGORY",
"break",
"ADD_PRODUCT","SEE_PRODUCTS","SEE_PRODUCT_DETAIL","MODIFY_PRODUCT","DELETE_PRODUCT","ADD_FILE_TO_PRODUCT","SEE_FILE_OF_PRODUCT","SEE_CONF_FILE_OF_PRODUCT","DELETE_FILE_OF_PRODUCT","SEE_PRICES","ADD_CONNECTION_TO_PRODUCT","SEE_CONNECTION_OF_PRODUCT",
"break",
"ADD_PARTNER","SEE_PARTNERS","SEE_PARTNER_DETAIL","MODIFY_PARTNER","DELETE_PARTNER","SEE_FILE_OF_PARTNER","SEE_CONF_FILE_OF_PARTNER","DELETE_FILE_OF_PARTNER",
"break",
"ADD_COUNTER","SEE_COUNTER","ADD_COUNTER_VALUE","MODIFY_COUNTER_VALUE","DELETE_COUNTER","DELETE_COUNTER_VALUE",
 "break",
 "WRITE_MESSAGE","SEE_MESSAGE","ADD_MESSAGE_TEXT","ADD_FILE_TO_MESSAGE",
 "break",
 "ADD_NEW_CONNECTION_TYPE","SEE_CONNECTION_TYPE",
 "break",
 "CAN_WRITE_LANG1","CAN_WRITE_LANG2"
  );  
 $asset_importance=array(gettext("Critic"),gettext("High"),gettext("Medium"),gettext("Low"));
 //}
//echo count($priviliges);

/**define('ADD_WORKORDER',3);   
define('ADD_WORKREQUEST',4);    
define('ADD_FILE',3);
define('CREATE_ASSET',2); 
*/
$pages = array
  (
  array(
  gettext('Workrequests'),'workrequests','SEE_WORKREQUESTS',gettext('RE')),
  
  array(
    gettext("Workorders"),"workorders","SEE_WORKORDERS",gettext('OR'),
    gettext("Works"),"index.php?page=works","SEE_WORKS",gettext('WR'),
    gettext("Queries"),"index.php?page=work_stats","SEE_STATS_OF_WORKS",gettext('Q')),
  
  array(gettext("Notifications"),"notifications","SEE_NOTIFICATIONS",gettext('NOT'),
    gettext("New notification"),"index.php?new=1&page=notifications","ADD_NOTIFICATION",gettext('NN')),
    
  array(gettext("Operator works"),"operators_works","SEE_OPERATORS_WORKS",gettext('OW'),
    gettext("Record new work"),"index.php?new=1&page=operators_works","RECORD_OPERATOR_WORK",gettext('RW')),  
    
  array(gettext("assets"),"assets","SEE_ASSETS",gettext('AS'),
    gettext("Create new asset"),"index.php?new=1&page=assets","ADD_ASSET",gettext('NA')),
    
  array(gettext("Locations"),"locations","SEE_LOCATIONS",gettext('LO'),
    gettext("Create new location"),"index.php?new=1&page=locations","ADD_LOCATION",gettext('NL')),
    
  
  array(
  gettext("Users"),"users","SEE_USERS",gettext('US'),
    gettext("Create new user"),"index.php?new=1&page=users","ADD_USER",gettext('NU')
    ),
    
  array(
  gettext("Stock"),"stock","SEE_STOCK",gettext('ST'),
    gettext("Put product into stock"),"index.php?into_stock=1&page=stock","PUT_PRODUCT_INTO_STOCK",gettext('PP'),
    gettext("Stock movements"),"index.php?page=stock_movements","SEE_PRODUCT_MOVING",gettext('SM')),
    
  array(
  gettext("Categories"),"categories","SEE_CATEGORY",gettext('CA'),
    gettext("Create new category"),"index.php?new=1&page=categories","ADD_CATEGORY",gettext('NC')
   ),

    array(
  gettext("Products"),"products","SEE_PRODUCTS",gettext('PR'),
    gettext("Create new product"),"index.php?new=1&page=products","ADD_PRODUCT",gettext('NP')),
    
      array(
  gettext("Partners"),"partners","SEE_PARTNERS",gettext('PA'),
    gettext("Create new partner"),"index.php?new=1&page=partners","ADD_PARTNER",gettext('NPa')),

    array(
  gettext("Counters"),"counters","SEE_COUNTER",gettext('CN'),
  gettext("Add new counter value"),"index.php?new_value=1&page=counters","ADD_COUNTER_VALUE",gettext('CV')),
    
   array(
  gettext("Messages"),"messages","SEE_MESSAGE",gettext('Msg'),
  gettext("New message text"),"index.php?new=1&page=messages","ADD_MESSAGE_TEXT",gettext('msgt')),
  
  array(
  gettext("Connections"),"connections","SEE_CONNECTION_TYPE",gettext('CO'),
  gettext("New connection type"),"index.php?new=1&page=connection_types","ADD_NEW_CONNECTION_TYPE",gettext('NCt'),
  gettext("New connection"),"index.php?new=1&page=connections","ADD_NEW_CONNECTION_TYPE",gettext('NCo')
  ));
  
  $activity_types=array(gettext("maintenance"),gettext("fix"),gettext("development"),gettext("production support"),gettext("maintenance group"));
  $priority_types=array(gettext("Urgent"),gettext("Medium"),gettext("Schedulable"));
  $workorder_statuses=array(gettext("Ongoing"),gettext("Suspended (lack of time)"),gettext("Waiting for material"),gettext("Waiting for decision"),gettext("Ready"),gettext("Deleted"));//the indexes are used on some places 
  
  $workrequest_statuses=array(gettext("All"),gettext("Active"),gettext("Ongoing"),gettext("Finished"),gettext("Deleted"));
  
  $notification_types=array(gettext("maintenance"),gettext("fix"),gettext("development"),gettext("safety"),gettext("material demand"));
  $notification_statuses=array(gettext("New"),gettext("Confirmed"),gettext("Work in progress"),gettext("Resolved"),gettext("Closed"),gettext("Deleted"));
  $pin_types=array(gettext("info"),gettext("looking for"),gettext("offers"));
  $pin_statuses=array(gettext("New"),gettext("Expired"),gettext("Closed"),gettext("Deleted"));

  $connection_types=array(gettext("Male"),gettext("Female"),gettext("Same"));
define('TITLES', [
    gettext('Mr.'),
    gettext('Mrs.'),
    gettext('Ms.')
   ]);  
define( 'IMG_SIZE',1500);  //after image uploading we resize it
define( 'THUMB_IMG_SIZE',80); 
$shift_change_times=array("06:00","14:00","22:00"); //it is used in includes/operating_by_shifts.php  
define('IOT_INTERVALS_ASSET_IDS',[]);
$holidays=array();//for includes/get_working_days_function.php
