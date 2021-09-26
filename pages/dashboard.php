<?php
if (PINBOARD && isset($_SESSION["SEE_PINBOARD"]))
{
echo "<div>\n<nav>\n";
echo "<div class='nav nav-tabs' id='nav-tab' role='tablist'>\n";
echo "<a class='nav-item nav-link active' data-toggle='tab' href='' role='tab' aria-controls='nav-home' aria-selected='true'>".gettext("Dashboard")."</a>\n";
echo "<a class='nav-item nav-link' href='index.php?page=pinboard' role='tab' aria-controls='nav-profile' aria-selected='false'>".gettext("Pinboard")."</a>\n";
echo "</div></nav></div>\n";
}
?>

<?php
 
if ($_SESSION['user_level']<4){

?> 
 <div class="content mt-3">
<?php
  /*          <div class="col-sm-12">
                <div class="alert  alert-success alert-dismissible fade show" role="alert">
                    <span class="badge badge-pill badge-success">Success</span> You successfully read this important alert message.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
*/
            ?>

            <div class="col-sm-6 col-lg-3"><a href="index.php?page=workrequests&workrequest_status=1">
                <div class="card text-white bg-flat-color-1">
                    <div class="card-body pb-0">
                         <?php /*   <div class="dropdown float-right">
                            <button class="btn bg-transparent dropdown-toggle theme-toggle text-light" type="button" id="dropdownMenuButton1" data-toggle="dropdown">
                                <i class="fa fa-cog"></i>
                            </button>
                        
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <div class="dropdown-menu-content">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </div>
                            
                        </div>*/?>
                        <h4 class="mb-0">
                            <span class="count">
<?php 
$SQL="SELECT count(workrequest_id) as count FROM workrequests WHERE workrequest_status=1 AND for_operators<>1";
$row=$dba->getRow($SQL);
echo $row['count']." ";
if ($row['count']>1)
echo gettext("pcs");
else
echo gettext("pc");

?>
                            </span>
                        </h4>
                        <p class="text-light"><?php echo gettext("active workrequest(s)");?></p>
                      
                    </div>

                </div>
                </a>
            </div>
            <!--/.col-->

     <div class="col-sm-6 col-lg-3"><a href="index.php?page=workorders&only_active=1">
                <div class="card text-white bg-flat-color-2">
                    <div class="card-body pb-0">
                    
                        <h4 class="mb-0">
                            <span class="count">
<?php 
$SQL="SELECT count(workorder_id) as count FROM workorders WHERE workorder_status<5";
$row=$dba->getRow($SQL);
echo $row['count']." ";
if ($row['count']>1)
echo gettext("pcs");
else
echo gettext("pc");
?>                            
                            </span>
                        </h4>
                        <p class="text-light"><?php echo gettext("active workorder(s)");?></p>


                    </div>
                </div>
                </a>
            </div>
            <!--/.col-->

            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-flat-color-3">
                    <div class="card-body pb-0">
                        
                        <h4 class="mb-0">
<?php
$fi = new FilesystemIterator(INFO_PATH, FilesystemIterator::SKIP_DOTS);

echo "<span class=\"count\">";
echo (iterator_count($fi)-1)." ".gettext("pcs");//-1 because of thumbs subfolder
 $size = -16*1024;// because of thumbs subfolder
foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(INFO_PATH)) as $file){
        $size += $file->getSize();
    }
  $mod = 1024;
    $units = explode(' ','B KB MB GB TB PB');
    for ($i = 0; $size > $mod; $i++) {
        $size /= $mod;
    }
echo " (".round($size, 2) . ' ' . $units[$i].")";   
echo "</span>";
?>
                            
                        </h4>
                        <p class="text-light"><?php echo gettext("info file(s)");?></p>

                    </div>

                </div>
            </div>
            <!--/.col-->
</div>            
<div class="content mt-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-flat-color-1">
                    <div class="card-body pb-0">
                       
                        <h4 class="mb-0"><span class="count">
<?php 
if (file_exists(LAST_BACKUP))
echo date($lang_date_format." H:i", filemtime(LAST_BACKUP));
?></span>
                        </h4>
                        <p class="text-light"><?php echo gettext("last backup");?></p>
                      
                    </div>

                </div>
            </div>
 

  
            <!--/.col-->

            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-flat-color-2">
                    <div class="card-body pb-0">
                        
                        <h4 class="mb-0">
                            <span class="count">
