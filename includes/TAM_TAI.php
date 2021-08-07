<?php
function TAM_TAI($start_date,$end_date,$TAM_OR_TAI,$naked):array
{
global $dba,$lang;
$tam_tai=array();
if (is_date_mysql_format($start_date) && is_date_mysql_format($end_date))
{

//$rand=get_random_string(5);
$rand="";
//creating a temp table to tamper it
$SQL="DROP table _workorder_works";
$dba->Query($SQL);
$SQL="CREATE TABLE _workorder_works 
        SELECT workorder_work_id,workorder_id,main_asset_id,workorder_work_start_time, workorder_work_end_time,unplanned_shutdown ,after_work_machine_can_run,workorder_user_id FROM workorder_works";
$SQL.=" WHERE DATE(workorder_work_start_time)>='".$start_date."' AND DATE(workorder_work_end_time)<='".$end_date."' ORDER BY  workorder_work_start_time,workorder_work_end_time";
$dba->Query($SQL);
if (LM_DEBUG)
error_log($SQL,0);
//if there is a work finished with "after_work_machine_can_run==0" we update its first workorder_work_end_time when the machine back to work and remove the all others 
//(there will be only one long period)
$SQL="SELECT * from _workorder_works WHERE after_work_machine_can_run IS FALSE ORDER BY workorder_id,workorder_work_start_time ASC";
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);
$workorder_id=0;
if (!empty($result)){
foreach($result as $row)
{
    if ($workorder_id!=$row['workorder_id'])
    {
    $workorder_id=$row['workorder_id'];
    
    $SQL="SELECT workorder_work_end_time as back_to_time FROM workorder_works WHERE workorder_id=".$row['workorder_id']." AND workorder_work_start_time>='".$row['workorder_work_start_time']."' AND after_work_machine_can_run IS TRUE ORDER BY workorder_work_start_time LIMIT 0,1";
    $row1=$dba->getRow($SQL);
    if (LM_DEBUG)
    error_log($SQL,0);
    if (!empty($row1['back_to_time']))
        $back_to_time=$row1['back_to_time'];
    else
        $back_to_time=$end_date." 23:59:00"; //not back, but after the end_date
    $SQL1="UPDATE _workorder_works SET after_work_machine_can_run= TRUE, workorder_work_end_time='".$back_to_time."' WHERE workorder_id=".$row['workorder_id']." AND workorder_work_start_time='".$row['workorder_work_start_time']."' AND workorder_user_id=".$row['workorder_user_id'];    
    $dba->Query($SQL1);
    if (LM_DEBUG)
    error_log($SQL1,0);
    }
}
$SQL="DELETE FROM _workorder_works WHERE after_work_machine_can_run IS FALSE";
$dba->Query($SQL);
}else
if (LM_DEBUG)
    error_log("There was no work end with no success",0);



if ($TAM_OR_TAI==1)
$SQL="SELECT asset_id FROM assets WHERE main_asset_category_id=1";
else
$SQL="SELECT asset_id FROM assets WHERE main_asset_category_id=10";
if (LM_DEBUG)
error_log($SQL,0);
$result1=$dba->Select($SQL);

foreach ($result1 as $row) {

$SQL="SELECT workorder_work_start_time, workorder_work_end_time,main_asset_id FROM ".$rand."_workorder_works WHERE";
if ($naked)
$SQL.=" unplanned_shutdown=1 AND";
$SQL.=" main_asset_id=".$row['asset_id']." AND DATE(workorder_work_start_time)>='".$start_date."' AND DATE(workorder_work_start_time)<='".$end_date."' ORDER BY  workorder_work_start_time,workorder_work_end_time";
$result=$dba->Select($SQL);
 if (LM_DEBUG)
error_log($SQL,0);   
      $sum = (new DateTime())->setTimestamp(0);
    $previousEnd = null;
    $previous_main_asset_id=null;  
    if (!empty($result)){
        foreach ($result as $time) {
            if ($previous_main_asset_id!=$time['main_asset_id'])
            $current_main_asset_id=$time['main_asset_id'];
            $currentStart = new DateTimeImmutable($time['workorder_work_start_time']);
            $currentEnd = new DateTimeImmutable($time['workorder_work_end_time']);
            
            if ($currentEnd < $previousEnd) continue;
            
            $sum->add($currentStart->diff($currentEnd));
            
            if ($previousEnd !== null && $currentStart < $previousEnd) {
                $sum->sub($currentStart->diff($previousEnd));
            }
            
            $previousEnd = $currentEnd;
        }
        }
        
        $tam_tai[$row['asset_id']]=$sum->getTimestamp()/60;
        }
}else // not valid date
lm_die("Not valid date at TAM_TAI.php");
return $tam_tai;
}
?>
