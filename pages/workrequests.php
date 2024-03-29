<div id='for_ajaxcall'>
</div>
<script>
function enable_create_workorder_button(){
var checkboxes = document.querySelectorAll('input[type="checkbox"]');
var id_name="";
checkboxes.forEach(e => {
e.removeAttribute("disabled");
})
if(Array.prototype.slice.call(checkboxes).some(x => x.checked))
{
document.getElementById("create_workorder_div").style.display = "block";



//checkboxes.forEach(e => { 
//if (e.checked && id_name=="")
//id_name=e.id;
//alert(class_name);
//if (e.id!=id_name && e.name!='employee_id[]')
//e.setAttribute("disabled","disabled");
// })

}
else
document.getElementById("create_workorder_div").style.display = "none";
}

function create_workorder(){
var checkboxes = document.querySelectorAll('input[name="workrequest_id[]"]');
var workrequest_ids=[];
var i=0;
checkboxes.forEach(e => { 
    if (e.checked){
    workrequest_ids.push(e.value);
    i++;
    }
 })
if (i==0){
alert("<?php echo gettext("There is no work checked!");?>");
return 0;
}

i=0;

checkboxes = document.querySelectorAll('input[name="employee_id[]"]');
var employee_ids=[];
checkboxes.forEach(e => { 
    if (e.checked){
    employee_ids.push(e.value);
    i++;
    }
 }) 
 
 if (i==0 && document.getElementById('workorder_partner_id').value==0){
    alert("<?php echo gettext("There is no employee or partner checked!");?>");
    return 0;
    }
var address="index.php?page=workrequests&create_workorder_from_workrequests=1&workrequest_ids="+JSON.stringify(workrequest_ids)+"&employee_ids="+JSON.stringify(employee_ids);
if (document.getElementById('workorder_partner_id').value>0)
address+="&workorder_partner_id="+document.getElementById('workorder_partner_id').value;
address+="&workorder_partner_supervisor_user_id="+document.getElementById('workorder_partner_supervisor_user_id').value;

address+="&valid="+document.getElementById('tit_id').value;

location.href=address;

}
</script>
<?php
if(isset($_GET['set_inactive']) && $_GET['set_inactive']==1 && isset($_GET['workrequest_id']) && $_GET['workrequest_id']>0 && is_it_valid_submit()){
$SQL="UPDATE workrequests SET workrequest_status=3 WHERE workrequest_id=".(int) $_GET['workrequest_id'];
if ($dba->Query($SQL))
lm_info(gettext("This workrequest has set as inactive."));
}else if(isset($_GET['set_inactive']) && $_GET['set_inactive']==0 && isset($_GET['workrequest_id']) && $_GET['workrequest_id']>0 && is_it_valid_submit()){
$SQL="UPDATE workrequests SET workrequest_status=1 WHERE workrequest_id=".(int) $_GET['workrequest_id'];
if ($dba->Query($SQL))
lm_info(gettext("This workrequest has set as active."));
}else if(isset($_GET['set_inactive']) && $_GET['set_inactive']==2 && isset($_GET['workrequest_id']) && $_GET['workrequest_id']>0 && is_it_valid_submit()){
$SQL="UPDATE workrequests SET workrequest_status=4 WHERE workrequest_id=".(int) $_GET['workrequest_id'];
if ($dba->Query($SQL))
lm_info(gettext("This workrequest has set as deleted."));
}

if(isset($_GET['create_workorder_from_workrequests']) && is_it_valid_submit()){


if(!empty($_GET['employee_ids']))
        {
        $employee_ids=array();
            foreach (json_decode($_GET["employee_ids"],true) as $employee_id)
            {
            $employee_ids['employee_id'.$employee_id]=1;
            }
       
        }
$workrequest_ids= json_decode($_GET["workrequest_ids"],true);
$saved_workorder=0;
foreach ($workrequest_ids as $workrequest_id)
    {
        $SQL="SELECT * FROM workrequests WHERE workrequest_id=".$workrequest_id;
        $row=$dba->getRow($SQL);
        if (LM_DEBUG)
            error_log($SQL,0);
     
            
    if (sizeof($workrequest_ids)>=1 ) 
            
    {
    $SQL="INSERT INTO workorders (asset_id,main_asset_id,location_id,main_location_id,priority";
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.=",workorder_short_".LANG1.",workorder_".LANG1;
    
    $SQL.=",user_id,workorder_time,workrequest_id,notification_id,request_type,replace_to_product_id,product_id_to_refurbish";
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    $SQL.=",workorder_short_".LANG2.",workorder_".LANG2;
            if (isset($_GET['workorder_partner_id']) && $_GET['workorder_partner_id']>0)
            $SQL.=",workorder_partner_id";
            
            if (isset($_GET['workorder_partner_supervisor_user_id']) && $_GET['workorder_partner_supervisor_user_id']>0)
            $SQL.=",workorder_partner_supervisor_user_id";
            
            foreach ($employee_ids as $key=>$value){
            $SQL.= ",".$key;
            }
            $SQL.=")";
            $SQL.=" VALUES ";
            $SQL.="(";
            $SQL.="'".(int) $row['asset_id']."',";
            $SQL.="'".(int) $row['main_asset_id']."',";
            $SQL.="'".(int) $row['location_id']."',";
            $SQL.="'".(int) $row['main_location_id']."',";
            $SQL.="'".(int) $row["priority"]."',";
            if ($_SESSION['CAN_WRITE_LANG1'])
            {
            $SQL.="'".$dba->escapeStr($row["workrequest_short_".LANG1])."',";
            $SQL.="'".$dba->escapeStr($row["workrequest_".LANG1])."',";
            }
            $SQL.="'".(int) $_SESSION["user_id"]."',";
           
            $SQL.="now(),";
            $SQL.=(int) $workrequest_id.",";
            $SQL.="0,";
            $SQL.=(int) $row['request_type'].",";
            $SQL.=(int) $row['replace_to_product_id'].",";
            $SQL.=(int) $row['product_id_to_refurbish'];
            if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
            {
            $SQL.=",'".$dba->escapeStr($row["workrequest_short_".LANG2])."',";
            $SQL.="'".$dba->escapeStr($row["workrequest_".LANG2])."'";
            
            }
            if (isset($_GET['workorder_partner_id']) && $_GET['workorder_partner_id']>0)
            $SQL.=",".(int) $_GET['workorder_partner_id'];
            
            if (isset($_GET['workorder_partner_supervisor_user_id']) && $_GET['workorder_partner_supervisor_user_id']>0)
            $SQL.=",".(int) $_GET['workorder_partner_supervisor_user_id'];
            
           
            foreach ($employee_ids as $key=>$value){
            $SQL.= ",".(int) $value;
            }
            $SQL.=")";
            if (LM_DEBUG)
            error_log($SQL,0);
            if ($dba->Query($SQL)){
            $workorder_id=$dba->insertedId();
                $saved_workorder++;
                     
                if ($row['product_id_to_refurbish']>0 && (int) $_GET['workorder_partner_id']>0)
                {//since this workorder is a refurbish need change the product location to the partner 
                    $SQL="SELECT stock_location_id FROM stock WHERE product_id=".$row['product_id_to_refurbish'];
                    $row1=$dba->getRow($SQL);
                    if (LM_DEBUG)
                        error_log($SQL,0);
                    if ($row1['stock_location_id']>0)
                    {
                        $SQL="UPDATE workorders SET orig_stock_location_id=".$row1['stock_location_id']." WHERE workorder_id=".$workorder_id;
                        $dba->Query($SQL);
                        if (LM_DEBUG)
                            error_log($SQL,0);
                    }else
                    lm_die("Something went wrong at ".__FILE__." row:".__ROW__);
                    
                    $SQL="INSERT INTO stock_movements (product_id,stock_movement_quantity,from_stock_location_id,to_partner_id,workorder_id) VALUES (";
                    $SQL.=$row['product_id_to_refurbish'].",1,".$row1['stock_location_id'].",".(int) $_GET['workorder_partner_id'].",".$workorder_id.")";
                    if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);
                    if (LM_DEBUG)
                        error_log($SQL,0);
                    
                    $SQL="UPDATE stock SET stock_location_id=0,stock_location_partner_id=".(int) $_GET['workorder_partner_id']." WHERE product_id=".$row['product_id_to_refurbish'];
                    if (LM_DEBUG)
                        error_log($SQL,0);
                    if (!$dba->Query($SQL))
                    lm_die($dba->err_msg." ".$SQL);
                    
                }
                $SQL="UPDATE workrequests SET workrequest_status=2 WHERE workrequest_id='".$workrequest_id."'";
                $dba->Query($SQL);
                if (LM_DEBUG)
                    error_log($SQL,0);
            }else
                lm_error(gettext("Failed to save new workorder ").$SQL." ".$dba->err_msg);
            if (LM_DEBUG)
            error_log($SQL,0);
            
  
    }
}
if ($saved_workorder==1)
lm_info(gettext("A new workorder has been saved."));
if ($saved_workorder>1)
lm_info($saved_workorder." ".gettext("pcs new workorders have been saved."));
}

