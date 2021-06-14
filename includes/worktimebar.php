<?php

     $workdate= $dba->escapeStr($_GET['param2']); 

 
      $SQL="SELECT asset_id,workorder_work_start_time, workorder_work_end_time,ROUND(TIME_TO_SEC(workorder_worktime)/60) as minutes FROM workorder_works WHERE workorder_works.deleted<>1 AND workorder_partner_id=0 AND ";
      $SQL.="DATE(workorder_work_start_time)='".$workdate."' AND workorder_user_id=".(int) $_GET['param3']." ORDER BY workorder_work_start_time";
      $res=$dba->Select($SQL);
 if (LM_DEBUG)
            error_log($SQL,0);       

$workdate_timestamp= strtotime($workdate); 


echo "<script src=\"".VENDORS_LOC."chart.js/dist/Chart.min.js\"></script>\n";
?>
<div style="width:50em;height:60px;padding-left:2em;">
  <canvas id="worktimebar"></canvas>
</div>
<script>

const ctx=document.getElementById("worktimebar").getContext('2d');;
var chart = getChart();
new Chart(ctx, chart);

function getChart() {
  return {
    "type": "horizontalBar",
    "data": {
      "datasets": [
            
      
          <?php
          $i=0;
          
           $SQL="SELECT TIME_FORMAT(".date("l", $workdate_timestamp)."_start,'%H:%i:%s') as start,TIME_FORMAT(".date("l", $workdate_timestamp)."_end,'%H:%i:%s') as end FROM users WHERE user_id=".(int) $_GET['param3'];
           //$start_time="06:00:00";
           $row=$dba->getRow($SQL);
                 
          $start_time=$row['start'];
           $shift_start_datetime=DateTime::createFromFormat($lang_date_format.' H:i:s', $workdate.' '.$start_time);
          //$end_time="14:00:00:00000";
          $end_time=$row['end'];
          if (LM_DEBUG)
            error_log($SQL." ".$start_time." ".$end_time,0); 
          $shift_end_datetime=DateTime::createFromFormat($lang_date_format.' H:i:s', $workdate.' '.$end_time);

          $last_end_datetime="";
          if (!empty($res)){
          foreach($res as $r){
             $start_datetime=new DateTime($r['workorder_work_start_time']);
             $end_datetime=new DateTime($r['workorder_work_end_time']);
            
                           
                
              if ($i++>0)
                  echo ",";
              
          
            
            if ($i==1 && $start_datetime>$shift_start_datetime)
             {
            
             $s_time=DateTime::createFromFormat('Y-m-d H:i:s', $workdate.' '.$start_time);
             $e_time=new DateTime($r['workorder_work_start_time']);
             echo "{\n\"label\": \"";
             
             echo gettext("Empty").": ";
              
              
             echo $start_time."-".date("H:i", strtotime($r['workorder_work_start_time']))."\",\n";
             echo "\"data\":[".(($e_time->getTimestamp() - $s_time->getTimestamp())/60)."],\n";
             echo "\"fill\":false,\n";
             echo "\"backgroundColor\": \"red\",\n";
             echo "\"borderColor\": \"rgb(0,100,0)\",\n";
             echo "\"hoverBorderColor\": \"rgb(255,255,255)\",\n";
             echo "\"borderWidth\": 1\n},"; 
               }
            
          if ($last_end_datetime!="" && $start_datetime!=$last_end_datetime)  //there is a hole beetween the activities
          {
             
             
             echo "{\n\"label\": \"";
           
        echo gettext("Empty").": ";
              
              
              echo $last_end_datetime->format('H:i')."-".date("H:i", strtotime($r['workorder_work_start_time']))."\",\n";
              echo "\"data\":[".(round(($start_datetime->getTimestamp() - $last_end_datetime->getTimestamp())/60))."],\n";
              echo "\"fill\":false,\n";
              echo "\"backgroundColor\": \"red\",\n";
              echo "\"borderColor\": \"rgb(0,100,0)\",\n";
              echo "\"hoverBorderColor\": \"rgb(255,255,255)\",\n";
              echo "\"borderWidth\": 1\n},";   
              
        }
             
             
             echo "{\n\"label\": \"";
             $k="";
        $n="";
       
        foreach ($asset_path=get_whole_path("asset",$asset_id=$r['asset_id'],1) as $k){
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."->";
        }
        
        echo substr($n,0,-7).": ";
             
             echo date("H:i", strtotime($r['workorder_work_start_time']))."-".date("H:i", strtotime($r['workorder_work_end_time']))."\",\n";
              echo "\"data\":[".$r["minutes"]."],\n";
              echo "\"fill\":false,\n";
              echo "\"backgroundColor\": \"rgba(0, 255, 127, 0.2)\",\n";
              echo "\"borderColor\": \"rgb(0,100,0)\",\n";
              echo "\"hoverBorderColor\": \"rgb(255,255,255)\",\n";
              echo "\"borderWidth\": 1\n}";
              
          $last_end_datetime=new DateTime($r['workorder_work_end_time']);
            
        } //end foreach
   

          if ($last_end_datetime!="" && $last_end_datetime<$shift_end_datetime)
              {
            echo ",{\n\"label\": \"";
            echo gettext("Empty").": ";
          
            echo date("H:i", strtotime($r['workorder_work_end_time']))."-".$end_time."\",\n";
              
              
            echo "\"data\":[".(round(($shift_end_datetime->getTimestamp() - $last_end_datetime->getTimestamp())/60))."],\n";
            echo "\"fill\":false,\n";
            echo "\"backgroundColor\": \"red\",\n";
            echo "\"borderColor\": \"rgb(0,100,0)\",\n";
            echo "\"hoverBorderColor\": \"rgb(255,255,255)\",\n";
            echo "\"borderWidth\": 1\n}"; 
               }
    }else //there is no work at all
    {
        echo "{\n\"label\": \"".gettext("Empty")." ".$start_time."-".$end_time."\",\n";
              echo "\"data\":[480],\n";
              echo "\"fill\":false,\n";
              echo "\"backgroundColor\": \"rgba(255, 0, 0, 0.2)\",\n";
              echo "\"borderColor\": \"rgb(0,100,0)\",\n";
              echo "\"hoverBorderColor\": \"rgb(255,255,255)\",\n";
              echo "\"borderWidth\": 1\n}";
        
    }
          ?>
    
      ]
    },
    "options": {

      "maintainAspectRatio": false,
      "responsive": true,
      "legend": {
        "display": false
      },
      "tooltips": {
        "enabled": true,
        "mode": "nearest"
      },
      "scales": {
        "xAxes": [{
          "display": false,
          "stacked": true
        }],
        "yAxes": [{
          "display": false,
          "stacked": true,
          "barPercentage": 0.4
        }]
      }
    }
  };
}
</script>

