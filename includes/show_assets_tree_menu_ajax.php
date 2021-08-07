<?php
$id=(int) $_GET['param2'];


$SQL="SELECT * FROM assets WHERE asset_id=".$id;
$row=$dba->getRow($SQL);
$info_exist=0;


foreach($row as $key=>$value){
		if (strstr($key,"info_file_id") && $value>0)
		$info_exist++;
		}

if ($row['asset_product_id']>0)
		{
            
            $SQL1="SELECT * FROM products WHERE product_id='".$row['asset_product_id']."'";
            $row1=$dba->getRow($SQL1);
            foreach($row1 as $key=>$value){
            if (strstr($key,"info_file_id") && $value>0)
            $info_exist++;
            }
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
		


$text="";

                            $text.= "<div class=\"dropdown float-left\">\n";
                            
                            $text.= "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"true\" id=\"asset_menu\">".gettext("Menu for");
                            $k="";
                                $n="";

                                foreach (get_whole_path("asset",$id,1) as $k){
                                    if ($n=="") // the first element is the main asset_id -> ignore it
                                    $n=" ";
                                    else
                                    $n.=$k."-><wbr>";
                                }
                               
                                $text.= substr($n,0,-7);
                            $text.= "</a>\n";
                           $text.= "<div style=\"margin-top:1.5em;margin-left:15em;\" class=\"user-menu dropdown-menu\">";
                            
                            if ($info_exist>0 && isset($_SESSION["SEE_FILE_OF_ASSET"])){
                              $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('show_info_files','".$id."','assets','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                            $text.=gettext("Show info file(s)")."</a>";}
                            
                             if (($asset_note_exist==1 && isset($_SESSION["SEE_FILE_OF_ASSET"])) || ($asset_note_conf_exist==1 && isset($_SESSION["SEE_CONF_FILE_OF_ASSET"])) && $row['grouped_asset']==0){
                              $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('show_asset_note','".$id."','assets','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                            $text.=gettext("Show asset note")."</a>";}
                            
                            if (isset($_SESSION["ADD_ASSET"]) && $row['grouped_asset']==0){
                            $text.= "<a class=\"nav-link\" href=\"index.php?page=assets&new=part&parent_id=".$id."\"><i class=\"fa fa-user\"></i> ";
                             $text.=gettext("New part")."</a>";}
                            
                             if (isset($_SESSION["MODIFY_ASSET"])){
                               $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('rename','".$id."','assets','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             $text.=gettext("Rename asset")."</a>";
                                
                                    if ($row['main_part']==1){
                               $text.= "<a class=\"nav-link\" href=\"index.php?page=assets&asset_id=".$id."&set_as_main_part=0\"><i class=\"fa fa-user\"></i> ";
                               $text.=gettext("Set as not main part")."</a>";
                                    }else{
                               $text.= "<a class=\"nav-link\" href=\"index.php?page=assets&asset_id=".$id."&set_as_main_part=1\"><i class=\"fa fa-user\"></i> ";
                               $text.=gettext("Set as main part")."</a>";     
                                   
                                    }
                                 
                                 
                                 
                                 }
                                 
                                  if (isset($_SESSION["ADD_CONNECTION_TO_ASSET"]) && $row['grouped_asset']==0){
                                $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('handle_connection','".$id."','assets','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             $text.=gettext("Handle connection")."</a>";
                                 }
                                 
                                  if (isset($_SESSION["ADD_COUNTER"]) && $row['grouped_asset']==0){
                                $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('add_new_counter','".$id."','','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             $text.=gettext("Add new counter")."</a>";  
                                }
                                
                               if (isset($_SESSION['ADD_ASSET']) && isset($_SESSION["moving_asset_id"]) && $row['grouped_asset']==0){
                               $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('asset_move_to','".$id."',0,'','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i>";
                               $text.=gettext("Paste asset")."</a>";
                               }else  if (isset($_SESSION['ADD_ASSET']) && $row['grouped_asset']==0)
                               {
                                $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('asset_move_from','".$id."',0,'','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                                $text.=gettext("Move asset")."</a>";
                               }
                            
                                if (isset($_SESSION["copy_asset_id"]) && isset($_SESSION['ADD_ASSET']) && $row['grouped_asset']==0){
                               
                               $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('paste_asset','".$id."',0,'','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                               $text.=gettext("Paste asset")."</a>";
                               }else if (isset($_SESSION['ADD_ASSET']) && $row['grouped_asset']==0){
                               $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('copy_asset','".$id."',0,'','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                                $text.=gettext("Copy asset")."</a>";
                            }
                                 if (isset($_SESSION['MODIFY_ASSET']) && $row['grouped_asset']==0){ 
                               $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('categories','',".$id.",'','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                            if ($row['asset_category_id']>0)
                               $text.=gettext("Modify category")."</a>";
                             else  
                                $text.=gettext("Add category")."</a>";
                  
                                $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('add_file',".$id.",'assets','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ".gettext("Add file")."</a>";
                                }
                            if ($row['asset_category_id']>0 && isset($_SESSION['MODIFY_ASSET']) && $row['grouped_asset']==0){
                            
                              $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('add_product','".$id."',".$row['asset_category_id'].",'','assets','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                              $text.=gettext("Add product type")."</a>";  }
                           
                         if (isset($_SESSION['ADD_WORKREQUEST']) && $row['main_part']==1){ 
                           $text.= "<a class=\"nav-link\" href=\"index.php?page=workrequests&new=1&asset_id=".$id."\"><i class=\"fa fa-user\"></i> ";
                               $text.=gettext("New workrequest")."</a>";
                           }
                            if (isset($_SESSION['ADD_WORKORDER']) && $row['main_part']==1){   
                           $text.= "<a class=\"nav-link\" href=\"index.php?page=workorders&new=1&asset_id=".$id."\"><i class=\"fa fa-user\"></i> ";
                               $text.=gettext("New workorder")."</a>";
                             }
                                 if (isset($_SESSION['SEE_ASSET_DETAIL'])){
                            $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('workorders_consumption_from_asset_id','".$id."','','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                              $text.=gettext("Work and material consumption")."</a>";
                              }
                                  if (isset($_SESSION['MODIFY_ASSET'])){
                             $text.= "<a class=\"nav-link\" href=\"index.php?page=assets&modify=1&asset_id=".$id."\"><i class=\"fa fa-user\"></i> ";
                               $text.=gettext("Modify asset")."</a>";
                              }
                              
                            if ($connection_exist>0 || $row['asset_product_id']>0){
                            $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('show_builtable_products','".$id."','','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                              $text.=gettext("Show builtable products")."</a>";
                              
                              }   
                                                           
                            $text.= "</div>";
                       $text.="</div>";

                       echo $text;
 
?>
<script>;
 $(document).ready(function () {
    $("#asset_menu").click();
});
</script>
