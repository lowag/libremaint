<?php


function is_it_valid_submit():bool{
if (lm_isset_str('valid')==$_SESSION['tit_id'])
    {
    $_SESSION['tit_id']=get_random_string(10);
    return true;
    }
else
    return false;
}

function lm_auth($username,$password):bool{
global $dba, $priviliges;
$SQL="select * FROM users WHERE username='".$username."'";
if (LM_DEBUG)
error_log($SQL,0);
$row=$dba->getRow($SQL);


if (password_verify($password,$row['password'])){
clearstatcache(); 
$_SESSION['logged']=1;
$_SESSION['user_id']=$row['user_id'];
$_SESSION['username']=$row['username'];
$_SESSION['user_level']=$row['user_level'];
$_SESSION['tit_id']=get_random_string(10);
$i=0;
$j=0;
foreach ($priviliges as $p){
    if ($p!="break" && ($row[$p]==1)){
    $_SESSION[$p]=1;
    $i++;
    }
$j++; 
}

    if ($_SESSION['logged']==1){ //check whether session is working 
        time_to_make_maintenance();
        return true;
        
        }
    else
        return false;
  
}
else{
lm_logout();
sleep(2);
return false;
}
}



function lm_logout():bool{
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
return true;}

function lm_die($m):void{
die($m);
}


function lm_info($m):void{
//echo "<div class=\"content mt-3\">\n";
//echo "<div class=\"col-sm-12\">\n";
echo "<div class=\"alert  alert-success alert-dismissible fade show\" role=\"alert\">\n";
echo "<span class=\"badge badge-pill badge-success\">".gettext("Success")."</span> ".$m."\n";
echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">";
echo "<span aria-hidden=\"true\">&times;</span>\n";
echo "</button>\n";
echo "</div>\n";

}

function lm_error($m):void{
//echo "<div class=\"content mt-3\">\n";
//echo "<div class=\"col-sm-12\">\n";
echo "<div class=\"alert  alert-success alert-dismissible fade show\" role=\"alert\">\n";
echo "<span class=\"badge badge-pill badge-danger\">".gettext("Error")."</span> ".$m."\n";
echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">";
echo "<span aria-hidden=\"true\">&times;</span>\n";
echo "</button>\n";
echo "</div>\n";

}


function lm_isset_int($ind) :int{
if (isset($_GET[$ind]) &&  $_GET[$ind]>0 )
return $_GET[$ind];
else if (isset($_POST[$ind]) &&  $_POST[$ind]>0)
return $_POST[$ind];
else
return 0;
}


function lm_isset_str($str) :string{
if (isset($_GET[$str]) &&  $_GET[$str]!="")
return $_GET[$str];
else if (isset($_POST[$str]) &&  $_POST[$str]!="")
return $_POST[$str];
else
return "";
}



function get_connection_name_from_id($connection_id):string{
global $dba,$lang;
$SQL="SELECT connection_name_".$lang." FROM connections WHERE connection_id='".$connection_id."'";
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($dba->affectedRows()==1)
return $row['connection_name_'.$lang];
else return gettext("Error").__FILE__." ".__LINE__;
}

function get_connection_type_from_id($type):string{
if (1==$type)
return gettext("male");
else if (2==$type)
return gettext("female");
else if (3==$type)
return gettext("same");
else
return gettext("something went wrong").__FILE__." at ".__LINE__;
}

function get_asset_name_from_id($id,$lang):string{
global $dba;
//$lang='hu';
if ($id>0 && valid($lang)){
$SQL="SELECT asset_name_".$lang." FROM assets WHERE asset_id='".(int) $id."'";
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if (!empty($row["asset_name_".$lang]))
return $row["asset_name_".$lang];
else
return gettext("no data, translation missing?");
}else
lm_die("get_asset_name_from_id: invalid id:".$id." or lang:".$lang);                    

}   

function get_location_name_from_id($id,$lang):string{
global $dba;

if ($id>0 && valid($lang)){
$SQL="SELECT location_name_".$lang." FROM locations WHERE location_id=".(int) $id;
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($row["location_name_".$lang]!="")
return $row["location_name_".$lang];
else
return gettext("no data, translation missing?");
}else
lm_die("get_location_name_from_id: invalid id:".$id." or lang:".$lang);                    

}

function get_category_name_from_id($id,$lang):string{
global $dba;
if ($id>0 && valid($lang)){
$SQL="SELECT category_name_".$lang." FROM categories WHERE category_id=".(int) $id;
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($row["category_name_".$lang]!="")
return $row["category_name_".$lang];
else
return gettext("no data");
}else 
lm_die("get_category_name_from_id: invalid id:".$id." or lang:".$lang);                    

}


function get_category_id_from_id($id):int{
global $dba;
if ($id>0 ){
$SQL="SELECT category_id FROM products WHERE product_id=".(int) $id;
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($row["category_id"]>0)
return $row["category_id"];
else
return gettext("no data");
}else 
lm_die("get_category_id_from_id: invalid id:".$id." ".$SQL);                    

}

function get_subcategory_id_from_id($id):int{
global $dba;
if ($id>0 ){
$SQL="SELECT subcategory_id FROM products WHERE product_id=".(int) $id;
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($row["subcategory_id"]>0)
return $row["subcategory_id"];
else
return 0;
}else 
lm_die("get_subcategory_id_from_id: invalid id:".$id);                    

}

function get_partner_name_from_id($id):string{
global $dba;
if ($id>0){
$SQL="SELECT partner_name FROM partners WHERE partner_id=".(int) $id;
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($row["partner_name"]!="")
return $row["partner_name"];
}else 
lm_die("get_partner_name_from_id: invalid id:".$id);                    

}

