
<div class='card' id='for_ajaxcall'>
</div>
<?php  

$asset_tree_has_changed=array();
$asset_id=lm_isset_int('asset_id'); //the $asset_id necessary for opening the required nodes in asset_tree.php
$main_asset_location_id=lm_isset_int('main_asset_location_id');
if (isset($_POST['asset_id']) && (int) $_POST["asset_id"]>0 && isset($_POST['counter_unit']) && is_it_valid_submit() && isset($_SESSION['ADD_COUNTER'])){ 

$asset_tree_has_changed[]=get_whole_path('asset',(int) $_POST['asset_id'],1)[0];

$SQL="INSERT INTO counters (main_asset_id,asset_id,counter_unit) VALUES ('".get_whole_path('asset',(int) $_POST['asset_id'],1)[0]."','".(int) $_POST['asset_id']."','".(int) $_POST['counter_unit']."')";
if ($dba->Query($SQL)){
            lm_info(gettext("The counter has been attached to the asset."));
            $has_written=true;
            //$asset_tree_has_changed=true;
            }
            else
            lm_error(gettext("Failed to attach counter to the asset ").$dba->err_msg);
       


}




if (isset($_POST['asset_id']) && (int) $_POST["asset_id"]>0 && isset($_FILES['info_file_name']['tmp_name']) && is_it_valid_submit() && isset($_SESSION['ADD_FILE_TO_ASSET'])){ //it is from the new file form
 
$table="assets";
$id=$asset_id;
$id_column="asset_id";
    require(INCLUDES_PATH."file_upload.php"); 
//$asset_tree_has_changed=true;
$asset_tree_has_changed[]=get_whole_path('asset',(int) $_POST['asset_id'],1)[0];
}



else if (isset($_POST['asset_id']) && isset($_POST["connection_id"]) && is_it_valid_submit() && isset($_SESSION['MODIFY_ASSET'])){// add new connection form
$SQL="SELECT * FROM assets WHERE asset_id='".(int) $_POST["asset_id"]."'";
if (LM_DEBUG)
            error_log($SQL,0); 
$row=$dba->getRow($SQL);
$has_written=false;
$conn_number=0;
foreach($row as $key=>$value) //has it already append?
    {
       	if (strstr($key,"connection_id") && $value==$_POST["connection_id"])
       	{
       	$has_written=true;
       	}
    }   	

if (!$has_written) 
    {
foreach($row as $key=>$value)
    {
       	if (strstr($key,"connection_id"))
       	{
       	$conn_number++;
       	if(0==$value && !$has_written){
        $SQL="UPDATE assets SET ".$key."='".(int) $_POST['connection_id']."', connection_type".substr($key,13)."=".(int) $_POST['connection_type']." WHERE asset_id='".(int) $_POST['asset_id']."'";
        if (LM_DEBUG)
            error_log($SQL,0); 
        if ($dba->Query($SQL)){
            lm_info(gettext("The connection has been attached to the asset."));
            $has_written=true;
            //$asset_tree_has_changed=true;
            
$asset_tree_has_changed[]=get_whole_path('asset',(int) $_POST['asset_id'],1)[0];
            }
            else
            lm_error(gettext("Failed to attach connection to the asset ").$SQL);
        }}
    }
}
if (!$has_written) 
    {
    $SQL="ALTER TABLE assets ADD COLUMN connection_id".++$conn_number." SMALLINT(3) UNSIGNED NULL,ADD COLUMN connection_type".$conn_number." SMALLINT(3) UNSIGNED NULL";
    if (LM_DEBUG)
            error_log($SQL,0); 
        if ($dba->Query($SQL))
        {
        $SQL="UPDATE assets SET connection_id".$conn_number."='".(int) $_POST['connection_id']."',connection_type".$conn_number."='".$_POST['connection_type']."' WHERE asset_id='".(int) $_POST['asset_id']."'";
        if (LM_DEBUG)
            error_log($SQL,0); 
        if ($dba->Query($SQL)){
            echo "<div class=\"card\">".gettext("The connection has been attached to the asset.")."</div>";
            $has_written=true;
            
$asset_tree_has_changed[]=get_whole_path('asset',(int) $_POST['asset_id'],1)[0];
            }
            else
            echo "<div class=\"card\">".gettext("Failed to attach connection to the asset ").$SQL."</div>";
        
        }else
        echo "<div class=\"card\">".gettext("Something went wrong").$SQL."</div>";
    }
    
    
}



