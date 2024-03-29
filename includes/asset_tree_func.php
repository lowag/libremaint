<?php
function tree_construct($id,$parent_id){
global $dba,$req_page,$lang,$nodes_must_be_opened;
static $resp;

if ($id>0){
$resp="";

$sql = "SELECT * FROM assets WHERE asset_id=".$id." OR grouped_asset_id=".$id."  ORDER BY asset_name_".$lang;
//$main_asset_id=0;
//+0
}
else
 $sql = "SELECT * FROM assets WHERE asset_parent_id=".$parent_id." OR grouped_asset_id=".$parent_id."  ORDER BY asset_name_".$lang;
 //else 
  //$sql = "SELECT * FROM assets WHERE asset_parent_id=".$id." ORDER BY asset_name_".$lang;
 $result = $dba->Select($sql);
 if (LM_DEBUG)
        error_log($sql,0); 
  if ($dba->affectedRows()>0){
   //if ($i==0)
   //$resp.="<div id=\"tree\">\n<ul id=\"treeData\" style=\"display: none;\">";
   //else
//
if ($parent_id>0)
            $resp.= "<ul>\n";  
foreach( $result as $row) { 
      
      
		$resp.="\t<li id=\"id".$row['asset_id']."\"";
		if (!empty($nodes_must_be_opened) && in_array($row['asset_id'],$nodes_must_be_opened,true))
		$resp.=" class=\"expanded\"";
		else
		$resp.=" class=\"folder\"";
		$resp.=" title=\"".$row['asset_name_'.$lang]."\">";
		if ($row["grouped_asset"])
		$resp.="<div style='color:green;display:inline;'>";
		if ($row["main_part"]==1){
            $resp.="<strong>";
            $resp.=$row['asset_name_'.$lang]; 
            $resp.="</strong>\n";
            }else
            $resp.=$row['asset_name_'.$lang]."\n"; 
		if ($row["grouped_asset"])
		$resp.="</div>";
		
		
		$info_exist=0;
		if ($row['asset_product_id']>0)
		{
            $resp.=" <small class='alert-warning'>".get_product_name_from_id($row['asset_product_id'],$lang)."</small>\n";
            $SQL1="SELECT * FROM products WHERE product_id='".$row['asset_product_id']."'";
            $row1=$dba->getRow($SQL1);
            foreach($row1 as $key=>$value){
            if (strstr($key,"info_file_id") && $value>0)
            $info_exist++;
            }
		}
		else if ($row['asset_category_id']>0)
		{
            $resp.=" <small class='alert-info'>".get_category_name_from_id($row['asset_category_id'],$lang)."</small>\n";
            if ($row['asset_subcategory_id']>0)
            $resp.="<small class='alert-info'> -> ".get_category_name_from_id($row['asset_subcategory_id'],$lang)."</small>\n";
		}
		
		
			
		foreach($row as $key=>$value){
		if (strstr($key,"info_file_id") && $value>0)
		$info_exist++;
		}
		
		$connection_exist=0;
		$connections="";
		foreach($row as $key=>$value){
		if (strstr($key,"connection_id") && $value>0){
		$connection_exist++;
		$connections.=get_connection_name_from_id($value);
		}}
		
		if (!empty($row['asset_note']))
		$asset_note_exist=1;
		else
		$asset_note_exist=0;
		
		if (!empty($row['asset_note_conf']))
		$asset_note_conf_exist=1;
		else
		$asset_note_conf_exist=0;
		
		$resp.=" \n";
		$resp.= "<div class=\"dropdown float-right\" id=\"menu_".$row['asset_id']."\">";
		$resp.= "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\""; 
		
		$resp.=" onClick=\"";
        if ($req_page=="notifications")
         $resp.="ajax_call('show_asset_with_its_parents','".$row['asset_id']."','','','','".URL."index.php','asset_name');document.notification_form.asset_id.value=".$row['asset_id'];
        
        else
        //2020.12.06->$resp.="ajax_call('show_asset_tree_menu','".$row['asset_id']."','','','','".URL."index.php','for_ajaxcall',".$row['grouped_asset'].")";
        $resp.="ajax_call('show_asset_tree_menu','".$row['asset_id']."','','','','".URL."index.php','for_ajaxcall')";
        $resp.="\"";
        
		$resp.=" aria-haspopup=\"true\" aria-expanded=\"false\">__";
                            $resp.= "<i class=\"fa fa-question-circle\"></i>";
                            $resp.="</a>\n";
		$resp.= "</div>";
		//.show_assets_tree_menu($row['asset_id'],$row['asset_category_id'],$row['main_part'],$info_exist,$connection_exist,$asset_note_exist,$asset_note_conf_exist);	
		//$i++;
		
		if ($connection_exist>0  && $_SESSION['SEE_CONNECTION_OF_ASSET'])
        $resp.=" <b class='alert-success'>".$connections."</b>";
		
		if (!empty($row['asset_note'])  && $_SESSION['SEE_FILE_OF_ASSET'])
		$resp.=" <b class='alert-danger'>".gettext("asset note")."</b>";
		
		if (!empty($row['asset_note_conf']) && $_SESSION['SEE_CONF_FILE_OF_ASSET'])
		 $resp.=" <b class='alert-danger'>".gettext("confidental asset note")."</b>";
		 
		if ($info_exist>0  && $_SESSION['SEE_FILE_OF_ASSET'])
        $resp.=" <strong class='alert-primary'>info</strong>";
        //$resp.="</li>";
        
        tree_construct(0,$row['asset_id']);
            //if ($parent_id>0)
        
               
              
                }
 if ($parent_id>0)               
 $resp.= "</ul>\n";
}

//
//$resp.= "\n</ul>\n";
 //
//
		return $resp;
		
}//function tree_construct($id,$main_asset_category_id,$main_asset_location_id)
?>
