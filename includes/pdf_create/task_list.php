<?php

require_once(VENDORS_PATH.'tcpdf/tcpdf.php');
ob_end_clean();
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetFont("freeserif", '', 10);
 
$pdf->AddPage();

 
// create some HTML content
$html = '<h2>';
$k="";
        $n="";

        foreach (get_whole_path("asset",$_GET['asset_id'],1) as $k){
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."-><wbr>";
        }
        
        $html.=substr($n,0,-7).'</h2>';
$html.='<h2>'.gettext('Maintenance schedule').'</h2>';
$SQL="SELECT workrequests.asset_id as asset_id,service_interval_date, service_interval_hours,service_interval_mileage,workrequest_short,workrequest FROM workrequests LEFT JOIN assets ON assets.asset_id=workrequests.asset_id WHERE main_asset_id='".(int) $_GET['asset_id']."' AND request_type='1' ORDER BY request_type, service_interval_hours,service_interval_date,service_interval_mileage,workrequests.asset_id";
$result=$dba->Select($SQL);

if (LM_DEBUG)
error_log($SQL,0);
$s_hours=0;
$s_date=0;
$s_asset_id=0;
foreach ($result as $row){
    
    if ($row['service_interval_hours']>0)
    {
    if ($s_asset_id!=$row['asset_id'] && $s_asset_id!=0)
            $html.="</ol>";
        if ($s_date!=$row['service_interval_hours'])
        $html.="<h3>".$row['service_interval_hours'].gettext(" hours")."</h3>";
        
        if ($s_asset_id!=$row['asset_id'])
        {
        $n="";
        foreach (get_whole_path("asset",$row['asset_id'],1) as $k)
        {
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."-><wbr>";
        }
        
        if ($n!="")
        $html.="<span>".substr($n,0,-7)."</span>";
         $s_asset_id=$row['asset_id'];
         $html.="<ol>";
         }
         
            $s_date=$row['service_interval_hours'];
        
        $html.="<li>".$row['workrequest_short']."</li>";
        if ($row['workrequest']!='')
        $html.="<br/>".$row['workrequest'];
        
    }
    else if ($row['service_interval_date']>0)
    {   
        if ($s_asset_id!=$row['asset_id'] && $s_asset_id!=0)
            $html.="</ol>";
        if ($s_date!=$row['service_interval_date'])
        $html.="<h3>".get_service_interval_date($row['service_interval_date'])."</h3>";
        
        if ($s_asset_id!=$row['asset_id'])
        {
        $n="";
        foreach (get_whole_path("asset",$row['asset_id'],1) as $k)
        {
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."-><wbr>";
        }
        
        if ($n!="")
        $html.="<span>".substr($n,0,-7)."</span>";
         $s_asset_id=$row['asset_id'];
         $html.="<ol>";
         }
         
            $s_date=$row['service_interval_date'];
        
        $html.="<li>".$row['workrequest_short']."</li>";
        if ($row['workrequest']!='')
        $html.="<br/>".$row['workrequest'];
        
    }
    else 
    if ($row['service_interval_mileage']>0)
    {
    if ($s_asset_id!=$row['asset_id'] && $s_asset_id!=0)
            $html.="</ol>";
        if ($s_date!=$row['service_interval_mileage'])
        $html.="<h3>".gettext("Every ").$row['service_interval_mileage']." km</h3>";
        
        if ($s_asset_id!=$row['asset_id'])
        {
        $n="";
        foreach (get_whole_path("asset",$row['asset_id'],1) as $k)
        {
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."-><wbr>";
        }
       
        if ($n!="")
        $html.="<span>".substr($n,0,-7)."</span>";
         $s_asset_id=$row['asset_id'];
         $html.="<ol>";
         }
         
            $s_date=$row['service_interval_mileage'];
        
        $html.="<li>".$row['workrequest_short']."</li>";
        if ($row['workrequest']!='')
        $html.="<br/>".$row['workrequest'];
        
    }


}


// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');
 
$pdf->lastPage();
 //ob_end_clean();
$pdf->Output('example.pdf', 'I');