else if ((isset($_POST['modal_product_type_'.LANG1]) && $_POST['modal_product_type_'.LANG1]!="") || (isset($_POST['modal_product_type_'.LANG2]) && $_POST['modal_product_type_'.LANG2]!="") && is_it_valid_submit() && isset($_SESSION['ADD_PRODUCT'])){// add new product from modal_add_new_product_form.php
if (!empty($dba->escapeStr($_POST['modal_new_manufacturer']))){
$SQL="INSERT INTO manufacturers (manufacturer_name) VALUES ('".$dba->escapeStr($_POST['modal_new_manufacturer'])."')";
if ($dba->Query($SQL)){
lm_info(gettext("The new manufacturer has been added."));
$manufacturer_id=$dba->insertedId();
}
else
lm_error(gettext("Failed to save new manufacturer.")." ".$dba->err_msg);
}

    $SQL="INSERT INTO products (category_id,subcategory_id";
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    $SQL.=",product_type_".LANG2;
    
     if (isset($_SESSION['CAN_WRITE_LANG1']))
    $SQL.=",product_type_".LANG1;
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    $SQL.=",product_properties_".LANG2;
    
    if (isset($_SESSION['CAN_WRITE_LANG1']))
    $SQL.=",product_properties_".LANG1;
    
    $SQL.=",manufacturer_id,product_stockable,quantity_unit,display) VALUES (";
    $SQL.=(int) $_POST['modal_category_id'].",";
    $SQL.=(int) $_POST['modal_subcategory_id'].",";
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    $SQL.="'".$dba->escapeStr($_POST['modal_product_type_'.LANG2])."',";
     if (isset($_SESSION['CAN_WRITE_LANG1']))
    $SQL.="'".$dba->escapeStr($_POST['modal_product_type_'.LANG1])."',";
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    $SQL.="'".$dba->escapeStr($_POST['modal_product_properties_'.LANG2])."',";
    
    if (isset($_SESSION['CAN_WRITE_LANG1']))
    $SQL.="'".$dba->escapeStr($_POST['modal_product_properties_'.LANG1])."',";
    
    if (!empty($_POST['modal_new_manufacturer']))
    $SQL.=$manufacturer_id.",";
    else
    $SQL.=(int) $_POST['modal_manufacturer_id'].",";
    $SQL.=(int) $_POST['modal_product_stockable'].",";
    $SQL.=(int) $_POST['modal_quantity_unit'].",";
    $display=0;

    foreach ($_POST["display"] as $key =>$value){
    $display+=$value;
    }
    $SQL.=$display.")";
    if (LM_DEBUG)
            error_log($SQL,0); 
        if ($dba->Query($SQL))
        {   $asset_product_id=$dba->insertedId();
            if ((int)$_POST['modal_product_stockable']==2){//unique product
            $SQL="INSERT INTO stock (product_id,product_category_id,product_subcategory_id,stock_location_id,stock_location_asset_id,stock_location_partner_id,stock_quantity,item_created) VALUES ";
            $SQL.="(".$asset_product_id.",".(int) $_POST['modal_category_id'].",".(int) $_POST['modal_subcategory_id'].",0,".(int) $_POST['modal_asset_id'].",0,1,now())";
            if (LM_DEBUG)
            error_log($SQL,0); 
            $dba->Query($SQL);
            }
            $SQL="UPDATE assets SET asset_product_id='".$asset_product_id."'  WHERE asset_id='".(int) $_POST['modal_asset_id']."'";
            if (LM_DEBUG)
            error_log($SQL,0); 
          
            if ($dba->Query($SQL)){
            lm_info(gettext("The product has been attached to the asset."));
            
$asset_tree_has_changed[]=get_whole_path('asset',(int) $_POST['modal_asset_id'],1)[0];
            }
            else
            lm_error(gettext("Failed to attach product to the asset ").$SQL);  
            
            }
            else
            lm_error(gettext("Failed to save new product ").$SQL);


            

}



