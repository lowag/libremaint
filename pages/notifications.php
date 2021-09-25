<?php echo "<link rel=\"stylesheet\" href=\"".CSS_LOC."css/not_message.css\">\n";
$new_message_has_saved=false;
if (isset($_POST['not_message_'.$lang]))
{
    if (!empty($dba->escapeStr($_POST['not_message_'.$lang])))
        {
        $SQL="INSERT INTO notifications_messages (user_id,notification_id, not_message_time,not_message_".$lang.") VALUES ";
        $SQL.="(".$_SESSION['user_id'].",".(int) $_POST['notification_id'].",NOW(),'".$dba->escapeStr($_POST['not_message_'.$lang])."')";
        if ($dba->Query($SQL)){
        $new_message_has_saved=true;
        }
        else
        lm_error(gettext("Failed to save the new message!")." ".$SQL." ".$dba->err_msg);
        }else
        lm_error(gettext("The message was empty!"));

}

?>
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
var checkboxes = document.querySelectorAll('input[name="notification_id[]"]');
var notification_ids=[];
var i=0;
checkboxes.forEach(e => { 
    if (e.checked){
    notification_ids.push(e.value);
    i++;
    }
 })
if (i==0){
alert("<?php echo gettext("There is no notification checked!");?>");
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
var address="index.php?page=notifications&create_workorder_from_notifications=1&notification_ids="+JSON.stringify(notification_ids)+"&employee_ids="+JSON.stringify(employee_ids);
if (document.getElementById('workorder_partner_id').value>0)
address+="&workorder_partner_id="+document.getElementById('workorder_partner_id').value;
address+="&workorder_partner_supervisor_user_id="+document.getElementById('workorder_partner_supervisor_user_id').value;

address+="&valid="+document.getElementById('tit_id').value;

location.href=address;

}
</script>
<?php
$SQL="SELECT users_assets FROM users WHERE user_id=".$_SESSION['user_id'];
$row=$dba->getRow($SQL);
if (!empty($row['users_assets']))
$users_assets=json_decode($row['users_assets'],true);
else
lm_die(gettext("There is no asset belong to you."));
 
if(isset($_GET['set_notification_status']) && $_GET['set_notification_status']>0 && $_GET['notification_id']>0 && is_it_valid_submit()){
$SQL="UPDATE notifications SET notification_status=".(int) $_GET['set_notification_status']." WHERE notification_id=".(int) $_GET['notification_id'];
if ($dba->Query($SQL))
lm_info(gettext("This notification status has set."));
}

if(isset($_GET['create_workorder_from_notifications']) && is_it_valid_submit()){


if(!empty($_GET['employee_ids']))
        {
        $employee_ids=array();
            foreach (json_decode($_GET["employee_ids"],true) as $employee_id)
            {
            $employee_ids['employee_id'.$employee_id]=1;
            }
       
        }
$notification_ids= json_decode($_GET["notification_ids"],true);

foreach ($notification_ids as $notification_id)
    {
        $SQL="SELECT * FROM notifications WHERE notification_id=".$notification_id;
        $row=$dba->getRow($SQL);
        if (LM_DEBUG)
            error_log($SQL,0);
     
            
    if (sizeof($notification_ids)>=1 ) 
            
    {
    $SQL="INSERT INTO workorders (asset_id,main_asset_id,location_id,main_location_id,priority,user_id,workorder_time,notification_id,workrequest_id,request_type,replace_to_product_id,product_id_to_refurbish";
    
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.=",workorder_short_".LANG1.",workorder_".LANG1;
    
    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
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
            if ($row['asset_id']>0)
            $SQL.="'".(int) $row['asset_id']."',";
            else
            $SQL.="'".(int) $row['main_asset_id']."',";
            $SQL.="'".(int) $row['main_asset_id']."',";
            $SQL.="'".(int) $row['location_id']."',";
            $SQL.="'".(int) $row['main_location_id']."',";
            $SQL.="'".(int) $row["priority"]."',";
            
            
            $SQL.="'".(int) $_SESSION["user_id"]."',";
           
            $SQL.="now(),";
            $SQL.=(int) $notification_id.",";
            $SQL.="0,";
            $SQL.=(int) $row['notification_type'].",";
            $SQL.=(int) $row['replace_to_product_id'].",";
            $SQL.=(int) $row['product_id_to_refurbish'].",";
            
            if ($_SESSION['CAN_WRITE_LANG1']){
            $SQL.="'".$dba->escapeStr($row["notification_short_".LANG1])."',";
            $SQL.="'".$dba->escapeStr($row["notification_".LANG1])."',";
            }
            if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
            {
            $SQL.="'".$dba->escapeStr($row["notification_short_".LANG2])."',";
            $SQL.="'".$dba->escapeStr($row["notification_".LANG2])."'";
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
                lm_info(gettext("The new workorder has been saved."));
     
            
                $SQL="UPDATE notifications SET notification_status=3 WHERE notification_id='".$notification_id."'";
                $dba->Query($SQL);
                if (LM_DEBUG)
                    error_log($SQL,0);
            }else
                lm_error(gettext("Failed to save new workorder ").$SQL." ".$dba->err_msg);
            if (LM_DEBUG)
            error_log($SQL,0);
            
  
    }
}}

