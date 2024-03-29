<?php
if (!isset($_SESSION['logged']))
header("Location: ".PAGES_PATH."bye.php");

if (isset($_GET['param1']) && $_GET['param1']=="is_it_valid_time_period"){
$start_time=new DateTime($dba->escapeStr($_GET['param2']));
$end_time=new DateTime($dba->escapeStr($_GET['param3']));

if (is_it_valid_worktime_period($dba->escapeStr($_GET['param2']),$dba->escapeStr($_GET['param3']),(int) $_GET['param4'],(int) $_GET['param5']) && $end_time>=$start_time)

{
    echo "<button type=\"submit\" id='submit_button' class=\"btn btn-primary btn-sm\">";
    echo "<i class=\"fa fa-dot-circle-o\"></i>";
    echo gettext("Submit");
    echo " </button>\n";
}
else if ($start_time>$end_time)
echo "<div class='bg-flat-color-4'>".gettext("The end time is earlier than the start time!")."</div>";
else 
echo "<div class='bg-flat-color-4'>".gettext("There is a recorded work in this time range.")."</div>";


} 
else if (isset($_GET['param1']) && $_GET['param1']=="is_it_valid_time_period_for_partners"){
echo "<button type=\"submit\" id='submit_button' class=\"btn btn-primary btn-sm\">";
    echo "<i class=\"fa fa-dot-circle-o\"></i>";
    echo gettext("Submit");
    echo " </button>\n";

}


else if (isset($_GET['param1']) && $_GET['param1']=="locations" && $_GET['param2']>0){//new asset form

    echo "<select name=\"asset_location\" id=\"asset_location\" class=\"form-control\">";
    $sel=get_whole_path_for_select("location",$_GET['param2'],1);
     if (LM_DEBUG)
    error_log(print_r($sel),0);
    echo "<option value='0'>".gettext("Please select!");
    foreach ($sel as $opt){
    echo "<option value='".$opt[0]."'>".$opt[1];
    }
    
    /*
    $SQL="SELECT location_id, location_name_".$lang." FROM locations WHERE location_parent_id='".$_GET['param2']."'";
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
    echo "<option value=\"".$row["location_id"]."\">".$row["location_name_".$lang]."</option>\n";
    }
    */
    
    echo "</select>";}
 
 
 
else if(isset($_GET['param1']) && $_GET['param1']=="categories"){//add category to an asset
//param1:"categories",param2:parent category_id, param3: asset_id
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
echo "<div class=\"card\"><div class=\"card-header\">\n";
echo "<strong>".gettext("Add category")."</strong></div>\n";

echo "<div class=\"card-body card-block\">";
echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\">\n";

echo "<div class=\"row form-group\">\n";
echo "<div class=\"col col-md-2\">";
    echo "<select name=\"category\" id=\"category\" class=\"form-control\"  onChange=\"ajax_call('categories',this.value,".$_GET["param3"].",'','','".URL."index.php','for_ajaxcall')\">\n";
    $SQL="SELECT category_id, category_name_".LANG2.", category_name_".LANG1." FROM categories WHERE category_parent_id=0";
    $SQL.=" ORDER BY category_name_".$lang;
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"\">".gettext("Please select")."</option>\n";
    echo "<option value=\"0\"";
     if (isset($_GET["param2"]) && $_GET["param2"]==0)
    echo " selected";
    echo ">".gettext("No category")."</option>\n";

    foreach ($result as $row){
    echo "<option value=\"".$row["category_id"]."\"";
    if (isset($_GET["param2"]) && $_GET["param2"]==$row["category_id"])
    echo " selected";
    echo ">".$row["category_name_".$lang]."</option>\n";
   
    }
    echo "</select></div>";
    
     if (isset($_GET['param2'])){
     if($_GET["param2"]>0){
    $SQL="SELECT category_id,category_name_".$lang." FROM categories WHERE category_parent_id='".(int) $_GET['param2']."'";
   $result=$dba->Select($SQL);
   if ($dba->affectedRows()>0){
    echo "<div class=\"col col-md-2\" id=\"subcategory\">";
          
    echo "<select name=\"subcategory\" id=\"subcategory\" class=\"form-control\" >\n";
    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
    echo "<option value=\"".$row["category_id"]."\">".$row["category_name_".$lang]."</option>\n";
        }
    echo "</select></div>";
   }}else
   echo "<input type=\"hidden\" name=\"subcategory\" id=\"subcategory\" value=\"0\">";


echo "</div>";
if (isset($_SESSION['MODIFY_ASSET']))
{
echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button>\n";
}else 
echo gettext("You have no permission to modify an asset!");
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
echo "<input type=\"hidden\" name=\"asset_id\" id=\"asset_id\" value=\"".$_GET["param3"]."\">";
echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"assets\"></form></div>";
 
    
    }
        echo "</div></div>";

       } 
    
    
    
    
else if(isset($_GET['param1']) && $_GET['param1']=="rename"){

echo "<script src=\"".VENDORS_LOC."jquery-validation/dist/jquery.validate.min.js\"></script>";
if ($lang!="en" && file_exists(VENDORS_PATH."jquery-validation/dist/localization/messages_".$lang.".js"))
echo "<script src=\"".VENDORS_LOC."jquery-validation/dist/localization/messages_".$lang.".js\"></script>";


echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
echo "<div class=\"card\"><div class=\"card-header\">\n";

if ($_GET['param3']=='assets' && isset($_SESSION['MODIFY_ASSET']))
echo "<strong>".gettext("Rename asset")." ".get_asset_name_from_id($_GET["param2"],$lang);

else if ($_GET['param3']=='locations' && isset($_SESSION['MODIFY_LOCATION']))
echo "<strong>".gettext("Rename location")." ".get_location_name_from_id($_GET["param2"],$lang);

else if ($_GET['param3']=='categories' && isset($_SESSION['MODIFY_CATEGORY']))
echo "<strong>".gettext("Rename category")." ".get_category_name_from_id($_GET["param2"],$lang);

else if ($_GET['param3']=='products' && isset($_SESSION['MODIFY_PRODUCT'])){
echo "<strong>".gettext("Rename product")." ";

echo get_product_name_from_id($_GET["param2"],$lang);
}
else
echo lm_die(gettext("You have no permission to modify!"));
echo "</strong>\n";
echo "</div><div class=\"card-body card-block\">";
echo "<form action=\"index.php\" id=\"rename_form\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\">\n";

if (isset($_SESSION['CAN_WRITE_LANG1']))
{
if ($_GET['param3']=='assets')
$orig_name=get_asset_name_from_id($_GET["param2"],LANG1);
else if ($_GET['param3']=='locations')
$orig_name=get_location_name_from_id($_GET["param2"],LANG1);
else if ($_GET['param3']=='categories')
$orig_name=get_category_name_from_id($_GET["param2"],LANG1);
else if ($_GET['param3']=='products'){

$SQL="SELECT product_type_".$lang." as product_name FROM products WHERE product_id='".(int) $_GET["param2"]."'";
$row=$dba->getRow($SQL);
$orig_name=$row['product_name'];

}
echo "<div class=\"row form-group\">\n";
echo "<div class=\"col col-md-2\"><label for=\"new_name\" class=\"form-control-label\">".gettext("New name:")."</label></div>\n";
echo "<div class=\"col-12 col-md-3\"><input type=\"text\" id=\"new_name_".LANG1."\" name=\"new_name_".LANG1."\" class=\"form-control\" value=\"".$orig_name."\" required></div></div>\n";
}

if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
{

if ($_GET['param3']=='assets')
$orig_name=get_asset_name_from_id($_GET["param2"],LANG2);
else if ($_GET['param3']=='locations')
$orig_name=get_location_name_from_id($_GET["param2"],LANG2);
else if ($_GET['param3']=='categories')
$orig_name=get_category_name_from_id($_GET["param2"],LANG2);
else if ($_GET['param3']=='products'){
$orig_name=get_product_name_from_id($_GET["param2"],LANG2);;
}

echo "<div class=\"row form-group\">\n";
echo "<div class=\"col col-md-2\"><label for=\"new_name_".LANG2."\" class=\"form-control-label\">".gettext("New name (").LANG2."): </label></div>\n";
echo "<div class=\"col-12 col-md-3\"><input type=\"text\" id=\"new_name_".LANG2."\" name=\"new_name_".LANG2."\" class=\"form-control\" value=\"";
if ($orig_name!='no data') //if $orig_name empty the get_.._name_from_id() returns with 'no-data' 
echo $orig_name;
echo "\" required></div></div>\n";
}

echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div>\n";
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"".$_GET['param3']."\">";
if ($_GET['param3']=='assets')
echo "<input type=\"hidden\" name=\"asset_id\" id=\"asset_id\" value=\"".(int) $_GET["param2"]."\">";
else if ($_GET['param3']=='locations')
echo "<input type=\"hidden\" name=\"location_id\" id=\"location_id\" value=\"".(int) $_GET["param2"]."\">";
else if ($_GET['param3']=='categories')
echo "<input type=\"hidden\" name=\"category_id\" id=\"category_id\" value=\"".(int) $_GET["param2"]."\">";
else if ($_GET['param3']=='products')
echo "<input type=\"hidden\" name=\"product_id\" id=\"product_id\" value=\"".(int) $_GET["param2"]."\">";
echo "</form></div>";

echo "<script>\n";
echo "$(\"#rename_form\").validate()\n";
echo "</script>\n";



}

else if(isset($_GET['param1']) && $_GET['param1']=="asset_move_from" && isset($_SESSION['MODIFY_ASSET'])){
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
echo "<div class=\"card\"><div class=\"card-header\">\n";
echo "<strong>".gettext("Move asset")." ".get_asset_name_from_id($_GET["param2"],$lang)."</strong>\n";
echo "</div></div>";
$_SESSION["moving_asset_id"]=$_GET["param2"];
//echo "<script>ajax_call('rebuild_asset_tree','','','','','".URL."index.php','asset_tree')</script>";
echo "<script>location.href='index.php?page=assets'</script>";
}

else if(isset($_GET['param1']) && $_GET['param1']=="asset_move_to" && isset($_SESSION['MODIFY_ASSET'])){

echo "<div class=\"card\"><div class=\"card-header\">\n";
echo "<strong>".gettext("Insert asset")." ".get_asset_name_from_id($_SESSION["moving_asset_id"],$lang)." ".gettext("to here: ").get_asset_name_from_id($_GET["param2"],$lang)."</strong>\n";
echo "</div></div>";
$SQL="UPDATE assets SET asset_parent_id=".$_GET["param2"]." WHERE asset_id=".$_SESSION["moving_asset_id"];
if (LM_DEBUG)
        error_log($SQL,0);
        $result=$dba->Query($SQL);
        $asset_id=$_SESSION["moving_asset_id"];
        $asset_tree_has_changed=array(get_whole_path("asset",$asset_id,1)[0]);
      
include(INCLUDES_PATH."asset_tree.php");
unset($_SESSION["moving_asset_id"]);
unset($_SESSION["copy_asset_id"]);
echo "<script>location.reload(true)</script>";
}

else if(isset($_GET['param1']) && $_GET['param1']=="location_move_from" && isset($_SESSION['MODIFY_LOCATION'])){
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
echo "<div class=\"card\"><div class=\"card-header\">\n";
echo "<strong>".gettext("Move location")." ".get_location_name_from_id($_GET["param2"],$lang)."</strong>\n";
echo "</div></div>";
$_SESSION["moving_location_id"]=$_GET["param2"];
//echo "<script>ajax_call('rebuild_asset_tree','','','','','".URL."index.php','asset_tree')</script>";
echo "<script>location.href='index.php?page=locations'</script>";
}

else if(isset($_GET['param1']) && $_GET['param1']=="location_move_to" && isset($_SESSION['MODIFY_LOCATION'])){

echo "<div class=\"card\"><div class=\"card-header\">\n";
echo "<strong>".gettext("Insert location")." ".get_location_name_from_id($_SESSION["moving_location_id"],$lang)." ".gettext("to here: ").get_location_name_from_id($_GET["param2"],$lang)."</strong>\n";
echo "</div></div>";
$SQL="UPDATE locations SET location_parent_id=".$_GET["param2"]." WHERE location_id=".$_SESSION["moving_location_id"];
if (LM_DEBUG)
        error_log($SQL,0);
        $result=$dba->Query($SQL);
unset($_SESSION["moving_location_id"]);

echo "<script>location.reload(true)</script>";
}


else if(isset($_GET['param1']) && $_GET['param1']=="copy_asset"  && isset($_SESSION['MODIFY_ASSET'])){
/*
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
echo "<div class=\"card\"><div class=\"card-header\">\n";
echo "<strong>".gettext("Copy asset")." ".get_asset_name_from_id($_GET["param2"],$lang)."</strong>\n";
echo "</div></div>";
*/
$_SESSION["copy_asset_id"]=$_GET["param2"];
echo "<script>location.href='index.php?page=assets';</script>\n";
/*
echo "<button type=\"button\" class=\"btn btn-primary btn-sm\" onClick=\"location.href='index.php?page=assets'\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext(" Ok ");
echo "</button>\n";
*/
}

else if(isset($_GET['param1']) && $_GET['param1']=="paste_asset" && isset($_SESSION['MODIFY_ASSET'])){
/*
echo "<div class=\"card\"><div class=\"card-header\">\n";
echo "<strong>".gettext("Paste asset")." ".get_asset_name_from_id($_SESSION["copy_asset_id"],$lang)." ".gettext("to here: ").get_asset_name_from_id($_GET["param2"],$lang)."</strong>\n";
echo "</div></div>";
*/
copy_asset_with_children($_SESSION["copy_asset_id"],$_GET["param2"]);
unset($_SESSION["moving_asset_id"]);
$asset_id=$_SESSION["copy_asset_id"];
$asset_tree_has_changed=array(get_whole_path("asset",$asset_id,1)[0]);
include(INCLUDES_PATH."asset_tree.php");
unset($_SESSION["copy_asset_id"]);
echo "<script>location.href='index.php?page=assets';</script>\n";
/*echo "<button type=\"button\" class=\"btn btn-primary btn-sm\" onClick=\"location.href='index.php?page=assets'\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext(" Ok ");
echo "</button>\n";
*/
}


else if (isset($_GET['param1']) && $_GET['param1']=="show_asset_tree_menu"){
include(INCLUDES_PATH."show_assets_tree_menu_ajax.php");


}


else if(isset($_GET['param1']) && $_GET['param1']=="add_file"){

echo "<script src=\"".VENDORS_LOC."jquery-validation/dist/jquery.validate.min.js\"></script>";
if ($lang!="en" && file_exists(VENDORS_PATH."jquery-validation/dist/localization/messages_".$lang.".js"))
echo "<script src=\"".VENDORS_LOC."jquery-validation/dist/localization/messages_".$lang.".js\"></script>";


echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
    echo "<div class=\"card\"><div class=\"card-header\">\n";
    if ($_GET['param3']=='assets' && isset($_SESSION['ADD_FILE_TO_ASSET']))
    echo "<strong>".gettext("Add file to")." ".get_asset_name_from_id($_GET["param2"],$lang)."</strong>\n";
    
    else if ($_GET['param3']=='locations' && isset($_SESSION['ADD_FILE_TO_LOCATION']))
    echo "<strong>".gettext("Add file to")." ".get_location_name_from_id($_GET["param2"],$lang)."</strong>\n";
    
    else if ($_GET['param3']=='products' && isset($_SESSION['ADD_FILE_TO_PRODUCT']))
    echo "<strong>".gettext("Add file to")." ".get_product_name_from_id($_GET["param2"],$lang)."</strong>\n";
    
    else if ($_GET['param3']=='users' && isset($_SESSION['ADD_FILE_TO_USER']))
    echo "<strong>".gettext("Add file to")." ".get_username_from_id($_GET["param2"])."</strong>\n";
    
    else if ($_GET['param3']=='workrequests' && isset($_SESSION['ADD_FILE_TO_WORKREQUEST'])){
    $SQL="SELECT workrequest_short_".$lang." FROM workrequests WHERE workrequest_id='".(int) $_GET["param2"]."'";
    $row=$dba->getRow($SQL);
    echo "<strong>".gettext("Add file to")." ".$row['workrequest_short_'.$lang]."</strong>\n";
   }
    else if ($_GET['param3']=='workorders' && isset($_SESSION['ADD_FILE_TO_WORKORDER'])){
    $SQL="SELECT workorder_short_".$lang." FROM workorders WHERE workorder_id='".(int) $_GET["param2"]."'";
    $row=$dba->getRow($SQL);
    echo "<strong>".gettext("Add file to")." ".$row['workorder_short_'.$lang]."</strong>\n";
   }
    else if ($_GET['param3']=='stock_movements' && isset($_SESSION['ADD_FILE_TO_PRODUCT_MOVING']))
    echo "<strong>".gettext("Add file to")." ".get_product_name_from_id($_GET["param4"],$lang)." </strong>\n";
    
    else{
    lm_die(gettext("You have no permission"));
    }
    echo "</div><div class=\"card-body card-block\">";
    echo "<form action=\"index.php\" method=\"post\" id=\"upload_form\" enctype=\"multipart/form-data\" class=\"form-horizontal\">\n";

    if (isset($_SESSION['CAN_WRITE_LANG1'])){
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label for=\"info_file_review\" class=\"form-control-label\">".gettext("File review:")."</label></div>\n";
    echo "<div class=\"col-12 col-md-3\"><input type=\"text\" id=\"info_file_review_".LANG1."\" name=\"info_file_review_".LANG1."\" class=\"form-control\" value=\"\" required></div></div>\n";
    }
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2'])){
     echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label for=\"info_file_review_".LANG2."\" class=\"form-control-label\">".gettext("File review (").LANG2."): </label></div>\n";
    echo "<div class=\"col-12 col-md-3\"><input type=\"text\" id=\"info_file_review_".LANG2."\" name=\"info_file_review_".LANG2."\" class=\"form-control\" value=\"\" required></div></div>\n";
    }
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label for=\"req_user_level\" class=\"form-control-label\">".gettext("Required user level:")."</label></div>\n";
    echo "<div class=\"col col-md-2\">";
        echo "<select name=\"req_user_level\" id=\"req_user_level\" class=\"form-control\")\">\n";
        $SQL="SELECT user_level_".$lang.", user_level_id FROM user_levels ORDER BY user_level_".$lang;
    if (LM_DEBUG)
        error_log($SQL,0);
        $result=$dba->Select($SQL);
        echo "<option value=\"0\">".gettext("Please select")."</option>\n";
        foreach ($result as $row){
        echo "<option value=\"".$row["user_level_id"]."\"";
        echo ">".$row["user_level_".$lang]."</option>\n";
    
        }
        echo "</select></div></div>";
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label for=\"confidential\" class=\"form-control-label\">".gettext("Confidential:")."</label></div>\n";
    echo "<div class=\"col-12 col-md-3\">";
    echo "<SELECT name='confidential' id='confidential'>\n";
    echo "<option value='0'>".gettext("No")."\n";
    echo "<option value='1'>".gettext("Yes")."\n";
    echo "</select>\n";
    echo "</div></div>\n";

    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label for=\"info_file_name\" class=\"form-control-label\">".gettext("File:")."</label></div>\n";
    echo "<div class=\"col-12 col-md-3\"><input type=\"file\" id=\"info_file_name\" name=\"info_file_name[]\"  multiple></div></div>\n";
    
    echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div>\n";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
    echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"".$_GET['param3']."\">";
    if ($_GET['param3']=='assets')
    echo "<input type=\"hidden\" name=\"asset_id\" id=\"asset_id\" value=\"".(int) $_GET["param2"]."\">";
    else if ($_GET['param3']=='locations')
    echo "<input type=\"hidden\" name=\"location_id\" id=\"location_id\" value=\"".(int) $_GET["param2"]."\">";
    else if ($_GET['param3']=='products')
    echo "<input type=\"hidden\" name=\"product_id\" id=\"product_id\" value=\"".(int) $_GET["param2"]."\">";
    else if ($_GET['param3']=='stock_movements')
    echo "<input type=\"hidden\" name=\"stock_movement_id\" id=\"stock_movement_id\" value=\"".(int) $_GET["param2"]."\">";
    else if ($_GET['param3']=='workrequests')
    echo "<input type=\"hidden\" name=\"workrequest_id\" id=\"workrequest_id\" value=\"".(int) $_GET["param2"]."\">";
    else if ($_GET['param3']=='workorders')
    echo "<input type=\"hidden\" name=\"workorder_id\" id=\"workorder_id\" value=\"".(int) $_GET["param2"]."\">";
    else if ($_GET['param3']=='users')
    echo "<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"".(int) $_GET["param2"]."\">";
    
    echo "</div></form></div>";

    
    echo "<script>\n";
    echo "$(\"#upload_form\").validate()\n";
    echo "</script>\n";

    
    
}