else if (isset($_POST['product_id']) && isset($_POST['asset_id']) && is_it_valid_submit() && isset($_SESSION['MODIFY_ASSET'])){

$SQL="UPDATE assets SET asset_product_id=".(int) $_POST['product_id']."  WHERE asset_id=".(int) $_POST['asset_id'];
if (LM_DEBUG)
        error_log($SQL,0); 
    if ($dba->Query($SQL)){
    $SQL="SELECT product_stockable FROM products WHERE product_id=".(int) $_POST['product_id'];
    $row=$dba->getRow($SQL);
        if ($row['product_stockable']==2){ //unique
        $SQL="UPDATE stock SET stock_location_asset_id=".(int) $_POST['asset_id'].", stock_location_id=0,stock_location_partner_id=0 WHERE product_id=".(int) $_POST['product_id'];
        $dba->Query($SQL);
        }
        lm_info(gettext("The product has been attached to the asset."));
        
$asset_tree_has_changed[]=get_whole_path('asset',(int) $_POST['asset_id'],1)[0];
        }
        else
        lm_error(gettext("Failed to attach product to the asset ").$SQL);

}




else if (isset($_POST['new']) && isset($_POST["asset_name_".$lang])  && is_it_valid_submit() && isset($_SESSION['ADD_ASSET'])){ //it is from the new asset form
    $SQL="INSERT INTO assets (";
     if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.="asset_name_".LANG1.",";
    
    $SQL.="main_asset_category_id,grouped_asset,grouped_asset_id,entry_point,asset_importance,";
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    $SQL.="asset_name_".LANG2.",";
    $SQL.="main_part,asset_parent_id,asset_location,asset_article,asset_note,asset_note_conf) VALUES (";
     if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.="'".$dba->escapeStr($_POST["asset_name_".LANG1])."',";
    
    $SQL.=(int) $_POST['main_asset_category_id'].",".(int) $_POST['grouped_asset'].",".(int) $_POST['grouped_asset_id'].",".(int)$_POST['entry_point'].",".(int)$_POST['asset_importance'].",";
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    $SQL.="'".$dba->escapeStr($_POST["asset_name_".LANG2])."',";
    
    $SQL.=(int) $_POST["main_part"].",".(int) $_POST["parent_id"].",".(int) $_POST["asset_location"].",'".$dba->escapeStr($_POST['asset_article']);
    $SQL.="','".$dba->escapeStr($_POST['asset_note']);
    $SQL.="','".$dba->escapeStr($_POST['asset_note_conf']);
    $SQL.="')";
    if (LM_DEBUG)
        error_log($SQL,0); 
    if ($dba->Query($SQL)){
        $asset_id=$dba->insertedId();//the $asset_id necessary for opening the required nodes in asset_tree.php
        lm_info(gettext("The new asset has been saved."));
        
$asset_tree_has_changed[]=get_whole_path('asset',(int) $asset_id,1)[0];
        }
        else
        lm_error(gettext("Failed to save new asset ").$dba->err_msg);

}
else if (isset($_POST['modify']) && isset($_POST["asset_id"]) && is_it_valid_submit() && isset($_SESSION['MODIFY_ASSET'])){
if(ENTRY_ACCESS_CONTROL){
$SQL="SELECT entry_point FROM assets WHERE asset_id=".(int) $_POST['asset_id'];
$row=$dba->getRow($SQL);
}

$SQL="UPDATE assets SET ";
if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
$SQL.="asset_name_".LANG2."='".$dba->escapeStr($_POST["asset_name_".LANG2])."',";
if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.="asset_name_".LANG1."='".$dba->escapeStr($_POST["asset_name_".LANG1])."',";
$SQL.="main_asset_category_id=".(int) $_POST['main_asset_category_id'].",";
$SQL.="grouped_asset=".(int) $_POST['grouped_asset'].",";
$SQL.="entry_point=".(int) $_POST['entry_point'].",";
$SQL.="asset_importance=".(int) $_POST['asset_importance'].",";
$SQL.="grouped_asset_id=".(int) $_POST['grouped_asset_id'].",";
$SQL.="main_part=".(int) $_POST['main_part'].",";
$SQL.="asset_parent_id=".(int) $_POST['asset_parent_id'].",";
$SQL.="asset_location=".(int) $_POST['asset_location'].",";
$SQL.="asset_article='".$dba->escapeStr($_POST['asset_article'])."',";
$SQL.="asset_note='".$dba->escapeStr($_POST['asset_note'])."',";
$SQL.="asset_note_conf='".$dba->escapeStr($_POST['asset_note_conf'])."'";

$SQL.=" WHERE asset_id=".(int) $_POST["asset_id"];
 if ($dba->Query($SQL)){
        if(ENTRY_ACCESS_CONTROL && $row['entry_point']!=(int) $_POST['entry_point'] && $_POST['entry_point']==0){//it isn't an entry point so we need to remove it from all users
        
        
        }
        if (LM_DEBUG)
        error_log($SQL,0); 
        lm_info(gettext("The asset has been modified."));
        
$asset_tree_has_changed[]=get_whole_path('asset',(int) $_POST['asset_id'],1)[0];
        }
        else
        lm_error(gettext("Failed to modify asset.").$dba->err_msg);
}


