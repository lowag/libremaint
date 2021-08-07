<?php
function show_locations_tree_menu($id,$info_exist,$set_as_stock):string{
global $dba;
$text="";
                            $text.= "<div class=\"user-area dropdown float-right\">\n";
                            
                            $text.= "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">__";
                            $text.= "<i class=\"fa fa-question-circle\"></i>";
                            $text.= "</a>\n";
                           $text.= "<div class=\"user-menu dropdown-menu\">";
                            
                            if ($info_exist>0){
                              $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('show_info_files','".$id."','locations','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                            $text.=gettext("Show info file(s)")."</a>";}
                            
                            $text.= "<a class=\"nav-link\" href=\"index.php?page=locations&new=part&parent_id=".$id."\"><i class=\"fa fa-user\"></i> ";
                         
                             $text.=gettext("New location")."</a>";
                             
                            
                               $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('rename','".$id."','locations','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                               $text.=gettext("Rename location")."</a>";
                               
                                if (isset($_SESSION["moving_location_id"])){
                               $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('location_move_to','".$id."',0,'','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i>";
                               $text.=gettext("Paste location")."</a>";
                               }else
                               {
                                $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('location_move_from','".$id."',0,'','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                                $text.=gettext("Move location")."</a>";
                               }
                               
                               
                               if (1==$set_as_stock){
                               $text.= "<a class=\"nav-link\" href=\"index.php?page=locations&location_id=".$id."&set_as_stock=0\"><i class=\"fa fa-user\"></i> ";
                               $text.=gettext("Unset as stock")."</a>";
                                    }else{
                               $text.= "<a class=\"nav-link\" href=\"index.php?page=locations&location_id=".$id."&set_as_stock=1\"><i class=\"fa fa-user\"></i> ";
                               $text.=gettext("Set as stock")."</a>";     
                                   
                                    }
                  
                                $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('add_file',".$id.",'locations','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ".gettext("Add file")."</a>";
                                
                                $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('show_assets_on_this_place',".$id.",'locations','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ".gettext("Assets on this place")."</a>";
                          
                                                         
                                                           
                            $text.= "</div>";
                       $text.="</div>";

                       return $text;
 }
?>
