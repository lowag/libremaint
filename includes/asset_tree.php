<?php
include(INCLUDES_PATH."asset_tree_func.php");
if (!empty($asset_tree_has_changed))
//if (isset($asset_id) && $asset_id>0)
{
$nodes_must_be_opened=get_whole_path_ids('asset',$asset_id,1);

$SQL="SELECT main_asset_category_id FROM assets WHERE asset_id='".get_whole_path('asset',$asset_id,1)[0]."'";
$row=$dba->getRow($SQL);
$main_asset_category_id=$row['main_asset_category_id'];

}
echo "<div id=\"tree\">\n";
echo "<ul id=\"treeData\" style=\"display: none;\">\n";
$resp="";

//$i=0;
$SQL="SELECT main_asset_category_".$lang.",main_asset_category_id FROM main_asset_categories";

$SQL.= " ORDER BY main_asset_category_".$lang;
$result=$dba->Select($SQL);
 if (isset($_SESSION['asset_location_id']) && $_SESSION['asset_location_id']>0)
$location_ids=get_locations_bellow_id($_SESSION['asset_location_id']);

foreach($result as $row){

echo " <li id=\"id_main_".$row['main_asset_category_id']."\"";
echo " class=\"folder\"";
echo ">".$row['main_asset_category_'.$lang];

$SQL = "SELECT asset_id FROM assets WHERE main_asset_category_id=".$row['main_asset_category_id'];
// $SQL = "SELECT asset_id FROM assets WHERE main_asset_category_id=4";
    if (isset($_SESSION['asset_location_id']) && $_SESSION['asset_location_id']>0){
    //$location_ids=get_locations_bellow_id($_SESSION['asset_location_id']);

    $SQL.=" AND asset_location IN (".implode(",",$location_ids).")";
    
    }
 $SQL.=" ORDER BY asset_name_".$lang;
    $result_main_asset_ids=$dba->Select($SQL);
    if (LM_DEBUG)
error_log($SQL,0);
        if ($dba->affectedRows()>0){
        echo "<ul>\n";
        foreach ($result_main_asset_ids as $main_asset_id)   
        {
        
        $resp1="";
        if (in_array($main_asset_id['asset_id'],$asset_tree_has_changed,true)|| !file_exists(ASSETS_PATH.$main_asset_id['asset_id']."_".$lang.".html"))   
        {
        //1st we have to delete teh file we want to recreate in all languages  
        foreach (glob(ASSETS_PATH.$main_asset_id['asset_id']."_*") as $filename) {
        unlink($filename);
        }
        
        $resp1.=(tree_construct($main_asset_id['asset_id'],0));
        echo $resp1;
        $resp1=str_replace("class=\"expanded\"","class=\"folder\"",$resp1);
        $tree = fopen(ASSETS_PATH.$main_asset_id['asset_id']."_".$lang.".html", "w") or die("Unable to open file!");
        fwrite($tree, $resp1);
        fclose($tree);
        }
        else
        include(ASSETS_PATH.$main_asset_id['asset_id']."_".$lang.".html");
        //echo "</li>\n";
        }
        //if ($i>1)
        //echo "</li>";
        echo "</ul>";
        }

}
echo "</ul>\n";

echo "</div>\n";
//$tree = fopen(INCLUDES_PATH."asset_tree.html", "w") or die("Unable to open file!");

//echo $html;
//$html=str_replace("class=\"expanded\"","class=\"folder\"",$html);
//fwrite($tree, $html);
//fclose($tree);
