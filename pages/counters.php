
<div id='for_ajaxcall'>
</div>
<?php
if (isset($_POST['counter_id'])  && isset($_POST['counter_value']) && $_POST['counter_value'] >0 && is_it_valid_submit())
{
$SQL="INSERT INTO counter_values (counter_id,counter_value) VALUES ('".(int) $_POST['counter_id']."','".(int) $_POST['counter_value']."')";
if ($dba->Query($SQL)){
            echo "<div class=\"card\">".gettext("The counter value has been written.")."</div>";
             }
            else
            echo "<div class=\"card\">".gettext("Failed to write counter value ").$dba->err_msg."</div>";
}
else if (isset($_POST['counter_value_id'])  && isset($_POST['counter_value']) && $_POST['counter_value'] >0 && is_it_valid_submit())
{
$SQL="UPDATE counter_values SET counter_value=".(int) $_POST['counter_value']." WHERE counter_value_id=".(int) $_POST['counter_value_id']."')";
if ($dba->Query($SQL)){
            echo "<div class=\"card\">".gettext("The counter value has been modified.")."</div>";
             }
            else
            echo "<div class=\"card\">".gettext("Failed to modify counter value ").$dba->err_msg."</div>";
}
if (isset($_GET["new_value"]) || isset($_GET["modify"])){
?>

<div class="card">
<div class="card-header">
<strong><?php 
if (isset($_GET["new_value"]))
echo gettext("New counter value");
else if (isset($_GET["modify"])){
echo gettext("Modify counter value");
$SQL="SELECT counter_id,counter_value,counter_value_time FROM counter_values WHERE counter_value_id=".(int) $_GET['counter_value_id'];
$row1=$dba->getRow($SQL);

}
?></strong>
</div><?php //card header ?>
<div class="card-body card-block">
<form action="index.php" id="counter_form" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-3\"><label for=\"counter_id\" class=\" form-control-label\">".gettext("Counter:")."</label></div>";

    echo "<div class=\"col col-md-2\">";
    echo "<select name=\"counter_id\" id=\"counter_id\" class=\"form-control\" required>\n";
    $SQL="SELECT counter_id, asset_name_".$lang." FROM counters LEFT JOIN assets on counters.asset_id=assets.asset_id";
    $SQL.=" ORDER BY asset_name_".$lang;
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
    echo "<option value=\"".$row["counter_id"]."\"";
    if (isset($_GET['modify']) && $row1['counter_id']==$row['counter_id'])
    echo " selected disabled";
    echo ">".$row["asset_name_".$lang]."</option>\n";
    
    }
    echo "</select></div></div>";
  
  
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"counter_value\" class=\"form-control-label\">".gettext("Counter value:")."</label></div>\n";
echo "<div class=\"col-12 col-md-2\"><input type=\"text\" id=\"counter_value\" name=\"counter_value\" placeholder=\"".gettext("Counter value")."\" class=\"form-control\"";
if (isset($_GET['modify']))
echo " VALUE='".$row1['counter_value']."'";
echo " required><small class=\"form-text text-muted\">".gettext("Counter value")."</small></div>\n";
echo "</div>";
if (isset($_GET['modify'])){
echo "<input type=\"hidden\" name=\"counter_value_id\" id=\"counter_value_id\" value=\"".$_GET['counter_value_id']."\">";

}
 echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
 
echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"counters\">";



?>
</div><?php //card-body card-block  ?>
<div class="card-footer">
<button type="submit" class="btn btn-primary btn-sm">
<i class="fa fa-dot-circle-o"></i><?php echo gettext(" Submit ");?>
</button>
<button type="reset" class="btn btn-danger btn-sm">
<i class="fa fa-ban"></i><?php echo gettext(" Reset ");?>
</button>
</div>
<input type="hidden" name="page" id="page" value="counters">
</form>
</div>
<?php //card  
echo "<script>\n";
echo "$(\"#counter_form\").validate()\n";
echo "</script>\n";
}//if (isset($_GET["new_value"]))
if ((isset($_GET['only_last']) && $_GET['only_last']==1) || !isset($_GET['only_last']))
    $_SESSION['show_all_counter_value']=1;