else if (isset($_POST['page']) && isset($_POST["new_name_".$lang]) && !empty($_POST["new_name_".$lang]) && is_it_valid_submit() && isset($_SESSION['MODIFY_ASSET'])){ //it is from the rename asset form
    $SQL="UPDATE assets SET ";
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.="asset_name_".LANG1."='".$dba->escapeStr($_POST["new_name_".LANG1])."'";
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']) && isset($_POST['new_name_'.LANG2]) && !empty($_POST['new_name_'.LANG2]))
        if (isset($_SESSION['CAN_WRITE_LANG1']))
        $SQL.=",";
    $SQL.="asset_name_".LANG2."='".$dba->escapeStr($_POST["new_name_".LANG2])."'";
    
    $SQL.=" WHERE asset_id=".(int) $_POST["asset_id"];
    if (LM_DEBUG)
        error_log($SQL,0); 
    if ($dba->Query($SQL)){
        lm_info(gettext("The asset has been renamed."));
        
$asset_tree_has_changed[]=get_whole_path('asset',(int) $_POST['asset_id'],1)[0];
        }
        else
        lm_error(gettext("Failed to rename asset ").$dba->err_msg);
}

else if (isset($_POST['page']) && isset($_POST["category"]) && is_it_valid_submit() && isset($_SESSION['MODIFY_ASSET'])){ //it is from the add category form
    $SQL="UPDATE assets SET asset_category_id=".(int) $_POST["category"];
    if (isset($_POST['subcategory']))
    $SQL.=",asset_subcategory_id=".(int) $_POST["subcategory"];
    $SQL.=", asset_product_id=0 WHERE asset_id=". (int) $_POST["asset_id"];
    if (LM_DEBUG)
        error_log($SQL,0); 
    if ($dba->Query($SQL)){
        lm_info(gettext("A new category has been added to the asset."));
        
$asset_tree_has_changed[]=get_whole_path('asset',(int) $_POST['asset_id'],1)[0];
        }
        else
        lm_error(gettext("Failed to add new category. ").$dba->err_msg);
}
else if (isset($_GET['set_as_main_part']) && isset($_GET["asset_id"])){ 
 $SQL="UPDATE assets SET main_part=".(int) $_GET['set_as_main_part']." WHERE asset_id='".(int) $_GET['asset_id']."'";
 if ($dba->Query($SQL)){
    $asset_tree_has_changed[]=get_whole_path('asset',(int) $_GET['asset_id'],1)[0];
        if ($_GET['set_as_main_part']==1)
        lm_info(gettext("The asset has set as main part."));
        else if ($_GET['set_as_main_part']==0){
        lm_error(gettext("The asset has unset as main part."));
        }
        else
        lm_info(gettext("Failed to set as main part ").$dba->err_msg);

        }
  else
        lm_error(gettext("Failed to set as main part ").$dba->err_msg);

 }








 else if ((isset($_GET["new"]) && isset($_SESSION['ADD_ASSET'])) || (isset($_GET["modify"]) && isset($_SESSION['MODIFY_ASSET']))){
?>

<div class="card">
<div class="card-header">
<strong><?php 
if (isset($_GET["new"]))
echo gettext("New asset");
else if (isset($_GET["modify"]) && isset($_GET["asset_id"])){
echo gettext("Modify asset");
$SQL="SELECT * FROM assets WHERE asset_id=".(int) $_GET["asset_id"];
$asset_row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
}
?></strong>
</div><?php //card header ?>
<div class="card-body card-block">
<form action="index.php" id="asset_form" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php

if ((isset($_GET["new"]) && $_GET["new"]=="part" && isset($_GET["parent_id"]) && $_GET["parent_id"]>0) || (isset($_GET["modify"]) && $asset_row["asset_parent_id"]>0)){

if (isset($_GET["modify"]))
$SQL="SELECT asset_id,asset_parent_id,asset_name_".$lang." FROM assets WHERE asset_id=".$asset_row["asset_parent_id"];

else
$SQL="SELECT asset_id,asset_parent_id,asset_name_".$lang." FROM assets WHERE asset_id=".(int) $_GET["parent_id"];

$row=$dba->getRow($SQL);

$parent=$row["asset_name_".$lang];

if (LM_DEBUG)
error_log($SQL,0); 

?>
<div class="row form-group">
<div class="col col-md-3"><label for="asset_parent_id" class=" form-control-label"><?php echo gettext("Parent name:");?></label></div>
<div class="col-12 col-md-9"><input type="text" id="asset_parent_name" name="asset_parent_name" class="form-control" value="<?php 

echo $parent;?>" disabled=""><small class="form-text text-muted" ></div>
</div>

<?php
echo "<input type='hidden' name='main_asset_category_id' id='main_asset_category_id' value=''>";
echo "<input type='hidden' name='asset_location' id='asset_location' value=''>";
}else {
if (isset($_GET["modify"])){
if ($asset_row["asset_location"]>0)
$main_location=get_whole_path_ids("location",$asset_row["asset_location"],1);
else
$main_location=0;
}

?>
<div class="row form-group">
<div class="col col-md-3"><label for="location_id" class="form-control-label"><?php echo gettext("Establishment:");?></label></div>
<div class="col-12 col-md-4">
<?php echo "<select name=\"location_id\" id=\"location_id\" class=\"form-control\" onChange=\"ajax_call('locations',this.value,0,'','','".URL."index.php','location')\">";

$SQL="SELECT location_name_".$lang.", location_id FROM locations WHERE location_parent_id=0";
$result=$dba->Select($SQL);
echo "<option value=\"0\">".gettext("Please select")."</option>\n";
foreach ($result as $row){
echo "<option value=\"".$row["location_id"]."\"";
if (isset($asset_row) && $main_location!=0 && $main_location[0]==$row["location_id"])
echo " selected";
echo ">".$row["location_name_".$lang]."</option>\n";
}
?>
</select></div>
</div>

<div class="row form-group">
<div class="col col-md-3"><label for="select" class=" form-control-label"><?php echo gettext("Location:");?></label></div>
<div class="col-12 col-md-4" id="location">
<select name="asset_location" id="asset_location" class="form-control">
<?php
if (isset($_GET["modify"])){
    if ($main_location!=0){
    $sel=get_whole_path_for_select("location",$main_location[0],1);
    echo "<option value='0'>".gettext("Please select!");
    foreach ($sel as $opt){
    echo "<option value='".$opt[0]."'";
    if ($opt[0]==$asset_row["asset_location"])
    echo " selected";
    echo ">".$opt[1];
    }
    }
    else
    echo "<option value='' disabled>".gettext("No location");
}
?>
</select></div>

</div>

<div class="row form-group">
<div class="col col-md-3"><label for="main_asset_category_id" class="form-control-label"><?php echo gettext("Asset category:");?></label></div>
<div class="col-12 col-md-4">
<?php 
echo "<select name=\"main_asset_category_id\" id=\"main_asset_category_id\" class=\"form-control\" >";

$SQL="SELECT main_asset_category_".$lang.", main_asset_category_id FROM main_asset_categories ORDER BY main_asset_category_".$lang;
$result=$dba->Select($SQL);
echo "<option value=\"0\">".gettext("Please select")."</option>\n";
foreach ($result as $row){
echo "<option value=\"".$row["main_asset_category_id"]."\"";
if (isset($_GET["modify"]) && $row["main_asset_category_id"]==$asset_row["main_asset_category_id"])
echo " selected";
echo ">".$row["main_asset_category_".$lang]."</option>\n";


}

?>
</select></div>
</div>
<?php
}

if ($_SESSION['CAN_WRITE_LANG1']){
echo "<div class=\"row form-group\">\n";
echo "<div class=\"col col-md-3\"><label for=\"asset_name_".LANG1."\" class=\"form-control-label\">\n";
echo gettext("Name:");
echo "</label></div>\n";
echo "<div class=\"col-12 col-md-9\"><input type=\"text\" id=\"asset_name_".LANG1."\" name=\"asset_name_".LANG1."\" placeholder=\"".gettext("Name")."\" class=\"form-control\"";
if (isset($_GET['modify']))
echo " value='".$asset_row['asset_name_'.LANG1]."'";
echo " required><small class=\"form-text text-muted\">".gettext("Asset name")."</small></div>\n";
echo "</div>\n";
}

if (isset($_SESSION['CAN_WRITE_LANG2']) && LANG2_AS_SECOND_LANG){
echo "<div class=\"row form-group\">\n";
echo "<div class=\"col col-md-3\"><label for=\"asset_name_".LANG2."\" class=\"form-control-label\">\n";
echo gettext("Name (").LANG2."):";
echo "</label></div>\n";
echo "<div class=\"col-12 col-md-9\"><input type=\"text\" id=\"asset_name_".LANG2."\" name=\"asset_name_".LANG2."\" placeholder=\"".gettext("Name (").LANG2.")\" class=\"form-control\"";
if (isset($_GET['modify']))
echo " value='".$asset_row['asset_name_'.LANG2]."'";
echo " required><small class=\"form-text text-muted\">".gettext("Asset name (").LANG2.")</small></div>\n";
echo "</div>\n";
}

?>

<?php
if ((isset($_GET['new']) && isset($row['asset_parent_id']) && $row['asset_parent_id']==0) || ((isset($_GET['modify']) && isset($asset_row['asset_parent_id']) && $asset_row['asset_parent_id']==0))){
?>
<div class="row form-group">
<div class="col col-md-3"><label for="grouped_asset" class="form-control-label"><?php echo gettext("Grouped:");?></label></div>
<div class="col-12 col-md-4">
<?php echo "<select name=\"grouped_asset\" id=\"grouped_asset\" class=\"form-control\">";

echo "<option value=\"0\"";
if (isset($_GET['modify']) && $asset_row["grouped_asset"]==0)
echo " selected";
echo ">".gettext("No")."</option>\n";
echo "<option value=\"1\"";
if (isset($_GET['modify']) && $asset_row["grouped_asset"]==1)
echo " selected";
echo ">".gettext("Yes")."</option>\n";
echo "</select></div>\n</div>\n";
} else {
if (isset($row['asset_id'])){
$asset_path=get_whole_path("asset",$row['asset_id'],1);
$SQL="SELECT asset_id,asset_name_".$lang." FROM assets WHERE asset_parent_id=".$asset_path[0]." AND grouped_asset=1 ORDER BY asset_name_".$lang;
$groups=$dba->Select($SQL);
if ($dba->affectedRows()>0){
?>
<div class="row form-group">
<div class="col col-md-3"><label for="grouped_asset_id" class="form-control-label"><?php echo gettext("Group:");?></label></div>
<div class="col-12 col-md-4">
<?php echo "<select name=\"grouped_asset_id\" id=\"grouped_asset_id\" class=\"form-control\">";

echo "<option value=\"0\"";
echo ">".gettext("No groups")."</option>\n";
foreach ($groups as $group){
echo "<option value=".$group['asset_id'];
if (isset($_GET['modify']) && $asset_row["grouped_asset_id"]==$group['asset_id'])
echo " selected";
echo ">".$group['asset_name_'.$lang];
}
echo "</select></div>\n</div>\n";
}}else
echo "<INPUT TYPE='hidden' name='grouped_asset_id' id='grouped_asset_id' value=''>";
}

?>
<div class="row form-group">
<div class="col col-md-3"><label for="asset_importance" class="form-control-label"><?php echo gettext("Importance:");?></label></div>
<div class="col-12 col-md-4">
<?php echo "<select name=\"asset_importance\" id=\"asset_importance\" class=\"form-control\">";

echo "<option value=\"0\"";
echo ">".gettext("Select!")."</option>\n";
foreach ($asset_importance as $index=>$importance){
echo "<option value=".++$index;
if (isset($_GET['modify']) && $asset_row["asset_importance"]==$index)
echo " selected";
echo ">".$importance;
}
echo "</select></div>\n</div>\n";
?>

<div class="row form-group">
<div class="col col-md-3"><label for="main_part" class="form-control-label"><?php echo gettext("Main part:");?></label></div>
<div class="col-12 col-md-4">
<?php echo "<select name=\"main_part\" id=\"main_part\" class=\"form-control\">";

echo "<option value=\"0\"";
if (isset($_GET['modify']) && $asset_row["main_part"]==0)
echo " selected";
echo ">".gettext("Not main part")."</option>\n";
echo "<option value=\"1\"";
if (isset($_GET['modify']) && $asset_row["main_part"]==1)
echo " selected";
echo ">".gettext("Main part")."</option>\n";
echo "</select></div>\n</div>\n";

if (ENTRY_ACCESS_CONTROL){
?>
<div class="row form-group">
<div class="col col-md-3"><label for="entry_point" class="form-control-label"><?php echo gettext("Entry point:");?></label></div>
<div class="col-12 col-md-4">
<?php echo "<select name=\"entry_point\" id=\"entry_point\" class=\"form-control\">";

echo "<option value=\"0\"";
if (isset($_GET['modify']) && $asset_row["main_part"]==0)
echo " selected";
echo ">".gettext("Not an entry point")."</option>\n";
echo "<option value=\"1\"";
if (isset($_GET['modify']) && $asset_row["entry_point"]==1)
echo " selected";
echo ">".gettext("Entry point")."</option>\n";
echo "</select></div>\n</div>\n";
}

echo "<div class=\"row form-group\">\n";
echo "<div class=\"col col-md-3\"><label for=\"asset_article\" class=\"form-control-label\">\n";
echo gettext("Asset article:");
echo "</label></div>\n";
echo "<div class=\"col-12 col-md-9\"><input type=\"text\" id=\"asset_article\" name=\"asset_article\" placeholder=\"".gettext("Asset article")."\" class=\"form-control\"";
if (isset($_GET["modify"]))
echo " value='".$asset_row["asset_article"]."'";
echo "><small class=\"form-text text-muted\">".gettext("Asset article")."</small></div>\n";
echo "</div>\n";


echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"asset_note\" class=\" form-control-label\">".gettext("Asset note:")."</label></div>";
echo "<div class=\"col-12 col-md-9\"><textarea name=\"asset_note\" id=\"asset_note\" rows=\"9\" placeholder=\"".gettext("asset note")."\" class=\"form-control\">";
if (isset($_GET['modify']))
echo $asset_row['asset_note'];
echo "</textarea></div>\n";
echo "</div>\n";

echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"asset_note_conf\" class=\" form-control-label\">".gettext("Asset note (conf):")."</label></div>";
echo "<div class=\"col-12 col-md-7\"><textarea name=\"asset_note_conf\" id=\"asset_note_conf\" rows=\"9\" placeholder=\"".gettext("asset note (conf)")."\" class=\"form-control\">";
if (isset($_GET['modify']))
echo $asset_row['asset_note_conf'];
echo "</textarea></div>\n";
echo "</div>\n";


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
<input type="hidden" name="page" id="page" value="assets">

<?php 
if (isset($_GET["parent_id"]))
echo "<input type=\"hidden\" name=\"parent_id\" id=\"parent_id\" value=\"".$_GET["parent_id"]."\">";
else if (isset($_GET["modify"]))
echo "<input type=\"hidden\" name=\"asset_parent_id\" id=\"asset_parent_id\" value=\"".$asset_row["asset_parent_id"]."\">";
if (isset($_GET["modify"]) && isset($_GET["asset_id"]) && $_GET["asset_id"]>0){
echo "<input type=\"hidden\" name=\"asset_id\" id=\"asset_id\" value=\"".$_GET["asset_id"]."\">";

echo "<input type=\"hidden\" name=\"modify\" id=\"modify\" value=\"1\">";

}
if (isset($_GET["new"]))
echo "<input type=\"hidden\" name=\"new\" id=\"new\" value=\"1\">";
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

?>
</form>
</div><?php //card  
echo "<script>\n";
echo "$(\"#asset_form\").validate({
  rules: {";
  if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2'])){
  echo  "asset_name_".LANG2.": {
        required: true,
        maxlength: ".$dba->get_max_fieldlength('assets','asset_name_'.LANG2)."
    }";}
    
    echo ",asset_name_".LANG1.": {
        required: true,
        maxlength: ".$dba->get_max_fieldlength('assets','asset_name_'.LANG1)."
    }
  }
})\n";
echo "</script>\n";
}
//if (isset($_GET["new"]))
echo "<div class=\"card\">\n";
if (isset($_GET["asset_location_id"]) && $_GET["asset_location_id"]>=0)
    $_SESSION['asset_location_id']=$_GET['asset_location_id'];

$SQL="SELECT location_id, location_name_".$lang." FROM locations WHERE location_parent_id=0 ORDER BY location_name_".$lang;
$result=$dba->Select($SQL);
echo "<SELECT name='locations' id='locations' onChange=\"location.href='index.php?page=assets&asset_location_id='+this.value\">\n";
echo "<option value=''>".gettext("All locations");
if (!empty($result))
{
foreach ($result as $row){
    echo "<option value='".$row['location_id']."'";
        if (isset($_SESSION['asset_location_id']) && $_SESSION['asset_location_id']==$row['location_id'])
        echo " selected";
        
    echo ">".$row['location_name_'.$lang]."\n";
    }}
echo "</select>";
echo "</div>\n";


echo "<div id=\"asset_tree\">\n";

include(INCLUDES_PATH."asset_tree.php");
/*
if ($asset_tree_has_changed==true || !file_exists(INCLUDES_PATH."asset_tree.html"))
{

//include(INCLUDES_PATH."show_assets_tree_menu.php");
include(INCLUDES_PATH."asset_tree.php");
}else
{
include(INCLUDES_PATH."asset_tree.html");
}
*/
echo "</div>\n";



?>