else if (isset($_GET['param1']) && $_GET['param1']=="copy_info_file"){
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
echo "<form method='POST' action='index.php' class='form-horizontal'>";

echo "<div class=\"card\"><div class=\"card-header\">\n";
    echo "<strong>".gettext("Copy info files...")."</strong></div>\n";

    echo "<div class=\"card-body card-block\">";

    echo "<div class=\"row form-group\">";
            echo "<div class=\"col col-md-3\"><label for=\"category_id\" class=\"form-control-label\">".gettext("Category:")."</label></div>";

            echo "<div class=\"col col-md-2\">";
            echo "<select name=\"category_id\" id=\"category_id\" class=\"form-control\" onChange=\"ajax_call('copy_info_file','".$_GET['param2']."',this.value,0,'".$_GET['param5']."','".URL."index.php','for_ajaxcall')\">\n";
            $SQL="SELECT category_id,category_name_".$lang." FROM categories WHERE category_parent_id=0";
            $SQL.=" ORDER BY category_name_".$lang;
            if (LM_DEBUG)
            error_log($SQL,0);
            $result=$dba->Select($SQL);
            echo "<option value=\"0\">".gettext("Please select")."</option>\n";
            foreach ($result as $row){
            if ($row["category_name_".$lang]!=""){
            echo "<option value=\"".$row["category_id"]."\"";
                if($_GET['param3']==$row["category_id"])
                echo " selected";
            echo ">".$row["category_name_".$lang]."</option>\n";
             }
            else{
            echo "<option value=\"".$row["category_id"]."\"";
            if($_GET['param3']==$row["category_id"])
                echo " selected";
            echo ">".$row["category_name_".LANG2]."</option>\n";
            }
            }
            echo "</select></div></div>";





    $SQL="SELECT category_id, category_name_".$lang." FROM categories WHERE category_parent_id=".(int) $_GET['param3'];
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    if ($dba->affectedRows()>0){
    echo "<div class=\"row form-group\">\n";


        echo "<div class=\"col col-md-3\"><label for=\"subcategory_id\" class=\"form-control-label\">\n";
        echo gettext("Subcategory:");
        echo "</label></div>\n";
        echo "<div class=\"col col-md-2\">\n";
            echo "<select name=\"subcategory_id\" id=\"subcategory_id\" class=\"form-control\"";
            echo " onChange=\"ajax_call('copy_info_file','".$_GET['param2']."','".$_GET['param3']."',this.value,'".$_GET['param5']."','".URL."index.php','for_ajaxcall')\"";
            echo ">\n";
            
            echo "<option value=\"0\">".gettext("Please select")."</option>\n";
            foreach ($result as $row){
            echo "<option value=\"".$row["category_id"]."\"";
            if ($_GET["param4"]==$row["category_id"])
            echo " selected";
            if ($row["category_name_".$lang]!="")
            echo ">".$row["category_name_".$lang]."</option>\n";
            else
            echo ">".$row["category_name_".LANG2]."</option>\n";
                }
            echo "</select>\n";
            }
            else
            echo "<INPUT TYPE='hidden' id='subcategory_id' name='subcategory_id' value='0'>\n";
        echo "</div>\n";
    echo "</div>\n";
    
        
    echo "<div class=\"row form-group\">\n";
    
        echo "<div class=\"col col-md-3\"><label for=\"product_id\" class=\"form-control-label\">\n";
        
            echo gettext("Product:");
            echo "</label></div>\n";
            echo "<div class=\"col col-md-2\">\n";
           
            echo "<select name=\"copy_product_id\" id=\"copy_product_id\" class=\"form-control\"";
            echo " onChange=\"ajax_call('copy_info_file','".$_GET['param2']."','".$_GET['param3']."','".$_GET['param4']."',this.value,'".URL."index.php','for_ajaxcall')\">\n";
            
            echo "<option value=\"0\">".gettext("Please select")."</option>\n";
            echo "<option value=\"new\">".gettext("New product")."</option>\n";
            $SQL="SELECT product_id, product_type_".$lang.", product_properties_".$lang." FROM products WHERE category_id=".(int) $_GET['param3']." AND product_stockable<3";
            
            if ($_GET['param4']>0)
            $SQL.=" AND subcategory_id=".(int) $_GET['param4'];
            
            if (LM_DEBUG)
            error_log($SQL,0);
            $result=$dba->Select($SQL);
            foreach ($result as $row){
            echo "<option value=\"".$row["product_id"]."\"";
            if ($_GET['param5']==$row["product_id"])
            echo " selected";
            echo ">".$row["product_type_".$lang]." ".$row["product_properties_".$lang]."</option>\n";
            }
            echo "</select>\n";
            echo "<input type='hidden' name='page' id='page' value='products'>";
            echo "<input type='hidden' name='product_id' id='product_id' value='".(int) $_GET['param2']."'>";
        echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
    
        echo "</div>\n";
        echo "</div>\n"; 
echo "</div>";//card-body
if ($_GET['param2']>0){
?>
<div class="card-footer">
<button type="submit" class="btn btn-primary btn-sm">
<i class="fa fa-dot-circle-o"></i> <?php echo gettext("Submit");?> 
</button>
<button type="reset" class="btn btn-danger btn-sm">
<i class="fa fa-ban"></i> <?php echo gettext("Reset");?>
</button>
</div>
<?php
}

echo "</div>\n";//card
echo "</form>\n";

}

else if(isset($_GET['param1']) && $_GET['param1']=="create_workorder"){
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
        if (isset($_GET["param2"]) && $_GET["param2"]>0)
        {$resp=array();
        //array_reverse((get_whole_path("asset",$_GET["param2"])));
        $path="";
        foreach((get_whole_path("asset",$_GET["param2"],1)) as $r){
        if ($path=="")// the first element is the main asset_id -> ignore it
        $path.="";
        else
        $path.="-><wbr>";
        $path.=$r;
        }
    
        echo "<div class=\"card\"><div class=\"card-header\">\n";
    echo "<strong>".gettext("Add a workorder to ").$path."</strong></div>\n";

    echo "<div class=\"card-body card-block\">";
    echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\">\n";
    echo "<div class=\"row form-group\">\n";


    echo "</div>";//class=\"row form-group
    echo "</div>";//card-body
    echo "<div class=\"card-footer\">";
    if (isset($_SESSION['ADD_WORKORDER']))
    {
    echo "<button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button>";
    }else
    echo gettext("You have no permission to add workorder!");
    echo "</div>\n";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

    echo "</form></div>";

        
        }

}




else if(isset($_GET['param1']) && $_GET['param1']=="show_info_files"){
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
        if (isset($_GET["param2"]) && $_GET["param2"]>0){//param2 is the asset "id"
       echo "<div class=\"row\">\n";
       if ($_GET['param3']=='assets' && isset($_SESSION['SEE_FILE_OF_ASSET'])){
       $SQL="SELECT * FROM assets WHERE asset_id='".(int) $_GET["param2"]."'";
       $col_name="asset_id";
       $demanded_privilege="DELETE_FILE_OF_ASSET";
       }
       else if ($_GET['param3']=='locations' && isset($_SESSION['SEE_FILE_OF_LOCATION'])){
       $SQL="SELECT * FROM locations WHERE location_id='".(int) $_GET["param2"]."'";
       $col_name="location_id";
       $demanded_privilege="DELETE_FILE_OF_LOCATION";
       }
       else if ($_GET['param3']=='products' && isset($_SESSION['SEE_FILE_OF_PRODUCT'])){
       $SQL="SELECT * FROM products WHERE product_id='".(int) $_GET["param2"]."'";
       $col_name="product_id";
       $demanded_privilege="DELETE_FILE_OF_PRODUCT";
       }
       else if ($_GET['param3']=='workorders' && isset($_SESSION['SEE_FILE_OF_WORKORDER'])){
       $SQL="SELECT * FROM workorders WHERE workorder_id='".(int) $_GET["param2"]."'";
       $col_name="workorder_id";
       $demanded_privilege="DELETE_FILE_OF_WORKORDER";
       }
       else if ($_GET['param3']=='workrequests' && isset($_SESSION['SEE_FILE_OF_WORKREQUEST'])){
       $SQL="SELECT * FROM workrequests WHERE workrequest_id='".(int) $_GET["param2"]."'";
       $col_name="workrequest_id";
       $demanded_privilege="DELETE_FILE_OF_WORKREQUEST";
       }
       else if ($_GET['param3']=='users' && isset($_SESSION['SEE_FILE_OF_USER'])){
       $SQL="SELECT * FROM users WHERE user_id='".(int) $_GET["param2"]."'";
       $col_name="user_id";
       $demanded_privilege="DELETE_FILE_OF_USER";
       }
       else if ($_GET['param3']=='stock_movements' && isset($_SESSION['SEE_FILE_OF_PRODUCT_MOVING'])){
       $SQL="SELECT * FROM stock_movements WHERE stock_movement_id='".(int) $_GET["param2"]."'";
       $col_name="stock_movement_id";
       $demanded_privilege="DELETE_FILE_OF_STOCK_MOVEMENT";
       }
       else if ($_GET['param3']=='pinboard' && isset($_SESSION['SEE_FILE_OF_PIN'])){
       $SQL="SELECT * FROM pinboard WHERE pin_id='".(int) $_GET["param2"]."'";
       $col_name="pin_id";
       $demanded_privilege="DELETE_FILE_OF_PINBOARD";
       }
       else
       lm_die(gettext("You have no permission to see files!"));
       if (LM_DEBUG)
        error_log($SQL,0); 
       $row=$dba->getRow($SQL);
       $info_files_ids=array();
        foreach($row as $key=>$value){
       	if (strstr($key,"info_file_id") && $value>0){
		$info_files_ids[]=$value;
		echo "<div class=\"col-sm-6 col-lg-3\">";
		$info=get_info_file_data($value);
		if ($info['confidential']!=1 || 
		($info['confidential'] && $_GET['param3']=='assets' && isset($_SESSION['SEE_CONF_FILE_OF_ASSET'])) ||
		($info['confidential'] && $_GET['param3']=='locations' && isset($_SESSION['SEE_CONF_FILE_OF_LOCATION'])) ||
		($info['confidential'] && $_GET['param3']=='products' && isset($_SESSION['SEE_CONF_FILE_OF_PRODUCT'])) ||
		($info['confidential'] && $_GET['param3']=='users' && isset($_SESSION['SEE_CONF_FILE_OF_USER'])) ||
		($info['confidential'] && $_GET['param3']=='workrequests' && isset($_SESSION['SEE_CONF_FILE_OF_WORKREQUEST'])) ||
		($info['confidential'] && $_GET['param3']=='workorders' && isset($_SESSION['SEE_CONF_FILE_OF_WORKORDER'])))
		{
		if ($value==(int) $_GET['param5'] && $_GET['param4']=='delete' && isset($_SESSION[$demanded_privilege])){
		$SQL="UPDATE ".$dba->escapeStr($_GET["param3"])." SET ".$key."=null WHERE ".$col_name."=".(int) $_GET["param2"];
		if ($dba->Query($SQL))
		echo gettext("deleted");
		}
		
		else if (substr($info["info_file_name"], -3)=="pdf")
        echo " <a href=\"".INFO_LOC.$info["info_file_name"]."\" target=\"_blank\">\n<IMG src=\"".INFO_THUMB_LOC."small_".substr($info["info_file_name"],0,-3)."jpg\">\n</a>\n<br/>\n";
		else if (exif_imagetype(INFO_PATH.$info["info_file_name"])){
            echo "<a href=\"".INFO_LOC.$info["info_file_name"]."\" data-toggle=\"lightbox\" data-gallery=\"gallery\" >\n";
            echo "<IMG src=\"".INFO_THUMB_LOC."small_".$info["info_file_name"]."\">\n";
            echo "</a>\n<br/>\n";
            }
        else {
            echo "<a href=\"".INFO_LOC.$info["info_file_name"]."\" target=\"_blank\">\n";
            echo "<span style=\"";
            echo "display: inline-block;
                    background: #000;
                    border-radius: 4px;
                    font-family: 'arial-black';
                    font-size: 14px;
                    color: #FFF;
                    padding: 8px 12px;
                    cursor: pointer;";
            echo "\">".substr($info["info_file_name"],strlen($info["info_file_name"])-4)."</span>\n";
            echo "</a>\n<br/>\n";
        }
		echo "<div id='info_file_review_".$value."' >";
		echo "<span onClick=\"ajax_call('modify_review_value','".$key."','".$value."','','','".URL."index.php','info_file_review_".$value."')\"";
		echo ">";
        echo $info["info_file_review_".$lang];
		echo "</span></div>\n";
		
		if (isset($_SESSION[$demanded_privilege]))
		echo "<a href=\"javascript:
         var a=confirm('".gettext("You are about to delete a file. Are you sure?")."');
                if (a==true)
         ajax_call('show_info_files','".$_GET['param2']."','".$_GET['param3']."','delete','".$value."','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-trash\"></i></a>";
		echo "</div>\n";
		}
		}			
		}
		if ($_GET['param3']=='assets' && $row['asset_product_id']>0 )
{
       $SQL="SELECT * FROM products WHERE product_id='".$row['asset_product_id']."'";
		$row=$dba->getRow($SQL);
		foreach($row as $key=>$value){
            if (strstr($key,"info_file_id") && $value>0 && !in_array($value,$info_files_ids))
            {
            echo "<div class=\"col-sm-6 col-lg-3\">";
            $info=get_info_file_data($value);
                if (substr($info["info_file_name"], -3)=="pdf")
                    echo " <a href=\"".INFO_LOC.$info["info_file_name"]."\" target=\"_blank\">\n<IMG src=\"".INFO_THUMB_LOC."small_".substr($info["info_file_name"],0,-3)."jpg\">\n</a>\n<br/>\n";
                else{
                    echo "<a href=\"".INFO_LOC.$info["info_file_name"]."\" data-toggle=\"lightbox\" data-gallery=\"gallery\" >\n";
                    echo "<IMG src=\"".INFO_THUMB_LOC."small_".$info["info_file_name"]."\">\n</a>\n<br/>\n";
                    }
                echo "<div id='info_file_review_".$value."' >";
                echo "<span onClick=\"ajax_call('modify_review_value','".$key."','".$value."','','','".URL."index.php','info_file_review_".$value."')\"";
                echo ">";
                echo $info["info_file_review_".$lang];
                echo "</span></div></div>";
            }			
		}
}
		
		
		
        echo "</div>";//card
        }
        }
        
        
else if(isset($_GET['param1']) && $_GET['param1']=="modify_review_value" && $_GET['param2']=="save".$_GET["param3"] && $_GET['param3']>0){
$SQL="UPDATE info_files SET info_file_review_".$lang."='".$_GET['param4']."' WHERE info_file_id=".(int)$_GET['param3'];
if (LM_DEBUG)
        error_log($SQL,0); 
if ($dba->Query($SQL))
echo $_GET['param4'];
else
echo gettext("An error occured!");
}
else if(isset($_GET['param1']) && $_GET['param1']=="modify_review_value" && $_GET['param2']!="" && $_GET['param3']>0){
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
//$_GET['param2'] column name, $_GET['param3'] the info_file_id we want to change
if (strstr($_GET['param2'],"info_file_id"))
$info=get_info_file_data($_GET['param3']);
$act_value=$info["info_file_review_".$lang];
echo "<div class=\"row form-group\">\n";
    echo "<form";
    echo " class=\"form-horizontal\">\n";
    echo "<INPUT TYPE='text' name='review' VALUE='".$act_value."'>";
    echo "<div class=\"card-footer\"><button";
        echo " onClick=\"ajax_call('modify_review_value','save".$_GET['param3']."','".$_GET['param3']."',review.value,'".$_GET['param2']."','".URL."index.php','info_file_review_".$_GET['param3']."')\"";

        echo "type=\"button\" class=\"btn btn-primary btn-sm\">\n";
        echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
        echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button>";
    echo "</div>\n";
    echo "</form>";
echo "</div>";   
}
else if (isset($_GET['param1']) && $_GET['param1']=="products"){
//echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
//echo "<span aria-hidden=\"true\">×</span>\n</button>";
    $SQL="SELECT category_id, category_name_".$lang." FROM categories WHERE category_parent_id='".$_GET['param2']."'";
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    if ($dba->affectedRows()>0){
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-3\"><label for=\"select\" class=\"form-control-label\">\n";
    echo gettext("Subcategory:");
    echo "</label></div>\n";
    echo "<div class=\"col-12 col-md-4\">\n";
    echo "<select name=\"subcategory_id\" id=\"subcategory_id\" class=\"form-control\">";
    
    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
    echo "<option value=\"".$row["category_id"]."\">".$row["category_name_".$lang]."</option>\n";
    }
    echo "</select>";
    }
    else
    echo "<INPUT TYPE='hidden' id='subcategory_id' name='subcategory_id' value='0'>";
}