if (isset($_POST['page']) && isset($_POST["new_workrequest"]) && !isset($_POST["workrequest_id"]) && is_it_valid_submit()){ //it is from the new workrequest form
//repetitive priority service_interval_date service_interval_hours workrequest_short 
$SQL="INSERT INTO workrequests (asset_id,main_asset_id,repetitive,priority,service_interval_date,service_interval_hours,counter_id,service_interval_mileage";

if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.=",workrequest_short_".LANG1.",workrequest_".LANG1;

$SQL.=",user_id,workrequest_time,for_operators,request_type,replace_to_product_id,product_id_to_refurbish";
if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
$SQL.=",workrequest_short_".LANG2.",workrequest_".LANG2;
$SQL.=")";
$SQL.=" VALUES ";
$SQL.="('". (int) $_POST["asset_id"]."',";
$SQL.="'".(int) get_whole_path("asset",$_POST['asset_id'],1)[0]."',";
if (isset($_POST["repetitive"]))
$SQL.="'".(int) $_POST["repetitive"]."',";
else
    $SQL.="'0',";
if ((int) $_POST["repetitive"]>1)
$SQL.="3,";
else
$SQL.="'".(int) $_POST["priority"]."',";
if (isset($_POST["service_interval_date"]))
$SQL.="'".(int) $_POST["service_interval_date"]."',";
else
    $SQL.="'0',";
if (isset($_POST["service_interval_hours"]))
$SQL.="'".(int) $_POST["service_interval_hours"]."',";
else
    $SQL.="'0',";
if (isset($_POST["counter_id"]))
$SQL.="'".(int) $_POST['counter_id']."',";
else
    $SQL.="'0',";
if (isset($_POST["service_interval_mileage"]))
$SQL.="'".(int) $_POST["service_interval_mileage"]."',";
else
    $SQL.="'0',";
if ($_SESSION['CAN_WRITE_LANG1'])
{
$SQL.="'".$dba->escapeStr($_POST["workrequest_short_".LANG1])."',";
$SQL.="'".$dba->escapeStr($_POST["workrequest_".LANG1])."',";
}

$SQL.="'".$_SESSION["user_id"]."',";
$SQL.="now(),";


if (isset($_POST["for_operators"]))
$SQL.=(int) $_POST["for_operators"].",";
else
$SQL.="'0',";
$SQL.="'".(int) $_POST["request_type"]."',";

if (isset($_POST["replace_to_product_id"]))

$SQL.="'".(int) $_POST["replace_to_product_id"]."'";
else
    $SQL.="'0'";
if (isset($_POST["product_id_to_refurbish"]))
$SQL.=",'".(int) $_POST["product_id_to_refurbish"]."'";
else
    $SQL.=",'0'";
if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
{
$SQL.=",'".$dba->escapeStr($_POST["workrequest_short_".LANG2])."'";
$SQL.=",'".$dba->escapeStr($_POST["workrequest_".LANG2])."'";
}
$SQL.=")";
if ($dba->Query($SQL))
        lm_info(gettext("The new workrequest has been saved."));
        else
        lm_error(gettext("Failed to save new workrequest ").$SQL." ".$dba->err_msg);
if (LM_DEBUG)
error_log($SQL,0);

}else if (isset($_POST['page']) && isset($_POST["workrequest_id"]) && isset($_POST['modify_workrequest'])  && is_it_valid_submit()){ //it is from the modify workrequest form
//repetitive priority service_interval_date service_interval_hours workrequest_short 
$SQL="UPDATE workrequests SET";
$SQL.=" repetitive='".(int) $_POST["repetitive"]."',";
$SQL.="priority='".(int) $_POST["priority"]."',";
$SQL.="service_interval_date='".(int) $_POST["service_interval_date"]."',";
$SQL.="service_interval_hours='".(int) $_POST["service_interval_hours"]."',";
$SQL.="service_interval_mileage='".(int) $_POST["service_interval_mileage"]."',";
if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.="workrequest_short_".LANG1."='".$dba->escapeStr($_POST["workrequest_short_".LANG1])."',";
$SQL.="workrequest_".LANG1."='".$dba->escapeStr($_POST["workrequest_".LANG1])."',";

if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
{
$SQL.="workrequest_short_".LANG2."='".$dba->escapeStr($_POST["workrequest_short_".LANG2])."',";
$SQL.="workrequest_".LANG2."='".$dba->escapeStr($_POST["workrequest_".LANG2])."',";

}
$SQL.="for_operators=".(int) $_POST["for_operators"].",";
$SQL.="request_type='".(int) $_POST["request_type"]."',";
$SQL.="replace_to_product_id='".(int) $_POST["replace_to_product_id"]."',";
$SQL.="product_id_to_refurbish='".(int) $_POST["product_id_to_refurbish"]."'";
$SQL.=" WHERE workrequest_id='".$_POST['workrequest_id']."'";
if ($dba->Query($SQL))
        lm_info(gettext("The workrequest has been modified."));
        else
        lm_error(gettext("Failed to modify workrequest ").$SQL." ".$dba->err_msg);
if (LM_DEBUG)
error_log($SQL,0);

}

