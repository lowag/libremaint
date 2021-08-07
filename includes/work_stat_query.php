<?php 
function work_stat_query($start_date,$end_date,$request_type,$priority,$sum_field,$group_by_field,$asset_vs_product):string{
global $dba,$lang_date_format,$lang,$important_only;

$html='';
$all_user=array();
$total_workers_array=array();
$SQL="SELECT user_id,surname,firstname FROM users WHERE user_level<4";
$result=$dba->Select($SQL);
foreach($result as $row)
    if (FIRSTNAME_IS_FIRST)
    $all_user[$row['user_id']]=$row['firstname']." ".$row['surname'];
    else
    $all_user[$row['user_id']]=$row['surname']." ".$row['firstname'];

$SQL="SELECT "; 
if (!empty($sum_field) && $sum_field=='workhour' && ($group_by_field=='priority' || $group_by_field=='request_type'))
    $SQL.="sum(TIME_TO_SEC(workorder_worktime)/3600) as ".$sum_field." ";
    else
    $SQL.="workorder_".$lang.",workorder_user_id,workorder_works.workorder_partner_id ,workorder_short_".$lang.",workorder_work_end_time,workorder_work_start_time,workorder_worktime, priority,workorder_work_".$lang.",workorders.main_asset_id,workorders.asset_id,unplanned_shutdown";
    if ($asset_vs_product==1)
        $SQL.=", asset_name_".$lang;
    else
        $SQL.=",product_id_to_refurbish";
    $SQL.=" FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id";

    if ($asset_vs_product==1)
        $SQL.=" LEFT JOIN assets ON assets.asset_id=workorder_works.main_asset_id";

    $SQL.=" WHERE workorder_works.deleted<>1";

if ($asset_vs_product==1 && $important_only==1)
$SQL.=" AND asset_importance=1";

if ($request_type>0)
$SQL.=" AND request_type=".$request_type;

if ($priority>0)
$SQL.=" AND priority=".$priority;

if (validateDate($start_date,"Y-m-d") && validateDate($end_date,"Y-m-d"))
{
$SQL.=" AND DATE(workorder_work_end_time) >='".$start_date."'";
$SQL.=" AND DATE(workorder_work_end_time) <='".$end_date."'";
}
else 
$SQL.=" AND workorder_work_end_time > DATE_SUB(NOW(), INTERVAL 1 MONTH)";

if ($asset_vs_product==1)
$SQL.=" AND workorders.asset_id>0";
else if ($asset_vs_product==0)
$SQL.=" AND product_id_to_refurbish>0";
if ((!empty($sum_field) && $sum_field=='workhour') && ($group_by_field=='priority' || $group_by_field=='request_type'))

$SQL.=" GROUP BY ".$group_by_field;
if (empty($group_by_field))
{
$SQL.=" ORDER BY ";
if ($asset_vs_product==1)
$SQL.="asset_name_".$lang.",";
$SQL.="workorder_work_start_time,workrequest_id,workorders.workorder_id" ; 
}
$result=$dba->Select($SQL);


if (LM_DEBUG)
error_log($SQL,0);
$s_hours=0;
$s_date=0;
$s_main_asset_id=0;
$s_product_id=0;
$s_asset_id=0;
$s_workorder_short="";
$i=0;
$sum_hours=0;
$total_hours=0;
$workers_array=array();
$n="";
if ($dba->affectedRows()>0){
foreach ($result as $row){
 
$i++;
    if ($i==1 || ($s_main_asset_id!=$row['main_asset_id'] && $s_main_asset_id!=0|| ($asset_vs_product==0 && ($s_product_id!=$row['product_id_to_refurbish'])) && $s_product_id>0) ){
    //$workers_array=array(1=>20,2=>11);
    
      //$workers_array[$row['workorder_user_id']]=10;
        if ($i!=1){
        $html.="<h3>".$main_asset_name." ".gettext('total:')." ".$sum_hours." ";
            if ($sum_hours>1)
        $html.=gettext("hours");
        else
        $html.=gettext("hour");
        $html.=" (";
            $j=0;
            foreach ($workers_array as $worker_id=>$hours){
            if ($j>0)
            $html.=", ";
            $html.= $all_user[$worker_id]." : ".round($hours,1)." ";
            if ($hours<=1)
            $html.=gettext("hour");
            else
            $html.=gettext("hours");
            $j++;
            
            }
        $html.=" )</h3>";
           
        $html.= "<hr size='1'>";
        }
        //$total_hours+=$sum_hours;
        $sum_hours=0;
        if ($asset_vs_product==1)
        $main_asset_name=get_asset_name_from_id($row['main_asset_id'],$lang);
        else if ($row['product_id_to_refurbish']>0)
        $main_asset_name=get_product_name_from_id($row['product_id_to_refurbish'],$lang).' '.gettext('refurbish');
        $html.="<H2></H2><H2>".$main_asset_name."</H2>";
         $workers_array=array();
        }
       if (empty($row['workorder_partner_id']) && !isset($workers_array[$row['workorder_user_id']]))
        $workers_array[$row['workorder_user_id']]=0;
       if (empty($row['workorder_partner_id']) && !isset($total_workers_array[$row['workorder_user_id']])) 
        $total_workers_array[$row['workorder_user_id']]=0;
        $s_main_asset_id=$row['main_asset_id'];
        if($asset_vs_product==0)
        $s_product_id=$row['product_id_to_refurbish'];
        else
        $s_product_id=0;
        
        
        
        if ($s_asset_id!=$row['asset_id'] || $s_workorder_short!=$row['workorder_short_'.$lang] ){
        $s_asset_id=$row['asset_id'];
       
        
        if ($row['main_asset_id']!=$row['asset_id'] && $asset_vs_product==1){
        
        foreach (get_whole_path("asset",$row['asset_id'],1) as $k)
        {
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."-><wbr>";
            
        }
        $html.="<strong>".trim(substr($n,0,-7)).": </strong>";
        $n="";
        }
        else if ($row['main_asset_id']==$row['asset_id'] || $asset_vs_product==0)
        $html.="<strong>".$main_asset_name.": </strong>";
        
        $s_workorder_short=$row['workorder_short_'.$lang];
         
           
        
        $html.=$row['workorder_short_'.$lang];
        if ($row['workorder_'.$lang]!='')
        $html.=": ".$row['workorder_'.$lang]; 
        $html.="<br/><table>"; 
        }
     
      //print_r($workers_array);
        if ($row['workorder_partner_id']>0)
        $html.="<tr><td><strong>".get_partner_name_from_id($row['workorder_partner_id'])." </strong></td>";
        else
        $html.="<tr><td><strong>".$all_user[$row['workorder_user_id']]." </strong></td>";
        $html.="<td>".date($lang_date_format." H:i", strtotime($row['workorder_work_start_time']))." - ".date("H:i", strtotime($row['workorder_work_end_time']))."</td>";
        $startTime = new DateTime($row['workorder_work_start_time']);
        $endTime = new DateTime($row['workorder_work_end_time']);
        $duration = $startTime->diff($endTime); //$duration is a DateInterval object
        $dur=round($duration->s / 3600 + $duration->i / 60 + $duration->h + $duration->days * 24,1);
        if (empty($row['workorder_partner_id']))
        {
        $workers_array[$row['workorder_user_id']]+=$dur;
        $total_workers_array[$row['workorder_user_id']]+=$dur;
        $total_hours+=$dur;
        }
        $html.= "<td style=\"width:30px;text-align:left;\">".$duration->format("%H:%I")."</td>";
        $sum_hours += round($duration->s / 3600 + $duration->i / 60 + $duration->h + $duration->days * 24, 1);
       
        $html.="<td style=\"width:200px;text-align:left;\">";
        if ($row['unplanned_shutdown'])
        $html.="<strong>".gettext("Unplanned shutdown")."</strong>";
        if ($row['unplanned_shutdown'] && !empty($row['workorder_work_'.$lang]))
        $html.=": ";
        $html.=$row['workorder_work_'.$lang]."</td>";
        
        
        $html.="</tr>";
        
        
$html.="</table>";
/*
        */
        
        }
        $html.="<h3>".$main_asset_name." ".gettext('total:')." ".$sum_hours." ";
        if ($sum_hours>1)
        $html.=gettext("hours");
        else
        $html.=gettext("hour");
        $html.=" (";
        $j=0;
        foreach ($workers_array as $worker_id=>$hours){
        if ($j>0)
        $html.=", ";
        $html.= $all_user[$worker_id]." : ".$hours." ";
        if ($hours<=1)
        $html.=gettext("hour");
        else
        $html.=gettext("hours");
        $j++;
        }
        $html.=")</h3>";
        $html.= "<hr size='1'>";
        $html.="<h3>".gettext('Total:')." ".$total_hours." ";
        if ($total_hours<=1)
        $html.=gettext("hour");
        else
        $html.=gettext("hours");
        
        
        $html.=" (";
        $j=0;
        foreach ($total_workers_array as $worker_id=>$hours){
        if ($j>0)
        $html.=", ";
        $html.= $all_user[$worker_id]." : ".round($hours,2)." ";
        if ($hours<=1)
        $html.=gettext("hour");
        else
        $html.=gettext("hours");
        $j++;
        }
        $html.=")</h3>";
        $html.= "<hr size='1'>";
        }
        return $html;
      }  
?>        