else if(isset($_GET['param1']) && $_GET['param1']=="search"){
$SQL="SELECT ".$dba->escapeStr($_GET['param2'])." FROM ".$dba->escapeStr($_GET['param3'])." WHERE ".$dba->escapeStr($_GET['param2'])." LIKE '%".$dba->escapeStr($_GET['param4'])."%' ORDER BY ".$dba->escapeStr($_GET['param2'])." LIMIT 0,10";
$result=$dba->Select($SQL);
if (LM_DEBUG)
            error_log($SQL,0); 
if ($dba->affectedRows($SQL)){
foreach ($result as $row){
echo "<span onClick=\"document.getElementById('".$dba->escapeStr($_GET['param5'])."').value='".$row[$dba->escapeStr($_GET['param2'])]."'\">".$row[$dba->escapeStr($_GET['param2'])]."</span><br/>";
}
}else
echo gettext("No match");
}


else if (isset($_GET['param1']) && $_GET['param1']=="into_stock"){
include(INCLUDES_PATH."modals/modal_add_new_product_form.php"); 

echo "<script src=\"".VENDORS_LOC."jquery-validation/dist/jquery.validate.min.js\"></script>";
if ($lang!="en" && file_exists(VENDORS_PATH."jquery-validation/dist/localization/messages_".$lang.".js"))
echo "<script src=\"".VENDORS_LOC."jquery-validation/dist/localization/messages_".$lang.".js\"></script>";

echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
 echo "<div class=\"card-body card-block\">";



echo "<form method='POST' id='into_stock_form' name='into_stock_form' action='index.php' class='form-horizontal'>";
    echo "<div class=\"row form-group\">";
            echo "<div class=\"col col-md-3\"><label for=\"category_id\" class=\"form-control-label\">".gettext("Category:")."</label></div>";

            echo "<div class=\"col col-md-2\">";
            echo "<select name=\"category_id\" id=\"category_id\" class=\"form-control\" onChange=\"ajax_call('into_stock','',this.value,0,'".$_GET['param5']."','".URL."index.php','for_ajaxcall')\">\n";
            $SQL="SELECT category_id,category_name_".$lang." FROM categories WHERE category_parent_id=0";
            $SQL.=" ORDER BY category_name_".$lang;
            if (LM_DEBUG)
            error_log($SQL,0);
            $result=$dba->Select($SQL);
            echo "<option value=\"0\">".gettext("Please select")."</option>\n";
            foreach ($result as $row){
            if ($row["category_name_".$lang]!=""){
            echo "<option value=\"".$row["category_id"]."\"";
                if($_GET['param3']==$row["category_id"])
                echo " selected";
            echo ">".$row["category_name_".$lang]."</option>\n";
             }
            else{
            echo "<option value=\"".$row["category_id"]."\"";
            if($_GET['param3']==$row["category_id"])
                echo " selected";
            echo ">".$row["category_name_".LANG2]."</option>\n";
            }
            }
            echo "</select></div></div>";





    $SQL="SELECT category_id, category_name_".$lang." FROM categories WHERE category_parent_id='".(int) $_GET['param3']."'";
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<div class=\"row form-group\">\n";
    if ($dba->affectedRows()>0){
    
        echo "<div class=\"col col-md-3\"><label for=\"subcategory_id\" class=\"form-control-label\">\n";
        echo gettext("Subcategory:");
        echo "</label></div>\n";
        echo "<div class=\"col col-md-2\">\n";
            echo "<select name=\"subcategory_id\" id=\"subcategory_id\" class=\"form-control\"";
            echo " onChange=\"ajax_call('into_stock','','".$_GET['param3']."',this.value,'".$_GET['param5']."','".URL."index.php','for_ajaxcall')\"";
            echo ">\n";
            
            echo "<option value=\"0\">".gettext("Please select")."</option>\n";
            foreach ($result as $row){
            echo "<option value=\"".$row["category_id"]."\"";
            if ($_GET["param4"]==$row["category_id"])
            echo " selected";
            if ($row["category_name_".$lang]!="")
            echo ">".$row["category_name_".$lang]."</option>\n";
            else
            echo ">".$row["category_name_".LANG2]."</option>\n";
                }
            echo "</select>\n";
            echo "</div>\n";
            }
            else
            echo "<INPUT TYPE='hidden' id='subcategory_id' name='subcategory_id' value='0'>\n";
        
    echo "</div>\n";
    
        
    echo "<div class=\"row form-group\">\n";
    
        echo "<div class=\"col col-md-3\"><label for=\"product_id\" class=\"form-control-label\">\n";
        
            echo gettext("Product:");
            echo "</label></div>\n";
            echo "<div class=\"col col-md-2\">\n";
           
            echo "<select name=\"product_id\" id=\"product_id\" class=\"form-control\"";
            echo " onChange=\"if (this.value!='new') ajax_call('into_stock',this.value,'".$_GET['param3']."',".$_GET['param4'].",'".$_GET['param5']."','".URL."index.php','for_ajaxcall')\">\n";
            
            echo "<option value=\"0\">".gettext("Please select")."</option>\n";
            echo "<option value=\"new\">".gettext("New product")."</option>\n";
            $SQL="SELECT product_id, product_type_".$lang.", product_properties_".$lang." FROM products WHERE category_id=".(int) $_GET['param3']." AND product_stockable<3";
            
            if ($_GET['param4']>0)
            $SQL.=" AND subcategory_id=".(int) $_GET['param4'];
            $SQL.=" ORDER BY product_type_".$lang;
            if (LM_DEBUG)
            error_log($SQL,0);
            $result=$dba->Select($SQL);
            foreach ($result as $row){
            echo "<option value=\"".$row["product_id"]."\"";
            if ($_GET['param2']==$row["product_id"])
            echo " selected";
            echo ">".$row["product_type_".$lang]." ".$row["product_properties_".$lang]."</option>\n";
            }
            echo "</select>\n";
        echo "</div>\n";
        echo "</div>\n"; 
       if ($_GET['param2']>0){ 
         echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-3\"><label for=\"quantity\" class=\" form-control-label\">".gettext("Quantity:")."</label></div>\n";
        echo "<div class=\"col col-md-1\"><input type=\"text\" id=\"quantity\" name=\"quantity\" placeholder=\"".gettext("quantity")."\" class=\"form-control\" value=\"1\"";
        $SQL="SELECT product_stockable,default_stock_location_id FROM products WHERE product_id=".(int) $_GET['param2'];
        $row=$dba->getRow($SQL);
        $default_stock_location_id=$row['default_stock_location_id'];
        if (2==$row['product_stockable'])
        echo " readonly=\"1\"";
        $unit=get_quantity_unit_from_product_id($_GET['param2']);
        echo "></div>".$unit[0]."\n";
        echo "</div>\n";
        
        
         echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-3\"><label for=\"quantity\" class=\" form-control-label\">".gettext("Min. quantity:")."</label></div>\n";
        echo "<div class=\"col col-md-1\"><input type=\"text\" id=\"min_stock_quantity\" name=\"min_stock_quantity\" placeholder=\"".gettext("Min. quantity")."\" class=\"form-control\" value=\"0\"";
        
        if (2==$row['product_stockable'])
        echo " readonly=\"1\"";
        echo "></div>".$unit[0] ."\n";
        echo "</div>\n";
        
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-3\"><label for=\"partner_id\" class=\"form-control-label\">";
        echo gettext("Partner:")."</label></div>\n";
        echo "<div class=\"col col-md-2\">\n";
        echo "<select id=\"partner_id\" name=\"partner_id\" class=\"form-control\"";
        echo " onChange=\"if (this.value=='new'){\n";
        echo " document.getElementById('new_partner').style.display='block';\n";
        echo "document.getElementById('partner_name').value='';\n";
        echo "document.getElementById('partner_address').value='';\n";
        echo "}else{\n";
        echo " document.getElementById('new_partner').style.display='none';\n";
        echo "document.getElementById('partner_name').value='new name';\n";
        echo "document.getElementById('partner_address').value='new address';\n";
        echo "}";
        echo "\"";
        echo "required>\n";
        echo "<option value=\"\">".gettext("Please select")."</option>\n";
        echo "<option value='new'>".gettext("New")."</option>\n";
        $SQL="SELECT partner_name, partner_id FROM partners WHERE active=1 ORDER BY partner_name";
        $result=$dba->Select($SQL);
        
        foreach ($result as $row1)
        echo "<option value=\"".$row1["partner_id"]."\">".$row1["partner_name"]."</option>\n";
        echo "</select>\n</div></div>\n";
        
        
        echo "<div id='new_partner' style='display:none;'>\n";
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-3\"><label for=\"partner\" class=\"form-control-label\">".gettext("Partner name:")."</label></div>\n";
        echo "<div class=\"col col-md-2\"><input type=\"text\" id=\"partner_name\" name=\"partner_name\" placeholder=\"".gettext("new partner")."\" class=\"form-control\" value=\"new partner\"><small class=\"form-text text-muted\">".gettext("new partner")."</small></div>\n";
        echo "</div>\n";
        
        echo "<div class=\"row form-group\">";
        echo "<div class=\"col col-md-3\"><label for=\"partner_address\" class=\" form-control-label\">".gettext("Partner address:")."</label></div>";
        echo "<div class=\"col col-md-5\"><input type=\"text\" id=\"partner_address\" name=\"partner_address\" placeholder=\"".gettext("address")."\" class=\"form-control\" value=\"new address\"></div>\n";
        echo "</div>";
        
        echo "</div>\n";
        
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-3\"><label for=\"stock_movement_time\" class=\"form-control-label\">";
        echo gettext("Date:")."</label></div>\n";
        echo "<div class=\"col col-md-2\">\n";
         echo "<input type=\"date\" id=\"stock_movement_time\" name=\"stock_movement_time\" value=\"".date($lang_date_format)."\">\n";
    
        echo "</div></div>\n";
        
        
        
        
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-3\">\n<label for=\"stock_location_id\" class=\"form-control-label\">\n";
        echo gettext("Stock:")."</label></div>\n";
        echo "<div class=\"col col-md-2\">\n";
        echo "<select id=\"stock_location_id\" name=\"stock_location_id\" class=\"form-control\" required>\n";

        $SQL="SELECT location_name_".$lang.", location_id FROM locations WHERE set_as_stock=1 ORDER BY location_name_".$lang;
        $result=$dba->Select($SQL);
        echo "<option value=\"\">".gettext("Please select")."</option>\n";
        foreach ($result as $row1){
        echo "<option value=\"".$row1["location_id"]."\"";
        if ($row1['location_id']==$default_stock_location_id)
        echo " selected";
        echo ">".$row1["location_name_".$lang]."</option>\n";
        }
        echo "</select></div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-3\"><label for=\"stock_place\" class=\" form-control-label\">".gettext("Place:")."</label></div>";
        echo "<div class=\"col col-md-2\"><input type=\"text\" id=\"stock_place\" name=\"stock_place\" placeholder=\"".gettext("place")."\" class=\"form-control\"></div>\n";
        echo "</div>\n";
        
        echo "<div class=\"card-footer\">";
        if (isset($_SESSION["PUT_PRODUCT_INTO_STOCK"])){
            if (2==$row['product_stockable'] && get_sum_quantity_from_product_id($_GET['param2'])>=1)
            echo gettext("There can be only 1 such item in the stock!");
            else{
                echo "<button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
                echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext("Submit")."</button>\n";
                }
        echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button>";
        }else
        echo gettext("You have no permission!");
       
        
        echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"stock\">\n";
        echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
        echo "</div>\n";
          }
        
        echo "</form></div>\n";
      
   
   
   
  
   ?>
   <script>
   
   $('#product_id').change(function(){
  //this is just getting the value that is selected
  if ($(this).val()=='new'){
    var title = '<?php echo gettext("Add new product"); ?>';
  $('.modal-title').html(title);
  $('.modal').modal('show'); 
}});
$("#into_stock_form").validate();
</script>
   <?php
   

}

else if (isset($_GET['param1']) && $_GET['param1']=="product_to_workorder"){
if ($_GET['param3']>0)
include(INCLUDES_PATH."modals/modal_add_new_product_form.php");
echo "<script src=\"".INCLUDES_LOC."luhn.js\"></script>\n";


echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";

 echo "<div class=\"card-body card-block\">";
echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\"";
if (!($_GET['param2']>0 || $_GET['param3']>0))
echo " onSubmit=\"return false;\"";

echo ">";
   
echo "<div class=\"row form-group\">";
            echo "<div class=\"col col-md-1\"><label for=\"product_id\" class=\"form-control-label\">".gettext("Product id:")."</label></div>";

            echo "<div class=\"col col-md-2\">";   
   
  echo " <INPUT TYPE='text' name='prod_id' id='prod_id' VALUE='";

if ((int) $_GET['param2']>0){
echo luhn((int)$_GET['param2']);
}
/*
require_once (VENDORS_PATH."mobiledetect/mobiledetectlib/Mobile_Detect.php");
  
$detect = new Mobile_Detect;
if ($detect->isMobile() || $detect->isTablet()) {
  echo "'>\n";
  echo "<button type='button' onClick=\"";
  echo "if (Validate(document.getElementById('prod_id').value)){
ajax_call('product_to_workorder',(document.getElementById('prod_id').value).substring(0,document.getElementById('prod_id').value.length-1),'',0,'".$_GET['param5']."','".URL."index.php','for_ajaxcall');}
else{
alert ('".gettext("Wrong number! Check it!")."');
   return false; 
    }\">";
  
  
  echo gettext("Send")."</button>\n";
}else
*/
{
echo "' SIZE='3' autocomplete='off' onKeyPress=\"this.onkeydown=function(e){
    if(e.keyCode==13 || e.which == 13){
    event.preventDefault();
if (Validate(this.value))
ajax_call('product_to_workorder',(this.value).substring(0,this.value.length-1),'',0,'".$_GET['param5']."','".URL."index.php','for_ajaxcall');
else
alert ('".gettext("Wrong number! Check it!")."');
   return false; 
    }
}\">";}
   