if (isset($_GET["new"]) || (isset($_GET["modify"]) && isset($_GET['workrequest_id']))){
if (isset($_GET['workrequest_id'])){
$SQL="SELECT * FROM workrequests WHERE workrequest_id='".(int)$_GET['workrequest_id']."'";
$workrequest_row=$dba->getRow($SQL);}
echo "<div id=\"workrequest_form\" class=\"card\">\n";
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('workrequest_form').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";

if (isset($_GET['asset_id']) && $_GET['asset_id']>0)
{
$SQL="SELECT workrequest_short_".$lang.",asset_id FROM workrequests WHERE asset_id=".(int) $_GET['asset_id']." AND (workrequest_status<3 OR repetitive>0)";
    $result=$dba->Select($SQL);
    if ($dba->affectedRows()){
        echo "<div class=\"card\">\n<div class=\"card-header\">\n";
        echo "<strong>".gettext("Workrequests for this asset")."</strong>";
        echo "</div>\n";

        foreach ($result as $row){
        echo '<li> ';
        $n="";
        foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
        if ($n=="") // the first element is the main asset_id -> ignore it
        $n=" ";
        else
        $n.=$k."-><wbr>";}

        if ($n!="")
        echo substr($n,0,-7);


        echo ' '.$row['workrequest_short_'.$lang].'</li>';

        }
        echo '</ul></div>';
}
}
?>

<div class="card-header">
<strong><?php 
if (isset($_GET["new"]))
    echo gettext("New workrequest ");
else if (isset($_GET["modify"]))
    echo gettext("Modify workrequest ");

if (lm_isset_int('asset_id')){
    echo gettext(" to ");
    $n="";
    foreach (get_whole_path("asset",$_GET['asset_id'],1) as $k)
    if ($n=="") // the first element is the main asset_id -> ignore it
    $n=" ";
    else
    $n.=$k."-><wbr>";
    echo substr($n,0,-7);
    
}
else if  (isset($_GET['modify']) && $workrequest_row['asset_id']>0){
    echo gettext(" to ");
    $n="";
    foreach (get_whole_path("asset",$workrequest_row['asset_id'],1) as $k)
    if ($n=="") // the first element is the main asset_id -> ignore it
    $n=" ";
    else
    $n.=$k."-><wbr>";
    echo substr($n,0,-7);
    
} 
else if (isset($_GET['product_id_to_refurbish'])){
    echo gettext(" (refurbish) ");
    echo get_product_name_from_id($_GET['product_id_to_refurbish'],$lang);
}
else if (isset($_GET['modify']) && $workrequest_row['product_id_to_refurbish']>0){
    echo gettext(" (refurbish) ");
    echo get_product_name_from_id($workrequest_row['product_id_to_refurbish'],$lang);
}


?></strong>
</div><?php //card header ?>
<div class="card-body card-block">

