<?php
function operating_by_shifts($asset_id,$start_date,$end_date):void
{
global $dba,$shift_change_times,$lang;


$sum_operating_time=array();
$labels=array();
for ($i=0;$i<=count($shift_change_times);$i++)
{
${"data".$i}=array();
}
//if (is_date_mysql_format($day1))
if (($start_date instanceof DateTime) && ($end_date instanceof DateTime))
{
  
  
  $daterange = new DatePeriod($start_date, new DateInterval('P1D'), $end_date);
    
    foreach($daterange as $date){
        
        $day1=$date->format("Y-m-d");
        
        $labels[]="'".$day1."'";
        foreach ($shift_change_times as $key=>$shift_change_time){
                $sum_operating_time[$shift_change_time]=0;
                if ($shift_change_time==end($shift_change_times))
                {
                $day2 = date('Y-m-d',strtotime("+1 day",strtotime($day1)));
                $next_shift_change_key=0;
                }
                else{
                $day2=$day1;
                $next_shift_change_key=$key+1;
                }
                $SQL="SELECT start_time, end_time FROM operatings WHERE asset_id=".$asset_id." AND start_time<=CAST('".$day1." ".$shift_change_time.":00' as datetime) ORDER BY start_time DESC LIMIT 0,1";
                if (LM_DEBUG)
                    error_log($SQL,0);
                $row=$dba->getRow($SQL);
                if (strtotime($row['end_time'])>strtotime($day1." ".$shift_change_time.":00"))
                {
                // it is on
            $sum_operating_time[$shift_change_time]+=strtotime($row['end_time'])/60- strtotime($day1." ".$shift_change_time.":00")/60;
            //print_r($sum_operating_time[$shift_change_time]." ");
                }
            
                $SQL="SELECT sum(timestampdiff(MINUTE,start_time,end_time)) as sum FROM operatings WHERE asset_id=".$asset_id." AND start_time>CAST('".$day1." ".$shift_change_time.":00' as datetime) AND end_time<CAST('".$day2." ".$shift_change_times[$next_shift_change_key].":00' as datetime)";
                $row=$dba->getRow($SQL);
                if (LM_DEBUG)
                    error_log($SQL,0);
                $sum_operating_time[$shift_change_time]+=$row['sum'];    
                //print_r($sum_operating_time[$shift_change_time]." "); 
                $SQL="SELECT start_time, end_time FROM operatings WHERE asset_id=".$asset_id." AND start_time<=CAST('".$day2." ".$shift_change_times[$next_shift_change_key].":00' as datetime) ORDER BY end_time DESC LIMIT 0,1";

                $row=$dba->getRow($SQL);
                if (LM_DEBUG)
                    error_log($SQL."\n".$row['end_time']."\n".$day2." ".$shift_change_times[$next_shift_change_key],0);
                if (strtotime($row['end_time'])>strtotime($day2." ".$shift_change_times[$next_shift_change_key].":00"))
                {
                
            $sum_operating_time[$shift_change_time]+=strtotime($day2." ".$shift_change_times[$next_shift_change_key].":00")/60 - strtotime($row['start_time'])/60;
                }
                
                ${"data".$key}[]=round(($sum_operating_time[$shift_change_time]/(strtotime($day2." ".$shift_change_times[$next_shift_change_key].":00")/60-(strtotime($day1." ".$shift_change_times[$key].":00")/60)))*100,1);
                //print_r("data".$key.":".$sum_operating_time[$shift_change_time]." ");
                
                }
        }
}else
lm_die("It is not a valid date");

echo "<canvas id=\"bar-chart-".$asset_id."\" width=\"600\" height=\"600\"></canvas>\n";
echo "<script>\n";
$backgroundColor=array("#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#f88fff","#808080","#009900","black","yellow","purple","orange");
?>
new Chart(document.getElementById("bar-chart-<?php echo $asset_id;?>"), {
    type: 'bar',
    data: {
    labels: [<?php echo implode(',',$labels);?>],
    datasets: [
               
        <?php
        for ($i=0;$i<count($shift_change_times);$i++)
        {
        if ($i>0)
        echo ",";
        echo "{data: [";
        echo implode(',',${"data".$i});
        echo "],\n";
        echo "label:'".gettext("Shift")." ".($i+1)."',\n";
        echo "backgroundColor:'".$backgroundColor[$i]."'}\n";
        }?>]
                
        },
    options: {
      title: {
        display: true,
        text: '<?php echo gettext("Asset operating time")." ".get_asset_name_from_id($asset_id,$lang);?>'
      },
      scales: {
        xAxes: [{
            beginAtZero: true,
            ticks: {
            autoSkip: false
      }
        }],
        yAxes: [{
            beginAtZero: true,
            ticks: {
            autoSkip: false,
            max:100
      }
        }]
    }
    }
})
</script>
<?php

}?>
