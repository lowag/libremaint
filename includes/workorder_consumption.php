<div class="card">
<?php
if (isset($_SESSION['SEE_WORKS'])){
if (isset($_GET['param5']) && $_GET['param1']=="product_to_workorder")
$workorder_id=(int) $_GET["param5"];
else if (isset($_GET['param2']))
$workorder_id=(int) $_GET['param2'];
else if (isset($_GET['workorder_id']))
$workorder_id=(int) $_GET['workorder_id'];

$SQL="SELECT asset_id FROM workorders WHERE workorder_id=".$workorder_id;
$row=$dba->getRow($SQL);
$SQL="SELECT * FROM assets WHERE asset_id=".$row['asset_id'];
$row=$dba->getRow($SQL);


$connection_exist=0;
foreach($row as $key=>$value){
		if (strstr($key,"connection_id") && $value>0){
		$connection_exist++;
		$connections.=get_connection_name_from_id($value)." ";
		}}
if ($connection_exist>0)
echo "<b class='alert-success'>".gettext("Connections:")." ".$connections.'</b>';

$SQL="select ROUND(SUM(TIME_TO_SEC(workorder_worktime)/3600),1) as workhour, username FROM workorder_works_".$lang." LEFT JOIN users ON workorder_works.workorder_user_id=users.user_id WHERE workorder_works.deleted<>1 AND workorder_works.workorder_id=".$workorder_id." GROUP BY workorder_user_id ORDER BY workhour DESC";
$result=$dba->Select($SQL);
if (!empty($result)){
echo "<table>";
//echo "<tr><th>".gettext("Employee")."</th><th>".gettext("Sum workhour")."</th></tr>";
$sum_hour=0;
foreach ($result as $row){
echo "<tr><td>".$row['username']."</td><td>".$row['workhour']." ".gettext('h')."</td></tr>";
$sum_hour+=$row['workhour'];
}
echo "<tr><td>".gettext('Sum workhours').": </td><td>".$sum_hour." ".gettext('h')."</td></tr>";
echo "</table>";

}

$SQL="SELECT workorder_id, workorder_work_id,workorder_work_start_time,workorder_work_end_time,workorder_work_".$lang.",workorder_user_id,workorder_partner_id FROM workorder_works WHERE workorder_works.deleted<>1 AND workorder_id='". $workorder_id."'";
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
echo "<th></th><th>".gettext("Start")."</th>";
echo "<th>".gettext("End")."</th>";

//if ($_SESSION['user_level']<3 || isset($_GET['user_id'])){
echo "<th>".gettext("Employee")."</th>";
echo "<th>".gettext("Partner")."</th>";
//}
echo "<th>".gettext("Work")."</th></tr>";

echo "</thead>";
echo "<tbody>";
$now=new datetime('now');
foreach ($result as $row){
echo "<tr>\n";
echo "<td>".$i++." ";
        echo "<a href=\"javascript:ajax_call('show_workorder_detail','".$row['workorder_id']."','','','','".URL."index.php','for_ajaxcall')\" title=\"show workorder details\"><i class=\"fa fa-info-circle\"></i></a> ";
        $allow_to_modify_date = new DateTime($row['workorder_work_start_time']); // Y-m-d
        $allow_to_modify_date->add(new DateInterval('P'.DAYS_ALLOW_TO_MODIFY_WORKS.'D'));
    
      if (isset($_SESSION["MODIFY_WORK"]) && ($allow_to_modify_date>$now || $SESSION['user_level']<3)){
         echo "<a href=\"index.php?page=works&modify=1&workorder_work_id=".$row['workorder_work_id']."&workorder_id=".$row['workorder_id'];
         echo "\" title=\"".gettext("alter work")."\"> <i class=\"fa fa-wrench\"></i></a> ";
         }                            
echo "</td>";
echo "<td>".date($lang_date_format." H:i", strtotime($row['workorder_work_start_time']))."</td>";
if (date("Y.m.d", strtotime($row['workorder_work_start_time']))==date("Y.m.d", strtotime($row['workorder_work_end_time'])))
echo "<td>".date("H:i", strtotime($row['workorder_work_end_time']))."</td>";
else
echo "<td>".date($lang_date_format." H:i", strtotime($row['workorder_work_end_time']))."</td>";
//if ($_SESSION['user_level']<3 || isset($_GET['user_id'])){
    echo "<td>".get_username_from_id($row["workorder_user_id"])."</td>"; 
    echo "<td>";
    if ($row["workorder_partner_id"]>0)
    echo get_partner_name_from_id($row["workorder_partner_id"]);
    echo "</td>"; 
  //}
 echo "<td>".$row['workorder_work_'.$lang]."</td>";
 echo "</tr>\n";
}
echo "</tbody></table>";
}else
echo gettext("There has not added any work to this workorder yet.");
}
else
echo gettext("You have no permission!");

if (isset($_SESSION['SEE_PRODUCT_MOVING'])){
$SQL="SELECT to_partner_id,from_partner_id,from_stock_location_id,to_stock_location_id,stock_movement_quantity,product_id,from_asset_id,to_asset_id,stock_movement_time FROM stock_movements WHERE workorder_id='".$workorder_id."'";
$result=$dba->Select($SQL);
echo "<div class=\"card-header\"><strong>".gettext("Materials")."</strong></div>\n";
if ($dba->affectedRows()>0)
    {
    
    echo "<table id=\"stock_movement-table\" class=\"table table-striped table-bordered\">\n";
    echo "<thead>\n<tr>\n";
    echo "<th></th><th>".gettext("Date")."</th><th>".gettext("Product")."</th><th>".gettext("Action")."</th>";
    echo "<th>".gettext("Quantity")."</th></tr>";
    echo "<tbody>";
    $i=1;
        foreach($result as $row){
    echo "<tr>\n";
    echo "<td>".$i++."</td>";
    echo "<td>".date($lang_date_format." h:i", strtotime($row['stock_movement_time']))."</td>\n";
    echo "<td>".get_product_name_from_id($row['product_id'],$lang)." <mark>".Luhn($row['product_id'])."</mark></td>\n";
    
    if ($row['to_partner_id']>0)
    echo "<td>".gettext("To partner").": ".get_partner_name_from_id($row['to_partner_id'])."</td>\n";
    else if ($row['from_partner_id']>0)
    echo "<td>".gettext("From partner").": ".get_partner_name_from_id($row['from_partner_id'])."</td>\n";
    else if ($row['to_stock_location_id']>0)
    echo "<td>".gettext("Destination").": ".get_location_name_from_id($row['to_stock_location_id'],$lang)."</td>\n";
    else if ($row['from_stock_location_id']>0)
    echo "<td>".gettext("Source").": ".get_location_name_from_id($row['from_stock_location_id'],$lang)."</td>\n";
   
    else if ($row['to_asset_id']>0)
    echo "<td>".gettext("Built to").": ".get_asset_name_from_id($row['to_asset_id'],$lang)."</td>\n";
    else if ($row['from_asset_id']>0)
    echo "<td>".gettext("Take from").": ".get_asset_name_from_id($row['from_asset_id'],$lang)."</td>\n";
   
    else
    echo "<td></td>";
    echo "<td>".round($row['stock_movement_quantity'])." ".get_quantity_unit_from_product_id($row['product_id'])[0]."</td>\n";
    echo "</tr>\n";
        
        }
    echo "</tbody></table>\n";
    echo "</div></div>";
    }else
    lm_info(gettext("There is no material consumption."));
    }else
    lm_die(gettext("You have no persmission"));
?> 
</div>
<hr>
