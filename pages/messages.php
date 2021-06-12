<?php

if (isset($_GET['checked']) && isset($_GET['message_id']) && (int) $_GET['message_id']>0){
$SQL="UPDATE received_messages SET user_id_who_checked=".$_SESSION['user_id'].",checking_time=NOW() WHERE message_id=".(int) $_GET['message_id'];
$dba->Query($SQL);

}
if (isset($_POST['new'])){

    $SQL="INSERT INTO messages (";
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.="message_".LANG1;
    
    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
    {
        if ($_SESSION['CAN_WRITE_LANG1'])
        $SQL.=",";
    $SQL.="message_".LANG2;
    }
    $SQL.=") VALUES (";
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.="'".$dba->escapeStr($_POST['message_'.LANG1])."'";
    
    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
       { 
        if ($_SESSION['CAN_WRITE_LANG1'])
        $SQL.=",";
        $SQL.="'".$dba->escapeStr($_POST['message_'.LANG2])."'";
        }
    $SQL.=")";
    if ($dba->Query($SQL))
    lm_info(gettext("The new message text has been saved."));
    else
    lm_info(gettext("Failed to save new message text."));
    }

    
else if (isset($_POST['modify'])){
$SQL="UPDATE messages SET ";
if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.="message_".LANG1."='".$dba->escapeStr($_POST['message_'.LANG1])."'";
if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
{
if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.=",";
$SQL.="message_".LANG2."='".$dba->escapeStr($_POST['message_'.LANG2])."'";
}
$SQL.=")";
if ($dba->Query($SQL))
    lm_info(gettext("The new message text has been modified."));
    else
    lm_info(gettext("Failed to modify new message text."));
}


else if (isset($_GET['new']) || isset($_GET['modify'])){
?>

<div class="card">
<div class="card-header">
<strong><?php 
if (isset($_GET['modify']) && isset($_GET['message_id']))
echo gettext("New message text");
else if (isset($_GET['new']))
echo gettext("Modify message text");
?></strong>
</div><?php //card header ?>
<div class="card-body card-block">
<form action="index.php" id="message_form" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php
if (isset($_GET['modify']) && isset($_GET['message_id']))
{
$SQL="SELECT asset_id,message FROM messages WHERE message_id=".(int) $_GET['message_id'];
$row=$dba->getRow($SQL);
echo "<input type=\"hidden\" name=\"modify\" id=\"modify\" value=\"1\">\n";
}else
echo "<input type=\"hidden\" name=\"new\" id=\"new\" value=\"1\">\n";
if ($_SESSION['CAN_WRITE_LANG1']){
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-3\"><label for=\"message_".LANG1."\" class=\" form-control-label\">".gettext("Message (").LANG1."):</label></div>\n";

    echo "<div class=\"col col-md-2\">\n";
     echo "<input type=\"text\" id=\"message_".LANG1."\" name=\"message_".LANG1."\" placeholder=\"".gettext("Message (").LANG1.")\" class=\"form-control\" required><small class=\"form-text text-muted\">".gettext("Message (").LANG1.")</small></div>\n";
    echo "</div>\n";
    }
    
  if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2']){
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-3\"><label for=\"message_".LANG2."\" class=\" form-control-label\">".gettext("Message:")."</label></div>\n";
        
    echo "<div class=\"col col-md-2\">\n";
    echo "<input type=\"text\" id=\"message_".LANG2."\" name=\"message_".LANG2."\" placeholder=\"".gettext("Message")."\" class=\"form-control\" required><small class=\"form-text text-muted\">".gettext("Message")."</small></div>\n";
    echo "</div>\n";
    }
    
    echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"messages\">\n";
    echo "<div class=\"card-footer\">\n";
    echo "<button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext(" Submit ")."</button>";
    echo "</form></form></div>\n";
  echo "<script>\n";
echo "$(\"#messsage_form\").validate({
  rules: {";
  if (LANG2_AS_SECOND_LANG && $_SESSION["CAN_WRITE_LANG2"])
{
  echo  "message_".LANG2.": {
        required: true,
        maxlength: ".$dba->get_max_fieldlength('messages','message_'.LANG2)."
    }
    ";}
    
    echo ",message_".LANG1.": {
        required: true,
        maxlength: ".$dba->get_max_fieldlength('messages','message_'.LANG1)."
    }
  }
})\n";
echo "</script>\n";  
?>

<div class="card-body">
<table id="bootstrap-data-table" class="table table-striped table-bordered">
<thead>
<tr>

<?php 

echo "<th>".gettext("Message id")."</th>";


echo "<th>".gettext("Message (").$lang.")</th>";

echo "</tr>\n";
?>
</thead>
<tbody>
<?php

$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;
$from=1;
$message_type=lm_isset_int('message_type');
if ($message_type>0)
$_SESSION['message_type']=$message_type;
$SQL="SELECT * FROM messages";
if (isset($_SESSION['message_type']) && $_SESSION['message_type']>0)
$SQL.=" WHERE message_type=".$_SESSION['message_type'];
$SQL.=" ORDER BY message_".$lang;
$result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
$from=($pagenumber-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log("page:".$pagenumber." ".$SQL,0);




if (!empty($result)){
foreach ($result as $row)
{
$from++;
echo "<tr><td>".$row['message_id'];

echo "<td>".$row['message_'.$lang]."</td>\n";
echo "</tr>\n";

}}
echo "</tbody></table></div>";
include(INCLUDES_PATH."pagination.php");

    
    
   }