if (isset($_POST['page']) && isset($_POST["new_notification"]) && !isset($_POST["notification_id"]) && is_it_valid_submit() && isset($_SESSION['ADD_NOTIFICATION'])){ //it is from the new notification form
//repetitive priority service_interval_date service_interval_hours notification_short 
$SQL="INSERT INTO notifications (asset_id,main_asset_id,priority";

if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.=",notification_short_".LANG1.",notification_".LANG1;

$SQL.=",user_id,notification_time,notification_type";

if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
$SQL.=",notification_short_".LANG2.",notification_".LANG2;
$SQL.=")";
$SQL.=" VALUES ";
$SQL.="(". (int) $_POST["main_asset_id"].",";
$SQL.=(int) $_POST["main_asset_id"].",";

$SQL.=(int) $_POST["priority"].",";

if ($_SESSION['CAN_WRITE_LANG1'])
{
$SQL.="'".$dba->escapeStr($_POST["notification_short_".LANG1])."',";
$SQL.="'".$dba->escapeStr($_POST["notification_".LANG1])."',";
}
$SQL.=$_SESSION["user_id"].",";
$SQL.="now(),";
$SQL.=(int) $_POST["notification_type"];

if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
{
$SQL.=",'".$dba->escapeStr($_POST["notification_short_".LANG2])."',";
$SQL.="'".$dba->escapeStr($_POST["notification_".LANG2])."'";
}
$SQL.=")";
if ($dba->Query($SQL)){
    $notification_id=$dba->insertedId();
        lm_info(gettext("The new notification has been saved."));
    if ((int)$_POST['priority']==1){
      $SQL="SELECT assets_users FROM assets where asset_id=".get_whole_path_ids('asset',(int) $_POST["main_asset_id"],1)[0];
                if (LM_DEBUG)
                error_log($SQL,0); 
                $row=$dba->getRow($SQL);
                if(!empty($row))
                {
                $users_to_message=json_decode($row['assets_users'],true);
                
                foreach ($users_to_message as $user){
                $SQL="SELECT user_level,telegram_chat_id FROM users WHERE user_id=".$user;
                $row=$dba->getRow($SQL);
                if ($row['user_level']<3 && !empty($row['telegram_chat_id'])){// we need to notify only the managers
                $SQL="INSERT INTO telegram_messages (user_id,sensor_id,received_message,sensor_value,notification_id) VALUES (".$user.",0,0,0,".$notification_id.")";
                $dba->Query($SQL);
                if (LM_DEBUG)
                error_log($SQL,0); 
                }
                if(is_user_working($user))
                send_telegram_messages();
                }
                }
       }
        }
        else
        lm_error(gettext("Failed to save new notification ").$SQL." ".$dba->err_msg);
if (LM_DEBUG)
error_log($SQL,0);

}else if (isset($_POST['page']) && isset($_POST["notification_id"]) && isset($_POST['modify_notification']) && is_it_valid_submit()){ //it is from the modify notification form
$own_notification=false;
if (!isset($_SESSION['MODIFY_NOTIFICATION']))
    {
    $SQL="SELECT user_id FROM notifications WHERE notification_id=".(int) $_POST["notification_id"];
    $row=$dba->getRow($SQL);
    if (empty($row) || $row['user_id']!=$_SESSION['user_id'])
    lm_error(gettext("You have no privilige to modify this notification!"));
    else
    $own_notification=true;
    }
else if (isset($_SESSION['MODIFY_NOTIFICATION']) || $own_notification){
    $SQL="UPDATE notifications SET ";
    $SQL.="main_asset_id='".(int) $_POST['main_asset_id']."',";
    $SQL.="asset_id='".(int) $_POST['asset_id']."',";
    $SQL.="priority='".(int) $_POST["priority"]."',";

    if ($_SESSION['CAN_WRITE_LANG1'])
    {
    $SQL.="notification_short_".LANG1."='".$dba->escapeStr($_POST["notification_short_".LANG1])."',";
    $SQL.="notification_".LANG1."='".$dba->escapeStr($_POST["notification_".LANG1])."',";
    }

    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
    {
    $SQL.="notification_short_".LANG2."='".$dba->escapeStr($_POST["notification_short_".LANG2])."',";
    $SQL.="notification_".LANG2."='".$dba->escapeStr($_POST["notification_".LANG2])."',";
    }

    $SQL.="notification_type='".(int) $_POST["notification_type"]."'";
    $SQL.=" WHERE notification_id='".(int) $_POST['notification_id']."'";
    if ($dba->Query($SQL))
            lm_info(gettext("The notification has been modified."));
            else
            lm_error(gettext("Failed to modify notification ").$SQL." ".$dba->err_msg);
    if (LM_DEBUG)
    error_log($SQL,0);
    if ((int)$_POST['priority']==1){
    $SQL="SELECT notification_id FROM telegram_messages WHERE notification_id=".(int) $_POST['notification_id'];
    $result=$dba->Select($SQL);
    if (empty($result)){
        $SQL="SELECT assets_users FROM assets where asset_id=".get_whole_path_ids('asset',(int) $_POST["main_asset_id"],1)[0];
                    if (LM_DEBUG)
                    error_log($SQL,0); 
                    $row=$dba->getRow($SQL);
                    if(!empty($row))
                    {
                    $users_to_message=json_decode($row['assets_users'],true);
                    
                    foreach ($users_to_message as $user){
                    $SQL="SELECT user_level,telegram_chat_id FROM users WHERE user_id=".$user;
                    $row=$dba->getRow($SQL);
                    if ($row['user_level']<3 && !empty($row['telegram_chat_id'])){// we need to notify only the managers
                    $SQL="INSERT INTO telegram_messages (user_id,sensor_id,received_message,sensor_value,notification_id) VALUES (".$user.",0,0,0,".(int) $_POST['notification_id'].")";
                    $dba->Query($SQL);
                    if (LM_DEBUG)
                    error_log($SQL,0); 
                    }
                    if(is_user_working($user))
                    send_telegram_messages();
                    }
                    }
                    }
        }
}
}