<form action="index.php" id="workrequest_form" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php
if (!isset($_GET['product_id_to_refurbish']) || (isset($_GET['modify']) && $workrequest_row['asset_id']>0)){//it is not a workrequest for a refurbish
echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"repetitive\" class=\" form-control-label\">".gettext("Repetitive:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-3\">";
        echo "<select name=\"repetitive\" id=\"repetitive\" class=\"form-control\"";
        echo " onChange=\"location.href='index.php?new=1&page=workrequests&asset_id=".@$_GET['asset_id']."&repetitive='+this.value\">\n";
        echo "<option value=\"0\" ";
        if (isset($_GET['repetitive']) && $_GET['repetitive']== 0 ) echo 'selected' ;
        echo '>'.gettext('No').'</option>\n';
        
        echo "<option value=\"1\" ";
        if (isset($_GET['repetitive']) && $_GET['repetitive']== 1 ) echo 'selected' ;
        echo ">".gettext("Periodic")."</option>\n";
        
        echo "<option value=\"2\" ";
        if (isset($_GET['repetitive']) && $_GET['repetitive']== 2 ) echo 'selected' ;
        echo ">".gettext("By workhours")."</option>\n";
        
        echo "<option value=\"3\" ";
        if (isset($_GET['repetitive']) && $_GET['repetitive']== 3 ) echo 'selected' ;
        echo ">".gettext("By date or workhours")."</option>\n";
        
        echo "<option value=\"4\" ";
        if (isset($_GET['repetitive']) && $_GET['repetitive']== 4 ) echo 'selected' ;
        echo ">".gettext("By mileage")."</option>\n";
        
        echo "<option value=\"5\" ";
        if (isset($_GET['repetitive']) && $_GET['repetitive']== 5 ) echo 'selected' ;
        echo ">".gettext("By date or mileage")."</option>\n";
        
        
        echo "</select>\n";
    echo "</div>";
echo "</div>";

if (!isset($_GET['repetitive']) || (isset($_GET['repetitive']) && $_GET['repetitive']== 0 )){
echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"priority\" class=\" form-control-label\">".gettext("Priority:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-3\">";
        echo "<select name=\"priority\" id=\"priority\" class=\"form-control\" >";
    
        foreach($priority_types as $id => $priority_type) //$priority_types from config/lm-settings.php
        {
       
        echo "<option value=\"".++$id."\"";//++$id because we store priority>0
        if (isset($_GET['modify']) && $workrequest_row['priority']==$id)
        echo " selected";
        
        echo ">".$priority_type."</option>\n";
        }
        echo "</select>\n";
    echo "</div>";
echo "</div>";
$SQL="SELECT product_id FROM stock WHERE stock_location_asset_id=";
if (isset($_GET['modify']))
$SQL.=$workrequest_row['asset_id'];
else
$SQL.=(int) $_GET['asset_id'];
$SQL.= " AND stock_quantity>0";
$result=$dba->Select($SQL);
if (LM_DEBUG)
        error_log($SQL,0);
if ($dba->affectedRows()>0)
    {
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label class=\"form-control-label\">\n";
    echo gettext("The part built in:");
    echo "</label></div>\n";
   
    echo "<div class=\"col col-md-2\">";
    $built_in_products=array();
    foreach ($result as $row1){
    echo get_product_name_from_id($row1['product_id'],$lang)."\n";
    $built_in_products[]=$row1['product_id'];  
    }
    
    echo "</div>\n";
    echo "</div>\n";
    }
$SQL="SELECT * FROM assets WHERE asset_id='".(int) $_GET['asset_id']."'";
$row1=$dba->getRow($SQL);
if (LM_DEBUG)
        error_log($SQL,0);
$products_can_connect=array();
foreach($row1 as $key => $value)
    {
    if (strstr($key,"connection_id") && $value>0)
        $products_can_connect=array_merge(get_products_id_can_connect($value,$row1['connection_type'.substr($key,13)]),$products_can_connect);
    
    }
if (count($products_can_connect)>1)
{
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label for=\"replace_to_product_id\" class=\"form-control-label\">\n";
    echo gettext("Replace to:");
    echo "</label></div>\n";

    echo "<div class=\"col col-md-3\">";
    echo "<select name=\"replace_to_product_id\" id=\"replace_to_product_id\" class=\"form-control\">";
    echo "<option value=''>".gettext("No");
    
    foreach ($products_can_connect as $pr){
        //if (!in_array($pr,$built_in_products ))
        if ($pr!=$row1['asset_product_id'])
        {
        echo "<option value='".$pr."'";
        if (isset($_GET['modify']) && $row['replace_to_product_id']==$pr)
            echo " selected";
        echo ">".get_product_name_from_id($pr,$lang)."\n";
            }
    }  
    echo "</select>\n";
    echo "</div>\n";
    echo "</div>\n";
}else
echo "<input type='hidden' name='replace_to_product_id' id='replace_to_product_id' value='0'>";

    
}else
echo "<INPUT TYPE=\"hidden\" name=\"priority\" id=\"priority\" VALUE=\"3\">";


if ((isset($_GET['repetitive']) && $_GET['repetitive']>0) || (isset($_GET['modify']) && $workrequest_row['repetitive']>0)) 
{
echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"for_operators\" class=\"form-control-label\">".gettext("For operators:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">";
        echo "<input type='checkbox' id='for_operators' name='for_operators' value='1'";
        if (isset($_GET['modify']) && $workrequest_row['for_operators']==1)
        echo " checked";
        echo "></div></div>";  
        }

 if (isset($_GET['repetitive']) && $_GET['repetitive']>1) 
        {
        echo "<div class=\"row form-group\">";
        echo "<div class=\"col col-md-3\"><label for=\"counter_id\" class=\" form-control-label\">".gettext("Counter:")."</label></div>";

        echo "<div class=\"col col-md-2\">";
        echo "<select name=\"counter_id\" id=\"counter_id\" class=\"form-control\">\n";
        $SQL="SELECT counter_id, asset_name_".$lang." FROM counters LEFT JOIN assets on counters.asset_id=assets.asset_id WHERE main_asset_id='".get_whole_path('asset',$_GET['asset_id'],1)[0]."'";
        $SQL.=" ORDER BY asset_name_".$lang;
        error_log($SQL,0);
        $result=$dba->Select($SQL);
        if ($dba->affectedRows()>0)
        echo "<option value=\"0\">".gettext("Please select")."</option>\n";
        else
        echo "<option value=\"\">".gettext("There is no counter here")."</option>\n";
        foreach ($result as $row){
        echo "<option value=\"".$row["counter_id"]."\"";
        if ($row['counter_id']==$workrequest_row['counter_id'])
        echo " selected";
        echo ">".$row["asset_name_".$lang]."</option>\n";
        
        }
        echo "</select></div></div>";
 
 
        }


if (isset($_GET['repetitive']) && ($_GET['repetitive']==1 || $_GET['repetitive']==3 || $_GET['repetitive']==5 ))  {


echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-3\"><label for=\"service_interval_date\" class=\" form-control-label\">".gettext("Service interval:")."</label></div>";

    echo "<div class=\"col col-md-2\">";
    echo "<select name=\"service_interval_date\" id=\"service_interval_date\" class=\"form-control\" required>\n";
    $SQL="SELECT user_level_id,user_level_".$lang." FROM user_levels ORDER BY user_level_".$lang;
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"\"";
    if (isset($_GET["modify"]) && $workrequest_row['service_interval_date']==0)
    echo " selected";
    echo ">".gettext("Please select")."</option>\n";
    
    echo "<option value=\"1\"";
        if (isset($_GET["modify"]) && $workrequest_row['service_interval_date']==1)
    echo " selected";
    echo ">".gettext('Daily')."</option>\n";
    
    echo "<option value=\"7\"";
    if (isset($_GET["modify"]) && $workrequest_row['service_interval_date']==7)
    echo " selected";
    echo ">".gettext('Weekly')."</option>\n";
    
    echo "<option value=\"14\"";
    if (isset($_GET["modify"]) && $workrequest_row['service_interval_date']==14)
    echo " selected";
    echo ">".gettext('Fortnigtly')."</option>\n";
    
    echo "<option value=\"30\"";
    if (isset($_GET["modify"]) && $workrequest_row['service_interval_date']==30)
    echo " selected";
    echo ">".gettext('Monthly')."</option>\n";
    
    echo "<option value=\"60\"";
    if (isset($_GET["modify"]) && $workrequest_row['service_interval_date']==60)
    echo " selected";
    echo ">".gettext('2 Monthly')."</option>\n";
    
    echo "<option value=\"90\"";
    if (isset($_GET["modify"]) && $workrequest_row['service_interval_date']==90)
    echo " selected";
    echo ">".gettext('3 Monthly')."</option>\n";
    
    echo "<option value=\"180\"";
    if (isset($_GET["modify"]) && $workrequest_row['service_interval_date']==180)
    echo " selected";
    echo ">".gettext('6 Monthly')."</option>\n";
    
    echo "<option value=\"365\"";
    if (isset($_GET["modify"]) && $workrequest_row['service_interval_date']==365)
    echo " selected";
    echo ">".gettext('Yearly')."</option>\n";
   
    echo "<option value=\"730\"";
    if (isset($_GET["modify"]) && $workrequest_row['service_interval_date']==730)
    echo " selected";
    echo ">".gettext('2 Yearly')."</option>\n";
   
echo "</select></div>";
if ($_GET['repetitive']==3 || $_GET['repetitive']==5 )
echo gettext(" OR");
echo "</div>";

}else
 echo "<INPUT TYPE=\"hidden\" name=\"service_interval_date\" id=\"service_interval_date\" VALUE=\"0\">";
 

 
if (isset($_GET['repetitive']) && ($_GET['repetitive']==2 || $_GET['repetitive']==3 ))  {
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"service_interval_hours\" class=\"form-control-label\">".gettext("Service interval")." (".gettext("h").")</label></div>\n";
echo "<div class=\"col col-md-2\"><input type=\"text\" id=\"service_interval_hours\" name=\"service_interval_hours\" class=\"form-control\"";
if (isset($_GET["modify"]))
echo " value=\"".$workrequest_row['service_interval_hours']."\"";
echo " required></div>\n";
echo "</div>";  

}else
 echo "<INPUT TYPE=\"hidden\" name=\"service_interval_hours\" id=\"service_interval_hours\" VALUE=\"0\">";
 
 
if (isset($_GET['repetitive']) && ($_GET['repetitive']==4 || $_GET['repetitive']==5))  {
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"service_interval_mileage\" class=\"form-control-label\">".gettext("Service interval")." (".gettext("km").")</label></div>\n";
echo "<div class=\"col col-md-2\"><input type=\"text\" id=\"service_interval_mileage\" name=\"service_interval_mileage\" class=\"form-control\"";
if (isset($_GET["modify"]))
echo " value=\"".$workrequest_row['service_interval_mileage']."\"";
echo " required></div>\n";

echo "</div>";  
}else
 echo "<INPUT TYPE=\"hidden\" name=\"service_interval_mileage\" id=\"service_interval_mileage\" VALUE=\"0\">";
}//end !isset[$_GET['product_id_to_refurbish']]
else{//refurbish
echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"priority\" class=\" form-control-label\">".gettext("Priority:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-3\">";
        echo "<select name=\"priority\" id=\"priority\" class=\"form-control\" >";
    
        foreach($priority_types as $id => $priority_type) //$priority_types from config/lm-settings.php
        {
       
        echo "<option value=\"".++$id."\"";//++$id because we store priority>0
        if (isset($_GET['modify']) && $workrequest_row['priority']==$id)
        echo " selected";
        
        echo ">".$priority_type."</option>\n";
        }
        echo "</select>\n";
    echo "</div>";
echo "</div>";
    echo "<input type='hidden' name='product_id_to_refurbish' id='product_id_to_refurbish' value='";
    if (isset($_GET['product_id_to_refurbish']))
        echo (int) $_GET['product_id_to_refurbish'];
    else if (isset($_GET['modify']) && $workrequest_row['product_id_to_refurbish']>0)
        echo $workrequest_row['product_id_to_refurbish'];
    echo "'>";
    
    
    echo "<input type='hidden' name='replace_to_product_id' id='replace_to_product_id' value='0'>\n";
    //echo "<INPUT TYPE=\"hidden\" name=\"priority\" id=\"priority\" VALUE=\"0\">\n";
    echo "<INPUT TYPE=\"hidden\" name=\"asset_id\" id=\"asset_id\" VALUE=\"0\">\n";
    echo "<INPUT TYPE=\"hidden\" name=\"service_interval_date\" id=\"service_interval_date\" VALUE=\"0\">\n";
    echo "<INPUT TYPE=\"hidden\" name=\"service_interval_hours\" id=\"service_interval_hours\" VALUE=\"0\">\n";
    echo "<INPUT TYPE=\"hidden\" name=\"service_interval_mileage\" id=\"service_interval_mileage\" VALUE=\"0\">\n";

}





 
 
if ($_SESSION['CAN_WRITE_LANG1'])
{
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"workrequest_short\" class=\"form-control-label\">".gettext("Workrequest (max.").$dba->get_max_fieldlength('workrequests','workrequest_short_'.LANG1)."):</label></div>\n";
echo "<div class=\"col col-md-3\"><input type=\"text\" id=\"workrequest_short_".LANG1."\" name=\"workrequest_short_".LANG1."\" class=\"form-control\"";

if (isset($_GET["modify"]))
echo " value=\"".$workrequest_row['workrequest_short_'.LANG1]."\"";

echo " required></div>\n";
echo "</div>";   
 }
 
 
 
if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2'])){
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"workrequest_short_".LANG2."\" class=\"form-control-label\">".gettext("Workrequest (").LANG2.", max.".$dba->get_max_fieldlength('workrequests','workrequest_short_'.LANG2)."):</label></div>\n";
echo "<div class=\"col col-md-3\"><input type=\"text\" id=\"workrequest_short_".LANG2."\" name=\"workrequest_short_".LANG2."\" class=\"form-control\"";

if (isset($_GET["modify"]))
echo " value=\"".$workrequest_row['workrequest_short_'.LANG2]."\"";

echo " required></div>\n";
echo "</div>"; 
}
 
 
echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"request_type\" class=\" form-control-label\">".gettext("Activity type:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-3\">";
        echo "<select name=\"request_type\" id=\"request_type\" class=\"form-control\" >";
  //$activity_types from lm-settings.php
        foreach ($activity_types as $id=>$activity_type)
        {
        echo "<option value=\"".++$id."\"";
        if (isset($_GET["modify"]) && $workrequest_row['request_type']==$id)
        echo " selected";
        echo ">".$activity_type."</option>\n";
        
        }
       
       
       
        echo "</select>\n";
    echo "</div>";
echo "</div>"; 
 
 if ($_SESSION['CAN_WRITE_LANG1']){ 
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"workrequest_".LANG1."\" class=\" form-control-label\">".gettext("Workrequest:")."</label></div>";
echo "<div class=\"col-12 col-md-9\"><textarea name=\"workrequest_".LANG1."\" id=\"workrequest_".LANG1."\" rows=\"9\" placeholder=\"".gettext("workrequest")."\" class=\"form-control\">";
if (isset($_GET["modify"]))
echo $workrequest_row['workrequest_'.LANG1];

echo "</textarea></div>\n";
echo "</div>\n";
}

if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2'])){        
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"workrequest_".LANG2."\" class=\" form-control-label\">".gettext("Workrequest (").LANG2."):</label></div>";
echo "<div class=\"col-12 col-md-9\"><textarea name=\"workrequest_".LANG2."\" id=\"workrequest_".LANG2."\" rows=\"9\" placeholder=\"".gettext("workrequest")."\" class=\"form-control\">";
if (isset($_GET["modify"]))
echo $workrequest_row['workrequest_'.LANG2];

echo "</textarea></div>\n";
echo "</div>\n"; 

}


if (isset($_GET["modify"])){
echo "<INPUT TYPE=\"hidden\" name=\"workrequest_id\" id=\"workrequest_id\" VALUE=\"".$_GET['workrequest_id']."\">";
echo "<input type='hidden' name='modify_workrequest' id='modify_workrequest' value='1'>\n";}
    else if (isset($_GET['new']))
        echo "<input type='hidden' name='new_workrequest' id='new_workrequest' value='1'>\n";
        
       
        
echo "<INPUT TYPE=\"hidden\" name=\"page\" id=\"page\" VALUE=\"workrequests\">";
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

if (lm_isset_int('asset_id')){
echo "<INPUT TYPE=\"hidden\" name=\"asset_id\" id=\"asset_id\" VALUE=\"".lm_isset_int('asset_id')."\">";
echo "<INPUT TYPE=\"hidden\" name=\"location_id\" id=\"location_id\" value=\"0\" >";
}
/*
if (lm_isset_int('location_id')){
echo "<INPUT TYPE=\"hidden\" name=\"asset_id\" id=\"asset_id\" value=\"0\">";
echo "<INPUT TYPE=\"hidden\" name=\"location_id\" id=\"location_id\" VALUE=\"".lm_isset_int('asset_id')."\">";
}*/
echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i> Submit </button>\n";
echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> Reset </button></div>\n";
echo "</form></div>";
echo "<script>\n";

echo "$(\"#workrequest_form\").validate({
  rules: {";
  if ($_SESSION['CAN_WRITE_LANG1'])
  echo "
    workrequest_short_".LANG1.": {
      required: true,
      maxlength: ".$dba->get_max_fieldlength('workrequests','workrequest_short_'.LANG1)."
    },
    workrequest_".LANG1.": {
      maxlength: ".$dba->get_max_fieldlength('workrequests','workrequest_'.LANG1)."
    }";
  if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2'])){
  echo ",workrequest_short_".LANG2.": {
       required: true,
       maxlength: ".$dba->get_max_fieldlength('workrequests','workrequest_short_'.LANG2)."
    },
    workrequest_".LANG2.": {
      maxlength: ".$dba->get_max_fieldlength('workrequests','workrequest_'.LANG2)."
    }";
  }
    
echo "  }
})\n";
echo "</script>\n";

}


