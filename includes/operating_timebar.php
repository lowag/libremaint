<?php
function operating_timebar($asset_id,$date):void{
    global $dba,$lang_date_format;
    if (is_date_mysql_format($date))
     $op_date= $date; 
    else
     $op_date=date('Y-m-d');   
 
      $SQL="SELECT start_time, end_time,round(TIMESTAMPDIFF(MINUTE,start_time,end_time)) as minutes FROM operatings WHERE asset_id=".$asset_id." AND " ;
      $SQL.="DATE(start_time)='".$op_date."' ORDER BY start_time";
      
      $res=$dba->Select($SQL);
 if (LM_DEBUG)
            error_log($SQL,0);       

$op_timestamp= strtotime($op_date); 
if ($dba->affectedRows()>0)
    
{
echo "<script src=\"".VENDORS_LOC."chart.js/dist/Chart.min.js\"></script>\n";
?>
<div style="width:50em;height:60px;padding-left:2em;">
  <canvas id="op_timebar"></canvas>
</div>
<script>

const ctx=document.getElementById("op_timebar").getContext('2d');;
var chart = getChart();
new Chart(ctx, chart);

function getChart() {
  return {
    "type": "horizontalBar",
    "data": {
      "datasets": [
            
      
          <?php
          $i=0;
          
           $start_time="06:00:00";
           $start_datetime=DateTime::createFromFormat('Y-m-d H:i:s', $op_date.' '.$start_time);
           $start_datetime->modify('-1 day')->format('Y-m-d H:i:s');
          $end_time="06:00:00";
          
          if (LM_DEBUG)
            error_log($SQL." ".$start_time." ".$end_time,0); 
          $end_datetime=DateTime::createFromFormat('Y-m-d H:i:s', $op_date.' '.$end_time);
          

          $last_end_datetime="";
          if (!empty($res)){
          foreach($res as $r){
             $start_datetime=new DateTime($r['start_time']);
             $end_datetime=new DateTime($r['end_time']);
            
                           
                
              if ($i++>0)
                  echo ",";
              
          
            
            if ($i==1 && $start_datetime>$start_datetime)
             {
            
             $s_time=DateTime::createFromFormat('Y-m-d H:i:s', $op_date.' '.$start_time);
             $e_time=new DateTime($r['start_time']);
             echo "{\n\"label\": \"";
             
             echo gettext("Empty").": ";
              
              
             echo $start_time."-".date("H:i", strtotime($r['start_time']))."\",\n";
             echo "\"data\":[".(round(($e_time->getTimestamp() - $s_time->getTimestamp())/60))."],\n";
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
              
              
              echo $last_end_datetime->format('H:i')."-".date("H:i", strtotime($r['start_time']))."\",\n";
              echo "\"data\":[".round(($start_datetime->getTimestamp() - $last_end_datetime->getTimestamp())/60)."],\n";
              echo "\"fill\":false,\n";
              echo "\"backgroundColor\": \"red\",\n";
              echo "\"borderColor\": \"rgb(0,100,0)\",\n";
              echo "\"hoverBorderColor\": \"rgb(255,255,255)\",\n";
              echo "\"borderWidth\": 1\n},";   
              
        }
             
             
             echo "{\n\"label\": \"";
             
             echo date("H:i", strtotime($r['start_time']))."-".date("H:i", strtotime($r['end_time']))."\",\n";
              echo "\"data\":[".$r["minutes"]."],\n";
              echo "\"fill\":false,\n";
              echo "\"backgroundColor\": \"rgba(0, 255, 127, 0.2)\",\n";
              echo "\"borderColor\": \"rgb(0,100,0)\",\n";
              echo "\"hoverBorderColor\": \"rgb(255,255,255)\",\n";
              echo "\"borderWidth\": 1\n}";
              
          $last_end_datetime=new DateTime($r['end_time']);
            
        } //end foreach
   

          if ($last_end_datetime!="" && $last_end_datetime<$end_datetime)
              {
            echo ",{\n\"label\": \"";
            echo gettext("Idle").": ";
          
            echo date("H:i", strtotime($r['end_time']))."-".$end_time."\",\n";
              
              
            echo "\"data\":[".(($end_datetime->getTimestamp() - $last_end_datetime->getTimestamp())/60)."],\n";
            echo "\"fill\":false,\n";
            echo "\"backgroundColor\": \"red\",\n";
            echo "\"borderColor\": \"rgb(0,100,0)\",\n";
            echo "\"hoverBorderColor\": \"rgb(255,255,255)\",\n";
            echo "\"borderWidth\": 1\n}"; 
               }
    }else //there is no work at all
    {
        echo "{\n\"label\": \"".gettext("Idle")." ".$start_time."-".$end_time."\",\n";
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
<?php
}
}
?>