else {
$message_types=array(gettext("all message"),gettext("error"),gettext("warning"),gettext("info"),gettext("online"));      
$message_type=lm_isset_int('message_type');
if ($message_type>=0)
$_SESSION['message_type']=$message_type;

if(is_user_working($_SESSION['user_id']))
        echo "user working";
        else
        echo "user not working";


if (isset($_GET['ip'])){
$url = 'http://'.$dba->escapeStr($_GET['ip']).'/param?ping=1';
 
//Once again, we use file_get_contents to GET the URL in question.
$contents = file_get_contents($url);
 
//If $contents is not a boolean FALSE value.
if($contents !== false){
    //Print out the contents.
    echo $contents;
}

}        
        
        ?>


<div class="card">
<table id="message-data-table" class="table table-striped table-bordered">
<thead>
<tr>
<th></th><th>

 <button class="btn btn-secondary dropdown-toggle" type="button" id="msg_type" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"<?php 
 
           
 
       if (isset($_SESSION['message_type']) && $_SESSION['message_type']>0){
                            echo " STYLE=\"background-color:orange;\"";
                            }
                            echo ">";
                            echo  $message_types[$_SESSION['message_type']];
                            ?></button>
                            <div class="dropdown-menu" aria-labelledby="msg_type">
<?php 

foreach ($message_types as $key => $value){
echo "<a class=\"dropdown-item media bg-flat-color-10\"";
if (isset($_SESSION['message_type']) && $_SESSION['message_type']==($key))
echo " style=\"background-color:orange;\"";
echo " href=\"index.php?page=messages&message_type=".($key)."\">\n";
echo "<i class=\"fa fa-warning\"></i> \n";

echo $value."</a>";
                            
}

echo "</div>\n";


echo gettext("Time")."</th><th>".gettext("Message type")."</th><th>".gettext("Sender")."</th><th>".gettext("Message")."</th></tr>";
?>
</thead>
<tbody>
<?php
if (isset($_SESSION['SEE_MESSAGE'])){

$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;
$from=1;
$SQL="SELECT users_assets FROM users WHERE user_id=".$_SESSION['user_id'];
$row=$dba->getRow($SQL);
$users_assets=json_decode($row['users_assets'], true);

$SQL="SELECT * FROM received_messages LEFT JOIN iot_sensors ON received_messages.sensor_id=iot_sensors.sensor_id WHERE 1=1";
$SQL.=" AND main_asset_id IN (";
$need_a_comma=false;
if (!empty($users_assets)){
foreach ($users_assets as $key=>$value){
if ($need_a_comma)
$SQL.=",";
$need_a_comma=true;
$SQL.=$value;

}}
$SQL.=")";

if (isset($_SESSION['message_type']) && $_SESSION['message_type']>0)
$SQL.=" AND message_type=".$_SESSION['message_type'];
$SQL.=" AND user_id_who_checked=0";

$SQL.=" ORDER BY message_time DESC";
$result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
$from=($pagenumber-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log("page:".$pagenumber." ".$SQL,0);


//print_r($users_assets);

if (!empty($result)){
foreach ($result as $row)
{
if (in_array(get_whole_path_ids('asset',$row['asset_id'],1)[0],$users_assets)){

$from++;
echo "<tr>";
echo "<td";
if ($row['message_type']==1)
echo " class=\"bg-flat-color-4\"";
else if ($row['message_type']==2)
echo " class=\"bg-flat-color-3\"";
else if ($row['message_type']==4)
echo " class=\"bg-flat-color-2\"";

echo ">";

if ($row['user_id_who_checked']==0 && (isset($_SESSION['CHECK_MESSAGE'])|| $_SESSION['user_level']<3))
echo "<a href='index.php?page=messages&message_id=".$row['message_id']."&checked=1'> <i class=\"fa fa-check\"></i> </a>";
else if ($row['user_id_who_checked']==0)
echo " <i class=\"fa fa-check\"></i> ";

echo $from;
echo "</td><td>".date("Y.m.d H:i", strtotime($row['message_time']))."</td>\n";

echo "<td>".$message_types[$row['message_type']]."</td>\n";
if ($row['asset_id']>0)
        {
        echo "<td>";
        $n="";
                foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
                if ($n=="") // the first element is the main asset_id -> ignore it
                $n=" ";
                else
                $n.=$k."-><wbr>";}

                if ($n!="")
                echo substr($n,0,-7);
        echo "</td>\n";
        }
else if ($row['user_id']>0)
echo "<td>".get_user_full_name_from_id($row['user_id'])."</td>\n";

$SQL="SELECT message_".$lang." FROM messages WHERE message_id=".(int) $row["received_message"];
    $row1=$dba->getRow($SQL);
echo "<td>".$row1['message_'.$lang];

if (isset($row['sensor_value']) && $row['sensor_value']!=0 && $row['unit_id']>0){
$unit=get_unit_from_id($row['unit_id']);
echo " ".round($row['sensor_value'],2)." ".$unit.' ';
echo $row['min_sensor_value'].$unit.' / '.$row['max_sensor_value'].$unit;
}
echo "</td>\n";
echo "<td><input type='hidden' name='ping' value='1'>";
echo "<button type=\"button\" class=\"btn btn-primary btn-sm\" onClick=\"location.href='index.php?page=messages&ip=".$row['ip']."'\">\n";
    echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext(" Request data ")."</button>";
echo "</td></tr>\n";

}


}
}
echo "</tbody></table></div>";
include(INCLUDES_PATH."pagination.php");
}
else
echo gettext("You have no permission!");


}
?>