if (isset($_POST['reason_to_close_'.LANG1]) || isset($_POST['reason_to_close_'.LANG2]) && $_SESSION['user_level']<3 && is_it_valid_submit())
    {
    $SQL="UPDATE notifications SET ";
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.="reason_to_close_".LANG1."='".$dba->escapeStr($_POST['reason_to_close_'.LANG1])."'";
    
    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
    {
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.=",";
    $SQL.="reason_to_close_".LANG2."='".$dba->escapeStr($_POST['reason_to_close_'.LANG2])."'";
    }
    $SQL.=" ,notification_status=5,notification_closing_time=NOW() WHERE notification_id=".(int) $_POST['notification_id'];
    
    if ($dba->Query($SQL))
    lm_info(gettext("Notification has closed"));
    else
    lm_info(gettext("Failed to close notification"). $dba->err_msg);
    }


if (isset($_SESSION['ADD_NOTIFICATION']) && isset($_GET["new"]) || (isset($_GET["modify"]) && isset($_GET['notification_id']))){
$own_notification=false;
if (isset($_GET["modify"]) && !isset($_SESSION['MODIFY_NOTIFICATION']))
    {
    $SQL="SELECT user_id FROM notifications WHERE notification_id=".(int) $_POST["notification_id"];
    $row=$dba->getRow($SQL);
    if (empty($row) || $row['user_id']!=$_SESSION['user_id'])
    lm_error(gettext("You have no privilige to modify this notification!"));
    else
    $own_notification=true;
    }
else if (isset($_GET["new"]) || isset($_SESSION['MODIFY_NOTIFICATION']) || $own_notification){

    if (isset($_GET['notification_id'])){
    $SQL="SELECT * FROM notifications WHERE notification_id='".(int) $_GET['notification_id']."'";
    $notification_row=$dba->getRow($SQL);}
    echo "<div id=\"notification_form\" class=\"card\">\n";
    ?>
    <div class="card-header">
    <strong><?php 
    if (isset($_GET["new"]))
        {
        echo gettext("New notification ");
        if (file_exists(MESSAGE_TO_NOTIFIERS) && filesize(MESSAGE_TO_NOTIFIERS)>0)
        lm_info(file_get_contents(MESSAGE_TO_NOTIFIERS));
        }
    else if (isset($_GET["modify"]))
        echo gettext("Modify notification ");
    ?></strong>
    </div><?php //card header 

    //we need to extend the assets e.g. if the main asset is 'air system' it's better to add its children e.g. 'air compressor 1', 'air_compressor 2', 'buffer'...
    /*
    $i=0;
    foreach ($users_assets as $users_asset_id){
    $i++;
    $SQL="SELECT grouped_asset,asset_id FROM assets WHERE asset_id=".$users_asset_id;
    $row=$dba->getRow($SQL);
    if ($row['grouped_asset']==1)
    {
    $SQL="SELECT asset_id FROM assets WHERE asset_parent_id=".$row['asset_id'];
    $result=$dba->Select($SQL);
        if ($dba->affectedRows()>0){
            foreach($result as $row){
            array_splice( $users_assets, $i, 0, $row['asset_id'] ); 
            }
        }
    }

    }
    */

    ?>

    <div class="card-body card-block">

    <form action="index.php" name="notification_form" id="notification_form" method="post" enctype="multipart/form-data" class="form-horizontal">

    <?php
    echo "<div class=\"row form-group\">";
        echo "<div class=\"col col-md-2\">\n";
            echo "<label for=\"main_asset_id\" class=\" form-control-label\">".gettext("Asset:")."</label>";
        echo "</div>\n";

        echo "<div class=\"col col-md-3\">";
            echo "<select name=\"main_asset_id\" id=\"main_asset_id\" class=\"form-control\" required";
            if (isset($_GET['modify']))
            echo " onChange=\"location.href='index.php?page=notifications&notification_id=".(int) $_GET['notification_id']."&modify=1&mod_asset_id='+this.value\"";
            echo ">\n";
            echo "<option value=''>".gettext("Select an asset!")."</option>\n";
            foreach($users_assets as $asset_id) 
            {
        
            echo "<option value=\"".$asset_id."\"";
            if (isset($_GET['modify']) && !isset($_GET['mod_asset_id']) && $notification_row['main_asset_id']==$asset_id || (isset($_GET['mod_asset_id']) && $_GET['mod_asset_id']==$asset_id))
            echo " selected";
            
            echo ">".get_asset_name_from_id($asset_id,$lang)."</option>\n";
            }
            echo "</select>\n";
        echo "</div>";
    echo "</div>";

    if ($_SESSION['user_level']<4 && isset($_GET['modify']) && ($notification_row['main_asset_id']>0 || (isset($_GET['mod_asset_id']) && $_GET['mod_asset_id']>0)))
    {//the operators shouldn't change the asset_id
    echo "<div class=\"row form-group\">";
        echo "<div class=\"col col-md-2\">\n";
            echo "<label for=\"asset_id\" class=\" form-control-label\">".gettext("Asset:")."</label>";
        echo "</div>\n";

    echo "<div id=\"tree\" class=\"col col-md-3\">\n";
    echo "<ul id=\"treeData\" style=\"display: none;\">\n";
    $resp="";
    include(INCLUDES_PATH."asset_tree_func.php");
    if (isset($_GET['mod_asset_id']))
    echo tree_construct($_GET['mod_asset_id'],0);
    else
    echo tree_construct($notification_row['main_asset_id'],0);    
    echo "</ul></div>";
    if (isset($_GET['modify']) && $notification_row['asset_id']>0){
    echo "<div id=\"asset_name\">";
    $n="";
    foreach (get_whole_path("asset",$notification_row['asset_id'],1) as $k){
    if ($n=="") // the first element is the main asset_id -> ignore it
    $n=" ";
    else
    $n.=$k."-><wbr>";}

    if ($n!="")
    echo substr($n,0,-7);
    echo "</div>";
    }   
    else
    echo "<div id=\"asset_name\"></div>";
    echo "</div>";
    }
    echo "<input type='hidden' name='asset_id' id='asset_id'>";



    echo "<div class=\"row form-group\">";
        echo "<div class=\"col col-md-2\">\n";
            echo "<label for=\"notification_type\" class=\" form-control-label\">".gettext("Notification type:")."</label>";
        echo "</div>\n";

        echo "<div class=\"col col-md-3\">";
            echo "<select name=\"notification_type\" id=\"notification_type\" class=\"form-control\" required>";
            echo "<option value=''>".gettext("Select!")."\n";
    //$notification_types from lm-settings.php
            foreach ($notification_types as $id=>$notification_type)
            {
            echo "<option value=\"".++$id."\"";
            if (isset($_GET["modify"]) && $notification_row['notification_type']==$id)
            echo " selected";
            echo ">".$notification_type."</option>\n";
            
            }
            echo "</select>\n";
        echo "</div>";
    echo "</div>"; 

    echo "<div class=\"row form-group\">";
        echo "<div class=\"col col-md-2\">\n";
            echo "<label for=\"priority\" class=\" form-control-label\">".gettext("Priority:")."</label>";
        echo "</div>\n";

        echo "<div class=\"col col-md-3\">";
            echo "<select name=\"priority\" id=\"priority\" class=\"form-control\" required>";
            echo "<option value=''>".gettext("Select!")."</option>\n";
            foreach($priority_types as $id => $priority_type) //$priority_types from config/lm-settings.php
            {
        
            echo "<option value=\"".++$id."\"";//++$id because we store priority>0
            if (isset($_GET['modify']) && $notification_row['priority']==$id)
            echo " selected";
            
            echo ">".$priority_type."</option>\n";
            }
            echo "</select>\n";
        echo "</div>";
        
    echo "</div>";
    
    if ($_SESSION['CAN_WRITE_LANG1']) {
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"notification_short_".LANG1."\" class=\"form-control-label\">".gettext("Notification short (max.").$dba->get_max_fieldlength('notifications','notification_short_'.LANG1).":)</label></div>\n";
    echo "<div class=\"col col-md-3\"><input type=\"text\" id=\"notification_short_".LANG1."\" name=\"notification_short_".LANG1."\" maxlength='".$dba->get_max_fieldlength('notifications','notification_short_'.LANG1)."' class=\"form-control\"";

    if (isset($_GET["modify"]))
    echo " value=\"".$notification_row['notification_short_'.LANG1]."\"";

    echo " required></div>\n";
    echo "</div>";   
    
    
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"notification\" class=\" form-control-label\">".gettext("Notification:")."</label></div>";
    echo "<div class=\"col-12 col-md-9\"><div id='worktext_lenght'></div><textarea name=\"notification_".LANG1."\" id=\"notification_".LANG1."\" rows=\"9\" placeholder=\"".gettext("notification")."\" class=\"form-control\" onKeyup=\"document.getElementById('worktext_lenght').innerHTML='".gettext('Characters left: ')."'+(".$dba->get_max_fieldlength('notifications','notification_'.LANG1)."-this.value.length)\">";
    if (isset($_GET["modify"]))
    echo $notification_row['notification_'.LANG1];

    echo "</textarea></div>\n";
    echo "</div>\n";
    }


    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2']){
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"notification_short_".LANG2."\" class=\"form-control-label\">".gettext("Notification short (").LANG2.", max.".$dba->get_max_fieldlength('notifications','notification_short_'.LANG2)."):</label></div>\n";
    echo "<div class=\"col col-md-3\"><input type=\"text\" id=\"notification_short_".LANG2."\" name=\"notification_short_".LANG2."\" maxlength='".$dba->get_max_fieldlength('notifications','notification_short_'.LANG2)."' class=\"form-control\"";

    if (isset($_GET["modify"]))
    echo " value=\"".$notification_row['notification_short_'.LANG2]."\"";

    echo " required></div>\n";
    echo "</div>";


    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"notification_".LANG2."\" class=\" form-control-label\">".gettext("Notification (").LANG2."):</label></div>";
    echo "<div class=\"col-12 col-md-9\"><div id='worktext_".LANG2."_lenght'></div><textarea name=\"notification_".LANG2."\" id=\"notification_".LANG2."\" rows=\"9\" placeholder=\"".gettext("notification")." ".LANG2."\" class=\"form-control\" onKeyup=\"document.getElementById('worktext_".LANG2."_lenght').innerHTML='".gettext('Characters left: ')."'+(".$dba->get_max_fieldlength('notifications','notification_'.LANG2)."-this.value.length)\">";
    if (isset($_GET["modify"]))
    echo $notification_row['notification_'.LANG2];

    echo "</textarea></div>\n";
    echo "</div>\n";
    }

    if (isset($_GET["modify"])){
    echo "<INPUT TYPE=\"hidden\" name=\"notification_id\" id=\"notification_id\" VALUE=\"".$_GET['notification_id']."\">";
    echo "<input type='hidden' name='modify_notification' id='modify_notification' value='1'>\n";}
        else if (isset($_GET['new']))
            echo "<input type='hidden' name='new_notification' id='new_notification' value='1'>\n";

    echo "<INPUT TYPE=\"hidden\" name=\"page\" id=\"page\" VALUE=\"notifications\">";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";


    /*
    if (lm_isset_int('location_id')){
    echo "<INPUT TYPE=\"hidden\" name=\"asset_id\" id=\"asset_id\" value=\"0\">";
    echo "<INPUT TYPE=\"hidden\" name=\"location_id\" id=\"location_id\" VALUE=\"".lm_isset_int('asset_id')."\">";
    }*/
    echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Send")." </button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div>\n";
    echo "</form></div>";
    echo "<script>\n";

    echo "$(\"#notification_form\").validate({
    rules: {
            asset_id:{
            required: true
            },
            priority:{
            required: true
            },
            notification_type:{
            required:true
            },";
            if ($_SESSION['CAN_WRITE_LANG1'])
            echo ",
            notification_short_".LANG1.": {
            required: true,
            maxlength: ".$dba->get_max_fieldlength('notifications','notification_short_'.LANG1)."
            },
            notification_".LANG1.": {
            maxlength: ".$dba->get_max_fieldlength('notifications','notification_'.LANG1)."
            },";
            if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2']){
            echo ",notification_short_".LANG2.": {
            required: true,
            maxlength: ".$dba->get_max_fieldlength('notifications','notification_short_'.LANG2)."
            },
            notification: {
            maxlength: ".$dba->get_max_fieldlength('notifications','notification_'.LANG2)."
            }";
            }
            
        echo " }
    })\n";
    echo "</script>\n";

}
}