function get_products_id_can_connect($connection_id,$connection_type):array{
global $dba;
$products=array();
if (1==$connection_type)
$s_connection_type=2;
else if (2==$connection_type)
$s_connection_type=1;
else if(3==$connection_type)
$s_connection_type=3;
$SQL1="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='products' AND COLUMN_NAME LIKE 'connection_id%'";
$result1=$dba->Select($SQL1);
if (LM_DEBUG)
error_log($SQL1,0);

$SQL="SELECT product_id FROM products WHERE 1=0 ";
    foreach($result1 as $row1)
    {
    $SQL.="OR ".$row1['COLUMN_NAME']."='".$connection_id."' AND connection_type".substr($row1['COLUMN_NAME'],13)."='".$s_connection_type."' ";
    }
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);

if ($dba->affectedRows()>0)
    {   
        foreach ($result as $row)
        {
        $products[]=$row['product_id'];
        }
    }
return $products;

}

function get_assets_id_can_connect($connection_id,$connection_type):array{
global $dba;
$assets=array();
if (1==$connection_type)
$s_connection_type=2;
else if (2==$connection_type)
$s_connection_type=1;
else if(3==$connection_type)
$s_connection_type=3;
$SQL1="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='assets' AND COLUMN_NAME LIKE 'connection_id%'";
$result1=$dba->Select($SQL1);
if (LM_DEBUG)
error_log($SQL1,0);

$SQL="SELECT asset_id FROM assets WHERE 1=0 ";
    foreach($result1 as $row1)
    {
    $SQL.="OR ".$row1['COLUMN_NAME']."='".$connection_id."' AND connection_type".substr($row1['COLUMN_NAME'],13)."='".$s_connection_type."' ";
    }
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log("connections:".$SQL,0);

if ($dba->affectedRows()>0)
    {   
        foreach ($result as $row)
        {
        $assets[]=$row['asset_id'];
        }
    }
return $assets;

}

function get_product_name_from_id($id,$lang):string{
global $dba;
if ($id>0){
$SQL="Select product_id,category_name_".$lang." ,products.category_id,products.subcategory_id,product_type_".$lang.",product_properties_".$lang.",display,manufacturer_id FROM products LEFT JOIN categories ON products.category_id=categories.category_id";
$SQL.=" HAVING product_id=".$id;
$row=$dba->getRow($SQL);
$d=$row['display'];
$name="";
if (LM_DEBUG)
error_log($SQL,0);
if (!empty($row["product_type_".$lang])){
$name2="";
$name1="";
if ((($d >> 0) & 1) && $row['category_id']>0)
$name1=$row["category_name_".$lang];
if ((($d >> 1) & 1) && $row['subcategory_id']>0)
$name2=get_category_name_from_id($row['subcategory_id'],$lang);

if (($d >> 5) & 1)
$name=$name2." ".$name1;
else
$name=$name1." ".$name2;

if (($d >> 2) & 1)
$name.=" ".$row['product_type_'.$lang];
if (($d >> 3) & 1)
$name.=" ".get_manufacturer_name_from_id($row['manufacturer_id']);
if (($d >> 4) & 1)
$name.=" ".$row['product_properties_'.$lang];
return $name;

}else
return gettext("no data, missing translation?");

}else 
lm_die("get_product_name_from_id: invalid id:".$id);                    

}


/*
function get_min_stock_quantity_from_id($id):float{
global $dba;
$SQL="SELECT min_stock_quantity FROM products WHERE product_id='".$id."'";
$row=$dba->getRow($SQL);
if ($row['min_stock_quantity']>0)
return $row['min_stock_quantity'];
else
return 0;
}*/


function get_manufacturer_name_from_id($id):string{
global $dba,$lang;
if ($id>0){
$SQL="Select manufacturer_name FROM manufacturers WHERE manufacturer_id='".$id."'";
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($row["manufacturer_name"]!="")
return $row["manufacturer_name"];
}else 
return "";                    

}


function get_username_from_id($id):string{
global $dba;
if ($id>0){
$SQL="SELECT username FROM users WHERE user_id='".$id."'";
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($row["username"]!="")
return $row["username"];
}else 
lm_die("get_username_from_id: invalid id:".$id);                    
}

function get_user_full_name_from_id($user_id):string{
global $dba;
if ($user_id>0){
$SQL="SELECT firstname,surname FROM users WHERE user_id='".$user_id."'";
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
 if (FIRSTNAME_IS_FIRST)
    return $row['surname']." ".$row['firstname'];
    else
    return $row['surname']." ".$row['firstname'];

}else 
lm_die("get_user_full_name_from_id: invalid id:".$user_id);                    
}


function get_quantity_unit_from_product_id($product_id):array{
global $dba,$lang;
$resp=array();
if ($product_id>0){
    $SQL="SELECT quantity_unit FROM products WHERE product_id=".$product_id;
    $row=$dba->getRow($SQL);
    
    $SQL="SELECT unit_".$lang.",unit_datatype FROM units WHERE unit_id=".$row['quantity_unit'];
    $row=$dba->getRow($SQL);
    
    $resp[0]=$row['unit_'.$lang];
    $resp[1]=$row['unit_datatype'];
    return $resp;
    
if (LM_DEBUG)
error_log($SQL,0);
}else 
return "get_quantity_unit_from_prouduct_id:".$product_id;                    
}


function get_unit_from_id($id):string{
global $dba,$lang;
if ($id>0){
$SQL="SELECT unit_".$lang." FROM units WHERE unit_id='".$id."'";
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($row["unit_".$lang]!="")
return $row["unit_".$lang];
}else 
return "get_unit_from_id: invalid id:".$id;  

}


function get_whole_path($name,$id,$i):array{
global $dba,$lang;
static $resp;
//$name: asset,location
//$id: asset_id,location_id
if ($i==1)
$resp=array();

$SQL="SELECT ".$name."_name_".$lang.", ".$name."_parent_id, ".$name."_id FROM ".$name."s WHERE ".$name."_id=".$id;
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($dba->affectedRows()>0)
{
array_push($resp,$row[$name."_name_".$lang]);
//$resp=$row[$name."_name_".$lang];
if ($row[$name."_parent_id"]>0){
    get_whole_path($name,$row[$name."_parent_id"],0);
    }
else
array_push($resp,$row[$name."_id"]); //the array's first element will be the main asset_id
}

return array_reverse($resp);


}



