<?php

$resp="<div id=\"tree\">\n";
$resp.="<ul id=\"treeData\" style=\"display: none;\">\n";


$resp="";
$i=0;
function tree_construct($id){
global $dba,$resp,$i,$lang;

 $sql = "SELECT * FROM categories WHERE category_parent_id='".$id."' ORDER BY category_name_".$lang;
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
		$resp.=" <li id=\"id".$i."\" class=\"folder\" title=\"".$row['category_name_'.$lang]."\">".$row['category_name_'.$lang]; 
		
        $i++;
        
		
		$info_exist=0;
		foreach($row as $key=>$value){
		if (strstr($key,"info_file_id") && $value>0)
		$info_exist++;
		}
		$resp.=" ".show_categories_tree_menu($row['category_id'],$info_exist);	
		$i++;
		if ($info_exist>0)
        $resp.=" <i>".$info_exist."</i>";
		tree_construct($row['category_id']);
        
                }
 $resp.= "</ul>\n";
}

		return $resp;
		
}

echo (tree_construct(0));
//echo $resp;
echo "</div>";