?>

<div class="card">
<div class="card-header">
<?php 
echo "<h2 style='display:inline;'>".gettext("Notifications")." </h2>";

$main_asset_id=lm_isset_int('main_asset_id');
if ($main_asset_id>0){
$_SESSION['main_asset_id']=$main_asset_id;
}
else if (isset($_GET['main_asset_id']) && $_GET['main_asset_id']=='all')
unset($_SESSION['main_asset_id']);

echo "<div class=\"card-body\">";
 echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">";
?>
<table id="notification-table" class="table table-striped table-bordered table-hover">
<thead>
<tr>

<?php 
echo "<th></th><th>"; 
echo "<div class=\"dropdown for-notification\">";
$notification_status=lm_isset_int('notification_status');
if ($notification_status>0)
$_SESSION['notification_status']=$notification_status;
else if (isset($_GET['notification_status']) && $notification_status==0){

unset($_SESSION['notification_status']);
}

$notification_type=lm_isset_int('notification_type');
if ($notification_type>0)
$_SESSION['notification_type']=$notification_type;
else if (isset($_GET['notification_type']) && $notification_type==0){

unset($_SESSION['notification_type']);
}

$notification_user_id=lm_isset_int('notification_user_id');
if ($notification_user_id>0)
$_SESSION['notification_user_id']=$notification_user_id;
else if (isset($_GET['notification_user_id']) && $notification_user_id==0){

unset($_SESSION['notification_user_id']);
}