?>

<div class="card">
<div class="card-header">
<?php 
if (!isset($_SESSION['list_workorder_types']))
$_SESSION['list_workorder_types']=1;

if (isset($_GET['list_workorder_types']) && (int) $_GET['list_workorder_types']>=0)
$_SESSION['list_workorder_types']= (int) $_GET['list_workorder_types'];



echo "<h2 style='display:inline;'>".gettext("Workrequests")." </h2>";
echo "<select name='list_workorder_types' id='list_workorder_types' onchange=\"location.href='index.php?page=workrequests&list_workorder_types='+this.value\">\n";
echo "<OPTION VALUE='0'";
if ($_SESSION['list_workorder_types']==0)
echo " selected";
echo ">".gettext('All types');
echo "<OPTION VALUE='1'";
if ($_SESSION['list_workorder_types']==1)
echo " selected";
echo ">".gettext('Only for maintenance group');
echo "<OPTION VALUE='2'";
if ($_SESSION['list_workorder_types']==2)
echo " selected";
echo ">".gettext('Only for operators');
echo "</select>";
?>
<strong>
<?php
$main_asset_id=lm_isset_int('main_asset_id');
if ($main_asset_id>0){
$_SESSION['main_asset_id']=$main_asset_id;
}
else if (isset($_GET['main_asset_id']) && $_GET['main_asset_id']=='all')
unset($_SESSION['main_asset_id']);