<?php 
$SQL="SELECT count(asset_id) as count FROM assets";
$row=$dba->getRow($SQL);
echo $row['count']." ";
if ($row['count']>1)
echo gettext("pcs");
else
echo gettext("pc");
?>                            
                            </span>
                        </h4>
                        <p class="text-light"><?php echo gettext("assets");?></p>


                    </div>
                </div>
            </div>
 
 
 
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-flat-color-3">
                    <div class="card-body pb-0">
                        
                        <h4 class="mb-0">
<?php
$fi = new FilesystemIterator(INFO_PATH, FilesystemIterator::SKIP_DOTS);

echo "<span class=\"count\">";
$SQL="SELECT COUNT(product_id) as pcs FROM stock";
$row=$dba->getRow($SQL);

echo $row['pcs']." ".gettext("pcs");   
echo "</span>";
?>
                            
                        </h4>
                        <p class="text-light"><?php echo gettext("products in stock");?></p>

                    </div>

                </div>
            </div>
<?php

  
   
  }//if ($_SESSION['user_level']<4) 
  /*
  #
  #
  */
  else{
  ?> 
<div class="content mt-3">

            <div class="col-sm-6 col-lg-3"><a href="index.php?page=notifications">
                <div class="card text-white bg-flat-color-1">
                    <div class="card-body pb-0">
                         <?php /*   <div class="dropdown float-right">
                            <button class="btn bg-transparent dropdown-toggle theme-toggle text-light" type="button" id="dropdownMenuButton1" data-toggle="dropdown">
                                <i class="fa fa-cog"></i>
                            </button>
                        
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <div class="dropdown-menu-content">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </div>
                            
                        </div>*/?>
                        <h4 class="mb-0">
                            <span class="count">
<?php 
$SQL="SELECT count(notification_id) as count FROM notifications WHERE user_id=".$_SESSION['user_id']." AND notification_status=1";
$row=$dba->getRow($SQL);
echo $row['count']." ";
if ($row['count']>1)
echo gettext("pcs");
else
echo gettext("pc");
?>
                            </span>
                        </h4>
                        <p class="text-light"><?php echo gettext("new notification(s)");?></p>
                      
                    </div>

                </div>
                </a>
            </div>
            <!--/.col-->

     <div class="col-sm-6 col-lg-3"><a href="index.php?page=notifications&notification_status=3">
                <div class="card text-white bg-flat-color-2">
                    <div class="card-body pb-0">
                    
                        <h4 class="mb-0">
                            <span class="count">
<?php 
$SQL="SELECT count(notification_id) as count FROM notifications WHERE user_id=".$_SESSION['user_id']." AND notification_status=2";//confirmed
$row=$dba->getRow($SQL);
echo $row['count']." ";
if ($row['count']>1)
echo gettext("pcs");
else
echo gettext("pc");
?>                            
                            </span>
                        </h4>
                        <p class="text-light"><?php echo gettext("confirmed notifications");?></p>


                    </div>
                </div>
                </a>
            </div>
            <!--/.col-->

            <div class="col-sm-6 col-lg-3"><a href="index.php?page=notifications&notification_status=3">
                <div class="card text-white bg-flat-color-3">
                    <div class="card-body pb-0">
                        
                        <h4 class="mb-0">
<?php
echo "<span class=\"count\">";
$SQL="SELECT count(notification_id) as count FROM notifications WHERE user_id=".$_SESSION['user_id']." AND notification_status=3";//resolved
$row=$dba->getRow($SQL);
echo $row['count']." ";
if ($row['count']>1)
echo gettext("pcs");
else
echo gettext("pc");
echo "</span>";
?>
                            
                        </h4>
                        <p class="text-light"><?php echo gettext("work in progress notifications");?></p>

                    </div>

                </div>
                </a>
            </div>
            <!--/.col-->
</div> 
  
  

<div class="content mt-3">
     <div class="col-sm-6 col-lg-3"><a href="index.php?page=notifications&notification_status=4">
                <div class="card text-white bg-flat-color-2">
                    <div class="card-body pb-0">
                    
                        <h4 class="mb-0">
                            <span class="count">
<?php 
$SQL="SELECT count(notification_id) as count FROM notifications WHERE user_id=".$_SESSION['user_id']." AND notification_status=4";//resolved
$row=$dba->getRow($SQL);
echo $row['count']." ";
if ($row['count']>1)
echo gettext("pcs");
else
echo gettext("pc");
?>                            
                            </span>
                        </h4>
                        <p class="text-light"><?php echo gettext("resolved notifications");?></p>


                    </div>
                </div>
                </a>
            </div>
            <!--/.col-->

     
     <div class="col-sm-6 col-lg-3"><a href="index.php?page=notifications&notification_status=5">
                <div class="card text-white bg-flat-color-3">
                    <div class="card-body pb-0">
                    
                        <h4 class="mb-0">
                            <span class="count">
<?php 
$SQL="SELECT count(notification_id) as count FROM notifications WHERE user_id=".$_SESSION['user_id']." AND notification_status=5";//closed
$row=$dba->getRow($SQL);
echo $row['count']." ";
if ($row['count']>1)
echo gettext("pcs");
else
echo gettext("pc");
?>                            
                            </span>
                        </h4>
                        <p class="text-light"><?php echo gettext("closed notifications");?></p>


                    </div>
                </div>
                </a>
            </div>
            <!--/.col-->

     
     
     <div class="col-sm-6 col-lg-3"><a href="index.php?page=works">
                <div class="card text-white bg-flat-color-1">
                    <div class="card-body pb-0">
                       
                        <h4 class="mb-0">
                            <span class="count">
<?php 
$SQL="SELECT count(operator_work_id) as count FROM operator_works WHERE deleted<>1 AND user_id=".$_SESSION['user_id'];
$row=$dba->getRow($SQL);
if (!empty($row))
{
echo (int) $row['count']." ";
if ($row['count']>1)
echo gettext("pcs");
else
echo gettext("pc");
}
//echo "0 pc";

?>
                            </span>
                        </h4>
                        <p class="text-light"><?php echo gettext("recorded work");?></p>
                      
                    </div>

                </div>
                </a>
            </div>
 <?php
 }//$_SESSION['user_level']>3
 ?>