if (isset($_SESSION['ADD_WORKORDER']) && (isset($_SESSION['notification_status']) && 0==$_SESSION['notification_status'] || (isset($_SESSION['notification_status']) && 1==$_SESSION['notification_status'])))
    {
    echo "<input type=\"checkbox\" style=\"display:inline;\" id=\"select_all\" name=\"select_all\"";
    echo " onChange=\"enable_create_workorder_button()\"";
    echo "> ";
    }
    
?>
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="notification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            <?php 
                            if (isset($_SESSION['notification_status']) && $_SESSION['notification_status']>0)
                            echo " STYLE=\"background-color:orange;\"";
                            ?>>
                                <?php echo gettext("S"); ?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="notification">
<?php 
echo "<a class=\"dropdown-item media bg-flat-color-10\"";
if (isset($_SESSION['notification_status']) && $_SESSION['notification_status']==0)
echo " style=\"background-color:orange;\"";
echo " href=\"index.php?page=notifications&notification_status=0\">\n";
echo "<i class=\"fa fa-warning\"></i>".gettext("All")."</a>";

foreach ($notification_statuses as $key => $value){
echo "<a class=\"dropdown-item media bg-flat-color-10\"";
if (isset($_SESSION['notification_status']) && $_SESSION['notification_status']==++$key)
echo " style=\"background-color:orange;\"";
echo " href=\"index.php?page=notifications&notification_status=".$key."\">\n";
echo "<i class=\"fa fa-warning\"></i>\n";
echo $value."</a>";
                            
}