else if (isset($_GET['only_last']) && $_GET['only_last']==0)
    unset($_SESSION['show_all_counter_value']);
    
if (isset($_SESSION['show_all_counter_value']))
    echo "<button type='button' class='btn btn-primary btn-sm' onClick=\"location.href='index.php?page=counters&only_last=0'\">".gettext("Show only last counter values")."</button>";
else
    echo "<button type='button' class='btn btn-primary btn-sm' onClick=\"location.href='index.php?page=counters&only_last=1'\">".gettext("Show all counter value")."</button>";
    
?>
<div class="card-body">

<table id="counters-table" class="table table-striped table-bordered">
<thead>
<tr>
<?php 
if (!isset($_SESSION['show_all_counter_value'])){

echo "<th></th><th>".gettext("Location")."</th>";
echo "<th>".gettext("Last value")."</th>";
echo "<th>".gettext("Unit")."</th></tr>";

?>
</thead>
<tbody>
<?php
$page=lm_isset_int('page');
if ($page<1)
$page=1;
$from=0;

$SQL="Select counters.asset_id as asset_id,counters.counter_id as counter_id,counter_unit FROM counters LEFT JOIN assets ON counters.asset_id=assets.asset_id ORDER BY asset_name_".$lang;

$result_all=$dba->Select($SQL);

$from=($page-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);
foreach ($result as $row)
{
//$SQL="SELECT max(counter_value) as max,counter_value_id,counter_value_time FROM counter_values WHERE counter_id='".$row['counter_id']."'";
$SQL="SELECT counter_value as max,counter_value_id,counter_value_time FROM counter_values WHERE counter_id=".$row['counter_id']." ORDER BY counter_value DESC LIMIT 0,1";
    $row1=$dba->getRow($SQL);
    if (LM_DEBUG)
            error_log($SQL,0); 
    $from++;
    echo "<tr><td>";
                  
                            echo "<a href=\"javascript:ajax_call('add_new_counter_value','".$row['counter_id']."','','','','".URL."index.php','for_ajaxcall')\" title=\"".gettext("Add new counter value")."\"><i class=\"fa fa-clock-o\"></i> ";
                             echo "</a>";
                            echo "<a href=\"index.php?page=counters&modify=1&counter_value_id=".$row1['counter_value_id']."\" title=\"".gettext("alter counter value")."\"> <i class=\"fa fa-wrench\" style='color:brown'></i></a>";
       
    echo "</td>\n";
    echo "<td>";
    $k="";
        $n="";
        if ($row['asset_id']>0)
        {
        foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."-><wbr>";
        }
       
        }
        echo substr($n,0,-7);
    echo "</td>";
    echo "<td>";
    
    echo $row1['max'];
    echo "</td>";
    echo "<td>".get_unit_from_id($row['counter_unit'])."</td>";
    echo "</tr>\n";
    }
} // if (isset($_SESSION['show_all_counter_value']))   
else{
$page=lm_isset_int('page');
if ($page<1)
$page=1;
$from=0;
$SQL="SELECT counter_value,counter_values.counter_id,counter_value_time,asset_id,counter_unit FROM counter_values LEFT JOIN counters ON counters.counter_id=counter_values.counter_id ORDER BY counter_value_time DESC";
$result_all=$dba->Select($SQL);

$from=($page-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);

echo "<th></th><th>Date</th><th>".gettext("Location")."</th>";
echo "<th>".gettext("Value")."</th>";
echo "<th>".gettext("Unit")."</th></tr>\n";
$i=1; 
if (!empty($result)){
    foreach ($result as $row){
    echo "<tr><td>".$i."</td>";
    echo "<td>".date("Y.m.d", strtotime($row["counter_value_time"]))."</td>";
    echo "<td>";
    $k="";
        $n="";
        if ($row['asset_id']>0)
        {
        foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."-><wbr>";
        }
        }
        echo substr($n,0,-7);
    echo "</td>";
    echo "<td>".$row['counter_value']."</td>\n";
    echo "<td>".get_unit_from_id($row['counter_unit'])."</td></tr>\n";
    }}

}

?>

</tbody>
</table>
<?php
include(INCLUDES_PATH."pagination.php");
?>
</div>



