<div class="card">
<?php
if (isset($_SESSION['SEE_WORKS'])){

$SQL="SELECT asset_id FROM assets WHERE main_asset_category_id>0 AND asset_id=".(int) $_GET['param2'];
$result=$dba->Select($SQL);
if ($dba->affectedRows()==1)
$this_is_a_main_asset=1; //top of the tree

$SQL="SELECT workorder_worktime, workorder_short_".$lang.",workorder_works.asset_id,workorder_works.workorder_id, workorder_work_id,workorder_work_start_time,workorder_work_end_time,workorder_work_".$lang.",workorder_user_id,workorder_works.workorder_partner_id FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id WHERE workorder_works.deleted<>1 AND";
if (isset($this_is_a_main_asset))
$SQL.=" workorder_works.main_asset_id=".(int) $_GET['param2'];
else{
//all_the_assets_under_this asset
$children=implode(',', get_whole_path_ids_children("asset",(int) $_GET['param2'],1));
$SQL.=" workorder_works.asset_id IN (".(int) $_GET['param2'];
if (!empty($children))
$SQL.=",". $children;
$SQL.= ")";
}
$SQL.=" ORDER BY workorder_work_end_time DESC";
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);

$i=1;

if ($dba->affectedRows()>0){
echo "<div class='card-header'><strong>".gettext("Activities")."</strong></div>";
echo "<div class='card-body'>";
echo "<table id=\"bootstrap-data-table\" class=\"table table-striped table-bordered\">\n";
echo "<thead><tr>\n";


echo "<th></th>";

echo "<th>".gettext("Asset")."</th>";
echo "<th>".gettext("Start")."</th>";
echo "<th>".gettext("End")."</th>";
echo "<th>".gettext("Time")."</th><th>Minutes</th>";
if ($_SESSION['user_level']<3 || isset($_GET['user_id'])){
echo "<th>".gettext("Employee")."</th>";
echo "<th>".gettext("Partner")."</th>";
}
echo "<th>".gettext("Task")."</th>";
echo "<th>".gettext("Work")."</th></tr>\n";

echo "</thead>\n";
echo "<tbody>";
$now=new datetime('now');
    foreach ($result as $row){



echo "<tr>\n";
echo "<td>".$i++." ";
        echo "<a href=\"javascript:ajax_call('show_workorder_detail','".$row['workorder_id']."','','','','".URL."index.php','for_ajaxcall')\" title=\"show workorder details\"><i class=\"fa fa-info-circle\"></i></a> ";
 
        $allow_to_modify_date = new DateTime($row['workorder_work_start_time']); // Y-m-d
        $allow_to_modify_date->add(new DateInterval('P14D'));
    
      if (isset($_SESSION["MODIFY_WORK"]) && $allow_to_modify_date>$now){
        
         echo "<a href=\"index.php?page=works&modify=1&workorder_work_id=".$row['workorder_work_id']."&workorder_id=".$row['workorder_id'];
        
         echo "\" title=\"".gettext("alter work")."\"> <i class=\"fa fa-wrench\"></i></a> ";
         }                            
echo "</td>";
if (!isset($this_is_a_main_asset)){
    echo "<td>";
    
    $n="";
    foreach (get_whole_path("asset",$row["asset_id"],1) as $k){
    if ($n=="") // the first element is the main asset_id -> ignore it
    $n=" ";
    else
    $n.=$k."-><wbr>";}
    
    if ($n!="")
    echo substr($n,0,-7);

    echo "</td>";
    }
echo "<td>".date($lang_date_format." H:i", strtotime($row['workorder_work_start_time']))."</td>";
if (date("Y.m.d", strtotime($row['workorder_work_start_time']))==date("Y.m.d", strtotime($row['workorder_work_end_time'])))
echo "<td>".date("H:i", strtotime($row['workorder_work_end_time']))."</td>";
else
echo "<td>".date($lang_date_format." H:i", strtotime($row['workorder_work_end_time']))."</td>";
$str_time=date("H:i", strtotime($row['workorder_worktime']));
echo "<td>".$str_time."</td>";
sscanf($str_time, "%d:%d", $hours, $minutes);
$m=$hours * 60 + $minutes;
echo "<td>".($hours * 60 + $minutes)."</td>";

if ($_SESSION['user_level']<3 || isset($_GET['user_id'])){
    echo "<td>".get_username_from_id($row["workorder_user_id"])."</td>"; 
    echo "<td>";
    if ($row["workorder_partner_id"]>0)
    echo get_partner_name_from_id($row["workorder_partner_id"]);
    echo "</td>"; 
  }
  echo "<td>".$row['workorder_short_'.$lang]."</td>";
 echo "<td>".$row['workorder_work_'.$lang]."</td>";
 echo "</tr>\n";

 
 }
echo "</tbody></table>";



if (isset($_SESSION['SEE_PRODUCT_MOVING'])){

foreach ($result as $row){
$SQL="SELECT to_partner_id,from_partner_id,from_stock_location_id,to_stock_location_id,stock_movement_quantity,product_id,from_asset_id,to_asset_id,stock_movement_time FROM stock_movements WHERE workorder_id=".$row['workorder_id'];

$resultm=$dba->Select($SQL);
if ($dba->affectedRows()>0)
    {
    echo "<div class=\"card-header\"><strong>".gettext("Materials");
    
    if (isset($this_is_a_main_asset)){
    
    $n="";
    foreach (get_whole_path("asset",$row["asset_id"],1) as $k){
    if ($n=="") // the first element is the main asset_id -> ignore it
    $n=" ";
    else
    $n.=$k."-><wbr>";}
    
    if ($n!="")
    echo " ".substr($n,0,-7);

    }
    
    echo "</strong></div>\n";
    
    echo "<table id=\"stock_movement-table\" class=\"table table-striped table-bordered\">\n";
    echo "<thead>\n<tr>\n";
    echo "<th></th>";
 
    echo "<th>".gettext("Date")."</th><th>".gettext("Product")."</th><th>".gettext("Action")."</th>";
    echo "<th>".gettext("Quantity")."</th></tr>";
    echo "<tbody>";
    $i=1;
        foreach($resultm as $material_row){
    echo "<tr>\n";
    echo "<td>".$i++."</td>";
    if (isset($this_is_a_main_asset)){
    echo "<td>";
    
    $n="";
    foreach (get_whole_path("asset",$row["asset_id"],1) as $k){
    if ($n=="") // the first element is the main asset_id -> ignore it
    $n=" ";
    else
    $n.=$k."-><wbr>";}
    
    if ($n!="")
    echo substr($n,0,-7);

    echo "</td>";
    }
    echo "<td>".date($lang_date_format, strtotime($material_row['stock_movement_time']))."</td>\n";
    echo "<td>".get_product_name_from_id($material_row['product_id'],$lang)."</td>\n";
    
    if ($material_row['to_partner_id']>0)
    echo "<td>".gettext("To partner").": ".get_partner_name_from_id($material_row['to_partner_id'])."</td>\n";
    else if ($material_row['from_partner_id']>0)
    echo "<td>".gettext("From partner").": ".get_partner_name_from_id($material_row['from_partner_id'])."</td>\n";
    else if ($material_row['to_stock_location_id']>0)
    echo "<td>".gettext("Destination").": ".get_location_name_from_id($material_row['to_stock_location_id'],$lang)."</td>\n";
    else if ($material_row['from_stock_location_id']>0)
    echo "<td>".gettext("Source").": ".get_location_name_from_id($material_row['from_stock_location_id'],$lang)."</td>\n";
   
    else if ($material_row['to_asset_id']>0)
    echo "<td>".gettext("Built to").": ".get_asset_name_from_id($material_row['to_asset_id'],$lang)."</td>\n";
    else if ($material_row['from_asset_id']>0)
    echo "<td>".gettext("Take from").": ".get_asset_name_from_id($material_row['from_asset_id'],$lang)."</td>\n";
   
    else
    echo "<td></td>";
    echo "<td>".round($material_row['stock_movement_quantity'])." ".get_quantity_unit_from_product_id($material_row['product_id'])[0]."</td>\n";
    echo "</tr>\n";
        
        }
    echo "</tbody></table>\n";
    echo "</div></div>";
    }
    }
    }//foreach
    else
    lm_die(gettext("You have no persmission"));
}else
echo gettext("There has not added any work to this workorder yet.");
}//foreach
else
echo gettext("You have no permission!");    
?> 
</div>
<hr>