if (isset($_SESSION['main_asset_id']) && $_SESSION['main_asset_id']>0){
echo get_asset_name_from_id($_SESSION['main_asset_id'],$lang);
echo " <button type=\"button\" id=\"create_task_list_button\" name=\"create_task_list_button\" class=\"btn btn-danger btn-sm\" ";
        echo " onClick=\"window.open('index.php?page=pdf_create&title=create_task_list&asset_id=".$_SESSION['main_asset_id']."','_blank')\"";
        echo ">".gettext("Create task list (pdf)")."</button>";
}
echo "</strong></div>";
echo "<div class=\"card-body\">";
 echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">";
?>
<table id="workrequest-table" class="table table-striped table-bordered table-hover">
<thead>
<tr>

<?php 
echo "<th>"; 
echo "<div class=\"dropdown for-notification\">";
$workrequest_status=lm_isset_int('workrequest_status');
if ($workrequest_status>0)
$_SESSION['workrequest_status']=$workrequest_status;
else if (isset($_GET['workrequest_status']) && $workrequest_status==0){

unset($_SESSION['workrequest_status']);
}

if (isset($_SESSION['ADD_WORKORDER']) && isset($_SESSION['workrequest_status']) && (0==$_SESSION['workrequest_status'] || 1==$_SESSION['workrequest_status']))
    {
    echo "<input type=\"checkbox\" style=\"display:inline;\" id=\"select_all\" name=\"select_all\"";
    echo " onChange=\"enable_create_workorder_button()\"";
    echo ">";
    }
    
?>
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="notification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            <?php 
                            if (isset($_SESSION['workrequest_status']) && $_SESSION['workrequest_status']>0)
                            echo " STYLE=\"background-color:orange;\"";
                            ?>>
                                <?php echo gettext("S"); ?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="notification">
<?php 

foreach ($workrequest_statuses as $key => $value){
echo "<a class=\"dropdown-item media bg-flat-color-10\"";
if (isset($_SESSION['workrequest_status']) && $_SESSION['workrequest_status']==$key)
echo " style=\"background-color:orange;\"";
echo " href=\"index.php?page=workrequests&workrequest_status=".$key."\">\n";
echo "<i class=\"fa fa-warning\"></i>\n";
echo $value."</a>";
                            
}

?>
                            </div>
                            </div>
                        <?php
                        
echo "</th>";

