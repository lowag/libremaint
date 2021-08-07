
<div id='for_ajaxcall'>
</div>
<?php
if (isset($_POST['location_parent_id']) && isset($_POST['location_name_en']))
{
$SQL="INSERT INTO locations (location_name_en,";
if ($lang!='en')
$SQL.="location_name_".$lang.",";
$SQL.="location_parent_id) VALUES ('".$dba->escapeStr($_POST['location_name_en'])."',";
if ($lang!='en')
$SQL.="'".$dba->escapeStr($_POST['location_name_'.$lang])."',";
$SQL.="'".(int) $_POST['location_parent_id']."')";
if ($dba->Query($SQL))
        echo "<div class=\"card\">".gettext("The new location has been saved.")."</div>";
        else
        echo "<div class=\"card\">".gettext("Failed to save new location ").$dba->err_msg."</div>";
if (LM_DEBUG)
error_log($SQL,0);
}

else if (isset($_POST['location_id']) && $_POST["location_id"]>0 && isset($_FILES['info_file_name']['tmp_name']) ){ //it is from the new file form
 
$table="locations";
$id=$_POST["location_id"];
$id_column="location_id";
    require(INCLUDES_PATH."file_upload.php"); 

}

else if (isset($_GET['set_as_stock']) && isset($_GET["location_id"])){ 
 $SQL="UPDATE locations SET set_as_stock=".(int) $_GET['set_as_stock']." WHERE location_id='".(int) $_GET['location_id']."'";
 if ($dba->Query($SQL)){
        if ($_GET['set_as_stock']==1)
        echo "<div class=\"card\">".gettext("The location has set as stock.")."</div>";
        else if ($_GET['set_as_stock']==0){
        echo "<div class=\"card\">".gettext("The location has unset as stock.")."</div>";
        
        
        }
        else
        echo "<div class=\"card\">".gettext("Failed to set as stock ").$dba->err_msg."</div>";

        }
  else
        echo "<div class=\"card\">".gettext("Failed to set as stock ").$dba->err_msg."</div>";

 }


else if (isset($_POST['page']) && isset($_POST["new_name_".$lang]) && !empty($_POST["new_name_".$lang])){ //it is from the rename asset form
    $SQL="UPDATE locations SET location_name_".$lang."='".$_POST["new_name_".$lang]."'";
    if (isset($_POST['new_name_en']) && !empty($_POST['new_name_en']))
    $SQL.=",location_name_en='".$dba->escapeStr($_POST["new_name_en"])."'";
   
    $SQL.=" WHERE location_id='".$_POST["location_id"]."'";
    if (LM_DEBUG)
        error_log($SQL,0); 
    if ($dba->Query($SQL))
        echo "<div class=\"card\">".gettext("The location has been renamed.")."</div>";
        else
        echo "<div class=\"card\">".gettext("Failed to rename location ").$dba->err_msg."</div>";
}
else if (isset($_GET["new"])){
?>

<div class="card">
<div class="card-header">
<strong><?php echo gettext("New location");?></strong>
</div><?php //card header ?>
<div class="card-body card-block">
<form action="index.php" id="location_form" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php

    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-3\"><label for=\"location_parent_id\" class=\" form-control-label\">".gettext("Parent location:")."</label></div>";

    echo "<div class=\"col col-md-2\">";
    echo "<select name=\"location_parent_id\" id=\"location_parent_id\" class=\"form-control\">\n";
    $SQL="SELECT location_id, location_name_en, location_name_".$lang." FROM locations WHERE";
    if (isset($_GET['parent_id']))
    $SQL.=" location_id='".(int) $_GET['parent_id']."'";
    else
    $SQL.=" location_parent_id=0";
    $SQL.=" ORDER BY location_name_".$lang;
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
    if (!empty($row["location_name_".$lang]))
    {
    echo "<option value=\"".$row["location_id"]."\"";
    if (isset($_GET['parent_id']) && $_GET['parent_id']==$row["location_id"])
    echo " selected";
    echo ">".$row["location_name_".$lang]."</option>\n";
     }
    else
    {
    echo "<option value=\"".$row["location_id"]."\"";
    if (isset($_GET['parent_id']) && $_GET['parent_id']==$row["location_id"])
    echo " selected";
    echo ">".$row["location_name_en"]."</option>\n";
    }
    }
    echo "</select></div></div>";
  
  
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"location_name_en\" class=\"form-control-label\">".gettext("Location name (English):")."</label></div>\n";
echo "<div class=\"col-12 col-md-9\"><input type=\"text\" id=\"location_name_en\" name=\"location_name_en\" placeholder=\"".gettext("Location name (English)")."\" class=\"form-control\" required><small class=\"form-text text-muted\">".gettext("Location name")."</small></div>\n";
echo "</div>";

if ($lang!="en"){
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"location_name_".$lang."\" class=\"form-control-label\">".gettext("Name:")."</label></div>\n";
echo "<div class=\"col-12 col-md-9\"><input type=\"text\" id=\"location_name_".$lang."\" name=\"location_name_".$lang."\" placeholder=\"".gettext("Name")."\" class=\"form-control\" required><small class=\"form-text text-muted\">".gettext("Location name")."</small></div>\n";
echo "</div>";

}
?>
</div><?php //card-body card-block  ?>
<div class="card-footer">
<button type="submit" class="btn btn-primary btn-sm">
<i class="fa fa-dot-circle-o"></i><?php echo gettext(" Submit ");?>
</button>
<button type="reset" class="btn btn-danger btn-sm">
<i class="fa fa-ban"></i><?php echo gettext(" Reset ");?>
</button>
</div>
<input type="hidden" name="page" id="page" value="locations">
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $_GET["parent_id"];?>">
</form>
</div>
<?php //card  
echo "<script>\n";
echo "$(\"#location_form\").validate()\n";
echo "</script>\n";
}//if (isset($_GET["new"]))

include(INCLUDES_PATH."show_locations_tree_menu.php");
include(INCLUDES_PATH."location_tree.php");
?>