function get_whole_path_ids($name,$id,$i):array{
global $dba;
static $res;
//all ids above the tree
//$name: asset,location
//$id: asset_id,location_id
if ($i==1)
$res=array();

$SQL="SELECT ".$name."_parent_id, ".$name."_id FROM ".$name."s WHERE ".$name."_id=".$id;
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($dba->affectedRows()>0)
{
array_push($res,$row[$name."_id"]);
//$resp=$row[$name."_name_".$lang];
if ($row[$name."_parent_id"]!=0){
    get_whole_path_ids($name,$row[$name."_parent_id"],0);
    }
}
return array_reverse($res);
}

function get_whole_path_ids_children($name,$id,$i):array{
global $dba;
static $res;
//all ids under the tree
//$name: asset,location
//$id: asset_id,location_id
if ($i==1)
$res=array();

$SQL="SELECT ".$name."_id FROM ".$name."s WHERE ".$name."_parent_id=".$id;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($dba->affectedRows()>0)
{
foreach ($result as $row){
array_push($res,$row[$name."_id"]);
//$resp=$row[$name."_name_".$lang];

    get_whole_path_ids_children($name,$row[$name."_id"],0);
    
}}
return array_reverse($res);
}

function get_whole_path_for_select($name,$id,$n):array{
global $dba,$lang;
static $i,$j,$res;
//$name: asset,location
//$id: asset_id,location_id
if ($n==1){
$res=array();
$i=0;
}
//if ($i==0)
//$SQL="SELECT ".$name."_parent_id, ".$name."_id,".$name."_name_".$lang." FROM ".$name."s WHERE ".$name."_id=".$id;
//else
$SQL="SELECT ".$name."_parent_id, ".$name."_id,".$name."_name_".$lang." FROM ".$name."s WHERE ".$name."_parent_id=".$id;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);
$i++;
$s="";
for ($j = 1; $j <$i; $j++) {
    $s.=" > ";
}
if ($dba->affectedRows()>0)
{
foreach($result as $row) 
{
array_push($res,array($row[$name."_id"],$s.$row[$name."_name_".$lang]));
//$resp=$row[$name."_name_".$lang];
if ($row[$name."_parent_id"]!=0){
    
    get_whole_path_for_select($name,$row[$name."_id"],0);
    }
 }
}
return $res;
}


function get_user_level_from_id($id):string{
global $dba,$lang;
$SQL="SELECT user_level_".$lang." FROM user_levels WHERE user_level_id=".$id;
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if ($dba->affectedRows()==1)
return $row["user_level_".$lang];
else
return gettext("Error encountered.");
}



function img_resize($newWidth, $targetFile, $originalFile):void {

    $info = getimagesize($originalFile);
    $mime = $info['mime'];
if (LM_DEBUG)
error_log("Filetype:".$mime,0);
    switch ($mime) {
            case 'image/jpeg':
                    $image_create_func = 'imagecreatefromjpeg';
                    $image_save_func = 'imagejpeg';
                    $new_image_ext = 'jpg';
                    break;

            case 'image/png':
                    $image_create_func = 'imagecreatefrompng';
                    $image_save_func = 'imagepng';
                    $new_image_ext = 'png';
                    break;

            case 'image/gif':
                    $image_create_func = 'imagecreatefromgif';
                    $image_save_func = 'imagegif';
                    $new_image_ext = 'gif';
                    break;

            default: 
                    throw new Exception('Unknown image type.');
    }

    $img = $image_create_func($originalFile);
    list($width, $height) = getimagesize($originalFile);

    $newHeight = ($height / $width) * $newWidth;
    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    if (file_exists($targetFile)) {
            unlink($targetFile);
    }
    //$image_save_func($tmp, "$targetFile.$new_image_ext");
    $image_save_func($tmp, "$targetFile");
}

function get_info_file_data($value):array{
global $dba;

$SQL="SELECT * FROM info_files WHERE info_file_id=".(int) $value;
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
return $row;
}

function get_service_interval_date($id):string{

switch ($id){
    case 0: 
    return gettext("No interval"); break;
    case 1: 
    return gettext("Daily"); break;
    case 7: 
    return gettext("Weekly"); break;
    case 14: 
    return gettext("Fortnigtly"); break;
    case 30: 
    return gettext("Monthly"); break;
    case 60: 
    return gettext("2 monthly"); break;
    case 90: 
    return gettext("3 monthly"); break;
    case 180: 
    return gettext("6 monthly"); break;
    case 365: 
    return gettext("Yearly"); break;
    default:
    return gettext("Error");
}
    


}

function get_employees_from_id($user_id):array{//get all users who can be ordered by user determined by $user_id
global $dba;
static $emp=array();
if (empty($emp))
    $emp[$user_id]=get_user_full_name_from_id($user_id);
$SQL="SELECT user_id, surname,firstname FROM users where user_parent_id='".$user_id."'";
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if (!empty($result)){
foreach ($result as $row){
   if (FIRSTNAME_IS_FIRST)
    $emp[$row['user_id']]=$row['surname']." ".$row['firstname'];
    else
    $emp[$row['user_id']]=$row['surname']." ".$row['firstname'];
    get_employees_from_id($row['user_id']);
}}
return $emp;
}

function get_locations_bellow_id($location_id):array{//get all locations under the location determined by $location_id
global $dba;
static $loc;
if (empty($loc))
    $loc[]=$location_id;
$SQL="SELECT location_id FROM locations where location_parent_id='".$location_id."'";
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if (!empty($result)){
foreach ($result as $row){
    $loc[]=$row['location_id'];
    get_locations_bellow_id($row['location_id']);
}
}
return $loc;
}

function get_task_from_id($where,$id):string{
global $dba,$lang;
if ($where!="workorder" && $where!="workrequest")
lm_die("wrong parameter in get_task_from_id at line ".__line__);
$SQL="SELECT ".$where."_short_".$lang.",".$where."_".$lang.",replace_to_product_id FROM ".$where."s WHERE ".$where."_id='".$id."'";
if (LM_DEBUG)
error_log("get_task_from_id".$SQL,0);

$row=$dba->getRow($SQL);
if (empty($row[$where]) && $row['replace_to_product_id']>0)
return gettext("replace");
else if (empty($row[$where.'_short_'.$lang]))
return gettext("missing translation??");
else
return $row[$where.'_short_'.$lang]."<p>".$row[$where.'_'.$lang]."</p>";
}