echo "<th>".gettext("Date")."</th>";

    echo "<th";
    if (isset($_SESSION['main_asset_id']) && $_SESSION['main_asset_id']>0)
        echo " STYLE=\"background-color:orange\"";
    echo ">".gettext("Asset");
    $SQL="SELECT distinct(main_asset_id), asset_name_".$lang." FROM workrequests LEFT JOIN assets on assets.asset_id=workrequests.main_asset_id ORDER BY asset_name_".$lang;
    $result=$dba->Select($SQL);
    echo " <select name=\"main_asset_id\" id=\"main_asset_id\" class=\"form-control\"";
            echo " onChange=\"location.href='index.php?page=workrequests&main_asset_id='+this.value\"";
            echo " style='display:inline;width:200px;'>\n";
    echo "<option value='all'>".gettext("All assets");
    if (!empty($result)){
    foreach($result as $row){
    echo "<option value='".$row['main_asset_id']."'";
    if (isset($_SESSION['main_asset_id']) && $row['main_asset_id']==$_SESSION['main_asset_id'])
    echo " selected";
    echo ">";
    if ($row['main_asset_id']==0)
            echo gettext("Refurbish");
            else
            echo $row['asset_name_'.$lang]."\n";
    }}
    echo "</select>\n";        
    echo "</th>";

echo "<th>".gettext("Interval")."</th>";
echo "<th>".gettext("Ready")."</th>";
echo "<th>".gettext("Norm")."</th>";
echo "<th>".gettext("Workrequest")."</th></tr>\n";
?>
</thead>
<tbody>
<?php

$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;

$SQL="SELECT user_id,workrequest_time,asset_id,main_asset_id,workrequest_short_".$lang;
if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
$SQL.=",workrequest_short_".LANG2;
$SQL.=",service_interval_date,service_interval_hours,repetitive,service_interval_mileage,last_ready_date,workrequest_id,workrequest_status,for_operators,product_id_to_refurbish,labour_norm FROM workrequests WHERE 1=1";
if (isset($_SESSION['main_asset_id']) && $_SESSION['main_asset_id']>0)
$SQL.=" AND main_asset_id='".$_SESSION['main_asset_id']."'";
else if (isset($_GET['main_asset_id']) && $_GET["main_asset_id"]!='all')
    $SQL.=" AND  product_id_to_refurbish>0";
if (isset($_SESSION['workrequest_status']) && $_SESSION['workrequest_status']>0)
$SQL.=" AND workrequest_status='".$_SESSION['workrequest_status']."'";

if ($_SESSION['list_workorder_types']==1)
    $SQL.=" AND for_operators=0";
else if ($_SESSION['list_workorder_types']==2)
    $SQL.=" AND for_operators=1";
    
$SQL.=" ORDER BY workrequest_time DESC";

$result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
$from=($pagenumber-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);

if (LM_DEBUG)
error_log($SQL,0);
if ($number_all>0){
foreach ($result as $row)
{
    $from++;
    echo "<tr><td";
    if (1==$row['workrequest_status'])
    echo " class='bg-flat-color-4'";
    else if (2==$row['workrequest_status'])
    echo " class='bg-flat-color-2'";
    else if (3==$row['workrequest_status'])
    echo " class='bg-flat-color-5'";
    else if (4==$row['workrequest_status'])
    echo " class='bg-flat-color-6'";
    else if (0==$row["workrequest_status"])
    echo " class='bg-flat-color-10'";
    echo "><div class=\"user-area dropdown float-right\">\n";
                            
                             echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
                             echo $from;
                             echo " <i class=\"fa fa-bars\"></i>\n";
                             echo "</a>\n";
                             
                             
                             echo "<div class=\"user-menu dropdown-menu\">";
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_workrequest_detail','".$row['workrequest_id']."','','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-info\"></i> ";
                             echo gettext("Show details")."</a>";
                            
                            
                            if (isset($_SESSION['SEE_FILE_OF_WORKREQUEST'])){
                              echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_info_files','".$row['asset_id']."','assets','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-file\"></i> ";
                            echo gettext("Show info file(s)")."</a>";}
                            
                            
                             
                             if (isset($_SESSION['MODIFY_WORKREQUEST']) && ($row['workrequest_status']==1 || $row['repetitive']>0)){
                            echo "<a class=\"nav-link\" href=\"index.php?modify=1&page=workrequests&asset_id=".$row['asset_id']."&repetitive=".$row['repetitive']."&workrequest_id=".$row['workrequest_id']."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Modify")."</a>";
                             
                             if ($row['workrequest_status']<3){
                              echo "<a class=\"nav-link\" href=\"index.php?set_inactive=1&page=workrequests&workrequest_id=".$row['workrequest_id']."&valid=".$_SESSION["tit_id"]."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Set it inactive")."</a>";
                             }
                             else{
                             echo "<a class=\"nav-link\" href=\"index.php?set_inactive=0&page=workrequests&workrequest_id=".$row['workrequest_id']."&valid=".$_SESSION["tit_id"]."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Set it active")."</a>";
                             }
                             }
                             if (isset($_SESSION['MODIFY_WORKREQUEST']) && ($row['workrequest_status']==1 || $row['workrequest_status']==3)){
                                                        
                             if ($row['workrequest_status']<4){
                              echo "<a class=\"nav-link\" href=\"index.php?set_inactive=2&page=workrequests&workrequest_id=".$row['workrequest_id']."&valid=".$_SESSION["tit_id"]."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Delete")."</a>";
                             }
                             else{
                             echo "<a class=\"nav-link\" href=\"index.php?set_inactive=0&page=workrequests&workrequest_id=".$row['workrequest_id']."&valid=".$_SESSION["tit_id"]."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Set it active")."</a>";
                             }
                             
                             
                             }
                             echo "</div>";
    echo "</div>";
    if (isset($_SESSION['ADD_WORKORDER']) && 2>$row['workrequest_status'] && !$row['for_operators'])
    
      {echo "<input type=\"checkbox\" class=\"checkBoxClass\" ";
      echo " onChange=\"enable_create_workorder_button()\" id=\"wr_".$row['main_asset_id']."\"";
      echo " name=\"workrequest_id[]\" value=\"".$row['workrequest_id']."\">";                        
    }
    echo "</td><td onClick=\"javascript:ajax_call('show_workrequest_detail','".$row['workrequest_id']."','','','','".URL."index.php','for_ajaxcall')\">\n";
