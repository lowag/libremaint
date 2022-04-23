
<?php

//<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-piechart-outlabels"></script>
echo "<script src=\"".VENDORS_LOC."chartjs-plugin-piechart-outlabels/chartjs-plugin-piechart-outlabels.js\"></script>";
//echo " <button type=\"button\" id=\"create_task_list_button\" name=\"create_task_list_button\" class=\"btn btn-danger btn-sm\" ";
      //  echo " onClick=\"window.open('index.php?page=pdf_create&title=work_stat_by_assets&period=last','_blank')\"";
        //echo ">".gettext("Last month's works")."</button>";
 if (!isset($_POST['make_chart'])){ 
 echo "<div class='card'>\n"; 
 echo "<div class='card-body card-block'>";
  echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\" id=\"work_form\" name=\"work_form\">";
        
  echo "<div class=\"row form-group\">\n";
        
        echo "<div class=\"col col-md-2\"><label for=\"start_time\" class=\"form-control-label\">".gettext("Start date:")."</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        
        echo "<input type=\"date\" name=\"start_date\" value=\"";
        echo date("Y-m-d");
        echo "\"></div></div>";
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"end_date\" class=\"form-control-label\">".gettext("End date:")."</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        
        echo "<input type=\"date\" name=\"end_date\" value=\"";
        echo date("Y-m-d");
        echo "\"></div></div>";
        
        
        echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"importance\" class=\"form-control-label\">".gettext("Only the important machine:")."</label></div>\n";
        echo "<div class=\"col col-md-3\">";
        
        echo "<SELECT name='important_only' id='important_only'>";
        echo "<OPTION VALUE='1'>".gettext("YES");
        echo "<OPTION VALUE='0'>".gettext("NO");
        echo "</SELECT>";
        echo "</div></div>";
        
        echo "<input type=\"hidden\" id='page' name='page' value='work_stats'>\n";
        echo "<input type=\"hidden\" id='make_chart' name='make_chart' value='1'>\n";
  /*      
        echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"request_type\" class=\" form-control-label\">".gettext("Activity type:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-3\">";
        echo "<select name=\"request_type\" id=\"request_type\" class=\"form-control\" >";
        echo "<option value=\"all\">".gettext("All");
     foreach($activity_types as $id => $activity_type) //$activity_types from config/lm-settings.php
     {
     echo "<option value=\"".++$id."\"";
    
     echo ">".$activity_type."</option>\n";
      }  
        echo "</select>\n";
    echo "</div>";
echo "</div>"; 
*/
echo "</div>";//card-body

        
        echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
        echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
        echo "</form>";
        //echo "</div>";
        echo "</div></div>\n";
        //echo "</div>\n";
}else{
$start=$dba->escapeStr($_POST['start_date']);
$end=$dba->escapeStr($_POST['end_date']);


$SQL="select SUM(TIME_TO_SEC(workorder_worktime)/3600) as workhour, priority FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id";

if (isset($_POST['important_only']) && $_POST['important_only']==1)
$SQL.=" LEFT JOIN assets ON assets.asset_id=workorders.asset_id";



$SQL.=" WHERE";

if (isset($_POST['important_only']) && $_POST['important_only']==1)
$SQL.=" assets.asset_importance=1 AND";

$SQL.=" DATE(workorder_work_start_time) >=DATE('".$start."') AND DATE(workorder_work_end_time) <=DATE('".$end."') GROUP BY priority ORDER BY workhour DESC" ; 

$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);

$SQL2="select SUM(TIME_TO_SEC(workorder_worktime)/3600) as workhour, request_type FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id";

if (isset($_POST['important_only']) && $_POST['important_only']==1)
$SQL2.=" LEFT JOIN assets ON assets.asset_id=workorders.asset_id";

$SQL2.=" WHERE";

if (isset($_POST['important_only']) && $_POST['important_only']==1)
$SQL2.=" assets.asset_importance=1 AND";

$SQL2.=" DATE(workorder_work_start_time) >=DATE('".$start."') AND DATE(workorder_work_end_time) <=DATE('".$end."') GROUP BY request_type ORDER BY workhour DESC" ; 
$result2=$dba->Select($SQL2);
if (LM_DEBUG)
error_log($SQL2,0);


$SQL3="select SUM(TIME_TO_SEC(workorder_worktime)/3600) as workhour, main_asset_id FROM workorder_works";

if (isset($_POST['important_only']) && $_POST['important_only']==1)
$SQL3.=" LEFT JOIN assets ON assets.asset_id=workorder_works.main_asset_id";

$SQL3.=" WHERE";

if (isset($_POST['important_only']) && $_POST['important_only']==1)
$SQL3.=" assets.asset_importance=1 AND";

$SQL3.=" DATE(workorder_work_start_time) >=DATE('".$start."') AND DATE(workorder_work_end_time) <=DATE('".$end."') GROUP BY main_asset_id ORDER BY workhour DESC"; 


$result3=$dba->Select($SQL3);