function time_to_make_maintenance():bool
{global $dba;
$SQL="SELECT service_interval_date,service_interval_hours,repetitive,service_interval_mileage,workrequest_id, counter_id FROM workrequests WHERE repetitive>0 AND workrequest_status=3";
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);
$now=new datetime('now');
if ($dba->affectedRows()>0){
foreach ($result as $workrequest_row){
$activate=false;
if ($workrequest_row['counter_id']>0)
{
$SQL="SELECT counter_value FROM counter_values WHERE counter_id=".$workrequest_row['counter_id']." ORDER BY counter_value_time DESC LIMIT 1";
$row=$dba->getRow($SQL);
$last_counter_value=$row['counter_value'];
}

$SQL="SELECT finish_time,counter_id,counter_value,workrequest_id FROM finished_workrequests WHERE workrequest_id=".$workrequest_row['workrequest_id']." ORDER BY finish_time DESC LIMIT 1";
$last_finished_workrequest=$dba->getRow($SQL);
if (LM_DEBUG)
error_log("\n\n".$SQL,0);
    if ($last_finished_workrequest['workrequest_id']>0){
    if (LM_DEBUG)
error_log("last_finish_time:".$last_finished_workrequest['finish_time']." service_interval_date:" .$workrequest_row['service_interval_date']."\n",0);
$next_time_activate = new DateTime($last_finished_workrequest['finish_time']); // Y-m-d
$next_time_activate->add(new DateInterval('P'.$workrequest_row['service_interval_date'].'D'));
    //$next_time_activate=new datetime($last_finished_workrequest['finish_time'] +"\"".$workrequest_row['service_interval_date']." days\"");
    
    if (LM_DEBUG)
error_log("next_time:".$next_time_activate->format('Y-m-d'),0);
    switch ($workrequest_row['repetitive']){
    case 1: //by date
    if ($now>=$next_time_activate)
    {
    $activate=true;
    if (LM_DEBUG)
    error_log("activate".$activate.", by date:".$next_time_activate->format('Y-m-d'),0);
    }
    break;
    
    case 2://by workhours
    if ($last_counter_value>$last_finished_workrequest['counter_value']+$workrequest_row['service_interval_hours']){
    $activate=true;
    if (LM_DEBUG)
    error_log("activate".$activate.", by workhours:".$next_time_activate->format('Y-m-d'),0);
    }
    break;
    
    case 3://by date or workhours
    if (($now>=$next_time_activate) || ($last_counter_value>$last_finished_workrequest['counter_value']+$workrequest_row['service_interval_hours'])){
    $activate=true;
    if (LM_DEBUG)
    error_log("by date or workhours:".$next_time_activate->format('Y-m-d'),0);
    }
    break;
    
    case 4://by mileage
    if ($last_counter_value>$last_finished_workrequest['counter_value']+$workrequest_row['service_interval_mileage']){
    $activate=true;
    if (LM_DEBUG)
    error_log("by mileage:".$next_time_activate->format('Y-m-d'),0);
    }
    break;
    
    case 5://by date or mileage
    if (($now>=$next_time_activate) || ($last_counter_value>$last_finished_workrequest['counter_value']+$workrequest_row['service_interval_mileage'])){
    $activate=true;
    if (LM_DEBUG)
    error_log("by date or by mileage:".$next_time_activate->format('Y-m-d'),0);
    }
    break;
     }
   
    if ($activate==true)
        {
        $SQL="UPDATE workrequests SET workrequest_status=1 WHERE workrequest_id=".$workrequest_row['workrequest_id'];
         if (LM_DEBUG)
    error_log($SQL."\n\n",0);
         if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);
        }
   
    
    }else // there haven't been this workrequest yet so the workorder_status should be 1
    {
    $SQL="UPDATE workrequests SET workrequest_status=1 WHERE workrequest_id=".$workrequest_row['workrequest_id'];
      if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);  
    
    }

}
}
return true;
}

function get_sum_quantity_from_product_id($product_id):int{
global $dba;
$SQL="SELECT SUM(stock_quantity) as sum FROM stock WHERE product_id='".(int) $product_id."' AND stock_location_id>0 GROUP BY product_id";
$row=$dba->getRow($SQL);
if (!empty($row) && $row['sum']>0)
return $row['sum'];
else
return 0;
}

function get_random_string($length):string{
$string_to_shuffle='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789_|';
return mb_substr(str_shuffle($string_to_shuffle),1,$length);
}

