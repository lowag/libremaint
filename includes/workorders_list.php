 
     <?php                           echo "<tr";
                        
                        echo " class=\"workorder_".$row['main_asset_id']."\"";
                        if (!isset($_SESSION['main_asset_id']))
                        echo " STYLE='display:none;'";
                        //else
                
                        echo ">\n<td";
                        echo " STYLE=\"border-left: 0.25em solid;border-left-color: #0275d8;\"";
                        if (5==$row1['workorder_status'])
                        echo " class='bg-flat-color-5'";
                        else if (0==$row1["workorder_status"])
                        echo " class='bg-flat-color-4'";
                        echo ">\n<div class='d-flex justify-content-between'>";
                        //echo "->";
                        if ($row1['priority']==1)
                        echo "! ";
                        if (5>$row1['workorder_status'])
                        echo "<a href=\"index.php?page=works&new=1&workorder_id=".$row1['workorder_id']."\" title=\"".gettext("add new activity")."\"> <i class=\"fa fa-clock-o\" style='color:blue'></i></a> ";
                    
                       
                    
                        if (isset($_SESSION['TAKE_PRODUCT_FROM_STOCK']))
                        echo "<a href=\"javascript:ajax_call('product_to_workorder','','','','".$row1["workorder_id"]."','".URL."index.php','for_ajaxcall')\" title=\"".gettext("Product to workorder")."\"> <i class=\"fa fa-cart-plus\" style='color:red'></i></a> ";
             
             $info_exist=0;
             foreach($row1 as $key=>$value){
		if (strstr($key,"info_file_id") && $value>0)
		$info_exist++;
		}
		
                    
                        if (isset($_SESSION["SEE_FILE_OF_WORKORDER"]) && $info_exist>0){
                                        
                        echo "<a href=\"javascript:ajax_call('show_info_files','".$row1["workorder_id"]."','assets','','','".URL."index.php','for_ajaxcall')\" title=\"".gettext("Show files")."\"> <i class=\"fa fa-file\" style='color:grey'></i> ";
                                            echo "</a>\n";}
                        if (isset($_SESSION["MODIFY_WORKORDER"])){
                        echo "<a href=\"index.php?page=workorders&modify=1&workorder_id=".$row1['workorder_id']."\" title=\"".gettext("alter workorder")."\"> <i class=\"fa fa-wrench\" style='color:brown'></i></a> ";
                        }
                                            
                        
                        

                        echo "</div></td><td";
                    
                        //echo " onClick=\"visibility('workorder_".$row1['main_asset_id']."')\"";
                        echo ">\n";
                        if (LANG2_AS_SECOND_LANG && $_SESSION['user_level']<3 && isset($_SESSION['CAN_WRITE_LANG2']) && $row1['workorder_short_'.LANG2]=="")
                        echo " * ";//tranlation needed
                        echo date($lang_date_format, strtotime($row1["workorder_time"]))."</td>\n";
                       
                            if (!lm_isset_int('asset_id')>0 || isset($_POST["workorder_user_id"])|| isset($_POST['modify_workorder']))
                            {
                                echo "<td>";
                                if (5>$row1['workorder_status'])
                                echo "<a href=\"index.php?page=works&new=1&workorder_id=".$row1['workorder_id']."\" title=\"".gettext("add new activity")."\">";
                                if ($row1['asset_id']>0){
                                $k="";
                                $n="";

                                foreach (get_whole_path("asset",$row1['asset_id'],1) as $k){
                                    if ($n=="") // the first element is the main asset_id -> ignore it
                                    $n=" ";
                                    else
                                    $n.=$k."-><wbr>";
                                }
                                
                                echo substr($n,0,-7);
                                }else if ($row1['product_id_to_refurbish']>0)
                                echo gettext("Refurbish").": ".get_product_name_from_id($row1['product_id_to_refurbish'],$lang);
                                //echo " ".$row1['workorder_short'];
                                if ($row1['workrequest_id']>0 && $row1['request_type']==1){
                                $SQL2="SELECT finish_time FROM finished_workrequests WHERE workrequest_id=".$row1['workrequest_id']." ORDER BY finish_time DESC LIMIT 0,1";
                                $row2=$dba->getRow($SQL2);
                                if (!(empty($row2['finish_time'])))
                                echo " <small style=\"color:red;\" title='".gettext("Last")."'>".date($lang_date_format, strtotime($row2['finish_time']))."</small>";
                                }
                                
                                if (5>$row1['workorder_status'])
                                echo "</a>";
                                echo "</td>\n";
                            }
                        
                            foreach ($employees as $user_id)
                            {
                            if ($row1['employee_id'.$user_id]==1){
                            if (($key= array_search($user_id,$user_column_to_hide)) !==false )
                            unset($user_column_to_hide[$key]);
                            echo "<td>X</td>";
                            }
                            else
                            echo "<td class='user_".$user_id."'></td>";
                            }
                            
                        echo "<td class='partner_col'>";
                        if (isset($row1['workorder_partner_id']) && $row1['workorder_partner_id']>0)
                        {
                        $there_no_partner_at_all=false;
                        echo get_partner_name_from_id($row1['workorder_partner_id']);
                        }
                        echo "</td>";
                            echo "<td>";
                             if (isset($_SESSION['SEE_WORKORDER_DETAIL'])){
                        echo "<a href=\"javascript:ajax_call('show_workorder_detail','".$row1["workorder_id"]."','".$row1['asset_id']."','','','".URL."index.php','for_ajaxcall')\" title=\"".gettext("Show details")."\"><i class='fa fa-info-circle'></i> ";
                        if (isset($row1['notification_id']) && $row1['notification_id']>0)
                        echo gettext("From notification").": ";
                        echo $row1['workorder_short_'.$lang]."</a> ";
                        
                        }
                        else{
                            if (isset($row1['notification_id']))
                                echo gettext("From notification").": ";
                            echo $row1['workorder_short_'.$lang];
                            
                            }
                            echo "</td></tr>\n";?>
