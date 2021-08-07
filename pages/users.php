
<?php
if (isset($_POST['modify']) && is_it_valid_submit() && isset($_SESSION['MODIFY_USER'])){
$SQL="UPDATE users SET";
//$SQL.=" username='".$dba->escapeStr($_POST['username'])."',";
$SQL.=" firstname='".$dba->escapeStr($_POST['firstname'])."',";
$SQL.=" surname='".$dba->escapeStr($_POST['surname'])."',";
$SQL.=" user_phone='".$dba->escapeStr($_POST['user_phone'])."',";
$SQL.=" user_email='".$dba->escapeStr($_POST['user_email'])."',";
$SQL.=" user_level=".(int) $_POST['user_level'].",";
$SQL.=" user_parent_id=".(int) $_POST['user_parent_id'].",";
$SQL.=" lang='".$dba->escapeStr($_POST['user_lang'])."',";
$SQL.=" user_created=NOW()";
$SQL.=" WHERE user_id=".$_SESSION['user_id'];


if ($dba->Query($SQL)){
lm_info(gettext("The user's data has been modified."));
}else
lm_error(gettext("Failed to modified user's data.")." ".$dba->err_msg." ".$SQL);

}





if (isset($_POST['new']) && isset($_POST['firstname']) && is_it_valid_submit() && isset($_SESSION['ADD_USER'])){//username | firstname | surname | user_phone | user_email             | user_level
if (!isset($_SESSION['ADD_USER']))
lm_die(gettext("You have no permission!"));

$SQL="INSERT INTO users (username,firstname,surname,user_phone,user_email,user_level,user_parent_id,lang,user_created,password,active) VALUES";
$SQL.="('".$dba->escapeStr($_POST['username'])."',";
$SQL.="'".$dba->escapeStr($_POST['firstname'])."',";
$SQL.="'".$dba->escapeStr($_POST['surname'])."',";
$SQL.="'".$dba->escapeStr($_POST['user_phone'])."',";
$SQL.="'".$dba->escapeStr($_POST['user_email'])."',";
$SQL.=(int) $_POST['user_level'].",";
$SQL.=(int) $_POST['user_parent_id'].",";
$SQL.="'".$dba->escapeStr($_POST['lang'])."',";
$SQL.="NOW(),";
$SQL.="'".password_hash($_POST['username'],PASSWORD_DEFAULT)."',1)";


if ($dba->Query($SQL)){
$user_id=$dba->insertedId();
        if ($_POST['user_level']<3)
        {
        
        $SQL=" select column_name from information_schema.columns where table_schema = 'libremaint' and table_name='users' and (column_name LIKE 'ADD_%' OR column_name LIKE 'SEE_%' OR column_name LIKE 'RECORD_%' OR column_name LIKE 'DELETE_%' OR column_name LIKE 'MODIFY_%' OR column_name LIKE 'PUT_%' OR column_name LIKE 'TAKE_%' OR column_name LIKE 'STOCK_%')";
        $result=$dba->Select($SQL);
        foreach($result as $row){
        $SQL="UPDATE users SET ".$row['column_name']."=1 WHERE user_id=".$user_id;
        $dba->Query($SQL);
        }
        }

        echo "<div class=\"card\">".gettext("The new user has been saved.")."</div>";
        //we need new column for this user in workorders table
            /*
            $SQL="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='workorders' AND COLUMN_NAME LIKE 'employee_id%'";
            $result=$dba->Select($SQL);
            $i=$dba->affectedRows();
         */
         $SQL=" select 1 from information_schema.columns where table_schema = 'libremaint' and table_name='workorders' and column_name='employee_id".$user_id."'";
         $result=$dba->Select($SQL);
         if ($dba->affectedRows()==0)
            {
            $SQL="ALTER TABLE workorders add column employee_id".$user_id." tinyint(2) UNSIGNED not null default 0 AFTER employee_id".--$user_id;
            if(!$dba->Query($SQL)) 
            lm_info(gettext("Failed to create new field ").$SQL."\n ".$dba->err_msg);
            }
        }
        else
        lm_info(gettext("Failed to save new user ").$dba->err_msg);
    if (LM_DEBUG)
    error_log($SQL,0);
}

else if (isset($_POST['user_id']) && isset($_POST["office_hours"]) && is_it_valid_submit()){
$SQL="UPDATE users SET ".$dba->escapeStr($_POST["office_hours"])."='".$dba->escapeStr($_POST["office_time"])."' WHERE user_id=".(int) $_POST['user_id'];
if ($dba->Query($SQL))  
lm_info(gettext("The user's office hours has has been modified."));
else
lm_info(gettext("Failed to modified user's office hours. ").$dba->err_msg);

}