function check_workorder_to_close($workorder_id):void
{
global $dba,$lang;
$all_employees_have_finished=true;
if ($workorder_id>0 ){
            $SQL="SELECT * FROM workorders WHERE workorder_id='".$workorder_id."'";
            $workorder_row=$dba->getRow($SQL);
            if (LM_DEBUG)
        error_log(__FILE__."*************".$SQL,0);
            if ($workorder_row['workrequest_id']>0)
            {
            $SQL="SELECT * FROM workrequests WHERE workrequest_id=".$workorder_row['workrequest_id'];
            $workrequest_row=$dba->getRow($SQL);
            if (LM_DEBUG)
        error_log($SQL,0);
            }
                foreach ($workorder_row as $key=>$value)
                {
                    if ($all_employees_have_finished==true && strstr($key,"employee_id") && $value==1 )
                    {
                    $SQL="SELECT workorder_status,workorder_work_end_time from workorder_works WHERE workorder_works.deleted<>1 AND workorder_user_id=".(int) substr($key,11)." AND workorder_id=".$workorder_id." ORDER BY workorder_work_end_time DESC LIMIT 1";
                    $row1=$dba->getRow($SQL);
                    if (!isset($latest_activity_time) || $row1['workorder_work_end_time']>$latest_activity_time)
                    $latest_activity_time=$row1['workorder_work_end_time'];
                    if (LM_DEBUG){
        error_log($SQL,0);
        error_log("latest_activity_time:".$latest_activity_time);
        }
                    if (5!=$row1['workorder_status'])
                    $all_employees_have_finished=false;
                    }
                   if ($all_employees_have_finished==true && $key=='workorder_partner_id' && $value>0) { 
                    $SQL="SELECT workorder_status,workorder_work_end_time from workorder_works WHERE workorder_works.deleted<>1 AND workorder_partner_id='".$value."' AND workorder_id=".$workorder_id."  ORDER BY workorder_work_end_time DESC LIMIT 1";
                    $row1=$dba->getRow($SQL);
                    if (5!=$row1['workorder_status'])
                    $all_employees_have_finished=false;
                    if (!isset($latest_activity_time) || $row1['workorder_work_end_time']>$latest_activity_time)
                    $latest_activity_time=$row1['workorder_work_end_time'];
                    }
                }
if ($all_employees_have_finished==true)
    {
    $SQL="UPDATE workorders SET workorder_status=5 WHERE workorder_id='".$workorder_id."'";
    if ($dba->Query($SQL))
    lm_info(gettext("This workorder has signed as finished."));
    if ($workorder_row['workrequest_id']>0){
       // $SQL="UPDATE workrequests SET workrequest_status=3,last_ready_date='".date("Y-m-d",strtotime($time))."' WHERE workrequest_id=".$workorder_row['workrequest_id'];
    $SQL="UPDATE workrequests SET workrequest_status=3,last_ready_date='".$latest_activity_time."' WHERE workrequest_id=".$workorder_row['workrequest_id'];

       $result=$dba->Query($SQL);
        if (LM_DEBUG)
        error_log($SQL,0);
            if (isset($workrequest_row) && $workrequest_row['repetitive']>0){
                $SQL="INSERT INTO finished_workrequests (";
                if ($workrequest_row['counter_id']>0 && $_POST['counter_'.$workrequest_row['counter_id']]>0){
                $SQL.="counter_id,counter_value,";
                $S="INSERT INTO counter_values (counter_id,counter_value,counter_value_time) VALUES (".$workrequest_row['counter_id'].",".$_POST['counter_'.$workrequest_row['counter_id']].",'".$latest_activity_time."')";
                    if (!$dba->Query($S))
                    lm_die($dba->err_msg." ".$S);
                }
                $SQL.="finish_time,workrequest_id,workorder_id) VALUES (";
                
                if ($workrequest_row['counter_id']>0 && $_POST['counter_'.$workrequest_row['counter_id']]>0)
                $SQL.=$workrequest_row['counter_id'].",".$_POST['counter_'.$workrequest_row['counter_id']].",";
                
                $SQL.="'".$latest_activity_time."',".$workorder_row['workrequest_id'].",".$workorder_id.")";
                $dba->Query($SQL);
                 if (LM_DEBUG)
        error_log($SQL." ".$dba->err_msg,0);
                
            }
        }
         if ($workorder_row['notification_id']>0){
       // $SQL="UPDATE workrequests SET workrequest_status=3,last_ready_date='".date("Y-m-d",strtotime($time))."' WHERE workrequest_id=".$workorder_row['workrequest_id'];
    $SQL="UPDATE notifications SET notification_status=4 WHERE notification_id=".$workorder_row['notification_id'];
    $dba->Query($SQL);
}
   
        if ($workorder_row['replace_to_product_id']>0){
       /*
        1. make a backup of asset's product_id -> orig_asset_product_id at workorder table 
        2. replace the product_id at the "assets" table 
        3. change the product location from stock_location_id->0 to stock_location_asset_id->asset_id
        4. put the removed product into stock: stock_location_id->$stock_location_id to stock_location_asset_id->0
        5. stock_movements: put the product to the asset
        6.stock_movements: remove the product from the asset
        */
        $SQL="SELECT asset_product_id,asset_location FROM assets WHERE asset_id=".$workorder_row['asset_id'];
        $row11=$dba->getRow($SQL);
        if (LM_DEBUG)
        error_log($SQL,0); 
        //1
        $SQL="UPDATE workorders SET orig_asset_product_id=".$row11['asset_product_id']." WHERE workorder_id=".$workorder_id;
        $dba->Query($SQL);
        if (LM_DEBUG)
        error_log($SQL,0); 
        //2
        $SQL="UPDATE assets SET asset_product_id=".$workorder_row['replace_to_product_id']." WHERE asset_id=".$workorder_row['asset_id'];
        if (LM_DEBUG)
        error_log($SQL,0); 
        
        if ($dba->Query($SQL))
        lm_info(gettext("This part has been replaced."));
        
        
        $SQL="SELECT stock_id,stock_location_id,stock_place FROM stock WHERE product_id=".$workorder_row['replace_to_product_id'];
        $row1=$dba->getRow($SQL);
        $stock_location_id=$row1["stock_location_id"];
        if ($stock_location_id=="") //there is stock_location we put the removed item to the asset's location
        {
       
        $stock_location_id=$row11["asset_location"];
        }
        $stock_place=$row1['stock_place'];
        if (LM_DEBUG)
        error_log($SQL,0); 
        
        
         
        //3
        $SQL="UPDATE stock SET stock_location_asset_id=".$workorder_row['asset_id'].",stock_location_id=0 WHERE stock_id=".$row1['stock_id'];
        $dba->Query($SQL);
        if (LM_DEBUG)
        error_log($SQL,0); 
        
        //  4. put the removed product into stock: stock_location_id->$stock_location_id to stock_location_asset_id->0
        $SQL="SELECT stock_id FROM stock WHERE product_id=".$row11['asset_product_id'];
        $row1=$dba->getRow($SQL);
        if (!empty($row1['stock_id']))
        {
        $SQL="UPDATE stock SET stock_location_asset_id=0,stock_location_id=".$stock_location_id.",stock_place='".$stock_place."' WHERE stock_id=".$row1['stock_id'];
        $dba->Query($SQL);
        if (LM_DEBUG)
        error_log($SQL,0);
        }else //this product doesn't have a stock_id
        {
        $SQL="SELECT category_id,subcategory_id FROM products WHERE product_id=".$row11['asset_product_id'];
        $row1=$dba->getRow($SQL);
        
        $SQL="INSERT INTO stock (product_id,product_category_id,product_subcategory_id,stock_location_id,stock_place,stock_quantity,item_created) VALUES";
            $SQL.="(".$row11['asset_product_id'].",";
            $SQL.=$row1['category_id'].",";
            $SQL.=$row1['subcategory_id'].",";
            $SQL.=$stock_location_id.",";
            $SQL.="'".$stock_place."',";
            $SQL.="'1',";
            $SQL.="'".$latest_activity_time."')";
        $dba->Query($SQL);
        if (LM_DEBUG)
        error_log($SQL,0);
        }
        
        //5 stock_movements: put the product to the asset
        $SQL="SELECT workorder_id FROM stock_movements WHERE workorder_id=".$workorder_id;
        $dba->Select($SQL);
        if ($dba->affectedRows()>0){// if it was a finished work we need to update only
        $SQL="UPDATE stock_movements SET stock_movement_time='".$latest_activity_time."' WHERE workorder_id=".$workorder_id;
        $dba->Query($SQL);
        }else{
        
        $SQL="INSERT INTO stock_movements (product_id,stock_movement_quantity,to_stock_location_id,from_asset_id,workorder_id,stock_movement_time) VALUES (";
        $SQL.=$row11['asset_product_id'].",1,".$stock_location_id.",".$workorder_row['asset_id'].",".$workorder_id.",'".$latest_activity_time."')";
        $dba->Query($SQL);
        if (LM_DEBUG)
        error_log($SQL,0); 
        
        //6 remove the product from the asset
        $SQL="INSERT INTO stock_movements (product_id,stock_movement_quantity,from_stock_location_id,to_asset_id,workorder_id,stock_movement_time) VALUES (";
        $SQL.=$workorder_row['replace_to_product_id'].",1,".$stock_location_id.",".$workorder_row['asset_id'].",".$workorder_id.",'".$latest_activity_time."')";
        $dba->Query($SQL);
        if (LM_DEBUG)
        error_log($SQL,0);
        }
        $asset_tree_has_changed[]=get_whole_path('asset',$workorder_row['asset_id'],1)[0];
        $asset_id=$workorder_row['asset_id'];
        include(INCLUDES_PATH."asset_tree.php"); //rebuild asset_tree
        }//if ($workorder_row['replace_to_product_id']>0){
           
        if (LM_DEBUG)
        error_log("workorder_row['product_id_to_refurbish']: ".$workorder_row['product_id_to_refurbish']."\n workorder_partner_id:".$workorder_row['workorder_partner_id'],0);
        
        
        if ($workorder_row['product_id_to_refurbish']>0 &&  $workorder_row['workorder_partner_id']>0){
        $SQL="SELECT workorder_id FROM stock_movements WHERE to_stock_location_id>0 AND workorder_id=".$workorder_id;
        
        $dba->Select($SQL);
        if (LM_DEBUG)
        error_log($SQL,0);
        if ($dba->affectedRows()>0){// if it was a finished work we need to update only
        $SQL="UPDATE stock_movements SET stock_movement_time='".$latest_activity_time."' WHERE workorder_id=".$workorder_id;
        $dba->Query($SQL);
        if (LM_DEBUG)
        error_log($SQL,0);
        $SQL="UPDATE stock SET stock_location_partner_id=0,stock_location_id=".(int) $workorder_row['orig_stock_location_id']." WHERE product_id=".$workorder_row['product_id_to_refurbish'];
        $dba->Query($SQL);
                    if (LM_DEBUG)
                        error_log($SQL,0);
        }else{
        $SQL="INSERT INTO stock_movements (product_id,stock_movement_quantity,to_stock_location_id,from_partner_id,workorder_id) VALUES (";
                    $SQL.=$workorder_row['product_id_to_refurbish'].",1,".$workorder_row['orig_stock_location_id'].",".(int) $workorder_row['workorder_partner_id'].",".$workorder_id.")";
                    if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);
                    if (LM_DEBUG)
                        error_log($SQL,0);
                    
                    $SQL="UPDATE stock SET stock_location_partner_id=0,stock_location_id=".(int) $workorder_row['orig_stock_location_id']." WHERE product_id=".$workorder_row['product_id_to_refurbish'];
                    if (LM_DEBUG)
                        error_log($SQL,0);
                    if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);
                    
        }
       
        }
    }
