<?php
function show_categories_tree_menu($id,$info_exist):string{
global $dba;
$text="";
                            $text.= "<div class=\"user-area dropdown float-right\">\n";
                            
                            $text.= "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">__";
                            $text.= "<i class=\"fa fa-question-circle\"></i>";
                            $text.= "</a>\n";
                           $text.= "<div class=\"user-menu dropdown-menu\">";
                            
                            if ($info_exist>0){
                              $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('show_info_files','".$id."','','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                            $text.=gettext("Show info file(s)")."</a>";}
                            
                            $text.= "<a class=\"nav-link\" href=\"index.php?page=categories&new=part&parent_id=".$id."\"><i class=\"fa fa-user\"></i> ";
                            $text.=gettext("New category")."</a>";
                          
                            
                               $text.= "<a class=\"nav-link\" href=\"javascript:ajax_call('rename','".$id."','categories','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                               $text.=gettext("Rename category")."</a>";
                                 
   
                                 
                                                           
                            $text.= "</div>";
                       $text.="</div>";

                       return $text;
 }
?>