else if (isset($_POST['user_id']) && isset($_POST["action"]) && $_POST["action"]=='user_priv_mod' && is_it_valid_submit()){// modify user priviligies
if (!isset($_SESSION['MODIFY_USER']))
lm_die(gettext("You have no permission!"));
$i=0;
$SQL="UPDATE users SET ";
    foreach ($priviliges as $p){
    if ($p!="break")
    {
    if ($i>0)
    $SQL.=",";
    if (isset($_POST[$p]) && $_POST[$p]==1)
    {
    $SQL.="`".$p."`=1";
        
    }
    else
    {
    $SQL.="`".$p."`=0";
    
    }
    $i++;
    }
    }
$SQL.=" WHERE user_id='".$_POST['user_id']."'";    
if ($dba->Query($SQL))  
echo "<div class=\"card\">".gettext("The user's priviliges has has been modified.")."</div>";
else
echo "<div class=\"card\">".gettext("Failed to modified user's priviliges.").$dba->err_msg." <br/>".$SQL."</div>";

}


else if (isset($_POST['user_id']) && isset($_POST["action"]) && $_POST["action"]=='users_assets' && is_it_valid_submit()){// modify user's assets
if (!isset($_SESSION['MODIFY_USER']))
lm_die(gettext("You have no permission!"));

$SQL="UPDATE users SET users_assets='".$dba->escapeStr($_POST["users_assets_json"])."'";
$SQL.=" WHERE user_id=".(int) $_POST['user_id'];
if ($dba->Query($SQL))
lm_info("The assets belong to user has been modified.");
else
lm_info("Failed to modified user's assets.");


 $SQL="SELECT asset_id,assets_users FROM assets WHERE asset_parent_id=0";           
 $result=$dba->Select($SQL); 
 
  foreach($result as $row)           
            {
            if (!isset($row['assets_users']))
            $assets_users=array();
            else
            $assets_users=json_decode($row['assets_users'],true);   
            
            
            if (in_array($row['asset_id'],$users_assets) && (empty($assets_users) || !in_array((int) $_POST['user_id'],$assets_users))) 
            
            $assets_users[]=(int) $_POST['user_id'];
            
            else if (!in_array($row['asset_id'],$users_assets) && in_array((int) $_POST['user_id'],$assets_users))
            
            $assets_users=array_merge(array_diff($assets_users,(int) $_POST['user_id']));
            
            array_unique($assets_users);
            asort($assets_users);
            
            $assets_users=json_encode($assets_users,true);
            $SQL="UPDATE assets SET assets_users='".$assets_users."' WHERE asset_id=".$row['asset_id'];
            
            $result=$dba->Query($SQL);
            }

//repair the assets_users column
/*
$SQL="SELECT user_id,users_assets FROM users";
 $res=$dba->Select($SQL);
 foreach ($res as $r)
 {
$users_assets=json_decode($r["users_assets"],true);
 
 $SQL="SELECT asset_id,assets_users FROM assets WHERE asset_parent_id=0";           
 $result=$dba->Select($SQL); 
 
  foreach($result as $row)           
            {
            
            if (!isset($row['assets_users']))
            $assets_users=array();
            else
            $assets_users=json_decode($row['assets_users'],true);   
            
            
            if (in_array($row['asset_id'],$users_assets) && (empty($assets_users) || !in_array($r['user_id'],$assets_users))) 
            
            $assets_users[]=(int) $r['user_id'];
            
            else if (!in_array($row['asset_id'],$users_assets) && in_array((int) $r['user_id'],$assets_users))
            
            $assets_users=array_merge(array_diff($assets_users,(int) $r['user_id']));
            
            array_unique($assets_users);
            asort($assets_users);
            
            $assets_users=json_encode($assets_users,true);
            $SQL="UPDATE assets SET assets_users='".$assets_users."' WHERE asset_id=".$row['asset_id'];
            
            $result=$dba->Query($SQL);
            }
            
  }*/          
}