else if ($workorder_row['workorder_status']==5) // it was finished, but now it isn't

    {
    $SQL="UPDATE workorders SET workorder_status=1 WHERE workorder_id='".$workorder_id."'";
    if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);
                    
    
    
    if ($workorder_row['workrequest_id']>0){
    $SQL="UPDATE workrequests SET workrequest_status=2 WHERE workrequest_id=".$workorder_row['workrequest_id'];
    if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);
    
    $SQL="DELETE FROM finished_workrequests WHERE workorder_id=".$workorder_row['workrequest_id'];
    if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);
    }
    
    
    if ($workorder_row['replace_to_product_id']>0){ // if there was a part change we have to back to the orig
        $SQL="UPDATE assets SET asset_product_id=".$workorder_row['orig_asset_product_id']." WHERE asset_id=".$workorder_row['asset_id'];
        $dba->Query($SQL);
        include(INCLUDES_PATH."asset_tree.php");
         }
     if ($workorder_row['product_id_to_refurbish']>0){
     
     $SQL="INSERT INTO stock_movements (product_id,stock_movement_quantity,to_stock_location_id,to_partner_id,workorder_id) VALUES (";
                    $SQL.=$workorder_row['product_id_to_refurbish'].",1,".$workorder_row['orig_stock_location_id'].",".(int) $workorder_row['workorder_partner_id'].",".$workorder_id.")";
                    if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);
                    if (LM_DEBUG)
                        error_log($SQL,0);
                    
                    $SQL="UPDATE stock SET stock_location_id=0,stock_location_partner_id=".(int) $workorder_row['workorder_partner_id']." WHERE product_id=".$workorder_row['product_id_to_refurbish'];
                    if (LM_DEBUG)
                        error_log($SQL,0);
                    if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);
                    
     
     } 
     if ($workorder_row['workrequest_id']>0)
     update_last_ready($workorder_row['workrequest_id']);
    }
     
    
    
}else
lm_die("Wrong value at check_workorder_to_close()...");
    
    }