if (LANG2_AS_SECOND_LANG && $_SESSION['user_level']<3 && isset($_SESSION['CAN_WRITE_LANG2']) && $row['workrequest_short_'.LANG2]=="")
    echo " * "; //translation needed
    
    echo date($lang_date_format, strtotime($row["workrequest_time"]))."</td>\n";
    
  /*  if ((!lm_isset_int('asset_id')>0 && !isset($_POST['valid'])) || isset($_POST["workrequest_".$lang]))
    {*/
        echo "<td onClick=\"javascript:ajax_call('show_workrequest_detail','".$row['workrequest_id']."','','','','".URL."index.php','for_ajaxcall')\">";
        
        if ($row['asset_id']>0)
        {
        $k="";
        $n="";
        foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."-><wbr>";
        }
        
        echo substr($n,0,-7);
        }
        
        else if ($row['product_id_to_refurbish']>0){
        echo gettext("Refurbish").": ".get_product_name_from_id($row['product_id_to_refurbish'],$lang);
        }
     //}
        echo "</td>\n";
    
    echo "<td onClick=\"javascript:ajax_call('show_workrequest_detail','".$row['workrequest_id']."','','','','".URL."index.php','for_ajaxcall')\">";
    if ($row['repetitive']==3){
    echo get_service_interval_date($row["service_interval_date"]);
    echo " / ".$row["service_interval_hours"]." ".gettext("hours");
    }
    else if ($row['repetitive']==1)
    echo get_service_interval_date($row["service_interval_date"]);
    
    
    else if ($row['repetitive']==2)
    echo $row["service_interval_hours"]." ".gettext("hours");
    
    else if ($row['repetitive']==4)
    echo $row["service_interval_mileage"]." ".gettext("km");
    else
    echo gettext("No interval");
    echo "</td>";
    echo "<td onClick=\"javascript:ajax_call('show_workrequest_detail','".$row['workrequest_id']."','','','','".URL."index.php','for_ajaxcall')\">";
    if (!empty($row['last_ready_date'])){
    echo date($lang_date_format, strtotime($row['last_ready_date']));
    
    $SQL="SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( `workorder_worktime` ) ) ) as worktime FROM workorder_works LEFT JOIN workorders ON workorder_works.workorder_id=workorders.workorder_id WHERE workrequest_id=".$row['workrequest_id']." GROUP BY workorders.workorder_id ORDER BY workorders.workorder_id DESC LIMIT 0,4";
    
    $result2=$dba->Select($SQL);
    if (!empty($result2)){
    foreach ($result2 as $row2){
    echo " ".date("H:i", strtotime($row2['worktime']))." | ";
    }}}
    echo "</td>";
    echo "<td onClick=\"javascript:ajax_call('show_workrequest_detail','".$row['workrequest_id']."','','','','".URL."index.php','for_ajaxcall')\">";
   if ($row['repetitive']>0)
    echo date("H:i", strtotime($row['labour_norm']));
    echo "</td>";
    echo "<td onClick=\"javascript:ajax_call('show_workrequest_detail','".$row['workrequest_id']."','','','','".URL."index.php','for_ajaxcall')\">";
    echo $row['workrequest_short_'.$lang]."</td></tr>\n";


}
}else
echo "<tr><td colspan='7'>".gettext("No match.")."</td></tr>";

echo "</tbody></table>";

echo "<div id=\"create_workorder_div\" STYLE=\"display:none;\"><button type=\"button\" id=\"create_workorder_button\" name=\"create_workorder_button\" class=\"btn btn-danger btn-sm\" ";
        echo " onClick=\"create_workorder();\"";
        echo ">".gettext("Create workorder")."</button>\n";


 echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col-5 col-md-2\"><label for=\"workorder_partner_id\" class=\"form-control-label\">";
        echo gettext("Partner involved in this workorder:")."</label></div>\n";
        echo "<div class=\"col-5 col-md-3\">\n";
        echo "<select id=\"workorder_partner_id\" name=\"workorder_partner_id\" class=\"form-control\"";
        echo " onChange=\"if (this.value>0) document.getElementById('workorder_partner_supervisor_user').style.display = 'block';
        else document.getElementById('workorder_partner_supervisor_user').style.display = 'none';\"";
        echo ">\n";
        $SQL="SELECT partner_name, partner_id FROM partners WHERE active=1 ORDER BY partner_name";
        $result1=$dba->Select($SQL);
        echo "<option value=\"0\">".gettext("No")."</option>\n";
        foreach ($result1 as $row1)
        echo "<option value=\"".$row1["partner_id"]."\">".$row1["partner_name"]."</option>\n";
        echo "</select>\n</div></div>\n";
 
 
echo "<div class=\"row form-group\" id='workorder_partner_supervisor_user'";
echo " style=\"display:none;\">\n";
echo "<div class=\"col-5 col-md-2\"><label for=\"workorder_partner_supervisor_user_id\" class=\"form-control-label\">";
        echo gettext("Partner supervisor:")."</label></div>\n";
        echo "<div class=\"col-5 col-md-3\">\n";
        echo "<select id=\"workorder_partner_supervisor_user_id\" name=\"workorder_partner_supervisor_user_id\" class=\"form-control\"";
        if (!isset($_SESSION['MODIFY_WORKORDER']))
        echo " disabled";
        echo ">\n";
        echo "<option value='0'>".gettext("Select");
        foreach (get_employees_from_id($_SESSION['user_id']) as $user_id =>$name){
        echo "<option value='".$user_id."'>".$name."\n";
        }
        echo "</select>\n";
        echo "</div>\n";
echo "<br/></div>\n";        

echo "<div class=\"row form-group\">\n";
echo "<div class=\"col-5 col-md-2\"><label for=\"employee_id\" class=\"form-control-label\">";
echo gettext("Employee(s):")."</label></div>\n";
echo "<div class=\"col-5 col-md-6\">\n";
foreach (get_employees_from_id($_SESSION['user_id']) as $user_id =>$name){
        echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"employee_id[]\" VALUE=\"".$user_id."\" > ".$name."\n";
        }
echo "</div></div>";  

//echo "<span id='bundled' style=\"display:none;\"><br/>".gettext(" Bundled workorder name:")." <INPUT TYPE=\"text\" NAME=\"bundled_workorder_name\" ID=\"bundled_workorder_name\"></span>";        
echo "<INPUT TYPE='hidden' name='tit_id' id='tit_id' value='".$_SESSION['tit_id']."'>";
echo "</div>\n</form>\n</div>\n";

include(INCLUDES_PATH."pagination.php");

       
echo "</div>\n";//card
 ?>
<script>
$(document).ready(function () {
    $("#select_all").click(function () {
        $(".checkBoxClass").prop('checked', $(this).prop('checked'));
    });
});
</script>
