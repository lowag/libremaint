<div class="content">


<?php 


require(INCLUDES_PATH."operating_by_shifts.php"); 
require(INCLUDES_PATH."operating_timebar.php"); 
$begin = new DateTime(date('Y-m-d',strtotime('-10 days')));
  $end = new datetime('now');
$i=0;  
foreach (IOT_INTERVALS_ASSET_IDS as $iot)
{
if ($i%2==0)
echo "<div class=\"row\">\n";
echo "<div class=\"col-md-auto\">\n";
operating_by_shifts($iot,$begin,$end); 
operating_timebar($iot,date('Y-m-d'));
echo "</div>\n";
$i++;
if ($i%2==0)
echo "</div>\n";


}
echo "</div>";
?>
