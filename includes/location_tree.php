<?php

$resp="<div id=\"tree\">\n";
$resp.="<ul id=\"treeData\" style=\"display: none;\">\n";
$resp="";
$i=0;
function tree_construct($id){
global $dba,$resp,$i,$lang;

 $sql = "SELECT * FROM locations WHERE location_parent_id='".$id."'";
 $result = $dba->Select($sql);
 if (LM_DEBUG)
        error_log($sql,0); 
  if ($dba->affectedRows()>0){
   if ($i==0)
   $resp.="<div id=\"tree\">\n<ul id=\"treeData\" style=\"display: none;\">";
   else
   $resp.= "<ul>\n";
   $i++;
foreach( $result as $row) { 
		$resp.=" <li id=\"id".$i."\" class=\"folder\" title=\"".$row['location_name_'.$lang]."\">".$row['location_name_'.$lang]; 
		$info_exist=0;
		foreach($row as $key=>$value){
		if (strstr($key,"info_file_id") && $value>0)
		$info_exist++;
		}
		
		$resp.=" ".show_locations_tree_menu($row['location_id'],$info_exist,$row['set_as_stock']);	
		$i++;
		if (1==$row['set_as_stock'])
        $resp.=" <strong class='alert-success'>".gettext("stock")."</strong>";
        
		if ($info_exist>0)
        $resp.=" <strong class='alert-primary'>info</strong>";
        tree_construct($row['location_id']);
        
                }
 $resp.= "</ul>\n";
}

		return $resp;
		
}

echo (tree_construct(0));
//echo $resp;
echo "</div>";