echo "</div></div>\n";   
  echo "<div class=\"row form-group\">\n";
            echo "<div class=\"col col-md-1\"><label for=\"category_id\" class=\"form-control-label\">".gettext("Category:")."</label></div>\n";

            echo "<div class=\"col col-md-2\">\n";
            echo "<select name=\"category_id\" id=\"category_id\" class=\"form-control\" onChange=\"ajax_call('product_to_workorder','',this.value,0,'".$_GET['param5']."','".URL."index.php','for_ajaxcall')\">\n";
            $SQL="SELECT category_id,category_name_".$lang." FROM categories WHERE category_parent_id=0";
            $SQL.=" ORDER BY category_name_".$lang;
            if (LM_DEBUG)
            error_log($SQL,0);
            $result=$dba->Select($SQL);
            echo "<option value=\"0\">".gettext("Please select")."</option>\n";
            if ((int) $_GET['param2']>0)
            {
            $category_id=get_category_id_from_id((int) $_GET['param2']);
            $subcategory_id=get_subcategory_id_from_id((int) $_GET['param2']);
                        }
            else
            {
            $category_id=0;
            $subcategory_id=0;
            }
            foreach ($result as $row){
            if ($row["category_name_".$lang]!=""){
            echo "<option value=\"".$row["category_id"]."\"";
                if(((int) $_GET['param3']>0 && $_GET['param3']==$row["category_id"]) || ($category_id>0 && $category_id==$row["category_id"]))
                echo " selected";
            echo ">".$row["category_name_".$lang]."</option>\n";
             }
            else{
            echo "<option value=\"".$row["category_id"]."\"";
            if($_GET['param3']==$row["category_id"])
                echo " selected";
            echo ">".$row["category_name_".LANG2]."</option>\n";
            }
            }
            echo "</select></div></div>";
            
 $SQL="SELECT category_id, category_name_".$lang." FROM categories WHERE ";
 if ((int) $_GET['param3']>0)
 $SQL.="category_parent_id='".$_GET['param3']."'";
 else if ((int) $_GET['param2']>0)
 $SQL.="category_parent_id='".$category_id."'";   
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    if ($dba->affectedRows()>0){
    echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-1\"><label for=\"subcategory_id\" class=\"form-control-label\">\n";
        echo gettext("Subcategory:");
        echo "</label></div>\n";
        echo "<div class=\"col col-md-2\">\n";
            echo "<select name=\"subcategory_id\" id=\"subcategory_id\" class=\"form-control\"";
            echo " onChange=\"ajax_call('product_to_workorder','','".$_GET['param3']."',this.value,'".$_GET['param5']."','".URL."index.php','for_ajaxcall')\"";
            echo ">";
            
            echo "<option value=\"0\">".gettext("Please select")."</option>\n";
            foreach ($result as $row){
            echo "<option value=\"".$row["category_id"]."\"";
            if (((int) $_GET['param4']>0 && $_GET["param4"]==$row["category_id"]) || ($subcategory_id>0 && $subcategory_id==$row['category_id']))
            echo " selected";
            if ($row["category_name_".$lang]!="")
            echo ">".$row["category_name_".$lang]."</option>\n";
            else
            echo ">".$row["category_name_".LANG2]."</option>\n";
                }
            echo "</select>";
            echo "</div>";
    echo "</div>";
            }
            else
            echo "<INPUT TYPE='hidden' id='subcategory_id' name='subcategory_id' value='0'>";
        
    
    
    echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-1\"><label for=\"product_id\" class=\"form-control-label\">\n";
            echo gettext("Product:");
            echo "</label></div>\n";
            echo "<div class=\"col col-md-2\">\n";
            echo "<select name=\"product_id\" id=\"product_id\" class=\"form-control\"";
            echo " onChange=\"ajax_call('product_to_workorder',this.value,'".$_GET['param3']."',".$_GET['param4'].",'".$_GET['param5']."','".URL."index.php','for_ajaxcall')\">";
            
            echo "<option value=\"0\">".gettext("Please select")."</option>\n";
           echo "<option value=\"new\">".gettext("New product")."</option>\n";
            $SQL="SELECT product_id, product_type_".$lang.", product_properties_".$lang." FROM products WHERE ";
            if ((int) $_GET['param3']>0)
            $SQL.="category_id=".(int) $_GET['param3'];
            else if ($category_id>0)
            $SQL.="category_id=".$category_id;
            
            
            $SQL.=" AND product_stockable<3";
            
            
            
            if ($_GET['param4']>0)
            $SQL.=" AND subcategory_id=".$_GET['param4'];
            if ($subcategory_id>0)
            $SQL.=" AND subcategory_id=".$subcategory_id;
            $SQL.=" ORDER BY product_type_".$lang;
            if (LM_DEBUG)
            error_log($SQL,0);
            $result=$dba->Select($SQL);
            foreach ($result as $row){
            echo "<option value=\"".$row["product_id"]."\"";
            if ((int) $_GET['param2']>0 && $_GET['param2']==$row["product_id"])
            echo " selected";
            echo ">".$row["product_type_".$lang]." ".$row["product_properties_".$lang]."</option>\n";
            }
            echo "</select>";
        echo "</div>";
        echo "</div>"; 
       if ($_GET['param2']>0){ 
        $unit=get_quantity_unit_from_product_id($_GET['param2']);
        
        echo "<div class=\"row form-group\">";
        echo "<div class=\"col col-md-1\"><label for=\"quantity\" class=\" form-control-label\">".gettext("Quantity:")."</label></div>";
        
        echo "<div class=\"col col-md-3\">";
        $SQL="SELECT stock_id,stock_location_id,stock_location_asset_id,stock_location_partner_id,stock_quantity,stock_place FROM stock WHERE product_id=".(int) $_GET['param2'];
        $result=$dba->Select($SQL);
         if (LM_DEBUG)
    error_log($SQL,0);
        if ($dba->affectedRows()>0)
        {
        echo "<table id=\"stock-table\" class=\"table table-striped table-bordered\">\n";
        echo "<thead><tr><th>".gettext("Location")."</th><th>".gettext("In stock")."</th><th>".gettext("Quantity")."</th></tr></thead>\n";
        echo "<tbody>";    
            foreach ($result as $row){
            echo "<tr><td>".get_location_name_from_id($row['stock_location_id'],$lang)." ".$row['stock_place']."</td>";
            echo "<td>".$row["stock_quantity"]." ".$unit[0]."</td>";
            echo "<td>";
            echo "<input type=\"text\" id=\"stock_id".$row['stock_id']."\" name=\"stock_id".$row['stock_id']."\" placeholder=\"".gettext("quantity")."\" class=\"form-control\" value=\"0\" onChange=\"if (this.value>0){
            if (this.value>".(float) $row["stock_quantity"].")
            {
            alert('".gettext("It can not be more than ").$row["stock_quantity"]."');
            this.value=".(float) $row["stock_quantity"].";
            }
            document.getElementsByClassName('card-footer')[0].style.display='block';}
            else 
            document.getElementsByClassName('card-footer')[0].style.display='none';\"> ".$unit[0];
            echo "</td></tr>\n";
            }
        echo "</tbody></table>\n";
        echo "<div class=\"card-footer\" style=\"display:none;\">";
    if ($_SESSION['ADD_PRODUCT_WORKORDER']){
    echo "<button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button>";
    }else
    echo gettext("You have no permission!");
    echo "</div>\n";
    echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"workorders\">";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

    echo "<input type=\"hidden\" name=\"".$_GET['param1']."\" id=\"".$_GET['param1']."\" value=\"1\">";
    echo "<INPUT TYPE='hidden' id='workorder_id' name='workorder_id' value='".$_GET['param5']."'>";
    echo "</form>";
        
        }
        else
        echo gettext("Out of stock");
        echo "</div>\n";
        echo "</div>\n";
      
}
       
echo "</div>\n";

?>
   <script>
$(function(){
    $("#prod_id").focus();
});

   $('#product_id').change(function(){
  //this is just getting the value that is selected
  if ($(this).val()=='new'){
  var title = '<?php echo gettext("Add new product"); ?>';
  $('.modal-title').html(title);
  $('.modal').modal('show'); 
}});</script>


<?php
require(INCLUDES_PATH."workorder_consumption.php");
}

else if(isset($_GET['param1']) && $_GET['param1']=="workorders_consumption_from_asset_id"){

echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
include(INCLUDES_PATH."workorders_consumption_from_asset_id.php"); 
}



else if(isset($_GET['param1']) && $_GET['param1']=="add_product"){

include(INCLUDES_PATH."modals/modal_add_new_product_form.php"); 

echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
 echo "<div class=\"card-body card-block\">";
 echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\">";
 echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-3\"><label for=\"product_id\" class=\"form-control-label\">".gettext("Add product:")."</label></div>\n";
    echo "<div class=\"col col-md-2\">";
        echo "<select name=\"product_id\" id=\"product_id\" class=\"form-control\")\">\n";
        echo "<option value=\"0\">".gettext("Please select")."</option>\n";
        echo "<option value='new'>".gettext("New product");
        $SQL="SELECT product_id,product_type_".$lang." FROM products";
        $SQL.=" WHERE category_id='".$_GET['param3']."'";
        $SQL.=" ORDER BY product_type_".$lang;
    if (LM_DEBUG)
        error_log("Add product: ".$SQL,0);
        $result=$dba->Select($SQL);
      
        foreach ($result as $row){
        echo "<option value=\"".$row["product_id"]."\"";
        echo ">".$row["product_type_".$lang]."</option>\n";
    
        }
        echo "</select></div></div>";
        
    echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div>\n";
    echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"".$_GET['param5']."\">";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

    echo "<input type=\"hidden\" name=\"category_id\" id=\"category_id\" value=\"".(int) $_GET['param3']."\">";
    echo "<input type=\"hidden\" name=\"asset_id\" id=\"asset_id\" value=\"".(int) $_GET["param2"]."\">";

    echo "</form></div>\n";?>
  <script><?php //for the modal_add_new_product_form.php (add product to asset > new product) ?>
  $('#product_id').change(function(){
  //this is just getting the value that is selected
  if ($(this).val()=='new'){
  var title = '<?php echo gettext("Add new product"); ?>';
  $('.modal-title').html(title);
  $('.modal').modal('show'); 
}});</script>  
<?php
}

