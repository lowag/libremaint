<div id='for_ajaxcall'></div>
<?php
    
    if (isset($_POST['stock_movement_id']) && $_POST["stock_movement_id"]>0 && isset($_FILES['info_file_name']['tmp_name']) && is_it_valid_submit() && isset($_SESSION['ADD_FILE_TO_PRODUCT_MOVING'])){ //it is from the new file form
 
$table="stock_movements";
$id=$_POST["stock_movement_id"];
$id_column="stock_movement_id";
    require(INCLUDES_PATH."file_upload.php"); 


}


$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;
    $SQL="SELECT * FROM stock_movements order by stock_movement_id DESC";
    $result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
$from=($pagenumber-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
     echo "<div>";
     echo "<div>";
    
      
    echo "<table id=\"stock_movement-table\" class=\"table table-striped table-bordered\">\n";
    echo "<thead>\n<tr>\n";
    echo "<th></th><th>".gettext("Date")."</th><th>".gettext("Product")."</th><th>".gettext("Movement")."</th><th>".gettext("Quantity")."</th></tr>";
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
    echo "<td>".$row['stock_movement_time']."</td>\n";
    echo "<td>".get_product_name_from_id($row['product_id'],$lang)." <mark>".Luhn($row['product_id'])."</mark></td>\n";
    
    if ($row['to_partner_id']>0)
    echo "<td>".gettext("To partner").": ".get_partner_name_from_id($row['to_partner_id'])."</td>\n";
    else if ($row['from_partner_id']>0)
    echo "<td>".gettext("From partner").": ".get_partner_name_from_id($row['from_partner_id'])."</td>\n";
    else if ($row['to_asset_id']>0)
    echo "<td>".gettext("Built to").": ".get_asset_name_from_id($row['to_asset_id'],$lang)."</td>\n";
    else if ($row['from_asset_id']>0)
    echo "<td>".gettext("Take from").": ".get_asset_name_from_id($row['from_asset_id'],$lang)."</td>\n";
    else if ($row['workorder_id']>0){
    echo "<td>";
    $SQL="SELECT product_id_to_refurbish FROM workorders WHERE workorder_id=".$row['workorder_id'];
    $row1=$dba->getRow($SQL);
    echo gettext("Built to").": ".get_product_name_from_id($row1['product_id_to_refurbish'],$lang);
    echo "</td>";}
    else
    echo "<td></td>";
    echo "<td>".round($row['stock_movement_quantity'])." ".get_quantity_unit_from_product_id($row['product_id'])[0]."</td>\n";
    echo "</tr>\n";
        
        }
    echo "</tbody></table>\n";
    echo "</div>";
    


include(INCLUDES_PATH."pagination.php");
echo "</div>";
    
?>