?>
                            </div>
                            </div>
                        <?php
                        
echo "</th>";

echo "<th>".gettext("Date")."</th>";
echo "<th>";
 echo "<select name=\"notification_type\" id=\"notification_type\" class=\"form-control\" required";
 echo " onChange=\"location.href='index.php?page=notifications&notification_type='+this.value\"";
 if(isset($_SESSION["notification_type"]) && $_SESSION['notification_type']>0)
 echo " style=\"background-color:orange;\"";
 echo ">";
        echo "<option value=''>".gettext("All type")."\n";
  //$notification_types from lm-settings.php
        foreach ($notification_types as $id=>$notification_type)
        {
        echo "<option value=\"".++$id."\"";
        if (isset($_SESSION["notification_type"]) && $_SESSION['notification_type']==$id)
        echo " selected=''";
        echo ">".$notification_type."</option>\n";
        
        }
         echo "</select>\n";
echo "</th>";
echo "<th";
if (isset($_SESSION['notification_user_id']) && $_SESSION['notification_user_id']>0)
        echo " STYLE=\"background-color:orange\"";
    echo ">".gettext("User");
    $SQL="SELECT DISTINCT(users.user_id),surname FROM notifications LEFT JOIN users ON users.user_id=notifications.user_id ORDER BY surname";
    
    $result=$dba->Select($SQL);
    
    echo " <select name=\"notification_user_id\" id=\"notification_user_id\" class=\"form-control\"";
            echo " onChange=\"location.href='index.php?page=notifications&notification_user_id='+this.value\"";
            echo " style='display:inline;width:200px;'>\n";
    echo "<option value='all'>".gettext("All users");
   
    foreach($result as $row){
    echo "<option value='".$row['user_id']."'";
    
    if (isset($_SESSION['notification_user_id']) && $row['user_id']==$_SESSION['notification_user_id']){
    echo " selected=1";
    }
    echo ">";
     echo get_user_full_name_from_id($row['user_id'])."\n";
    }
    echo "</select>\n";        
    