function copy_asset_with_children($source_asset_id,$target_asset_parent_id):void
{
global $dba;
static $t="_copied";
$SQL="SELECT COLUMN_NAME as info FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='assets' AND COLUMN_NAME LIKE 'info_file_id%'";
$info_file_ids=$dba->Select($SQL);

$SQL="SELECT COLUMN_NAME as connection_id FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='assets' AND COLUMN_NAME LIKE 'connection_id%'";
$connection_ids=$dba->Select($SQL);

$SQL="SELECT COLUMN_NAME as connection_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='assets' AND COLUMN_NAME LIKE 'connection_type%'";
$connection_types=$dba->Select($SQL);



$SQL="SELECT asset_category_id";
if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.=",asset_name_".LANG1;

if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
$SQL.=",asset_name_".LANG2;

$SQL.=",grouped_asset_id,grouped_asset,main_part,asset_parent_id,asset_location,asset_article";
$SQL.=",asset_subcategory_id,asset_product_id,asset_note,asset_note_conf";
    if (!empty($info_file_ids))
    {
        foreach ($info_file_ids as $info_column)
        $SQL.=",".$info_column['info'];
    }
    
     if (!empty($connection_ids))
    {
        foreach ($connection_ids as $connid_column)
        $SQL.=",".$connid_column['connection_id'];
    }
    
    if (!empty($connection_types))
    {
        foreach ($connection_types as $conntype_column)
        $SQL.=",".$conntype_column['connection_type'];
    }
    
$SQL.=" FROM assets WHERE asset_id='".(int) $source_asset_id."'";
$source_row=$dba->getRow($SQL);

$SQL="INSERT INTO assets (";
if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.="asset_name_".LANG1.",";

if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
$SQL.="asset_name_".LANG2.",";

$SQL.="grouped_asset_id,grouped_asset,main_part,asset_parent_id,asset_location,asset_article";
$SQL.=",asset_category_id,asset_subcategory_id,asset_product_id,asset_note,asset_note_conf";
if (!empty($info_file_ids))
    {
        foreach ($info_file_ids as $info_column)
        $SQL.=",".$info_column['info'];
    }
    
     if (!empty($connection_ids))
    {
        foreach ($connection_ids as $connid_column)
        $SQL.=",".$connid_column['connection_id'];
    }
    
    if (!empty($connection_types))
    {
        foreach ($connection_types as $conntype_column)
        $SQL.=",".$conntype_column['connection_type'];
    }
    
$SQL.=") VALUES (";
if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.="'".$source_row['asset_name_'.LANG1].$t."',";

if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
$SQL.="'".$source_row['asset_name_'.LANG2].$t."',";

$SQL.="'".(int)$source_row['grouped_asset_id']."','".(int) $source_row['grouped_asset']."','".(int)$source_row['main_part']."','".(int)$target_asset_parent_id."','".(int)$source_row['asset_location']."','".$source_row['asset_article']."','";
$SQL.= (int)$source_row['asset_category_id']."','".(int)$source_row['asset_subcategory_id']."','".(int) $source_row['asset_product_id']."','".$source_row['asset_note']."','".$source_row['asset_note_conf']."'";
if (!empty($info_file_ids))
    {
        foreach ($info_file_ids as $info_column)
        $SQL.=",'". (int) $source_row[$info_column['info']]."'";
    }
    
 if (!empty($connection_ids))
    {
        foreach ($connection_ids as $connid_column)
        $SQL.=",'".(int) $source_row[$connid_column['connection_id']]."'";
    }
    
    if (!empty($connection_types))
    {
        foreach ($connection_types as $conntype_column)
    $SQL.=",'".(int) $source_row[$conntype_column['connection_type']]."'";
    }      
$SQL.=")";    
    if (LM_DEBUG)
        error_log($dba->err_msg." ".$SQL,0);
if ($dba->Query($SQL))
echo "";
else
echo $dba->err_msg." ".$SQL;
$next_target_parent_id=$dba->insertedId();
$SQL="SELECT asset_id FROM assets WHERE asset_parent_id='".$source_asset_id."'";
$result=$dba->Select($SQL);
$t="";
foreach ($result as $row)
copy_asset_with_children($row['asset_id'],$next_target_parent_id);
}

