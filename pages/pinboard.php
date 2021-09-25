<?php 
if (PINBOARD && $_SESSION["SEE_PINBOARD"])
{
echo "<div>\n<nav>\n";
echo "<div class='nav nav-tabs' id='nav-tab' role='tablist'>\n";
echo "<a class='nav-item nav-link'  href='index.php?page=dashboard' role='tab' aria-controls='nav-profile' aria-selected='true'>".gettext("Dashboard")."</a>\n";
echo "<a class='nav-item nav-link active' data-toggle='tab' href='' role='tab' aria-controls='nav-home' aria-selected='false'>".gettext("Pinboard")."</a>\n";
echo "</div></nav>\n";
}
echo "<link rel=\"stylesheet\" href=\"".CSS_LOC."css/not_message.css\">\n";
$new_message_has_saved=false;
if (isset($_POST['pin_message_'.$lang]))
{
    if (!empty($dba->escapeStr($_POST['pin_message_'.$lang])))
        {
        $SQL="INSERT INTO pinboard_messages (user_id,pinboard_id, pin_message_time,pin_message_".$lang.") VALUES ";
        $SQL.="(".$_SESSION['user_id'].",".(int) $_POST['pinboard_id'].",NOW(),'".$dba->escapeStr($_POST['pin_message_'.$lang])."')";
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
<?php

if (isset($_POST['page']) && isset($_POST["new_pin"]) && !isset($_POST["pinboard_id"]) && is_it_valid_submit() && isset($_SESSION['ADD_TO_PINBOARD'])){ //it is from the new pin form
$SQL="INSERT INTO pinboard (";
$SQL.="user_id,pin_time,pin_type";
if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.=",pin_short_".LANG1.",pin_".LANG1;



if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
$SQL.=",pin_short_".LANG2.",pin_".LANG2;
$SQL.=",pin_status)";
$SQL.=" VALUES ";
$SQL.="(";
$SQL.=$_SESSION["user_id"].",";
$SQL.="now(),";
$SQL.=(int) $_POST["pin_type"];
if ($_SESSION['CAN_WRITE_LANG1'])
{
$SQL.=",'".$dba->escapeStr($_POST["pin_short_".LANG1])."',";
$SQL.="'".$dba->escapeStr($_POST["pin_".LANG1])."'";
}


if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
{
$SQL.=",'".$dba->escapeStr($_POST["pin_short_".LANG2])."',";
$SQL.="'".$dba->escapeStr($_POST["pin_".LANG2])."'";
}

$SQL.=",1)";
if ($dba->Query($SQL)){
    $pin_id=$dba->insertedId();
        lm_info(gettext("The new pin has been saved."));
        if ($pin_id>0 && isset($_FILES['info_file_name']['tmp_name']) && isset($_SESSION['ADD_FILE_TO_PIN'])){ //it is from the new file form
        $table="pinboard";
        $id=$pin_id;
        $id_column="pin_id";
        require(INCLUDES_PATH."file_upload.php"); 
        }
        }
        else
        lm_error(gettext("Failed to save new pin ").$SQL." ".$dba->err_msg);
if (LM_DEBUG)
error_log($SQL,0);

}else if (isset($_POST['page']) && isset($_POST["pin_id"]) && isset($_POST['modify_pin']) && is_it_valid_submit()){ //it is from the modify pin form
$own_pin=false;
if (!isset($_SESSION['MODIFY_PIN']))
    {
    $SQL="SELECT user_id FROM pinboard WHERE pin_id=".(int) $_POST["pin_id"];
    $row=$dba->getRow($SQL);
    if (empty($row) || $row['user_id']!=$_SESSION['user_id'])
    lm_error(gettext("You have no privilige to modify this pin!"));
    else
    $own_pin=true;
    }
else if (isset($_SESSION['MODIFY_PIN']) || $own_pin){
    $pin_id=(int) $_POST["pin_id"];
    $SQL="UPDATE pinboard SET ";
    
    if ($_SESSION['CAN_WRITE_LANG1'])
    {
    $SQL.="pin_short_".LANG1."='".$dba->escapeStr($_POST["pin_short_".LANG1])."',";
    $SQL.="pin_".LANG1."='".$dba->escapeStr($_POST["pin_".LANG1])."',";
    }

    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
    {
    $SQL.="pin_short_".LANG2."='".$dba->escapeStr($_POST["pin_short_".LANG2])."',";
    $SQL.="pin_".LANG2."='".$dba->escapeStr($_POST["pin_".LANG2])."',";
    }

    $SQL.="pin_type='".(int) $_POST["pin_type"]."'";
    $SQL.=" WHERE pin_id='".(int) $_POST['pin_id']."'";
    if ($dba->Query($SQL)){
            if ($pin_id>0 && isset($_FILES['info_file_name']['tmp_name']) && isset($_SESSION['ADD_FILE_TO_PIN'])){ //it is from the new file form

            $table="pinboard";

            $id=$pin_id;
            $id_column="pin_id";
                require(INCLUDES_PATH."file_upload.php"); 
            }
            
            lm_info(gettext("The pin has been modified."));
            }
            else
            lm_error(gettext("Failed to modify pin ").$SQL." ".$dba->err_msg);
    if (LM_DEBUG)
    error_log($SQL,0);
    
}
}




if (isset($_POST['reason_to_close_'.LANG1]) || isset($_POST['reason_to_close_'.LANG2]) && $_SESSION['user_level']<3 && is_it_valid_submit())
    {
    $SQL="UPDATE pinboard SET ";
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.="reason_to_close_".LANG1."='".$dba->escapeStr($_POST['reason_to_close_'.LANG1])."'";
    
    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
    {
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.=",";
    $SQL.="reason_to_close_".LANG2."='".$dba->escapeStr($_POST['reason_to_close_'.LANG2])."'";
    }
    $SQL.=" ,pin_status=5,pin_closing_time=NOW() WHERE pin_id=".(int) $_POST['pin_id'];
    
    if ($dba->Query($SQL))
    lm_info(gettext("The pin has closed"));
    else
    lm_info(gettext("Failed to close pin"). $dba->err_msg);
    }


if (isset($_SESSION['ADD_TO_PINBOARD']) && isset($_GET["new"]) || (isset($_GET["modify"]) && isset($_GET['pin_id']))){
$own_pin=false;
if (isset($_GET["modify"]) && !isset($_SESSION['MODIFY_PIN']))
    {
    $SQL="SELECT user_id FROM pinboard WHERE pin_id=".(int) $_POST["pin_id"];
    $row=$dba->getRow($SQL);
    if (empty($row) || $row['user_id']!=$_SESSION['user_id'])
    lm_error(gettext("You have no privilige to modify this pin!"));
    else
    $own_pin=true;
    }
else if (isset($_GET["new"]) || isset($_SESSION['MODIFY_PIN']) || $own_pin){

    if (isset($_GET['pin_id'])){
    $SQL="SELECT * FROM pinboard WHERE pin_id='".(int) $_GET['pin_id']."'";
    $pin_row=$dba->getRow($SQL);}
    echo "<div id=\"pin_form\" class=\"card\">\n";
    ?>
    <div class="card-header">
    <strong><?php 
    if (isset($_GET["new"]))
        {
        echo gettext("New pin");
        }
    else if (isset($_GET["modify"]))
        echo gettext("Modify pin");
    ?></strong>
    </div><?php
    ?>

    <div class="card-body card-block">

    <form action="index.php" name="pin_form" id="pin_form" method="post" enctype="multipart/form-data" class="form-horizontal">

    <?php
   

    echo "<div class=\"row form-group\">";
        echo "<div class=\"col col-md-2\">\n";
            echo "<label for=\"pin_type\" class=\" form-control-label\">".gettext("Pin type:")."</label>";
        echo "</div>\n";

        echo "<div class=\"col col-md-3\">";
            echo "<select name=\"pin_type\" id=\"pin_type\" class=\"form-control\" required>";
            echo "<option value=''>".gettext("Select!")."\n";
            foreach ($pin_types as $id=>$pin_type)
            {
            echo "<option value=\"".++$id."\"";
            if (isset($_GET["modify"]) && $pin_row['pin_type']==$id)
            echo " selected";
            echo ">".$pin_type."</option>\n";
            
            }
            echo "</select>\n";
        echo "</div>";
    echo "</div>"; 

    
    if ($_SESSION['CAN_WRITE_LANG1']) {
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"pin_short_".LANG1."\" class=\"form-control-label\">".gettext("Pin short (max.").$dba->get_max_fieldlength('pinboard','pin_short_'.LANG1).":)</label></div>\n";
    echo "<div class=\"col col-md-3\"><input type=\"text\" id=\"pin_short_".LANG1."\" name=\"pin_short_".LANG1."\" maxlength='".$dba->get_max_fieldlength('pinboard','pin_short_'.LANG1)."' class=\"form-control\"";

    if (isset($_GET["modify"]))
    echo " value=\"".$pin_row['pin_short_'.LANG1]."\"";

    echo " required></div>\n";
    echo "</div>";   
    
    
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"pin\" class=\" form-control-label\">".gettext("Pin:")."</label></div>";
    echo "<div class=\"col-12 col-md-9\"><div id='worktext_lenght'></div><textarea name=\"pin_".LANG1."\" id=\"pin_".LANG1."\" rows=\"9\" placeholder=\"".gettext("pin")."\" class=\"form-control\" onKeyup=\"document.getElementById('worktext_lenght').innerHTML='".gettext('Characters left: ')."'+(".$dba->get_max_fieldlength('pinboard','pin_'.LANG1)."-this.value.length)\">";
    if (isset($_GET["modify"]))
    echo $pin_row['pin_'.LANG1];

    echo "</textarea></div>\n";
    echo "</div>\n";
    }


    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2']){
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"pin_short_".LANG2."\" class=\"form-control-label\">".gettext("Pin short (").LANG2.", max.".$dba->get_max_fieldlength('pinboard','pin_short_'.LANG2)."):</label></div>\n";
    echo "<div class=\"col col-md-3\"><input type=\"text\" id=\"pin_short_".LANG2."\" name=\"pin_short_".LANG2."\" maxlength='".$dba->get_max_fieldlength('pinboard','pin_short_'.LANG2)."' class=\"form-control\"";

    if (isset($_GET["modify"]))
    echo " value=\"".$pin_row['pin_short_'.LANG2]."\"";

    echo " required></div>\n";
    echo "</div>";


    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"pin_".LANG2."\" class=\" form-control-label\">".gettext("Pin (").LANG2."):</label></div>";
    echo "<div class=\"col-12 col-md-9\"><div id='worktext_".LANG2."_lenght'></div><textarea name=\"pin_".LANG2."\" id=\"pin_".LANG2."\" rows=\"9\" placeholder=\"".gettext("pin")." ".LANG2."\" class=\"form-control\" onKeyup=\"document.getElementById('worktext_".LANG2."_lenght').innerHTML='".gettext('Characters left: ')."'+(".$dba->get_max_fieldlength('pinboard','pin_'.LANG2)."-this.value.length)\">";
    if (isset($_GET["modify"]))
    echo $pin_row['pin_'.LANG2];

    echo "</textarea></div>\n";
    echo "</div>\n";
    }
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label for=\"info_file_name\" class=\"form-control-label\">".gettext("File:")."</label></div>\n";
    echo "<div class=\"col-12 col-md-\"><input type=\"file\" id=\"info_file_name\" name=\"info_file_name[]\"  multiple></div></div>\n";
    
    if (isset($_GET["modify"])){
    echo "<INPUT TYPE=\"hidden\" name=\"pin_id\" id=\"pin_id\" VALUE=\"".$_GET['pin_id']."\">";
    echo "<input type='hidden' name='modify_pin' id='modify_pin' value='1'>\n";}
        else if (isset($_GET['new']))
            echo "<input type='hidden' name='new_pin' id='new_pin' value='1'>\n";

    echo "<INPUT TYPE=\"hidden\" name=\"page\" id=\"page\" VALUE=\"pinboard\">";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";



    echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Send")." </button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div>\n";
    echo "</form></div>";
    echo "<script>\n";

    echo "$(\"#pin_form\").validate({
    rules: {
            pin_type:{
            required:true
            },";
            if ($_SESSION['CAN_WRITE_LANG1'])
            echo ",
            pin_short_".LANG1.": {
            required: true,
            maxlength: ".$dba->get_max_fieldlength('pinboard','pin_short_'.LANG1)."
            },
            pin_".LANG1.": {
            maxlength: ".$dba->get_max_fieldlength('pinboard','pin_'.LANG1)."
            }";
            if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2']){
            echo ",pin_short_".LANG2.": {
            required: true,
            maxlength: ".$dba->get_max_fieldlength('pinboard','pin_short_'.LANG2)."
            },
            pin: {
            maxlength: ".$dba->get_max_fieldlength('pinboard','pin_'.LANG2)."
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
echo "<h2 style='display:inline;'>".gettext("Pinboard")." </h2>";
if ($_SESSION['ADD_TO_PINBOARD'])
echo "<a href=\"index.php?page=pinboard&new=1\"><button type=\"button\" class=\"btn btn-primary\">".gettext("New pin")."</button></a>";
echo "<div class=\"card-body\">";
 echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">";
?>
<table id="pin-table" class="table table-striped table-bordered table-hover">
<thead>
<tr>

<?php 
echo "<th></th><th>"; 
echo "<div class=\"dropdown for-pin\">";
$pin_status=lm_isset_int('pin_status');
if ($pin_status>0)
$_SESSION['pin_status']=$pin_status;
else if (isset($_GET['pin_status']) && $pin_status==0){

unset($_SESSION['pin_status']);
}

$pin_type=lm_isset_int('pin_type');
if ($pin_type>0)
$_SESSION['pin_type']=$pin_type;
else if (isset($_GET['pin_type']) && $pin_type==0){

unset($_SESSION['pin_type']);
}

$pin_user_id=lm_isset_int('pin_user_id');
if ($pin_user_id>0)
$_SESSION['pin_user_id']=$pin_user_id;
else if (isset($_GET['pin_user_id']) && $pin_user_id==0){

unset($_SESSION['pin_user_id']);
}
    
?>
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="pin" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            <?php 
                            if (isset($_SESSION['pin_status']) && $_SESSION['pin_status']>0)
                            echo " STYLE=\"background-color:orange;\"";
                            ?>>
                                <?php echo gettext("S"); ?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="pin">
<?php 
echo "<a class=\"dropdown-item media bg-flat-color-10\"";
if (isset($_SESSION['pin_status']) && $_SESSION['pin_status']==0)
echo " style=\"background-color:orange;\"";
echo " href=\"index.php?page=pinboard&pin_status=0\">\n";
echo "<i class=\"fa fa-warning\"></i>".gettext("All")."</a>";

foreach ($pin_statuses as $key => $value){
echo "<a class=\"dropdown-item media bg-flat-color-10\"";
if (isset($_SESSION['pin_status']) && $_SESSION['pin_status']==++$key)
echo " style=\"background-color:orange;\"";
echo " href=\"index.php?page=pinboard&pin_status=".$key."\">\n";
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
 echo "<select name=\"pin_type\" id=\"pin_type\" class=\"form-control\" required";
 echo " onChange=\"location.href='index.php?page=pinboard&pin_type='+this.value\"";
 if(isset($_SESSION["pin_type"]) && $_SESSION['pin_type']>0)
 echo " style=\"background-color:orange;\"";
 echo ">";
        echo "<option value=''>".gettext("All type")."\n";
  
        foreach ($pin_types as $id=>$pin_type)
        {
        echo "<option value=\"".++$id."\"";
        if (isset($_SESSION["pin_type"]) && $_SESSION['pin_type']==$id)
        echo " selected=''";
        echo ">".$pin_type."</option>\n";
        
        }
         echo "</select>\n";
echo "</th>";
echo "<th";
if (isset($_SESSION['pin_user_id']) && $_SESSION['pin_user_id']>0)
        echo " STYLE=\"background-color:orange\"";
    echo ">".gettext("User");
    $SQL="SELECT DISTINCT(users.user_id),surname FROM pinboard LEFT JOIN users ON users.user_id=pinboard.user_id ORDER BY surname";
    
    $result=$dba->Select($SQL);
    
    echo " <select name=\"pin_user_id\" id=\"pin_user_id\" class=\"form-control\"";
            echo " onChange=\"location.href='index.php?page=pinboard&pin_user_id='+this.value\"";
            echo " style='display:inline;width:200px;'>\n";
    echo "<option value='all'>".gettext("All users");
   
    foreach($result as $row){
    echo "<option value='".$row['user_id']."'";
    
    if (isset($_SESSION['pin_user_id']) && $row['user_id']==$_SESSION['pin_user_id']){
    echo " selected=1";
    }
    echo ">";
     echo get_user_full_name_from_id($row['user_id'])."\n";
    }
    echo "</select>\n";        
    
echo "</th>";
    

echo "<th>".gettext("Pin")."</th></tr>";
?>
</thead>
<tbody>
<?php

$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;
if (!empty($users_assets)){
$SQL="SELECT user_id,pin_time,";
if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.="pin_short_".LANG1.",";

if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
$SQL.="pin_short_".LANG2.",";

$SQL.="pin_type,pin_id,pin_status FROM pinboard WHERE pin_status<6";

if (isset($_SESSION['pin_status']) && $_SESSION['pin_status']>0)
$SQL.=" AND pin_status='".$_SESSION['pin_status']."'";

if (isset($_SESSION['pin_type']) && $_SESSION['pin_type']>0)
$SQL.=" AND pin_type='".$_SESSION['pin_type']."'";

if (isset($_SESSION['pin_user_id']) && $_SESSION['pin_user_id']>0)
$SQL.=" AND user_id='".$_SESSION['pin_user_id']."'";
$SQL.=" ORDER BY pin_time DESC";

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
    if (1==$row['pin_status'])
    echo " class='bg-flat-color-4'";
    else if (2==$row['pin_status'])
    echo " class='bg-flat-color-2'";
    else if (3==$row['pin_status'])
    echo " class='bg-flat-color-5'";
    else if (4==$row['pin_status'])
    echo " class='bg-flat-color-6'";
    else if (0==$row["pin_status"])
    echo " class='bg-flat-color-10'";
    echo ">";
$SQL="SELECT COUNT(pin_message_id) as num FROM pinboards_messages WHERE pin_id=".$row['pin_id'];
$row1=$dba->getRow($SQL);

if (!isset($row1) || $row1['num']==0)
    echo " <a href=\"javascript:ajax_call('pin_messages',".$row["pin_id"].",'','','','".URL."index.php','for_ajaxcall')\" title=\"new message\"><i class=\"fa fa-envelope\"></i></a> ";
    else{
        
        
        $SQL="SELECT COUNT(pin_message_id) as num FROM pinboard_messages WHERE pin_id=".$row['pin_id']." AND JSON_CONTAINS(has_red,'".$_SESSION['user_id']."')";
        $row2=$dba->getRow($SQL);
        if (LM_DEBUG)
            error_log($SQL,0); 
          
        if ($row1['num']==$row2['num'])
            echo " <a href=\"javascript:ajax_call('pin_messages',".$row["pin_id"].",'','','','".URL."index.php','for_ajaxcall')\" title=\"new message\"><i class=\"fa fa-envelope-open\" style=\"color:green;\"></i></a> ";
        else
            echo " <a href=\"javascript:ajax_call('pin_messages',".$row["pin_id"].",'','','','".URL."index.php','for_ajaxcall')\" title=\"new message\"><i class=\"fa fa-bell\" style=\"color:red;\"></i></a> ";
        }

    
    echo "<div class=\"user-area dropdown float-right\">\n";
                            
                             echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
                             echo $from;
                             echo "</a>\n";
                             
                             
                             echo "<div class=\"user-menu dropdown-menu\">";
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_pin_detail','".$row['pin_id']."','','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-info\"></i> ";
                             echo gettext("Show details")."</a>";
                            
                            
                          if ($row["info_file_id1"]>0){
                              echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_info_files','".$row['pin_id']."','pinboard','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-file\"></i> ";
                            echo gettext("Show info file(s)")."</a>";
                            }
                            
                             
                             if ((isset($_SESSION['MODIFY_PIN']) || $row['user_id']==$_SESSION['user_id']) && ($row['pin_status']<3) || $_SESSION['user_level']<3){
                            echo "<a class=\"nav-link\" href=\"index.php?modify=1&page=pinboard&pin_id=".$row['pin_id']."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Modify")."</a>";
                             }
                             
                           
                             if (isset($_SESSION["MODIFY_PIN"]) && $row['user_id']==$_SESSION['user_id'] || $_SESSION['user_level']<3){
                             echo "<a class=\"nav-link\" href=\"index.php?set_pin_status=6&page=pinboard&pin_id=".$row['pin_id']."&valid=".$_SESSION["tit_id"]."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Set it deleted")."</a>";
                             
                             
                             }
                                                         
                            
                           
                             echo "</div>";
    echo "</div>\n";

echo "</td>\n";
echo "<td onClick=\"javascript:ajax_call('show_pin_detail','".$row['pin_id']."','','','','".URL."index.php','for_ajaxcall')\">\n";
    if (LANG2_AS_SECOND_LANG && $_SESSION['user_level']<3 && $row['pin_short_'.LANG2]=='')//to see if translation needed
    echo "*";
echo $pin_statuses[--$row["pin_status"]];
echo "</td>\n<td>";
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']) && $_SESSION['user_level']<3 && $row['pin_short_'.LANG2]=='')//to see if translation needed
    echo " * ";
    echo date($lang_date_format." H:i", strtotime($row["pin_time"]))."</td>\n";
    
echo "<td onClick=\"javascript:ajax_call('show_pin_detail','".$row['pin_id']."','','','','".URL."index.php','for_ajaxcall')\">".$pin_types[--$row["pin_type"]]."</td>";
echo "<td onClick=\"javascript:ajax_call('show_pin_detail','".$row['pin_id']."','','','','".URL."index.php','for_ajaxcall')\">".get_username_from_id($row['user_id'])."</td>";    

  
    
    echo "<td>".$row['pin_short_'.$lang]."</td></tr>\n";


}
}
echo "</tbody></table>";




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
echo "ajax_call('pinboard_messages',".(int) $_POST["pin_id"].",'','','','".URL."index.php','for_ajaxcall')";
?>
</script>