echo "</th>";
    echo "<th";
    if (isset($_SESSION['main_asset_id']) && $_SESSION['main_asset_id']>0)
        echo " STYLE=\"background-color:orange\"";
    echo ">".gettext("Asset");
    if (!empty($users_assets)){
    $SQL="SELECT distinct(main_asset_id), asset_name_".$lang." FROM notifications LEFT JOIN assets on assets.asset_id=notifications.main_asset_id";
    $SQL.=" AND main_asset_id IN ('".join("','",$users_assets)."')";

    $SQL.=" ORDER BY asset_name_".$lang;
    $result=$dba->Select($SQL);
    }
    echo " <select name=\"main_asset_id\" id=\"main_asset_id\" class=\"form-control\"";
            echo " onChange=\"location.href='index.php?page=notifications&main_asset_id='+this.value\"";
            echo " style='display:inline;width:200px;'>\n";
    echo "<option value='all'>".gettext("All assets");
    $has=false;
    if (!empty($result)){
    foreach($result as $row){
    echo "<option value='".$row['main_asset_id']."'";
    
    if (isset($_SESSION['main_asset_id']) && $row['main_asset_id']==$_SESSION['main_asset_id']){
    echo " selected=1";
    $has=true;
    }
    echo ">";
     echo $row['asset_name_'.$lang]."\n";
    }}
    echo "</select>\n";        
    echo "</th>";



echo "<th>".gettext("Notification")."</th></tr>";
?>
</thead>
<tbody>
<?php

$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;
if (!empty($users_assets)){
$SQL="SELECT user_id,notification_time,asset_id,main_asset_id,";
if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.="notification_short_".LANG1.",";

if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
$SQL.="notification_short_".LANG2.",";

$SQL.="notification_type,notification_id,notification_status FROM notifications WHERE notification_status<6 AND";
$SQL.=" main_asset_id IN ('".join("','",$users_assets)."')";

if ($has==true && isset($_SESSION['main_asset_id']) && $_SESSION['main_asset_id']>0)
$SQL.=" AND main_asset_id='".$_SESSION['main_asset_id']."'";

if (isset($_SESSION['notification_status']) && $_SESSION['notification_status']>0)
$SQL.=" AND notification_status='".$_SESSION['notification_status']."'";

if (isset($_SESSION['notification_type']) && $_SESSION['notification_type']>0)
$SQL.=" AND notification_type='".$_SESSION['notification_type']."'";

if (isset($_SESSION['notification_user_id']) && $_SESSION['notification_user_id']>0)
$SQL.=" AND user_id='".$_SESSION['notification_user_id']."'";
$SQL.=" ORDER BY notification_time DESC";

$result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
$from=($pagenumber-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
}
if (LM_DEBUG)
error_log($SQL,0);
if (!empty($result)){
foreach ($result as $row)
{
    $from++;
    echo "<tr><td";
    if (1==$row['notification_status'])
    echo " class='bg-flat-color-4'";
    else if (2==$row['notification_status'])
    echo " class='bg-flat-color-2'";
    else if (3==$row['notification_status'])
    echo " class='bg-flat-color-5'";
    else if (4==$row['notification_status'])
    echo " class='bg-flat-color-6'";
    else if (0==$row["notification_status"])
    echo " class='bg-flat-color-10'";
    echo ">";
$SQL="SELECT COUNT(not_message_id) as num FROM notifications_messages WHERE notification_id=".$row['notification_id'];
$row1=$dba->getRow($SQL);

if (!isset($row1) || $row1['num']==0)
    echo " <a href=\"javascript:ajax_call('notification_messages',".$row["notification_id"].",'','','','".URL."index.php','for_ajaxcall')\" title=\"new message\"><i class=\"fa fa-envelope\"></i></a> ";
    else{
        
        
        $SQL="SELECT COUNT(not_message_id) as num FROM notifications_messages WHERE notification_id=".$row['notification_id']." AND JSON_CONTAINS(has_red,'".$_SESSION['user_id']."')";
        $row2=$dba->getRow($SQL);
        if (LM_DEBUG)
            error_log($SQL,0); 
          
        if ($row1['num']==$row2['num'])
            echo " <a href=\"javascript:ajax_call('notification_messages',".$row["notification_id"].",'','','','".URL."index.php','for_ajaxcall')\" title=\"new message\"><i class=\"fa fa-envelope-open\" style=\"color:green;\"></i></a> ";
        else
            echo " <a href=\"javascript:ajax_call('notification_messages',".$row["notification_id"].",'','','','".URL."index.php','for_ajaxcall')\" title=\"new message\"><i class=\"fa fa-bell\" style=\"color:red;\"></i></a> ";
        }

    
    echo "<div class=\"user-area dropdown float-right\">\n";
                            
                             echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
                             echo $from;
                             echo "</a>\n";
                             
                             
                             echo "<div class=\"user-menu dropdown-menu\">";
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_notification_detail','".$row['notification_id']."','','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-info\"></i> ";
                             echo gettext("Show details")."</a>";
                            
                            
                            if (isset($_SESSION['SEE_FILE_OF_WORKREQUEST'])){
                              echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_info_files','".$row['asset_id']."','assets','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-file\"></i> ";
                            echo gettext("Show info file(s)")."</a>";}
                            
                            
                             
                             if ((isset($_SESSION['MODIFY_NOTIFICATION']) || $row['user_id']==$_SESSION['user_id']) && ($row['notification_status']<3) || $_SESSION['user_level']<3){
                            echo "<a class=\"nav-link\" href=\"index.php?modify=1&page=notifications&asset_id=".$row['asset_id']."&notification_id=".$row['notification_id']."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Modify")."</a>";
                             }
                             
                             if (isset($_SESSION['MODIFY_NOTIFICATION']) && $row['notification_status']<3 && $_SESSION['user_level']<3){
                              echo "<a class=\"nav-link\" href=\"index.php?set_notification_status=2&page=notifications&notification_id=".$row['notification_id']."&valid=".$_SESSION["tit_id"]."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Set it confirmed")."</a>";
                             
                              echo "<a class=\"nav-link\" href=\"javascript:ajax_call('closing_notification','".$row["notification_id"]."','','','','".URL."index.php','for_ajaxcall')\" title=\"".gettext("Close this notification")."\"><i class='fa fa-info-circle'></i> ".gettext("Close this notification")."</a> ";
                             
                             }
                             if (isset($_SESSION["DELETE_NOTIFICATION"]) && $row['user_id']==$_SESSION['user_id'] || $_SESSION['user_level']<3){
                             echo "<a class=\"nav-link\" href=\"index.php?set_notification_status=6&page=notifications&notification_id=".$row['notification_id']."&valid=".$_SESSION["tit_id"]."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Set it deleted")."</a>";
                             
                             
                             }
                                                         
                            