else if (isset($_POST['user_id']) && ENTRY_ACCESS_CONTROL && isset($_POST["action"]) && $_POST["action"]=='users_entry_points' && is_it_valid_submit()){// modify user's assets
if (!isset($_SESSION['MODIFY_USER']))
lm_die(gettext("You have no permission!"));


$SQL="UPDATE users SET users_entry_points='".$dba->escapeStr($_POST['users_entry_points_json'])."'";
$SQL.=" WHERE user_id=".(int) $_POST['user_id'];
if (LM_DEBUG)
error_log($SQL,0);
if ($dba->Query($SQL))
lm_info("The entry points belong to user has been modified.");
else
lm_info("Failed to modified user's entry points.");

$users_entry_points=json_decode($_POST["users_entry_points_json"],true);
         
 $SQL="SELECT asset_id,assets_entry_users FROM assets WHERE entry_point=1";           
 $result=$dba->Select($SQL); 
 
 $assets_entry_users=array();
  foreach($result as $row)           
            {
            
            if (!empty($row['assets_entry_users']))
            $assets_entry_users=json_decode($row['assets_entry_users'],true);   
            
            
            if (in_array($row['asset_id'],$users_entry_points)) 
            {
           
            $assets_entry_users[]=(int) $_POST['user_id'];
            }
            else
            $assets_entry_users=array_merge(array_diff($assets_entry_users,(int) $_POST['user_id']));
            $assets_entry_users=json_encode(array_unique($assets_entry_users));
            $SQL="UPDATE assets SET assets_entry_users='".$assets_entry_users."' WHERE asset_id=".$row['asset_id'];
            
            $result=$dba->Query($SQL);
            if (LM_DEBUG)
            error_log($SQL,0);
            }


}