function validateDate($date,$lang_date_format)
{
if ($date=="")
return 0;
else {
    $d = DateTime::createFromFormat($lang_date_format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($lang_date_format) === $date;
}
    }

    
function is_it_valid_worktime_period($start_time,$end_time,$user_id,$workorder_id):bool{
global $dba;

$SQL="SELECT count(*)=0 as valid FROM workorder_works WHERE workorder_works.deleted<>1 AND (workorder_work_start_time<'".$end_time."') ";
$SQL.="AND (workorder_work_end_time >'".$start_time."') AND workorder_partner_id=0 AND workorder_user_id=".$user_id;

if ($workorder_id>0)
$SQL.=" AND workorder_id<>".$workorder_id;
$row=$dba->getRow($SQL);
if (LM_DEBUG)
    error_log($SQL,0);
//echo $SQL." valid=".$row['valid'];
if (isset($row) && $row['valid']==1)
return true;
else
return false;
}    

function update_last_ready($workrequest_id):void{
global $dba;


if ($workrequest_id>0)
    {
    $SQL="SELECT workorder_work_end_time, workorder_user_id FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id WHERE workorder_works.deleted<>1 AND workorder_status=5 AND workrequest_id=".$workrequest_id." ORDER BY workorder_work_end_time DESC LIMIT 0,1";
    $row=$dba->getRow($SQL);
    if (!empty($row['workorder_work_end_time']))
    {
    $SQL="UPDATE workrequests SET last_ready_date='".date("Y-m-d",strtotime($row['workorder_work_end_time']))."' last_ready_user_id=".$row['workorder_user_id']." WHERE workrequest_id=".$workrequest_id;
    if (!$dba->Query($SQL))
    error_log($dba->err_msg." ".$SQL,0);
    }else
    {
    $SQL="UPDATE workrequests SET last_ready_date=null,last_ready_user_id=null WHERE workrequest_id=".$workrequest_id;
    if (!$dba->Query($SQL) && LM_DEBUG)
    error_log($dba->err_msg." ".$SQL,0);
    }
}
}
/*
function repair():void{
global $dba;
$SQL="SELECT workrequest_id,workorder_id FROM workorders WHERE workrequest_id>0 ORDER BY workrequest_id";
$result=$dba->Select($SQL);
foreach ($result as $row){
if (!empty($row['workrequest_id'])){
    $SQL="SELECT workorder_work_end_time FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id WHERE workorder_works.workorder_status=4 AND workrequest_id=".$row['workrequest_id']." ORDER BY workorder_work_end_time DESC LIMIT 0,1";
    $row1=$dba->getRow($SQL);
    error_log($dba->err_msg." ".$SQL,0);
    if (!empty($row1['workorder_work_end_time']))
    {
    $SQL="UPDATE workrequests SET last_ready_date='".date("Y-m-d",strtotime($row1['workorder_work_end_time']))."' WHERE workrequest_id=".$row['workrequest_id'];
    error_log($dba->err_msg." ".$SQL,0);
    if (!$dba->Query($SQL))
    error_log($dba->err_msg." ".$SQL,0);
    }else
    {
    $SQL="UPDATE workrequests SET last_ready_date=null WHERE workrequest_id=".$row['workrequest_id'];
    if (!$dba->Query($SQL))
    error_log($dba->err_msg." ".$SQL,0);
    }
}
}
}
*/

function Luhn($number):int
{
   
    	$stack = 0;
    	$number = str_split(strrev($number), 1);

    	foreach ($number as $key => $value)
    	{
    		if ($key % 2 == 0)
    		{
    			$value = array_sum(str_split($value * 2, 1));
    		}

    		$stack += $value;
    	}

    	$stack %= 10;

    	if ($stack != 0)
    	{
    		$stack -= 10;
    	}

    	$number = implode('', array_reverse($number)) . abs($stack);
   

    return $number;
}

function is_user_working($user_id):bool{
global $dba;
$SQL="SELECT ".date("l")."_start as start,".date("l")."_end as end FROM users WHERE user_id=".$user_id;
if (LM_DEBUG)
error_log($SQL,0); 

$row=$dba->getRow($SQL);

$shift_start_datetime=new DateTime(date("Y-m-d").' '.$row['start']);
$shift_end_datetime=new DateTime(date("Y-m-d").' '.$row['end']);
$now=new DateTime();

if ($now>$shift_start_datetime && $now<$shift_end_datetime)
return true;
else
return false;
}

function send_telegram_messages():void{
global $dba,$lang;
$SQL="SELECT * FROM telegram_messages WHERE status=0";
$result=$dba->Select($SQL);

if ($dba->affectedRows()>0)
{
        foreach ($result as $row){
        if ($row['notification_id']>0){
        $SQL="SELECT lang FROM users WHERE user_id=".$row['user_id'];
        $row1=$dba->getRow($SQL);
        $lang=$row1['lang'];
        if (!empty($lang))
        {
        $SQL="SELECT main_asset_id,notification_short_".$lang.",";
               
        $SQL.="user_id FROM notifications WHERE notification_id=".$row['notification_id'];
        $row1=$dba->getRow($SQL);
        $sender=get_asset_name_from_id($row1['main_asset_id'],$lang)." (".get_username_from_id($row1['user_id']).")";
        $message=$row1['notification_short_'.$lang];
        }else
        lm_die(gettext("Empty lang in users table"));
        }
        else if ($row['sensor_id']>0){
        $SQL="SELECT asset_id FROM iot_sensors WHERE sensor_id=".$row['sensor_id'];
        $row1=$dba->getRow($SQL);
        $sender=get_asset_name_from_id($row1['asset_id'],$lang);
        $SQL="SELECT message_".$lang." FROM messages WHERE message_id=".(int) $row["received_message"];
        $row1=$dba->getRow($SQL);
        $message=$row1['message_'.$lang];
        }
        $SQL="SELECT telegram_chat_id,lang FROM users WHERE user_id=".$row['user_id'];
        $row2=$dba->getRow($SQL);
        
        
        
          if (LM_DEBUG)
error_log('python /var/www/send_message.py '.$row2['telegram_chat_id'].' "'.$sender.': '.$message.' '.date($lang_date_format." H:i", strtotime($row['received_time'])).'"',0); 

        exec('python '.TELEGRAM_SENDSCRIPT_PATH.'send_message.py '.$row2['telegram_chat_id'].' "'.$sender.': '.$message.' '.date($lang_date_format." H:i", strtotime($row['received_time'])).'"');
        
        $SQL="UPDATE telegram_messages SET status=1,sending_time=now() WHERE message_id=".$row['message_id'];
        $dba->Query($SQL);
        }    
}
}

function get_max_allowed_string_lenght($table_name,$column_name):int
{
global $dba;
$SQL="select CHARACTER_MAXIMUM_LENGTH as max from information_schema.columns
where table_schema = DATABASE() AND table_name = '".$table_name."' AND COLUMN_NAME = '".$column_name."'";
$row=$dba->getRow($SQL);
if (!empty($row))
return $row['max'];
else
lm_die("Something went wrong... ".$dba->err_msg." ".$SQL);
}

function is_date_mysql_format($date):bool
{
    if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date))
        return true;
    else
        return false;
}

function removing_accents($with_accent):string
{
$accents = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                     'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                     'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                     'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                     'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
return strtr( $with_accent, $accents);

}