<div class="content mt-3">

<?php
$SQL="select CONCAT(surname,' ', firstname) as name , count(notification_id) as n from notifications left join users on notifications.user_id=users.user_id group by notifications.user_id ORDER BY n DESC" ; 
$result=$dba->Select($SQL);
if ($dba->affectedRows()>0){
  ?>
  <div class="col-sm-6 col-lg-6">
  <canvas id="bar-chart-not" width="500" height="500"></canvas>
  <script>
  new Chart(document.getElementById("bar-chart-not"), {
    type: 'bar',
    data: {
    labels: [<?php
    $i=0;
    foreach ($result as $row){
   
    if (!empty($row["name"])){ 
    if ($i!=0)
    echo ",";
    echo "\"".$row['name']."\"";
    $i++;}
    }
    ?>],
      datasets: [{
        label: "<?php echo gettext("Notification numbers per person");?>",
        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#f88fff","#808080","#009900","black","yellow","purple","orange"],
        data: [<?php
        $k=array();
        $i=0;
        foreach ($result as $row){
        if ($row["n"]>0){ 
        if ($i!=0)
        echo ",";
        echo $row['n'];
        $i++;
        }
        }
        
        ?>]
      }]
    },
    options: {
      title: {
        display: true,
        text: '<?php echo gettext("Notification numbers per person");?>'
      },
      scales: {
        xAxes: [{
            beginAtZero: true,
            ticks: {
            autoSkip: false
      }
        }]
    }
    }
});</script>

  </div>
<?php
}
if (file_exists(INCLUDES_PATH."spec_graphs.php"))
require(INCLUDES_PATH."spec_graphs.php"); 
?>

  <?php
 
  
  
  if (isset($_SESSION['SEE_STATS_OF_WORKS']))
  {
  ?>
  
<div class="content mt-3"> 




<?php
//$SQL="select SUM(TIME_TO_SEC(workorder_work_end_time  - workorder_work_start_time)/3600) as workhour, priority FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id WHERE `workorder_work_end_time` >now() - INTERVAL 30 day GROUP BY priority ORDER BY workhour DESC" ; 

$SQL="select SUM(TIME_TO_SEC(workorder_worktime)/3600) as workhour, priority FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id WHERE workorder_works.deleted<>1 AND `workorder_work_end_time` >now() - INTERVAL 30 day GROUP BY priority ORDER BY workhour DESC" ; 



$result=$dba->Select($SQL);
if ($dba->affectedRows()>0){
?>
<div class="col-sm-6 col-lg-3">
  <canvas id="pie-chart2" width="500" height="500"></canvas>
<script>
new Chart(document.getElementById("pie-chart2"), {
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
    options: {
      title: {
        display: true,
        text: '<?php echo gettext("Working hours per priorities");?>'
      }
    }
});</script>
</div>
<?php } ?>




<?php
//$SQL="select SUM(TIME_TO_SEC(workorder_work_end_time  - workorder_work_start_time)/3600) as workhour, request_type FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id WHERE `workorder_work_end_time` >now() - INTERVAL 30 day GROUP BY request_type ORDER BY workhour DESC" ; 

$SQL="select SUM(TIME_TO_SEC(workorder_worktime)/3600) as workhour, request_type FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id WHERE workorder_works.deleted<>1 AND `workorder_work_end_time` >(now() - INTERVAL 30 day) GROUP BY request_type ORDER BY workhour DESC" ; 
$result=$dba->Select($SQL);
if ($dba->affectedRows()>0){
?>
<div class="col-sm-6 col-lg-3">
  <canvas id="pie-chart3" width="500" height="500"></canvas>
<script>
new Chart(document.getElementById("pie-chart3"), {
    type: 'pie',
    data: {
    labels: [<?php
    $i=0;
    foreach ($result as $row){
   
    if ($row["request_type"]>0){ 
    if ($i!=0)
    echo ",";
    echo "\"".$activity_types[$row["request_type"]-1]."\"";
    $i++;}
    }
    ?>],
      datasets: [{
        label: "<?php echo gettext("Working hours per activities");?>",
        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#ffffff","#808080","#009900","black","yellow"],
        data: [<?php
        $k=array();
        $i=0;
        foreach ($result as $row){
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
    options: {
      title: {
        display: true,
        text: '<?php echo gettext("Working hours per activities");?>'
      }
    }
});</script>
<p style="text-align:center;"><a href="<?php echo URL;?>/index.php?page=pdf_create&title=work_stat_by_assets&period=last" target="_blank">
<?php echo gettext("List last month's activities...");?>
</a></p>


</div>
<?php }?>

<?php
//$SQL="select SUM(TIME_TO_SEC(workorder_work_end_time  - workorder_work_start_time)/3600) as workhour, main_asset_id FROM workorder_works WHERE `workorder_work_end_time` >now() - INTERVAL 30 day GROUP BY main_asset_id ORDER BY workhour DESC" ; 

$SQL="select SUM(TIME_TO_SEC(workorder_worktime)/3600) as workhour, main_asset_id FROM workorder_works WHERE workorder_works.deleted<>1 AND `workorder_work_end_time` >now() - INTERVAL 30 day GROUP BY main_asset_id ORDER BY workhour DESC" ; 


$result=$dba->Select($SQL);
if ($dba->affectedRows()>0){
?>
<div class="col-sm-6 col-lg-3">
  <canvas id="pie-chart" width="500" height="600"></canvas>
<script>
new Chart(document.getElementById("pie-chart"), {
    type: 'pie',
    data: {
    labels: [<?php
    $i=0;
    foreach ($result as $row){
   
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
        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#ffffff","#808080","#009900","black","yellow"],
        data: [<?php
        $k=array();
        $i=0;
        $sum_others=0;
        foreach ($result as $row){
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
    options: {
      title: {
        display: true,
        text: '<?php echo gettext("Working hours per assets");?>'
      }
    }
});
</script>
</div>
<?php } ?>




</div>
<?php
}
?>


</div><!-- /#right-panel -->

    <!-- Right Panel -->