else if(isset($_GET['param1']) && $_GET['param1']=="show_workorder_detail"){

if ($_GET['param3']=='back_to_stock' && (int) $_GET['param4']>0)
    {
    if (restore_stock_movement($_GET['param4']))
        lm_info(gettext('The product has backed to stock'));
    else
        lm_info(gettext('Failed to back the product to stock'));
    
    }



$SQL="SELECT * FROM workorders WHERE workorder_id='".(int) $_GET['param2']."'";
$row=$dba->getRow($SQL);
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">";
echo "<span aria-hidden=\"true\">×</span></button>";
echo "<div class=\"card\">";
    echo "<div class=\"card-header\">\n";
    $k="";
        $n="";

        foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."-><wbr>";
        }
        echo "<strong>".gettext("Show workorder details...")." ".substr($n,0,-7)."</strong>\n";
        echo "</div>\n";
        echo "<div class=\"card-body card-block\">";
        echo "<form class=\"form-horizontal\">\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"ordered_by\" class=\"form-control-label\">".gettext("Ordered by:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".get_username_from_id($row["user_id"])."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"order_time\" class=\"form-control-label\">".gettext("Order time:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["workorder_time"]."</div></div>\n";
        
        if (!empty($row["workorder_deadline"])){
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"order_time\" class=\"form-control-label\">".gettext("Workorder deadline:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["workorder_deadline"]."</div></div>\n";
        }
       
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"workorder\" class=\"form-control-label\">".gettext("Workorder:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["workorder_".$lang]."</div></div>\n";
        
        if ($row["replace_to_product_id"]>0)
        {
            echo "<div class=\"row form-group\">\n";
            echo "<div class=\"col col-md-2\"><label for=\"replace_to_product_id\" class=\"form-control-label\">".gettext("Replace to:")."</label></div>\n";
            echo "<div class=\"col-12 col-md-3\">".get_product_name_from_id($row["replace_to_product_id"],$lang)."</div></div>\n";
        } else if ($row["product_id_to_refurbish"]>0)
        {
            echo "<div class=\"row form-group\">\n";
            echo "<div class=\"col col-md-2\"><label for=\"product_id_to_refurbish\" class=\"form-control-label\">".gettext("Refurbish:")."</label></div>\n";
            echo "<div class=\"col-12 col-md-3\">".get_product_name_from_id($row["product_id_to_refurbish"],$lang)."</div></div>\n";
        }
        if ($row["workorder_partner_id"]>0)
        {
            echo "<div class=\"row form-group\">\n";
            echo "<div class=\"col col-md-2\"><label for=\"workorder_partner_id\" class=\"form-control-label\">".gettext("Partner:")."</label></div>\n";
            echo "<div class=\"col-12 col-md-3\">".get_partner_name_from_id($row["workorder_partner_id"])."</div></div>\n";
        }
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"workorder_short_".$lang."\" class=\"form-control-label\">".gettext("Workorder(short):")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["workorder_short_".$lang]."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"request_type\" class=\"form-control-label\">".gettext("Type:")."</label></div>\n";
        //$activity_types from confg/lm-settings.php
        echo "<div class=\"col-12 col-md-3\">".$activity_types[$row["request_type"]-1]."</div></div>\n";
        
    echo "</form></div>\n";
echo "</div>\n";

require(INCLUDES_PATH."workorder_consumption.php"); 
echo "<div id='info_files'></div>";
if (isset($row["asset_id"])) //when the workorder's type is "refurbish" there is no asset_id
echo "<script>ajax_call('show_info_files','".$row['asset_id']."','assets','','','".URL."index.php','info_files');</script>";
}




else if(isset($_GET['param1']) && $_GET['param1']=="show_stock_movements"){
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
$unit=get_quantity_unit_from_product_id($_GET['param2']);
    $SQL="SELECT stock_movement_id, to_partner_id,from_partner_id,workorder_id,from_stock_location_id,to_stock_location_id,stock_movement_quantity,product_id,from_asset_id,to_asset_id,stock_movement_time, deleted FROM stock_movements WHERE product_id='".(int) $_GET['param2']."'";
   $SQL.=" ORDER BY stock_movement_time DESC"; 
    if (LM_DEBUG)
            error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<div class=\"card\">\n<div class=\"card-header\">";
    echo "<strong>".get_product_name_from_id($_GET['param2'],$lang)."</strong>\n</div><div class=\"card-body\">";
    if (!isset($_SESSION['SEE_PRODUCT_MOVING']))
    lm_die(gettext("You have no permission!"));
    
    if ($dba->affectedRows()>0)
    {
    
    echo "<table id=\"stock_movement-table\" class=\"table table-striped table-bordered\">\n";
    echo "<thead>\n<tr>\n";
    echo "<th></th><th>".gettext("Date")."</th><th>".gettext("Movement")."</th><th>".gettext("Quantity")."</th></tr>";
    echo "<tbody>";
    $i=1;
        foreach($result as $row){
    echo "<tr>\n";
    echo "<td>";
     if (isset($_SESSION["SEE_FILE_OF_PRODUCT_MOVING"]) && isset($row['info_file_id1']) && $row['info_file_id1']>0){
     echo "<a href=\"javascript:ajax_call('show_info_files','".$row['stock_movement_id']."','stock_movements','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-info\"></i> ";
    echo "</a>";
    }
    
    echo "<div class=\"user-area dropdown float-right\">\n";
                            
                             echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
                             
    echo $i++;
     echo "</a>\n";
                             echo "<div class=\"user-menu dropdown-menu\">";
    
    if (isset($_SESSION['ADD_FILE_TO_PRODUCT'])){
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('add_file',".$row['stock_movement_id'].",'stock_movements','".$row['product_id']."','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ".gettext("Add file")."</a>";
                             }
                             
    echo "</div></div>";
    
    echo "</td>";
    echo "<td>".date($lang_date_format, strtotime($row['stock_movement_time']))."</td>\n";
    echo "<td>";
    if ($row['deleted']==1)
        echo "<span class=\"text-danger\">".gettext("DELETED")."</span> ";
    if ($row['to_partner_id']>0)
    echo gettext("To partner").": ".get_partner_name_from_id($row['to_partner_id'])."</td>\n";
    else if ($row['from_partner_id']>0)
    echo gettext("From partner").": ".get_partner_name_from_id($row['from_partner_id'])."</td>\n";
    else if ($row['to_asset_id']>0)
        {
        echo gettext("Built to").": ";
        $k="";
            $n="";
        
            foreach ($asset_path=get_whole_path("asset",$asset_id=$row['to_asset_id'],1) as $k){
                if ($n=="") // the first element is the main asset_id -> ignore it
                $n=" ";
                else
                $n.=$k."-><wbr>";
            }
            
            echo substr($n,0,-7).": ";
        echo "</td>\n";
        }
    else if ($row['from_asset_id']>0)
    {
    echo gettext("Take from").": ";
    $k="";
            $n="";
        
            foreach ($asset_path=get_whole_path("asset",$asset_id=$row['from_asset_id'],1) as $k){
                if ($n=="") // the first element is the main asset_id -> ignore it
                $n=" ";
                else
                $n.=$k."-><wbr>";
            }
            unset($resp);
            echo substr($n,0,-7).": ";
    echo "</td>\n";
        }
    else if ($row['workorder_id']>0)
    {
    echo gettext("Built to").": ";
    $SQL="SELECT product_id_to_refurbish,main_asset_id FROM workorders WHERE workorder_id=".$row['workorder_id'];
    $row1=$dba->getRow($SQL);
   
    if ($row1['main_asset_id']>0)
    echo get_asset_name_from_id($row1['main_asset_id'],$lang);

    else if ($row1['product_id_to_refurbish']>0)
    echo get_product_name_from_id($row1['product_id_to_refurbish'],$lang);
    echo "</td>\n";
    }
    else if ($row['from_stock_location_id']>0 && $row['to_stock_location_id']>0)
    echo gettext("Transfer items from")." ".get_location_name_from_id($row['from_stock_location_id'],$lang)." dest.:".get_location_name_from_id($row['to_stock_location_id'],$lang)."</td>";
    
    else if ($row['from_stock_location_id']>0 && $row['to_stock_location_id']==0)
    echo "<span class=\"text-danger\">".gettext("Lack of inventory:")."</span> ".get_location_name_from_id($row['from_stock_location_id'],$lang)."</td>";
    
    else if ($row['from_stock_location_id']==0 && $row['to_stock_location_id']>0)
    echo "<span class=\"text-danger\">".gettext("Inventory surplus:")."</span> ".get_location_name_from_id($row['to_stock_location_id'],$lang)."</td>";
    
    else
    echo "</td>";
    echo "<td>".round($row['stock_movement_quantity'])." ".$unit[0]."</td>\n";
    echo "</tr>\n";
        
        }
    echo "</tbody></table>\n";
    echo "</div></div>";
    }

}


else if(isset($_GET['param1']) && $_GET['param1']=="show_workrequest_detail"){
if (!$_SESSION['SEE_WORKREQUEST_DETAIL'])
lm_die(gettext("You have no permission!"));
$SQL="SELECT user_id,asset_id,workrequest_".$lang.",workrequest_short_".$lang.",workrequest_time,request_type,priority FROM workrequests WHERE workrequest_id='".(int) $_GET['param2']."'";
if (LM_DEBUG)
        error_log("Workrequest detail: ".$SQL,0);
$row=$dba->getRow($SQL);
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">";
echo "<span aria-hidden=\"true\">×</span></button>";
echo "<div class=\"card\">";
    echo "<div class=\"card-header\">\n";
    $k="";
        $n="";
        if ($row['asset_id']>0)
            {
            foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
                if ($n=="") // the first element is the main asset_id -> ignore it
                $n=" ";
                else
                $n.=$k."-><wbr>";
            }
            }
     
        echo "<strong>".gettext("Show workrequest details...")." ".substr($n,0,-7)."</strong>\n";
    echo "</div>\n";
    echo "<div class=\"card-body card-block\">";
        echo "<form class=\"form-horizontal\">\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"ordered_by\" class=\"form-control-label\">".gettext("Requested by:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".get_username_from_id($row["user_id"])."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"request_time\" class=\"form-control-label\">".gettext("Request time:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["workrequest_time"]."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"workrequest_".$lang."\" class=\"form-control-label\">".gettext("Workrequest:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["workrequest_".$lang]."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"workrequest_short_".$lang."\" class=\"form-control-label\">".gettext("Workrequest(short):")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["workrequest_short_".$lang]."</div></div>\n";
        

        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"request_type\" class=\"form-control-label\">".gettext("Type:")."</label></div>\n";
        //$activity_types from confg/lm-settings.php
        echo "<div class=\"col-12 col-md-3\">".$activity_types[$row["request_type"]-1]."</div></div>\n";
        
        if ($row['request_type']!=1)
        {
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"priority_type\" class=\"form-control-label\">".gettext("Priority:")."</label></div>\n";
        //$activity_types from confg/lm-settings.php
        echo "<div class=\"col-12 col-md-3\">".$priority_types[$row["priority"]-1]."</div></div>\n";
        }
    echo "</form></div>\n";
echo "</div>\n";

}


else if(isset($_GET['param1']) && $_GET['param1']=="show_notification_detail"){
if (!$_SESSION['SEE_NOTIFICATION_DETAILS'])
lm_die(gettext("You have no permission!"));
$SQL="SELECT * FROM notifications WHERE notification_id='".(int) $_GET['param2']."'";
if (LM_DEBUG)
        error_log("Notification detail: ".$SQL,0);
$row=$dba->getRow($SQL);
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">";
echo "<span aria-hidden=\"true\">×</span></button>";
echo "<div class=\"card\">";
    echo "<div class=\"card-header\">\n";
    $k="";
        $n="";
        if ($row['asset_id']>0)
            {
            foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
                if ($n=="") // the first element is the main asset_id -> ignore it
                $n=" ";
                else
                $n.=$k."-><wbr>";
            }
            }
        
        echo "<strong>".gettext("Show notification details...")." ".substr($n,0,-7)."</strong>\n";
    echo "</div>\n";
    echo "<div class=\"card-body card-block\">";
        
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"notified_by\" class=\"form-control-label\">".gettext("Notified by:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".get_username_from_id($row["user_id"])."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"notification_time\" class=\"form-control-label\">".gettext("Notification time:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["notification_time"]."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"notification_short_".$lang."\" class=\"form-control-label\">".gettext("Notification(short):")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["notification_short_".$lang]."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"notification\" class=\"form-control-label\">".gettext("Notification:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["notification_".$lang]."</div></div>\n";
        
            

        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"notification_type\" class=\"form-control-label\">".gettext("Type:")."</label></div>\n";
        //$activity_types from confg/lm-settings.php
        echo "<div class=\"col-12 col-md-3\">".$notification_types[--$row["notification_type"]]."</div></div>\n";
        
     
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"priority_type\" class=\"form-control-label\">".gettext("Priority:")."</label></div>\n";
        //$activity_types from confg/lm-settings.php
        echo "<div class=\"col-12 col-md-3\">".$priority_types[$row["priority"]-1]."</div></div>\n";
        
        if ($row["notification_status"]==5 && !empty($row["notification_closing_time"]))
        {
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"notification_closing_time\" class=\"form-control-label\">".gettext("Closing time").": </label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".date($lang_date_format." H:i", strtotime($row["notification_closing_time"]))."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"reason_to_close_".$lang."\" class=\"form-control-label\">".gettext("Reason to close").": </label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["reason_to_close_".$lang]."</div></div>\n";
        
        }    
    echo "</div>\n";
echo "</div>\n";

}

else if(isset($_GET['param1']) && $_GET['param1']=="show_pin_detail"){
if (!$_SESSION['SEE_PINBOARD'])
lm_die(gettext("You have no permission!"));
$SQL="SELECT * FROM pinboard WHERE pin_id='".(int) $_GET['param2']."'";
if (LM_DEBUG)
        error_log("Pin detail: ".$SQL,0);
$row=$dba->getRow($SQL);
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">";
echo "<span aria-hidden=\"true\">×</span></button>";
echo "<div class=\"card\">";
    echo "<div class=\"card-header\">\n";
    echo "<strong>".gettext("Show pin details...")." "."</strong>\n";
    echo "</div>\n";
    echo "<div class=\"card-body card-block\">";
        
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"pinned_by\" class=\"form-control-label\">".gettext("Pinned by:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".get_username_from_id($row["user_id"])."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"pin_time\" class=\"form-control-label\">".gettext("Pin time:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["pin_time"]."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"pin_short_".$lang."\" class=\"form-control-label\">".gettext("Pin(short):")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["pin_short_".$lang]."</div></div>\n";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"pin\" class=\"form-control-label\">".gettext("Pin:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$row["pin_".$lang]."</div></div>\n";
        
            

        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"pin_type\" class=\"form-control-label\">".gettext("Type:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">".$pin_types[--$row["pin_type"]]."</div></div>\n";
            
    echo "</div>\n";
echo "</div>\n";
echo "<div id='info_files'></div>";
if (isset($row["pin_id"])) //when the workorder's type is "refurbish" there is no asset_id
echo "<script>ajax_call('show_info_files','".$row['pin_id']."','pinboard','','','".URL."index.php','info_files');</script>";

}


else if(isset($_GET['param1']) && $_GET['param1']=="handle_connection"){
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";

if ($_GET['param4']=="delete" && (int)$_GET['param2']>0 && strstr($_GET["param5"],"connection_id")){
                if ($_GET['param3']=="assets"){
                $SQL="UPDATE assets SET ".$_GET["param5"]."=NULL WHERE asset_id=".(int)$_GET["param2"];
                $asset_id=(int)$_GET["param2"];
                $asset_tree_has_changed=array(get_whole_path("asset",$asset_id,1)[0]);
                include(INCLUDES_PATH."asset_tree.php");
                }
                else if ($_GET['param3']=="products")
                $SQL="UPDATE products SET ".$_GET["param5"]."=NULL WHERE product_id=".(int)$_GET["param2"];
                $result=$dba->Query($SQL);               
                if (LM_DEBUG)
                error_log($SQL,0);
            }


echo "<div class=\"card\"\>\n";

 
    echo "<div class=\"card-header\">\n";
    if ($_GET['param3']=='assets')
        echo "<strong>".gettext("Handle connection")." ".get_asset_name_from_id($_GET["param2"],$lang)."</strong><br/>\n";
     if ($_GET['param3']=='products')
        echo "<strong>".gettext("Handle connection")." ".get_product_name_from_id($_GET["param2"],$lang)."</strong><br/>\n";
        if (($_GET['param3']=='assets' && isset($_SESSION['ADD_CONNECTION_TO_ASSET']))|| ($_GET['param3']=='products' && isset($_SESSION['ADD_CONNECTION_TO_PRODUCT'])))
        {
        echo "<button type=\"button\" class=\"btn btn-danger btn-sm\"";
        echo " onClick=\"javascript:ajax_call('handle_connection','".$_GET['param2']."','",$_GET['param3'],"','add_connection','','".URL."index.php','for_ajaxcall')\"";
        echo "> Add connection </button>\n";
        
        echo "<button type=\"button\" class=\"btn btn-danger btn-sm\"";
        
         echo " onClick=\"javascript:ajax_call('handle_connection','".$_GET['param2']."','",$_GET['param3'],"','delete_connection','','".URL."index.php','for_ajaxcall')\"";
         
        echo "> Delete connection </button>\n";
    echo "</div>\n";
    }else
    echo gettext("You have no permission!");
   
    
    if ($_GET['param4']=="add_connection" && (($_GET['param3']=='assets' && isset($_SESSION['ADD_CONNECTION_TO_ASSET']))|| ($_GET['param3']=='products' && isset($_SESSION['ADD_CONNECTION_TO_PRODUCT']))))
        { 
        echo "<div class=\"card-body card-block\">\n";
        echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\">";
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"connection_category_id\" class=\"form-control-label\">".gettext("Connection category:")."</label></div>\n";
        echo "<div class=\"col col-md-2\">";
        echo "<select name=\"connection_category_id\" id=\"connection_category_id\" class=\"form-control\"";
        echo " onChange=\"javascript:ajax_call('handle_connection','".$_GET['param2']."','".$_GET['param3']."','add_connection',this.value,'".URL."index.php','for_ajaxcall')\"";
        echo ">\n";
        $SQL="SELECT connection_category_".$lang.", connection_category_id FROM connection_categories ORDER BY connection_category_".$lang;
        if (LM_DEBUG)
        error_log($SQL,0);
        $result=$dba->Select($SQL);
        echo "<option value=\"0\">".gettext("Please select")."</option>\n";
        foreach ($result as $row)
        {
            echo "<option value=\"".$row["connection_category_id"]."\"";
            if ($_GET['param5']==$row["connection_category_id"])
                echo "selected";
            echo ">".$row["connection_category_".$lang]."</option>\n";
        }
        echo "</select></div></div>";
        
            if ($_GET['param5']>0)
            {
            echo "<div class=\"row form-group\">\n";
                echo "<div class=\"col col-md-2\"><label for=\"connection_id\" class=\"form-control-label\">".gettext("Connection:")."</label></div>\n";
               echo "<div class=\"col col-md-2\">";
                    echo "<select name=\"connection_id\" id=\"connection_id\" class=\"form-control\">\n";
                                            
                    $SQL="SELECT connection_id,connection_name_".$lang." FROM connections WHERE connection_category_id='".$_GET['param5']."'";
                    
                    if (LM_DEBUG)
                    error_log($SQL,0);
                    $result=$dba->Select($SQL);
                    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
                    foreach ($result as $row)
                    {
                        echo "<option value=\"".$row["connection_id"]."\"";
                        echo ">".$row["connection_name_".$lang]."</option>\n";
                    }
                    echo "</select>";
                echo "</div>\n";
            echo "</div>\n";
            
            echo "<div class=\"row form-group\">\n";
                echo "<div class=\"col col-md-2\"><label for=\"connection_type\" class=\"form-control-label\">".gettext("Connection type:")."</label></div>\n";
                echo "<div class=\"col col-md-2\">";
                    echo "<select name=\"connection_type\" id=\"connection_type\" class=\"form-control\">\n";
                    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
                   $i=1;
                foreach ($connection_types as $connection_type)
                echo "<option value=\"".$i++."\">".$connection_type."</option>\n";
                    echo "</select>";
                echo "</div>\n";
            echo "</div>\n";
            
     //echo "</div>\n";  //card-body  
      echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
        echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
        echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button>\n";       
     
            }
        echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"".$_GET['param3']."\">";
        if ($_GET['param3']=='assets')
        echo "<input type=\"hidden\" name=\"asset_id\" id=\"asset_id\" value=\"".$_GET["param2"]."\">";
        else if ($_GET['param3']=='products')
        echo "<input type=\"hidden\" name=\"product_id\" id=\"product_id\" value=\"".$_GET["param2"]."\">";
        echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

        echo "</form></div>";
        }//add_connection
            
    
    echo "<p>".gettext("Connections")."<ul>";
      
        
        $i=0;
  if ($_GET['param3']=='products')
  {
    $SQL="SELECT * FROM products WHERE product_id=".(int) $_GET['param2'];
    $row=$dba->getRow($SQL);
   
    foreach($row as $key=>$value)
        {
       
         if (strstr($key,"connection_id") && $value>0)
            {
            $products_can_connect=array();
            $assets_can_connect=array();
           
            echo " <li><strong>".get_connection_name_from_id($value)." ".get_connection_type_from_id($row['connection_type'.substr($key,13)])."</strong>";
            
                if ($_GET['param4']=="delete_connection")
                {
                echo " <button type=\"button\" id=\"delete_connection_button\" name=\"delete_connection_button\" class=\"btn\" ";
                echo "onClick=\"
                var a=confirm('".gettext("You are about to delete a connection. Are you sure?")."');
                if (a==true)
                ajax_call('handle_connection','".$_GET['param2']."','",$_GET['param3'],"','delete','".$key."','".URL."index.php','for_ajaxcall')\"";
                echo ">".gettext("Delete")."</button>";
                }
            echo "</li>";
        
            $i++;
            $products_can_connect=array_merge(get_products_id_can_connect($value,$row['connection_type'.substr($key,13)]),$products_can_connect);
                
            foreach($products_can_connect as $product_id)
                {
                $SQL="SELECT stock_location_partner_id FROM stock WHERE product_id=".(int) $product_id;
                $row1=$dba->getRow($SQL);
                echo get_product_name_from_id($product_id,$lang);
                if ($_GET['param3']=="assets" && $row['asset_product_id']==$product_id)
                echo " <strong style=\"color:green;\">".gettext("built in")."</strong>";
                else if (get_sum_quantity_from_product_id($product_id)>0)
                echo " <strong style=\"color:green;\">".gettext("in stock")."</strong>";
                else if ($row1['stock_location_partner_id']>0)
                echo " <strong style=\"color:green;\">".get_partner_name_from_id[$row1['stock_location_partner_id']]."</strong>";
                echo "<br/>\n";
                }
            $assets_can_connect=array_merge(get_assets_id_can_connect($value,$row['connection_type'.substr($key,13)]),$assets_can_connect);
                $x=1;
                
            foreach($assets_can_connect as $asset_id)
                {
               
                $SQL="SELECT asset_product_id FROM assets WHERE asset_id=".$asset_id;
                $row2=$dba->getRow($SQL);
                $n="";
                foreach (get_whole_path("asset",$asset_id,1) as $k)
                if ($n=="") // the first element is the main asset_id -> ignore it
                $n=" ";
                else
                $n.=$k."-><wbr>";
                
                echo $x++.". ".substr($n,0,-7);
                
                if ($row2['asset_product_id']==$_GET['param2'])
                echo " <strong style=\"color:green;\">".gettext("built in")."</strong>";
                echo "<br/>\n";
                }
            
            
            
            }       
        }
  } 
  else  if ($_GET['param3']=='assets')
  {
    $SQL="SELECT * FROM assets WHERE asset_id=".(int) $_GET['param2'];
    $row=$dba->getRow($SQL);
    $products_can_connect=array();
     
        foreach($row as $key=>$value)
        {
         if (strstr($key,"connection_id") && $value>0)
            {
            
            echo " <li><strong>".get_connection_name_from_id($value)." ".get_connection_type_from_id($row['connection_type'.substr($key,13)])."</strong>";
            if ($_GET['param4']=="delete_connection"){
            echo " <button type=\"button\" id=\"delete_connection_button\" name=\"delete_connection_button\" class=\"btn\" ";
        echo "onClick=\"ajax_call('handle_connection','".$_GET['param2']."','",$_GET['param3'],"','delete','".$key."','".URL."index.php','for_ajaxcall')\"";
        echo ">".gettext("Delete")."</button>";}
        echo "</li>";

            $i++;
            $products_can_connect=array_merge(get_products_id_can_connect($value,$row['connection_type'.substr($key,13)]),$products_can_connect);
                $x=1;
                foreach($products_can_connect as $product_id)
                {
                $SQL="SELECT stock_location_partner_id,stock_location_asset_id,stock_location_id FROM stock WHERE product_id=".(int) $product_id;
                $row1=$dba->getRow($SQL);
                echo $x++.". ".get_product_name_from_id($product_id,$lang);
                if ($_GET['param3']=="assets" && $row['asset_product_id']==$product_id)
                echo " <strong style=\"color:red;\">".gettext("built in to here")."</strong>";
                else if ($row1['stock_location_id']>0)
                echo " <strong style=\"color:green;\">".gettext("in stock")."</strong>";
                else if ($row1['stock_location_partner_id']>0)
                echo " <strong style=\"color:green;\">".get_partner_name_from_id[$row1['stock_location_partner_id']]."</strong>";
                else if ($row1['stock_location_asset_id']>0)
                echo " <strong style=\"color:green;\">".gettext("built in").get_asset_name_from_id($row1['stock_location_asset_id'],$lang)."</strong>";
                echo "<br/>\n";
                }
            
            
            
            
            }       
        }
  } 
  
   
        if ($i==0)
        echo gettext("There is no connection...");
        
    echo "</ul></p>";
    
echo "</div>\n";//card

}

else if(isset($_GET['param1']) && $_GET['param1']=="add_new_counter"){
if (!$_SESSION['ADD_COUNTER'])
lm_die(gettext("You have no permission!"));
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
echo "<div class=\"card\"><div class=\"card-header\">\n";
    echo "<strong>".gettext("Add new counter to")." ".get_asset_name_from_id($_GET["param2"],$lang)."</strong>\n";
    echo "</div><div class=\"card-body card-block\">";
    echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\">\n";

    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label for=\"counter_unit\" class=\"form-control-label\">".gettext("Counter unit:")."</label></div>\n";
    echo "<div class=\"col-12 col-md-2\">";
            echo "<select name=\"counter_unit\" id=\"counter_unit\" class=\"form-control\">\n";
        $SQL="SELECT unit_id,unit_".$lang." FROM units ORDER BY unit_".$lang;
        $result=$dba->Select($SQL);
        foreach ($result as $row)
        {       
         echo "<option value=\"".$row['unit_id']."\">".$row['unit_'.$lang]."</option>\n";
         }
         echo "</select>";
            
    echo "</div></div>\n";

    
   
    echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div>\n";
    echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"assets\">";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

    echo "<input type=\"hidden\" name=\"asset_id\" id=\"asset_id\" value=\"".$_GET['param2']."\">";

    echo "</div></form>";

}

else if(isset($_GET['param1']) && $_GET['param1']=="add_new_counter_value"){
if (!$_SESSION['ADD_COUNTER_VALUE'])
lm_die(gettext("You have no permission!"));
?>
<div class="card">
<div class="card-header">
<strong><?php echo gettext("New counter value");?></strong>
</div><?php //card header ?>
<div class="card-body card-block">
<form action="index.php" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php
//if ($_GET["new"]=="category" && isset($_GET["parent_id"]) && ($_GET["parent_id"]>0)){
//$SQL="SELECT category_name_$lang FROM categories WHERE category_id='".$_GET["parent_id"]."'";


    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-3\">\n<label for=\"counter_id\" class=\" form-control-label\">".gettext("Counter:")."\n</label>\n</div>\n";

    echo "<div class=\"col col-md-2\">";
    $SQL="SELECT asset_name_".$lang." FROM counters LEFT JOIN assets on counters.asset_id=assets.asset_id WHERE counter_id='".(int) $_GET['param2']."'";
    $row=$dba->getRow($SQL);
if (LM_DEBUG)
   error_log($SQL,0); 
    echo $row['asset_name_'.$lang];
    echo "<INPUT TYPE='hidden' name='counter_id' id='counter_id' value='".(int) $_GET['param2']."'>\n";
    echo "</div></div>\n";
$SQL="SELECT max(counter_value) as value,counter_value_time FROM counter_values WHERE counter_id='".(int) $_GET['param2']."' group by counter_value_time";  
$row=$dba->getRow($SQL);
if (LM_DEBUG)
            error_log($SQL,0); 
echo "<div class=\"row form-group\">\n";
echo "<div class=\"col col-md-3\"\n><label for=\"counter_value\" class=\"form-control-label\">".gettext("Counter value (last ").$row['value']." / ".date("Y.m.d", strtotime($row["counter_value_time"]))."):</label>\n</div>\n";
echo "<div class=\"col-12 col-md-2\">\n<input type=\"text\" id=\"counter_value\" name=\"counter_value\" placeholder=\"".gettext("Counter value")."\" class=\"form-control\" onChange=\"if (this.value<last_counter_value.value){alert('".gettext("The value must be greater than ".$row['value']."!")."');submit.disabled=true;}else submit.disabled=false \">\n<small class=\"form-text text-muted\">".gettext("Counter value")."</small>\n</div>\n";
echo "</div>\n";
 

echo "<div class=\"card-footer\">\n";
echo "<input type=\"hidden\" name=\"last_counter_value\" id=\"last_counter_value\" value=\"".$row['value']."\">\n";
echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"counters\">\n";
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

echo "<button type=\"submit\" name=\"submit\" class=\"btn btn-primary btn-sm\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit");
echo " </button>\n";
echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\">\n";
echo "<i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button>\n";
echo "</form>\n";
echo "</div></div>\n";

echo "</div>\n";

}


else if(isset($_GET['param1']) && $_GET['param1']=="show_partner_detail"){
if (!$_SESSION['SEE_PARTNER_DETAIL'])
lm_die(gettext("You have no permission!"));
$SQL="SELECT * FROM partners WHERE partner_id='".(int) $_GET['param2']."'";
$row=$dba->getRow($SQL);
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">";
echo "<span aria-hidden=\"true\">×</span></button>";
echo "<form class=\"form-horizontal\" method=\"POST\">\n";

echo "<div class=\"card\">";
    echo "<div class=\"card-header\">\n";
           if (isset($_GET['param3']) && $_GET['param3']=='modify')
           echo "<strong>".gettext("Modify partner details...")." ".$row['partner_name']."</strong>\n";
    
    echo "</div>\n";
    
    echo "<div class=\"card-body card-block\">\n";
        
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"partner_name\" class=\"form-control-label\">".gettext("Partner name:")."</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
        echo "<input type='text' name='partner_name' id='partner_name' value='".$row["partner_name"]."'>";
        else
        echo $row["partner_name"];
        echo "</div></div>\n";
        
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"partner_address\" class=\"form-control-label\">".gettext("Address:")."</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
        echo "<input type='text' name='partner_address' id='partner_address' value='".$row["partner_address"]."'>";
        else
        echo $row["partner_address"];
        echo "</div></div>\n";
        
        $i=1;
        while (isset($row["contact".$i."_surname"]))
        {
        echo "<strong>".$i.". ".gettext("contact")."</strong>";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"contact".$i."_firstname_is_first\" class=\"form-control-label\">".gettext("Firstname is first").":</label></div>\n";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
        {
            
            echo "<div class=\"col col-md-3\">";
            echo "<select name=\"contact".$i."_firstname_is_first\" id=\"contact".$i."_firstname_is_first\">";
            echo "<option value='1'";
            if ($row["contact".$i."_firstname_is_first"]==1)
            echo " selected";
            echo ">".gettext("Yes");
            echo "<option value='0'";
            if ($row["contact".$i."_firstname_is_first"]==0)
            echo " selected";
            echo ">".gettext("No");
            echo "</options></select>";
            
        echo "</div>";
        }else{
        if ($row["contact".$i."_firstname_is_first"]==1)
        echo gettext("Yes");
        else
        echo gettext("No");
        }
        echo "</div>\n";
        
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"contact".$i."_surname\" class=\"form-control-label\">".gettext("Name").":</label></div>\n";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
        {
        echo "<select name=\"contact".$i."_title\" id=\"contact".$i."_title\">";
        foreach(TITLES as $key=>$value)
        {
        echo "<option value='".$key++."'";
        if ($key==$row["contact".$i."_title"])
        echo " selected";
        echo ">".$value;
        
        }
        echo "</options>\n";
        echo "</select>\n";
        }
        else
        echo TITLES[$row["contact".$i."_title"]-1]." ";
        
        if ($row['contact'.$i."_firstname_is_first"]==true)
        {
            echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
            {
                echo "<input type='text' placeholder='firstname' name=\"contact".$i."_firstname\" name=\"contact".$i."_firstname\" value=\"".$row["contact".$i."_firstname"]."\" size='5'>";
                echo "<input type='text' placeholder='surname' name=\"contact".$i."_surname\" name=\"contact".$i."_surname\" value=\"".$row["contact".$i."_surname"]."\" size='5'>";
            }
            else
            echo $row["contact".$i."_firstname"]." ".$row["contact".$i."_surname"];
            echo "</div></div>\n";
        }
        else
        {
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
            {
               echo "<input type='text' placeholder='surname' name=\"contact".$i."_surname\" name=\"contact".$i."_surname\" value=\"".$row["contact".$i."_surname"]."\" size='5'>";
               echo "<input type='text' placeholder='firstname' name=\"contact".$i."_firstname\" name=\"contact".$i."_firstname\" value=\"".$row["contact".$i."_firstname"]."\" size='5'>";
               
            }
            else
            echo "<div class=\"col col-md-3\">".$row["contact".$i."_surname"]." ".$row["contact".$i."_firstname"];
            echo "</div></div>\n";
        }
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"contact".$i."_position\" class=\"form-control-label\">".gettext("Position").":</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
         echo "<input type='text' class=\"form-control\" name=\"contact".$i."_position\" name=\"contact".$i."_position\" value=\"".$row["contact".$i."_position"]."\">";   
        else
        echo $row["contact".$i."_position"];
        echo "</div></div>\n";
        
        
         echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"contact".$i."_phone\" class=\"form-control-label\">".gettext("Phone").":</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
         echo "<input type='text' class=\"form-control\" name=\"contact".$i."_phone\" name=\"contact".$i."_phone\" value=\"".$row["contact".$i."_phone"]."\">";   
        else
        echo $row["contact".$i."_phone"];
        echo "</div></div>\n";
        
        
           echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"contact".$i."_email\" class=\"form-control-label\">".gettext("Email").":</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
         echo "<input type='text' class=\"form-control\" name=\"contact".$i."_email\" name=\"contact".$i."_email\" value=\"".$row["contact".$i."_email"]."\">";   
        else
        echo $row["contact".$i."_email"];
        echo "</div></div>\n";
      
        
       $i++;
        }
        #last contact
         echo "<strong>".$i.". ".gettext("contact")."</strong>";
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"contact".$i."_surname\" class=\"form-control-label\">".gettext("Name").":</label></div>\n";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
        {
        echo "<select name=\"contact".$i."_title\" id=\"contact".$i."_title\">";
        foreach(TITLES as $key=>$value)
        {
        echo "<option value='".$key++."'";
        
        echo ">".$value;
        
        }
        echo "</options>\n";
        echo "</select>\n";
        }
        
        if ($row['contact'.$i."_firstname_is_first"]==true)
        {
            echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
            {
                echo "<input type='text' placeholder='firstname' name=\"contact".$i."_firstname\" name=\"contact".$i."_firstname\" value=\"\" size='5'>";
                echo "<input type='text' placeholder='surname' name=\"contact".$i."_surname\" name=\"contact".$i."_surname\" value=\"\" size='5'>";
            }
            echo "</div></div>\n";
        }
        else
        {
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
            {
               echo "<input type='text' placeholder='surname' name=\"contact".$i."_surname\" name=\"contact".$i."_surname\" value=\"\" size='5'>";
               echo "<input type='text' placeholder='firstname' name=\"contact".$i."_firstname\" name=\"contact".$i."_firstname\" value=\"\" size='5'>";
               
            }
            echo "</div></div>\n";
        }
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"contact".$i."_position\" class=\"form-control-label\">".gettext("Position").":</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
         echo "<input type='text' class=\"form-control\" name=\"contact".$i."_position\" name=\"contact".$i."_position\" value=\"\">";   
        
        echo "</div></div>\n";
        
        
         echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"contact".$i."_phone\" class=\"form-control-label\">".gettext("Phone").":</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
         echo "<input type='text' class=\"form-control\" name=\"contact".$i."_phone\" name=\"contact".$i."_phone\" value=\"\">";   
        echo "</div></div>\n";
        
        
           echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"contact".$i."_email\" class=\"form-control-label\">".gettext("Email").":</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify')
         echo "<input type='text' class=\"form-control\" name=\"contact".$i."_email\" name=\"contact".$i."_email\" value=\"\">";   
        echo "</div></div>\n";
        
        
        #last contact end
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"partner_tags\" class=\"form-control-label\">".gettext("Partner tags:")."</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        if (isset($_GET['param3']) && $_GET['param3']=='modify'){
        echo "<textarea name='partner_tags' id='partner_tags'>";
        echo $row["partner_tags"];
        echo "</textarea>\n";}
        else
        echo $row["partner_tags"];     
        echo "</div></div>\n";
        
 if (isset($_GET['param3']) && $_GET['param3']=='modify' && $_SESSION['user_level']<3)
    {
    if (!$_SESSION['MODIFY_PARTNER'])
lm_die(gettext("You have no permission!"));
    echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext("Submit")."</button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div>\n";
    echo "<input type='hidden' name='page' id='page' value='partners'>";
    echo "<input type='hidden' name='partner_id' id='partner_id' value='".$_GET['param2']."'>";
    echo "<input type='hidden' name='valid' id='valid' value='".$_SESSION['tit_id']."'>";
    }
    echo "</form></div>\n";
echo "</div></div>\n";
}

else if(isset($_GET['param1']) && $_GET['param1']=="show_assets_with_this_product"){
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
echo "<div class=\"card\"\>\n";

 
    echo "<div class=\"card-header\">\n";
    if ($_GET['param3']=='assets')
        echo "<strong>".gettext("Assets with:")." ".get_asset_name_from_id($_GET["param2"],$lang)."</strong><br/>\n";
     if ($_GET['param3']=='products')
        echo "<strong>".gettext("Assets with:")." ".get_product_name_from_id($_GET["param2"],$lang)."</strong><br/>\n";
    echo "</div>";//card-header
    echo "<div class= \"card-body\">\n";
    
$category_id=lm_isset_int('category_id');  
$subcategory_id=lm_isset_int('subcategory_id');  
    
$SQL="SELECT asset_id FROM assets WHERE asset_product_id='".(int) $_GET['param2']."' ORDER BY asset_name_".$lang;
$result=$dba->Select($SQL);
if (LM_DEBUG)
            error_log($SQL,0); 
if ($dba->affectedRows()>0)
{
    echo "<table id=\"bootstrap-data-table\" class=\"table table-striped table-bordered\">\n";
    echo "<thead>\n";
    echo "<tr>\n";
    echo "<th></th><th>".gettext("Asset")."</th>";
    echo "</tr></thead>\n";
    echo "<tbody>\n";
    $i=1;
    foreach ($result as $row)
    {
    echo "<tr><td>".$i++."</td>\n<td>";
    
    $n="";
    foreach (get_whole_path("asset",$row['asset_id'],1) as $k)
    if ($n=="") // the first element is the main asset_id -> ignore it
    $n=" ";
    else
    $n.=$k."-><wbr>";
    echo substr($n,0,-7);
    
    echo "</td></tr>\n";
    }
echo "</tbody></table>\n";
  
}
else
echo gettext("There is no asset with this product.");
echo "</div>";//card
}

else if(isset($_GET['param1']) && $_GET['param1']=="show_assets_products_with_this_connection"){
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
echo "<div class=\"card\"\>\n";

 
    echo "<div class=\"card-header\">\n";
   
        echo "<strong>".gettext("Assets with:")." ".get_connection_name_from_id($_GET["param2"],$lang)."</strong><br/>\n";
    
    echo "</div>";//card-header
    echo "<div class= \"card-body\">\n";
    
    
$SQL="SELECT asset_id, asset_name_".$lang." FROM assets WHERE ";
    $SQL1="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='assets' AND COLUMN_NAME LIKE 'connection_id%'";

    $result1=$dba->Select($SQL1);
    $n=0;
        foreach($result1 as $row1){
        if ($n>0)
        $SQL.=" OR ";
        $SQL.=$row1['COLUMN_NAME'] ."=".(int) $_GET['param2'];
        if ($_GET['param3']>0)
        $SQL.=" AND connection_type".substr($row1['COLUMN_NAME'],13,strlen($row1['COLUMN_NAME']))."=".(int) $_GET['param3'];
        $n++;
        }

$SQL.=" ORDER BY asset_name_".$lang;

$result=$dba->Select($SQL);
if (LM_DEBUG)
            error_log($SQL,0); 
if ($dba->affectedRows()>0)
{
    echo "<table id=\"bootstrap-data-table\" class=\"table table-striped table-bordered\">\n";
    echo "<thead>\n";
    echo "<tr>\n";
    echo "<th></th><th>".gettext("Asset")."</th>";
    echo "</tr></thead>\n";
    echo "<tbody>\n";
    $i=1;
    foreach ($result as $row)
    {
    echo "<tr><td>".$i++."</td>\n<td>";
    
    $n="";
    foreach (get_whole_path("asset",$row['asset_id'],1) as $k)
    if ($n=="") // the first element is the main asset_id -> ignore it
    $n=" ";
    else
    $n.=$k."-><wbr>";
    echo substr($n,0,-7);
   
    echo "</td></tr>\n";
    }
echo "</tbody></table>\n";
  
}
else
echo gettext("There is no asset with this connection.");
echo "</div>";//card
//end assets

//products
echo "<div class=\"card\"\>\n";

 
    echo "<div class=\"card-header\">\n";
   
        echo "<strong>".gettext("Products with:")." ".get_connection_name_from_id($_GET["param2"],$lang)."</strong><br/>\n";
    
    echo "</div>";//card-header
    echo "<div class= \"card-body\">\n";
    
    
$SQL="SELECT product_id, product_type_".$lang." FROM products WHERE ";
    $SQL1="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='products' AND COLUMN_NAME LIKE 'connection_id%'";

    $result1=$dba->Select($SQL1);
    $n=0;
        foreach($result1 as $row1){
        if ($n>0)
        $SQL.=" OR ";
        $SQL.=$row1['COLUMN_NAME'] ."=".(int) $_GET['param2'];
        if ($_GET['param3']>0)
        $SQL.=" AND connection_type".substr($row1['COLUMN_NAME'],13,strlen($row1['COLUMN_NAME']))."=".(int) $_GET['param3'];
        $n++;
        }

$SQL.=" ORDER BY product_type_".$lang;

$result=$dba->Select($SQL);
if (LM_DEBUG)
            error_log($SQL,0); 
if ($dba->affectedRows()>0)
{
    echo "<table id=\"bootstrap-data-table\" class=\"table table-striped table-bordered\">\n";
    echo "<thead>\n";
    echo "<tr>\n";
    echo "<th></th><th>".gettext("Product")."</th>";
    echo "</tr></thead>\n";
    echo "<tbody>\n";
    $i=1;
    foreach ($result as $row)
    {
    echo "<tr><td>".$i++."</td>\n<td>";
    echo get_product_name_from_id($row['product_id'],$lang);
    echo "</td></tr>\n";
    }
echo "</tbody></table>\n";
  
}
else
echo gettext("There is no product with this connection.");
echo "</div>";//card


//end products
}



else if (isset($_GET['param1']) && $_GET['param1']=="show_user_detail"){//from users.php
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>\n";
echo "<script src=\"".INCLUDES_LOC."javascripts.js\"></script>\n";
echo "<div class=\"card\"\>\n";
if (!$_SESSION['SEE_USER_DETAIL'])
lm_die(gettext("You have no permission!"));
$SQL="SELECT * FROM users WHERE user_id='".(int) $_GET['param2']."'";
$row=$dba->getRow($SQL);
    echo "<div class=\"card-header\">\n";
    echo "<strong>".gettext("Show user details...")." ".get_username_from_id($_GET['param2'])."</strong>\n";
    echo "</div>";//card-header
    
    

 echo "<form class=\"form-horizontal\" method=\"POST\">\n";
    echo "<div class= \"card-body\">\n";
    echo "<button type='button' onClick='invert_check()' class=\"btn btn-primary btn-sm\">".gettext("Invert check")."</button> ";
echo "<button type='button' onClick='check_uncheck_all()' class=\"btn btn-primary btn-sm\">".gettext("Check/uncheck all")."</button><br/></br>";
    $i=0;
    foreach ($priviliges as $p){//from lm-settings.php
           
        if ($p!="break") {$i++;echo $p." <INPUT STYLE='margin-right:1em;' TYPE='checkbox' name='".$p."'";
            if (isset($row[$p]) && $row[$p]==1)
            echo " checked value=1>";
            else
            echo " value=1>";
            
            //echo " onChange=\"ajax_call('show_user_detail','".$_GET['param2']."','users','','','".URL."index.php','for_ajaxcall')\">  ";
            }
            else{ 
            echo "<br/>";
            $i=0;
            }
            ;
    if ($i%3==0) echo "<br/>";
    }
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"users\">";
echo "<input type=\"hidden\" name=\"action\" id=\"action\" value=\"user_priv_mod\">";
echo "<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"".(int) $_GET['param2']."\">";


echo "</div>";//card-body
    echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext("Submit")."</button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div></form>\n";
echo "</div>";//card    
}



else if (isset($_GET['param1']) && $_GET['param1']=="show_users_office-hours"){// from users.php
    if ($_GET['param4']!="" && isset($_SESSION['MODIFY_USER']))
    {
    echo "<div class=\"card\"><div class=\"card-header\">";
    echo "<strong>".gettext("Modify ").get_username_from_id($_GET['param2'])."'s ".$_GET['param4']."</strong></div>\n";
    ?>
    <div class="card-body card-block">
    <form action="index.php" id="category_form" method="post" enctype="multipart/form-data" class="form-horizontal">
    
    <?php echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-3\"><label for=\"".$_GET["param4"]."\" class=\" form-control-label\">".gettext("New value:")."</label></div>";
    
    echo "<div class=\"col-12 col-md-2\"><input type=\"time\" id=\"office_time\" name=\"office_time\" class=\"form-control\"";
    $SQL="SELECT ".$dba->escapeStr($_GET['param4'])." as val FROM users WHERE user_id=".(int) $_GET['param2'];
    $row=$dba->getRow($SQL);
    echo " VALUE='".date("H:i", strtotime($row['val']))."'";
    echo " required></div>\n";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
    echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"users\">";
    echo "<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"".(int) $_GET['param2']."\">";
    echo "<input type=\"hidden\" name=\"office_hours\" id=\"office_hours\" value=\"".$dba->escapeStr($_GET['param4'])."\">";
    
    echo "</div>";
    
    echo "<div class=\"card-footer\">\n";
    echo "<button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\">";
    echo "<i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div>\n";
    }

echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>\n";
?>
<div class="card-body">
<table id="bootstrap-data-table" class="table table-striped table-bordered">
<thead>
<?php
$SQL="SELECT * FROM users WHERE user_id=".(int) $_GET['param2'];
$row=$dba->getRow($SQL);
echo "<tr><th></th><th>".gettext("Start")."</th><th>".gettext("End")."</th></tr><thead>\n<tbody>";
$week_of_day=$days = ['Monday'=>gettext('Monday'),'Tuesday'=>gettext('Tuesday'),'Wednesday'=>gettext('Wednesday'),'Thursday'=>gettext('Thursday'),'Friday'=>gettext('Friday'),'Saturday'=>gettext('Saturday'),'Sunday'=>gettext('Sunday')];

foreach ($week_of_day as $key=>$value){
/*
$SQL="ALTER TABLE `libremaint`.`users` 
ADD COLUMN `".$value."_start` TIME(4) DEFAULT '07:00'";
$dba->Query($SQL);

$SQL="ALTER TABLE `libremaint`.`users` 
ADD COLUMN `".$value."_end` TIME(4) DEFAULT '20:00'";
$dba->Query($SQL);
*/

echo "<tr><td>".$value."</td><td";
if(isset($_SESSION['MODIFY_USER']))
echo " onClick=\"ajax_call('show_users_office-hours','".(int) $_GET['param2']."','users','".$key."_start','','".URL."index.php','for_ajaxcall')\"";
echo ">".date("H:i", strtotime($row[$key.'_start']))."</td>\n";
echo "<td";
if(isset($_SESSION['MODIFY_USER']))
echo " onClick=\"ajax_call('show_users_office-hours','".(int) $_GET['param2']."','users','".$key."_end','','".URL."index.php','for_ajaxcall')\"";
echo ">".date("H:i", strtotime($row[$key.'_end']))."</td></tr>";
}
echo "</tbody></table>";

}







else if (isset($_GET['param1']) && $_GET['param1']=="show_users_assets"){// from users.php
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>\n";
echo "<script src=\"".INCLUDES_LOC."javascripts.js\"></script>\n";

$SQL="SELECT asset_id,asset_name_".$lang." FROM assets WHERE asset_parent_id=0 ORDER BY asset_name_".$lang;
$result=$dba->Select($SQL);


$i=0;

$SQL="SELECT users_assets FROM users WHERE user_id=".(int) $_GET['param2'];
$row=$dba->getRow($SQL);
if (!empty($row['users_assets']))
$users_assets=json_decode($row['users_assets'],true);
else
$user_assets=array();
echo "<div class='card'>\n";
echo "<div class='card-header'>\n";
echo gettext("Assets belong to").": ".get_username_from_id($_GET['param2'])."</div>";
echo "<form class=\"form-horizontal\" method=\"POST\" onSubmit=\"return users_assets_to_json()\">\n";
echo "<div class='card-body'>";
echo "<button type='button' onClick='invert_check()' class=\"btn btn-primary btn-sm\">".gettext("Invert check")."</button> ";
echo "<button type='button' onClick='check_uncheck_all()' class=\"btn btn-primary btn-sm\">".gettext("Check/uncheck all")."</button><br/></br>";
foreach ($result as $row){
echo "<INPUT STYLE='margin-left:1em;margin-right:1em;' TYPE='checkbox' name='users_assets[]'";
            if (isset($users_assets) && in_array($row['asset_id'],$users_assets))
            echo " checked value=".$row['asset_id'].">";
            else
            echo " value=".$row['asset_id']."> ";
echo $row['asset_name_'.$lang];
$i++;
if ($i%2==0) echo "<br/>";

}
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
    echo "<input type=\"hidden\" name=\"users_assets_json\" id=\"users_assets_json\">";
    echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"users\">";
    echo "<input type=\"hidden\" name=\"action\" id=\"action\" value=\"users_assets\">";
    echo "<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"".(int) $_GET['param2']."\">";



    echo "</div><div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext("Submit")."</button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div></form>\n";


}


else if (isset($_GET['param1']) && $_GET['param1']=="show_users_entry_points"){// from users.php
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>\n";


?><script>
function users_entry_points_to_json(){
checkboxes = document.querySelectorAll('input[name="users_entry_points[]"]');
var users_entry_points=[];
checkboxes.forEach(e => { 
    if (e.checked){
    users_entry_points.push(e.value);
    
    }
})

document.getElementById("users_entry_points_json").value=JSON.stringify(users_entry_points);

}

function check_uncheck(){

  var checkboxes = document.querySelectorAll('input[type="checkbox"]');
  for (var checkbox of checkboxes) {
    if (checkbox.checked)
    checkbox.checked = false;
    else
    checkbox.checked=true;
    
  }



}
</script>
<?php

$SQL="SELECT asset_id,asset_name_".$lang." FROM assets WHERE entry_point=1 ORDER BY asset_name_".$lang;
$result=$dba->Select($SQL);


$i=0;

$SQL="SELECT users_entry_points FROM users WHERE user_id=".(int) $_GET['param2'];
$row=$dba->getRow($SQL);
if (!empty($row['users_entry_points']))
$users_entry_points=json_decode($row['users_entry_points'],true);
else
$users_entry_points=array();
echo "<div class='card'>\n";
echo "<div class='card-header'>\n";
echo gettext("Entry points access for ").get_username_from_id((int) $_GET['param2'])."</div>";
echo "<form class=\"form-horizontal\" method=\"POST\" onSubmit=\"return users_entry_points_to_json()\">\n";
echo "<div class='card-body'>";
echo "<button type='button' onClick='check_uncheck()' class=\"btn btn-primary btn-sm\">".gettext("Check all")."</button><br/>";

foreach ($result as $row){
echo "<INPUT STYLE='margin-left:1em;margin-right:1em;' TYPE='checkbox' name='users_entry_points[]'";
            if (in_array($row['asset_id'],$users_entry_points))
            echo " checked value=".$row['asset_id'].">";
            else
            echo " value=".$row['asset_id']."> ";
 $k="";
                                $n="";

                                foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
                                    if ($n=="") // the first element is the main asset_id -> ignore it
                                    $n=" ";
                                    else
                                    $n.=$k."-><wbr>";
                                }
                                
                                echo substr($n,0,-7);
            
$i++;
if ($i%2==0) echo "<br/>";

}
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
    echo "<input type=\"hidden\" name=\"users_entry_points_json\" id=\"users_entry_points_json\">";
    echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"users\">";
    echo "<input type=\"hidden\" name=\"action\" id=\"action\" value=\"users_entry_points\">";
    echo "<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"".(int) $_GET['param2']."\">";



    echo "</div><div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext("Submit")."</button>\n";
    echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div></form>\n";


}





else if (isset($_GET['param1']) && $_GET['param1']=="show_builtable_products")
{
    $i=0;
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>\n";
echo "<div class=\"card\"\>\n";
if (!$_SESSION['SEE_CONNECTION_OF_ASSET'])
lm_die(gettext("You have no permission!"));
 echo "<div class=\"card-header\">\n";
    echo "<strong>".gettext("Products can be built to")." ".get_asset_name_from_id($_GET['param2'],$lang)."</strong>\n";
    echo "</div>";//card-header
echo "<div class= \"card-body\">\n";
    
$SQL="SELECT * FROM assets WHERE asset_id=".(int) $_GET['param2'];
    $row=$dba->getRow($SQL);
 
if ($row['asset_product_id']>0)
{
$SQL="SELECT product_stockable FROM products WHERE product_id=".$row['asset_product_id'];
$row1=$dba->getRow($SQL);
        if ($row1['product_stockable']==1)
        {
        echo "<strong>".gettext("The product built in:")."</strong> ".get_product_name_from_id($row['asset_product_id'],$lang)." <mark>".Luhn($row['asset_product_id'])."</mark> ";

        $in_stock=get_sum_quantity_from_product_id($row['asset_product_id']);
        if ($in_stock>0)
        echo "<strong style=\"color:green;\">";
        else
        echo "<strong style=\"color:red;\">";

        echo gettext("in stock").": ".$in_stock." ".get_quantity_unit_from_product_id($row['asset_product_id'])[0];
        echo "</strong>";
        }
}
    
    $products_can_connect=array();
        foreach($row as $key=>$value)
        {
         if (strstr($key,"connection_id") && $value>0)
            {
            
            echo "<li><strong>".get_connection_name_from_id($value)." ".get_connection_type_from_id($row['connection_type'.substr($key,13)])."</strong></li>";
            $i++;
            $products_can_connect=array_merge(get_products_id_can_connect($value,$row['connection_type'.substr($key,13)]),$products_can_connect);
                
                foreach($products_can_connect as $product_id)
                {
                $SQL="SELECT stock_location_partner_id,stock_location_asset_id FROM stock WHERE product_id=".(int) $product_id;
                $row1=$dba->getRow($SQL);
                echo get_product_name_from_id($product_id,$lang)." <mark>".Luhn($product_id)."</mark>";
                
                if ($row['asset_product_id']==$product_id)
                echo " <strong style=\"color:green;\">".gettext("built in here")."</strong>";
                else if ($row1['stock_location_asset_id']>0){
                echo " <strong style=\"color:green;\">".gettext("built in"); 
                $n="";
                foreach (get_whole_path("asset",$row1['stock_location_asset_id'],1) as $k){
                if ($n=="") // the first element is the main asset_id -> ignore it
                $n=" ";
                else
                $n.=$k."-><wbr>";}
                
                if ($n!="")
                echo substr($n,0,-7);
                echo "</strong>";}
                else if ($row1['stock_location_partner_id']>0)
echo " <strong style=\"color:green;\">".get_partner_name_from_id($row1['stock_location_partner_id'])."</strong>";
                else if (get_sum_quantity_from_product_id($product_id)>0)
                echo " <strong style=\"color:green;\">".gettext("in stock")." ".get_sum_quantity_from_product_id($product_id)." ".get_quantity_unit_from_product_id($product_id)[0]."</strong>";
                echo "<br/>\n";
                }
            
            
            
            
            }       
        }
echo "</div></div>";
}


else if (isset($_GET['param1']) && $_GET['param1']=="show_assets_on_this_place")
{
echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>\n";
echo "<div class=\"card\"\>\n";
 echo "<div class=\"card-header\">\n";
    echo "<strong>".gettext("Assets on this place:")." ".get_location_name_from_id($_GET['param2'],$lang)."</strong>\n";
    echo "</div>";//card-header
echo "<div class= \"card-body\">\n";

function sweep($location_id){    
global $dba,$lang,$i;

$SQL="SELECT location_parent_id, location_id,location_name_".$lang." FROM locations WHERE location_parent_id=".$location_id." ORDER BY location_parent_id";
$result=$dba->Select($SQL);


if ($sum=$dba->affectedRows()>0)
{
$i++;
foreach($result as $row)
    { 
    $n="";
    for ($j = 1; $j <$i; $j++) {
    $n.=" > ";
    }
    echo $n."<strong>".$row["location_name_".$lang]."</strong><br/>";
    
    $SQL="SELECT * FROM assets WHERE asset_location=".$row['location_id']." ORDER BY asset_name_".$lang;
        $asset_result=$dba->Select($SQL);
    foreach ($asset_result as $asset_row){
        echo $n.$asset_row["asset_name_".$lang]."<br/>";
     
    }
    sweep($row["location_id"]);
    }
    
    }
    else if ($sum==0)
    $i--;
}    
$i=0;//global
sweep($_GET['param2']);
unset($i);
echo "</div></div>";    
}
else if (isset($_GET['param1']) && $_GET['param1']=="show_asset_note"){ 

$SQL="SELECT asset_note,asset_note_conf FROM assets WHERE asset_id=".(int) $_GET['param2'];
$row=$dba->getRow($SQL);
if (!empty($row['asset_note']) && isset($_SESSION["SEE_FILE_OF_ASSET"]))
echo "<p>".$row['asset_note']."</p>";
if (!empty($row['asset_note_conf']) && isset($_SESSION["SEE_CONF_FILE_OF_ASSET"]))
echo "<br/><p>".$row['asset_note_conf']."</p>";

}

else if (isset($_GET['param1']) && $_GET['param1']=="product_moving_from_stock_to_stock"){ 

echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
echo "<span aria-hidden=\"true\">×</span>\n</button>";
echo "<div class=\"card\"><div class=\"card-header\">\n";
echo "<strong>".get_product_name_from_id((int) $_GET['param2'],$lang)." ".gettext("moving from")." ".get_location_name_from_id((int) $_GET['param3'],$lang)."</strong></div>\n";


echo "<div class=\"card-body card-block\">";
echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\">\n";

echo "<div class=\"row form-group\">\n";
 echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"dest_stock_location_id\" class=\"form-control-label\">".gettext("Destination:")."</label>";
    echo "</div>\n";
    echo "<div class=\"col col-md-3\">";
    echo "<select name=\"dest_stock_location_id\" id=\"dest_stock_location_id\" class=\"form-control\">\n";
    $SQL="SELECT location_id,location_name_".$lang." FROM locations WHERE set_as_stock=1";
    $SQL.=" ORDER BY location_name_".$lang;
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"\">".gettext("Please select")."</option>\n";
   
    foreach ($result as $row){
    echo "<option value=\"".$row["location_id"]."\"";
    
    echo ">".$row["location_name_".$lang]."</option>\n";
   
    }
   echo "</select>";
     echo "</div></div>";
     
   echo "<div class=\"row form-group\">\n";
   echo "<div class=\"col col-md-2\">\n";  
   echo "<label for=\"dest_stock_place\" class=\"form-control-label\">".gettext("Dest. stock place")."</label></div>";
   echo "<div class=\"col col-md-3\">\n";
   echo "<INPUT TYPE=text name=\"dest_stock_place\" id=\"dest_stock_place\" class=\"form-control-label\">";
   echo "</div></div>";
   
   
   
   $SQL="SELECT stock_quantity,stock_place FROM stock WHERE product_id=".(int) $_GET['param2']." AND stock_location_id=".(int) $_GET['param3']." ORDER BY stock_place";
    $result=$dba->Select($SQL);
   $unit=get_quantity_unit_from_product_id((int) $_GET['param2']);
   foreach ($result as $row){     
   echo "<div class=\"row form-group\">\n";
   echo "<div class=\"col col-md-2\">\n";
   echo "<label for=\"quantity_".$row['stock_place']."\" class=\"form-control-label\">".gettext("Quantity ")." (max ".round($row['stock_quantity'])." ".$unit[0].") ".$row['stock_place']."</label>";
   echo "</div>\n";
   
   echo "<div class=\"col col-md-3\">\n";
   echo "<INPUT TYPE=text name=\"quantity_".$row['stock_place']."\" id=\"quantity_".$row['stock_place']."\" class=\"form-control-label\" VALUE=\"".round($row['stock_quantity'])."\" size='2'
 onChange=\"if (this.value>".$row['stock_quantity'].")
 {
 submit.disabled=true;
 alert('".gettext("The value must be smaller or equal than")." ".round($row['stock_quantity'])."');
 }
 else
 submit.disabled=false;
 \"  
   > ".$unit[0];
   echo "</div></div>\n";
    }
   echo "<INPUT TYPE='hidden' name='product_moving_from_stock_to_stock' id='product_moving_from_stock_to_stock' VALUE='1'>";
   echo "<INPUT TYPE='hidden' name='stock_location_id' id='stock_location_id' VALUE='".(int) $_GET['param3']."'>";
   
    echo "<INPUT TYPE='hidden' name='product_id' id='product_id' VALUE='".(int) $_GET['param2']."'>\n";
    echo "<INPUT type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">\n";
    echo "<INPUT type=\"hidden\" name=\"page\" id=\"page\" value=\"stock\">\n";

   
   
   echo "<div class=\"card-footer\"><button name='submit' type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
   echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button></div>\n";
   echo "</div>";//card-body
   echo "</form></div>"; //card

}

else if (isset($_GET['param1']) && $_GET['param1']=="modify_min_stock_quantity"){ 
echo "<div class=\"card\"><div class=\"card-header\">\n";
$SQL="SELECT product_id,min_stock_quantity FROM stock WHERE stock_id=".(int) $_GET['param2'];
$row=$dba->getRow($SQL);
$unit=get_quantity_unit_from_product_id($row['product_id']);
echo "<strong>".get_product_name_from_id($row['product_id'],$lang)."</strong></div>\n";

echo "</div><div class=\"card-body card-block\">";
    echo "<form action=\"index.php\" method=\"post\" id=\"upload_form\" enctype=\"multipart/form-data\" class=\"form-horizontal\">\n";

    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label for=\"info_file_review\" class=\"form-control-label\">".gettext("Minimum quantity:")."</label></div>\n";
    echo "<div class=\"col col-md-1\"><input type=\"text\" id=\"min_stock_quantity\" name=\"min_stock_quantity\" class=\"form-control\" value=\"";
    if ($unit[1]=="int")
    echo (int) $row['min_stock_quantity'];
    else
    echo $row['min_stock_quantity'];
    echo "\" required> </div> ".$unit[0]."</div>\n";
     echo "<div class=\"card-footer\"><button name='submit' type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
   echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button></div>\n";
    echo "<INPUT type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">\n";
    echo "<INPUT type=\"hidden\" name=\"page\" id=\"page\" value=\"stock\">\n";
     echo "<INPUT type=\"hidden\" name=\"stock_id\" id=\"stock_id\" value=\"".(int) $_GET['param2']."\">\n";
    echo "<INPUT type=\"hidden\" name=\"modify_min_stock_quantity\" id=\"modify_min_stock_quantity\" value=\"1\">\n";

   echo "</div>";//card-body
   echo "</form></div>"; //card
}


else if (isset($_GET['param1']) && $_GET['param1']=="show_worktimebar"){
require_once(INCLUDES_PATH."worktimebar.php");


if ($_GET['param4']!="modify" && isset($_GET['param3']) && (int) $_GET['param3']>0 && isset($_GET['param2']) && is_date_mysql_format($dba->escapeStr($_GET['param2'])))
{
//finding last finished work 
 $SQL="SELECT workorder_work_end_time FROM workorder_works WHERE workorder_works.deleted<>1 AND workorder_user_id=".(int) $_GET['param3']." AND DATE(workorder_work_start_time)='".$dba->escapeStr($_GET['param2'])."' AND workorder_partner_id=0 ORDER BY workorder_work_end_time DESC LIMIT 0,1";
       if (LM_DEBUG)
            error_log($SQL,0);
        $row=$dba->getRow($SQL);
        if (!empty($row))
        echo "<script>document.getElementById('workorder_work_start_time').value='".date("H:i", strtotime($row['workorder_work_end_time']))."';</script>";
        else
        {
        $SQL="SELECT ".date("l", strtotime($dba->escapeStr($_GET['param2'])))."_start as start FROM users WHERE user_id=".(int) $_GET['param3'];
        if (LM_DEBUG)
            error_log($SQL,0);
        $row=$dba->getRow($SQL);
        if (!empty($row))
        {
        echo "<script>document.getElementById('workorder_work_start_time').value='".date("H:i", strtotime($row['start']))."';\n";
        echo "document.getElementById('workorder_work_end_time').value='".date("H:i", strtotime($row['start']."+10 minutes"))."';\n";
        echo "window.check_time_period();</script>\n";
        }
        }

}        
}
else if (isset($_GET['param1']) && $_GET['param1']=="show_asset_with_its_parents")
{
if ($_GET['param2']>0){


$n="";
foreach (get_whole_path("asset",$_GET['param2'],1) as $k){
if ($n=="") // the first element is the main asset_id -> ignore it
$n=" ";
else
$n.=$k."-><wbr>";}

if ($n!="")
echo substr($n,0,-7);

}
}



else if (isset($_GET['param1']) && $_GET['param1']=="notification_messages")
{
    $SQL="SELECT not_message_id,not_message_".$lang.",user_id,not_message_time,has_red FROM notifications_messages WHERE notification_id=".(int) $_GET['param2']." ORDER BY not_message_id";
    $result=$dba->Select($SQL);
    $dba->Query($SQL);
            if (LM_DEBUG)
            error_log($SQL,0);
?>                  
<div class="page-content page-container" id="page-content">
    <div class="padding">
        <div class="row container d-flex justify-content-center">
            <div class="col-md-12">
                <div class="box box-warning direct-chat direct-chat-warning">
                    
                    <div class="box-header with-border"><?php
                      echo "<h3 class=\"box-title\">".gettext("Messages")."</h3>";
                      echo "<div class=\"box-tools pull-right\"> <span data-toggle=\"tooltip\" title=\"\" class=\"badge bg-yellow\">";
                      echo $dba->affectedRows();
                      echo "</span> <button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"remove\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\"><i class=\"fa fa-times\"></i> </button> </div>
                    </div>\n";
                   echo "<div class=\"box-body\">";
             $i=1;
             if ($dba->affectedRows()>0){ 
                     foreach ($result as $row){ 
                      $user_id=0;
                    if ($i){ 
                   
                    echo "<div class=\"direct-chat-messages\">\n";
                    
                    if (!empty($row['has_red']))
                   $users_who_red=json_decode($row['has_red'],true); 
                   else
                   $users_who_red=array();
                        if (is_array($users_who_red) && !in_array($_SESSION['user_id'],$users_who_red)) 
                        {
                        $users_who_red[]=(int) $_SESSION['user_id'];
                        $users_who_red=json_encode(array_unique($users_who_red));
                        $SQL="UPDATE notifications_messages SET has_red='".$users_who_red."' WHERE notification_id=".(int)$_GET['param2'];
                        $dba->Query($SQL);
                       if (LM_DEBUG)
                        error_log($SQL,0); 
                        }
                    $i=0;
                    }
                   
                   
                            if ($user_id==0 || $user_id==$row['user_id'])
                           echo "<div class=\"direct-chat-msg\">\n";
                           else
                           echo "<div class=\"direct-chat-msg right\">\n";
                           
                                echo "<div class=\"direct-chat-info clearfix\">\n"; 
                                if ($user_id==0 || $user_id==$row['user_id'])
                                {
                                echo "<span class=\"direct-chat-name pull-left\">";
                                  echo "</span> <span class=\"direct-chat-timestamp pull-left\"> ";
                                  }
                                else
                                {
                                echo "<span class=\"direct-chat-name pull-right\">";
                                  echo "</span> <span class=\"direct-chat-timestamp pull-right\"> ";}
                                $user_id=$row['user_id'];
                                
                                echo get_user_full_name_from_id($row['user_id']);
                              
                                echo " ".date($lang_date_format." H:i", strtotime($row["not_message_time"]));
                                echo "</span> </div> <img class=\"direct-chat-img\" src=\"https://img.icons8.com/color/36/000000/administrator-male.png\" alt=\"message user image\">\n";
                                echo "<div class=\"direct-chat-text\">";
                                echo $row['not_message_'.$lang];
                                echo "</div>\n";
                            echo "</div>\n";
                         }
                         echo "</div>";
                         }    
                    
                       
                   echo "</div>\n";
                  
                                
                   echo "<div class=\"box-footer\">";
                      echo "<form action=\"#\" method=\"post\">\n";
                         echo "<div class=\"input-group\"> <input type=\"text\" name=\"not_message_".$lang."\" placeholder=\"";
                         echo gettext("Type Message ...");
                    echo "\" class=\"form-control\"> <span class=\"input-group-btn\"> <button type=\"submit\" class=\"btn btn-warning btn-flat\"> ".gettext("Send")." </button> </span> </div>\n";
                    echo "<input type='hidden' name='page' id='page' value='notifications'>\n";
                    echo "<input type='hidden' name='notification_id' id='notification_id' value='".(int) $_GET['param2']."'>\n";
                       echo "\n</form>";
                        
                  echo "\n</div>";
                    
               echo "</div>
            </div>
        </div>
    </div>
</div>\n";

}

else if (isset($_GET['param1']) && $_GET['param1']=="pin_messages")
{
    $SQL="SELECT pin_message_id,pin_message_".$lang.",user_id,pin_message_time,has_red FROM pinboards_messages WHERE pin_id=".(int) $_GET['param2']." ORDER BY pin_message_id";
    $result=$dba->Select($SQL);
    $dba->Query($SQL);
            if (LM_DEBUG)
            error_log($SQL,0);
?>                  
<div class="page-content page-container" id="page-content">
    <div class="padding">
        <div class="row container d-flex justify-content-center">
            <div class="col-md-12">
                <div class="box box-warning direct-chat direct-chat-warning">
                    
                    <div class="box-header with-border"><?php
                      echo "<h3 class=\"box-title\">".gettext("Messages")."</h3>";
                      echo "<div class=\"box-tools pull-right\"> <span data-toggle=\"tooltip\" title=\"\" class=\"badge bg-yellow\">";
                      echo $dba->affectedRows();
                      echo "</span> <button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"remove\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\"><i class=\"fa fa-times\"></i> </button> </div>
                    </div>\n";
                   echo "<div class=\"box-body\">";
             $i=1;
             if ($dba->affectedRows()>0){ 
                     foreach ($result as $row){ 
                      $user_id=0;
                    if ($i){ 
                   
                    echo "<div class=\"direct-chat-messages\">\n";
                    
                    if (!empty($row['has_red']))
                   $users_who_red=json_decode($row['has_red'],true); 
                   else
                   $users_who_red=array();
                        if (is_array($users_who_red) && !in_array($_SESSION['user_id'],$users_who_red)) 
                        {
                        $users_who_red[]=(int) $_SESSION['user_id'];
                        $users_who_red=json_encode(array_unique($users_who_red));
                        $SQL="UPDATE pinboards_messages SET has_red='".$users_who_red."' WHERE pin_id=".(int)$_GET['param2'];
                        $dba->Query($SQL);
                       if (LM_DEBUG)
                        error_log($SQL,0); 
                        }
                    $i=0;
                    }
                   
                   
                            if ($user_id==0 || $user_id==$row['user_id'])
                           echo "<div class=\"direct-chat-msg\">\n";
                           else
                           echo "<div class=\"direct-chat-msg right\">\n";
                           
                                echo "<div class=\"direct-chat-info clearfix\">\n"; 
                                if ($user_id==0 || $user_id==$row['user_id'])
                                {
                                echo "<span class=\"direct-chat-name pull-left\">";
                                  echo "</span> <span class=\"direct-chat-timestamp pull-left\"> ";
                                  }
                                else
                                {
                                echo "<span class=\"direct-chat-name pull-right\">";
                                  echo "</span> <span class=\"direct-chat-timestamp pull-right\"> ";}
                                $user_id=$row['user_id'];
                                
                                echo get_user_full_name_from_id($row['user_id']);
                              
                                echo " ".date($lang_date_format." H:i", strtotime($row["pin_message_time"]));
                                echo "</span> </div> <img class=\"direct-chat-img\" src=\"https://img.icons8.com/color/36/000000/administrator-male.png\" alt=\"message user image\">\n";
                                echo "<div class=\"direct-chat-text\">";
                                echo $row['not_message_'.$lang];
                                echo "</div>\n";
                            echo "</div>\n";
                         }
                         echo "</div>";
                         }    
                    
                       
                   echo "</div>\n";
                  
                                
                   echo "<div class=\"box-footer\">";
                      echo "<form action=\"#\" method=\"post\">\n";
                         echo "<div class=\"input-group\"> <input type=\"text\" name=\"pin_message_".$lang."\" placeholder=\"";
                         echo gettext("Type Message ...");
                    echo "\" class=\"form-control\"> <span class=\"input-group-btn\"> <button type=\"submit\" class=\"btn btn-warning btn-flat\"> ".gettext("Send")." </button> </span> </div>\n";
                    echo "<input type='hidden' name='page' id='page' value='pinboards'>\n";
                    echo "<input type='hidden' name='pin_id' id='pin_id' value='".(int) $_GET['param2']."'>\n";
                       echo "\n</form>";
                        
                  echo "\n</div>";
                    
               echo "</div>
            </div>
        </div>
    </div>
</div>\n";

}


else if (isset($_GET['param1']) && $_GET['param1']=="closing_notification" && $_SESSION['user_level']<3)
{
$SQL="SELECT * FROM notifications WHERE notification_id=".(int) $_GET['param2'];
$row=$dba->getRow($SQL);
if (empty($row))
lm_error("There is no such notification!");
else
    {
    
    echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
    echo "<span aria-hidden=\"true\">×</span>\n</button>";
    echo "<form action=\"index.php\" id=\"rename_form\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\">\n";
    echo "<div class=\"card\"><div class=\"card-header\">\n";
    echo "<strong>".gettext("Closing notification:")."</strong> ".$row['notification_short_'.LANG1];
    echo "</div>";
    echo "<div class=\"card-body card-block\">";
    
    if (isset($_SESSION['CAN_WRITE_LANG1']))
    {
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-3\"><label for=\"reason_to_close_".LANG1."\" class=\"form-control-label\">".gettext("The reason to close")." (".LANG1."):</label></div>\n";
    echo "<div class=\"col-12 col-md-5\"><input type=\"text\" id=\"reason_to_close_".LANG1."\" name=\"reason_to_close_".LANG1."\" class=\"form-control\" value=\"".$row['reason_to_close_'.LANG1]."\" required></div></div>\n";
    }
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    {
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-3\"><label for=\"reason_to_close_".LANG2."\" class=\"form-control-label\">".gettext("The reason to close")." (".LANG2."):</label></div>\n";
    echo "<div class=\"col-12 col-md-5\"><input type=\"text\" id=\"reason_to_close_".LANG2."\" name=\"reason_to_close_".LANG2."\" class=\"form-control\" value=\"".$row['reason_to_close_'.LANG2]."\" required></div></div>\n";
    }
    echo "<INPUT TYPE='hidden' name='notification_id' id='notification_id' VALUE='".(int) $_GET['param2']."'>";
    echo "<INPUT TYPE='hidden' name='page' id='page' VALUE='notifications'>";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
    echo "</div>";//card-body
    
    
    echo "<div class=\"card-footer\"><button name='submit' type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button></div>\n";
   
    echo "</form></div>"; //card
    
    }
}

else if (isset($_GET['param1']) && $_GET['param1']=="stocktaking"){
if (!$_SESSION['STOCK-TAKING'])
    lm_die(gettext("You have no permission to stocktaking!"));
else{
$SQL="SELECT product_id, stock_location_id, stock_quantity, stock_place FROM stock WHERE stock_id='".$_GET['param2']."'";

$row=$dba->getRow($SQL);


echo "<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"document.getElementById('for_ajaxcall').innerHTML=''\">\n";
    echo "<span aria-hidden=\"true\">×</span>\n</button>";
    echo "<form action=\"index.php\" id=\"stocktaking-form\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\">\n";
    echo "<div class=\"card\"><div class=\"card-header\">\n";
    echo "<strong>".gettext("Stocktaking").": </strong> ".get_product_name_from_id($row['product_id'],$lang)." ".get_location_name_from_id($row['stock_location_id'],$lang);
    echo "</div>";
    echo "<div class=\"card-body card-block\">";
    
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-3\"><label for=\"stock_place\" class=\"form-control-label\">".gettext("Stock place").":</label></div>\n";
    echo "<div class=\"col-3 col-md-1\"><input type=\"text\" id=\"stock_place\" name=\"stock_place\" class=\"form-control\" value=\"".$row['stock_place']."\"></div></div>\n";


    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-3\"><label for=\"stock_quantity\" class=\"form-control-label\">".gettext("Stock quantity").":</label></div>\n";
    echo "<div class=\"col-3 col-md-1\">".floatval($row['stock_quantity'])." ".get_quantity_unit_from_product_id($row['product_id'])[0]."</div></div>\n";



    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-3\"><label for=\"real_stock_quantity\" class=\"form-control-label\">".gettext("Real stock quantity").":</label></div>\n";
    echo "<div class=\"col-3 col-md-1\"><input type=\"text\" id=\"real_stock_quantity\" name=\"real_stock_quantity\" class=\"form-control\" value=\"".floatval($row['stock_quantity'])."\" required> </div>".get_quantity_unit_from_product_id($row['product_id'])[0]."</div>\n";
    
    
    echo "<INPUT TYPE='hidden' name='stock_id' id='stock_id' VALUE='".(int) $_GET['param2']."'>";
    echo "<INPUT TYPE='hidden' name='page' id='page' VALUE='stock'>";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
    echo "</div>";//card-body
    
    
    echo "<div class=\"card-footer\"><button name='submit' type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Record")." </button></div>\n";
   
    echo "</form></div>"; //card
    



}
    
    
}
?>