echo "<form action=\"index.php\" method=\"post\" target=\"_blank\" enctype=\"multipart/form-data\" class=\"form-horizontal\" id=\"work_form\" name=\"work_form\" >";
echo "<input type=\"hidden\" id='page' name='page' value='pdf_create'>\n";
if (isset($_POST['request_type']))
echo "<input type=\"hidden\" id='request_type' name='request_type' value='".$_POST['request_type']."'>\n";
else
echo "<input type=\"hidden\" id='request_type' name='request_type' value=''>\n";
echo "<input type=\"hidden\" id='start_date' name='start_date' value='".$_POST['start_date']."'>\n";
echo "<input type=\"hidden\" id='end_date' name='end_date' value='".$_POST['end_date']."'>\n";
echo "<input type=\"hidden\" id='chart1' name='chart1' value=''>\n";
echo "<input type=\"hidden\" id='chart2' name='chart2' value=''>\n";
echo "<input type=\"hidden\" id='chart3' name='chart3' value=''>\n";
echo "<button type=\"submit\" class=\"btn btn-primary btn-sm\" onClick=\"return pdf_create()\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext("Next")."</button></form>\n";

?>

<div class="chart-container" style="position: relative; width:800px">
<canvas width='250' id="pie-chart1">
<canvas width='250' id="pie-chart2">
<canvas width='250' id="pie-chart3">
</div>
<script>



var options={
                    responsive: false,
                    zoomOutPercentage: 100, // makes chart 55% smaller (50% by default, if the property is undefined)
                    plugins: {
                        
                        outlabels: {
                            text: '%l %p',
                            color: 'black',
                            stretch: 30,
                            font: {
                                resizable: false,
                                size: 12
                            }
                        }
                    }
      }
    ;

var my_chart1=new Chart(document.getElementById("pie-chart1"), {
    type: 'pie',
    data: {
    labels: [<?php
    $i=0;
    foreach ($result as $row){
   
    if ($row["priority"]>0){ 
    if ($i!=0)
    echo ",";
    echo "\"".$priority_types[$row["priority"]-1]."\"";
    $i++;}
    }
    ?>],
      datasets: [{
        label: "<?php echo gettext("Working hours per priorities");?>",
        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#ffffff","#808080","#009900","black","yellow"],
        data: [<?php
        $k=array();
        $i=0;
        foreach ($result as $row){
        if ($row["priority"]>0){ 
        if ($i!=0)
        echo ",";
        echo round($row['workhour'],2);
        $i++;
        }
        }
        
        ?>]
      }]
    },
    options: options
    
});

/*----------------------------------------------
-------------------------------------------------
*/
    
var my_chart2=new Chart(document.getElementById("pie-chart2"), {
    type: 'pie',
    data: {
    labels: [<?php
    $i=0;
    foreach ($result2 as $row){
   
    if ($row["request_type"]>0){ 
    if ($i!=0)
    echo ",";
    echo "\"".$activity_types[$row["request_type"]-1]."\"";
    $i++;}
    }
    ?>],
      datasets: [{
        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#ffffff","#808080","#009900","black","yellow"],
        data: [<?php
        $k=array();
        $i=0;
        foreach ($result2 as $row){
        if ($row["request_type"]>0){ 
        if ($i!=0)
        echo ",";
        echo round($row['workhour'],2);
        $i++;
        }
        }
        
        ?>]
      }]
    },
    options: options
    
});

/*------------------------------------------------------------
--------------------------------------------------------------
--------------------------------------------------------------*/
var my_chart3=new Chart(document.getElementById("pie-chart3"), {
    type: 'pie',
    data: {
    labels: [<?php
    $i=0;
    foreach ($result3 as $row){
   
    if ($row["main_asset_id"]>0){ 
    if ($i!=0 && $i<9)
    echo ",";
    if ($i<9){
    //$arr = explode(' ',get_asset_name_from_id($row["main_asset_id"],$lang));
    echo "\"".get_asset_name_from_id($row["main_asset_id"],$lang)."\"";
    //echo "\"".$arr[0]."\"";
    }else if ($i==9)
    echo ",\"".gettext("others")."\"";
    $i++;}
    }
    ?>],
      datasets: [{
        label: "<?php echo gettext("Working hours per assets");?>",
        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","purple","#808080","#009900","black","green"],
        data: [<?php
        $k=array();
        $i=0;
        $sum_others=0;
        foreach ($result3 as $row){
        if ($row["main_asset_id"]>0){ 
        if ($i!=0 && $i<9)
        echo ",";
        if ($i<9)
        echo round($row['workhour'],2);
        else{
        if ($row['workhour']>0)
        $sum_others+=round($row['workhour'],2);
        
        }
        $i++;
        }
        }
        echo ",".$sum_others;
        
        ?>]
      }]
    },
    options: options
});


    
    
    
    
    function pdf_create(){
var loc="";

    document.getElementById("chart1").value=document.getElementById("pie-chart1").toDataURL();
    document.getElementById("chart2").value=document.getElementById("pie-chart2").toDataURL();
    document.getElementById("chart3").value=document.getElementById("pie-chart3").toDataURL();
    
    return true;
}
</script>

<?php

}  
  
?>