//$notification_statuses=array(gettext("New"),gettext("Confirmed"),gettext("Work in progress"),gettext("Resolved"),gettext("Closed"),gettext("Deleted"));
                           
                             echo "</div>";
    echo "</div>\n";
if (isset($_SESSION['ADD_WORKORDER']) && 3>$row['notification_status'])
  {echo "<input type=\"checkbox\" class=\"checkBoxClass\" ";
   echo " onChange=\"enable_create_workorder_button()\" id=\"wr_".$row['main_asset_id']."\"";
    echo " name=\"notification_id[]\" value=\"".$row['notification_id']."\">";                        
  }
echo "</td>\n";
echo "<td onClick=\"javascript:ajax_call('show_notification_detail','".$row['notification_id']."','','','','".URL."index.php','for_ajaxcall')\">\n";
    if (LANG2_AS_SECOND_LANG && $_SESSION['user_level']<3 && $row['notification_short_'.LANG2]=='')//to see if translation needed
    echo "*";
echo $notification_statuses[--$row["notification_status"]];
echo "</td>\n<td>";
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']) && $_SESSION['user_level']<3 && $row['notification_short_'.LANG2]=='')//to see if translation needed
    echo " * ";
    echo date($lang_date_format." H:i", strtotime($row["notification_time"]))."</td>\n";
    
echo "<td onClick=\"javascript:ajax_call('show_notification_detail','".$row['notification_id']."','','','','".URL."index.php','for_ajaxcall')\">".$notification_types[--$row["notification_type"]]."</td>";
echo "<td onClick=\"javascript:ajax_call('show_notification_detail','".$row['notification_id']."','','','','".URL."index.php','for_ajaxcall')\">".get_username_from_id($row['user_id'])."</td>";    
    if ((!lm_isset_int('asset_id')>0 && !isset($_POST['valid'])) || isset($_POST["notification"]))
    {
        echo "<td onClick=\"javascript:ajax_call('show_notification_detail','".$row['notification_id']."','','','','".URL."index.php','for_ajaxcall')\">";
        
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
        
          
        echo "</td>\n";
    }
  
    
    echo "<td>".$row['notification_short_'.$lang]."</td></tr>\n";


}
}
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
<?php
if ($new_message_has_saved)
echo "ajax_call('notification_messages',".(int) $_POST["notification_id"].",'','','','".URL."index.php','for_ajaxcall')";
?>
</script>