if (isset($_GET["new"]) || (isset($_GET["modify"]))){
?>
<div class="card">
<div class="card-header">
<strong><?php 
if (isset($_GET["new"]))
echo gettext("New user");
else{
$SQL="SELECT * FROM users WHERE user_id=".(int) $_SESSION['user_id'];
$row_mod=$dba->getRow($SQL);
if (empty($row_mod))
lm_die(gettext('Something went wrong...'));
echo gettext("Modify user");

}

?></strong>
</div><?php //card header ?>
<div class="card-body card-block">
<form action="index.php" id="user_form" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php
if (!isset($_SESSION['ADD_USER']) || !isset($_SESSION['MODIFY_USER']))
lm_die(gettext("You have no permission!"));
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"user_parent_id\" class=\" form-control-label\">".gettext("Report to:")."</label></div>";

    echo "<div class=\"col col-md-2\">";
    echo "<select name=\"user_parent_id\" id=\"user_parent_id\" class=\"form-control\">\n";
    $SQL="SELECT user_id,firstname,surname FROM users";
    $SQL.=" ORDER BY surname";
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
            if (FIRSTNAME_IS_FIRST){
            echo "<option value=\"".$row["user_id"]."\" ";
                    if(isset($_GET["modify"]) && $row_mod['user_parent_id']==$row['user_id'])
                        echo "selected";
            echo ">".$row["firstname"]." ".$row["surname"]."</option>\n";
            }
            else{
            echo "<option value=\"".$row["user_id"]."\" ";
                if(isset($_GET["modify"]) && $row_mod['user_parent_id']==$row['user_id'])
                    echo "selected";
            echo ">".$row["surname"]." ".$row["firstname"]."</option>\n";
            }
    }
    echo "</select></div></div>";
  

  echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"user_level\" class=\" form-control-label\">".gettext("User level:")."</label></div>";

    echo "<div class=\"col col-md-2\">";
    echo "<select name=\"user_level\" id=\"user_level\" class=\"form-control\" required>\n";
    $SQL="SELECT user_level_id,user_level_".$lang." FROM user_levels ORDER BY user_level_id";
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
    echo "<option value=\"".$row["user_level_id"]."\" ";
    if (isset($_GET['modify']) && $row_mod['user_level']==$row['user_level_id'])
    echo "selected";
    echo ">".$row["user_level_".$lang]."</option>\n";
   
    }
    echo "</select></div></div>";
   
  
  
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"firstname\" class=\"form-control-label\">".gettext("Firstname:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"firstname\" name=\"firstname\" placeholder=\"".gettext("Firstname")."\" class=\"form-control\" ";
if (isset($_GET['modify']))
echo " VALUE=\"".$row_mod["firstname"]."\"";
echo " required></div>\n";
echo "</div>";
  
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"surname\" class=\"form-control-label\">".gettext("Surname:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"surname\" name=\"surname\" placeholder=\"".gettext("Surname")."\" class=\"form-control\" ";
if (isset($_GET['modify']))
echo " VALUE=\"".$row_mod["surname"]."\"";
echo " required></div>\n";
echo "</div>";

echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"username\" class=\"form-control-label\">".gettext("Username:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"username\" name=\"username\" placeholder=\"".gettext("Username")."\" class=\"form-control\"";
if (isset($_GET['modify']))
echo " disabled VALUE=\"".$row_mod["username"]."\"";
echo " required></div>\n";
echo "</div>";

echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"user_lang\" class=\" form-control-label\">".gettext("User language:")."</label></div>";

    echo "<div class=\"col col-md-2\">";
    echo "<select name=\"lang\" id=\"lang\" class=\"form-control\" required>\n";
    
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"\">".gettext("Please select")."</option>\n";
    foreach ($valid_languages as $user_lang_text=>$user_lang){
    echo "<option value=\"".$user_lang."\" ";
    if (isset($_GET['modify']) && $row_mod["lang"]==$user_lang)
    echo "selected";
    echo ">".$user_lang_text."</option>\n";
   
    }
echo "</select></div></div>";

echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"user_email\" class=\"form-control-label\">".gettext("Email:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"user_email\" name=\"user_email\" placeholder=\"".gettext("Email")."\" class=\"form-control\"";
if (isset($_GET['modify']))
echo " VALUE=\"".$row_mod["user_email"]."\"";
echo "></div>\n";
echo "</div>";

echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"user_phone\" class=\"form-control-label\">".gettext("Phone:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"user_phone\" name=\"user_phone\" placeholder=\"".gettext("Phone")."\" class=\"form-control\"";
if (isset($_GET['modify']))
echo " VALUE=\"".$row_mod["user_phone"]."\"";
echo "></div>\n";
echo "</div>";
echo "<INPUT TYPE=\"hidden\" name=\"page\" id=\"page\" value=\"users\">";
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
if (isset($_GET['modify']))
echo "<INPUT TYPE=\"hidden\" name=\"modify\" id=\"modify\" value=\"1\">";
else
echo "<INPUT TYPE=\"hidden\" name=\"new\" id=\"new\" value=\"1\">";

echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext("Submit")."</button>\n";
echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i>".gettext("Reset")."</button></div>\n";
echo "</form></div>";
}
if (isset($_GET["new"]) || isset($_GET['modify']))
{
echo "<script>\n";
echo "$(\"#user_form\").validate(
{
  rules: {";
  
  echo  "username: {
        required: true,
        maxlength: ".$dba->get_max_fieldlength('users','username')."
    }
    surname: {
        required: true,
        maxlength: ".$dba->get_max_fieldlength('users','surname')."
    }
    firstname: {
        required: true,
        maxlength: ".$dba->get_max_fieldlength('users','firstname')."
    }
}
}
)\n";
echo "</script>\n";
}


if (isset($_SESSION['SEE_USERS'])){
$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;
$from=1;
$SQL="SELECT user_id,username,firstname,surname,user_phone,user_email,user_level,firstname_is_first FROM users ORDER BY surname";
$result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
$from=($pagenumber-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log("page:".$pagenumber." ".$SQL,0);

?>
<div id='for_ajaxcall'>
</div>
<div class="card-body">
<table id="bootstrap-data-table" class="table table-striped table-bordered">
<thead>
<tr>
<th></th>
<?php echo "<th>".gettext("Username")."</th><th>".gettext("Name")."</th><th>".gettext("Email")."</th><th>".gettext("Phone")."</th><th>".gettext("User level")."</th></tr>";
?>
</thead>
<tbody>
<?php
foreach ($result as $row)
{
$from++;
echo "<tr><td>".$from;

  if (isset($_SESSION["SEE_USER_DETAIL"])){
                            echo " <a href=\"javascript:ajax_call('show_user_detail','".$row['user_id']."','users','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i></a>";
                            
                            echo " <a href=\"javascript:ajax_call('show_users_assets','".$row['user_id']."','users','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-bell\"></i></a>";
                            
                            if (ENTRY_ACCESS_CONTROL)
                            echo " <a href=\"javascript:ajax_call('show_users_entry_points','".$row['user_id']."','users','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-sign-in\"></i></a>";
                            
                            echo " <a href=\"javascript:ajax_call('show_users_office-hours','".$row['user_id']."','users','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-clock-o\"></i></a>";
                          }
echo "</td><td>".$row['username']."</td>\n";
if ($row["firstname_is_first"])
echo "<td>".$row['firstname']." ".$row['surname']."</td>\n";
else
echo "<td>".$row['surname']." ".$row['firstname']."</td>\n";

echo "<td>".$row['user_email']."</td>\n";
echo "<td>".$row['user_phone']."</td>\n";
echo "<td>".get_user_level_from_id($row['user_level'])."</td></tr>\n";

}
echo "</tbody></table></div>";
include(INCLUDES_PATH."pagination.php");
}
else
echo gettext("You have no permission!");
?>




