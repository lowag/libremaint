<?php 
if (isset($_GET['workorder_id'])){
$SQL="SELECT workorder_status FROM workorders WHERE workorder_id=".(int) $_GET['workorder_id'];
$workorder_row=$dba->getRow($SQL);

}
?>
<script>
function check_time_period(){
var start_time=document.getElementById('workorder_work_start_date').value+' '+document.getElementById('workorder_work_start_time').value;
var end_time=document.getElementById('workorder_work_end_date').value+' '+document.getElementById('workorder_work_end_time').value;
<?php
if (isset($_GET['workorder_id']) && $workorder_row['workorder_status']<5){
echo "
if (workorder_partner_id.checked)
ajax_call('is_it_valid_time_period_for_partners',start_time,end_time,document.getElementById('workorder_user_id').value,'','".URL."index.php','ajax_button');
else
ajax_call('is_it_valid_time_period',start_time,end_time,document.getElementById('workorder_user_id').value,'";
if (isset($_GET['modify']) && isset($_GET['workorder_id']))
echo (int) $_GET['workorder_id'];
else
echo 0;
echo "','".URL."index.php','ajax_button');"; 
}
?>

}

</script>
<div id='for_ajaxcall'>
</div>
<?php
if (isset($_GET['delete']) && isset($_SESSION['DELETE_WORK']) && is_it_valid_submit()){
$SQL="SELECT workorders.workorder_id,workorder_user_id,workrequest_id FROM workorders LEFT JOIN workorder_works ON workorders.workorder_id=workorder_works.workorder_id WHERE workorder_work_id=".(int) $_GET['workorder_work_id'];
$row=$dba->getRow($SQL);
if (LM_DEBUG)
        error_log($SQL,0);
        
if ($row['workorder_user_id']==$_SESSION['user_id'] || $_SESSION['user_level']<3)
    {
    $SQL="UPDATE workorder_works SET deleted=1 WHERE workorder_work_id=".(int) $_GET['workorder_work_id'];
    $dba->Query($SQL);
    
    if (LM_DEBUG)
        error_log($SQL,0);
    
    check_workorder_to_close($row['workorder_id']);
    
    if ($row['workrequest_id']>0){
    $SQL="SELECT workorder_user_id,workorder_work_end_time FROM workorder_works WHERE workrequest_id=".$row['workrequest_id']." AND workorder_works.deleted<>1 ORDER BY workorder_work_end_time DESC LIMIT 0,1";
    $row1=$dba->getRow($SQL);
    $SQL="UPDATE workrequests SET last_ready_date='".$row1['workorder_work_end_time']."', last_ready_user_id=".$row1['workorder_user_id']." WHERE workrequest_id=".$row['workrequest_id'];
            $dba->Query($SQL);
    if (LM_DEBUG)
        error_log($SQL,0);
    }
    }
}

if (isset($_POST['page']) && isset($_POST['modify']) && isset($_POST["workorder_work_".$lang]) && isset($_SESSION['MODIFY_WORK']) && is_it_valid_submit()){// add/modify work form 

{
$SQL="SELECT * FROM workorders WHERE workorder_id=".(int) $_POST['workorder_id'];
$workorder_row=$dba->getRow($SQL);


$SQL="UPDATE workorder_works SET ";

$SQL.="workorder_id='".$workorder_row['workorder_id']."'";
$SQL.=",workorder_work_start_time='".$_POST['workorder_work_start_date']." ".$_POST['workorder_work_start_time']."'";
$SQL.=",workorder_work_end_time='".$_POST['workorder_work_end_date']." ".$_POST['workorder_work_end_time']."'";
$interval=date_diff(new DateTime($_POST['workorder_work_start_date']." ".$_POST['workorder_work_start_time']),new DateTime($_POST['workorder_work_end_date']." ".$_POST['workorder_work_end_time']));

$SQL.=",workorder_worktime='".$interval->format('%h:%i:%s')."'";

if ($_SESSION['CAN_WRITE_LANG1'])
$SQL.=",workorder_work_".LANG1."='".$dba->escapeStr($_POST['workorder_work_'.LANG1])."'";

if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
$SQL.=",workorder_work_".LANG2."='".$dba->escapeStr($_POST['workorder_work_'.LANG2])."'";

$SQL.=",main_asset_id='".$workorder_row['main_asset_id']."'";
$SQL.=",asset_id='".$workorder_row['asset_id']."'";
if ($_SESSION['user_level']>2)
$SQL.=",workorder_user_id=".$_SESSION['user_id'];
else
$SQL.=",workorder_user_id='".(int) $_POST['workorder_user_id']."'";
$SQL.=",workorder_status='".(int) $_POST['workorder_status']."'";
$SQL.=",unplanned_shutdown=".(int) $_POST['unplanned_shutdown'];
$SQL.=",after_work_machine_can_run=".(int) $_POST['after_work_machine_can_run'];

if (isset($_POST['workorder_partner_id']))
$SQL.=",workorder_partner_id='".$workorder_row['workorder_partner_id']."'";
else
$SQL.=",workorder_partner_id='0'";

$SQL.=" WHERE workorder_work_id='". (int) $_POST['workorder_work_id']."'";
 if (LM_DEBUG)
        error_log($SQL,0);
if ($dba->Query($SQL))
        {
        $SQL="UPDATE workorders SET workorder_status=".(int) $_POST['workorder_status']." WHERE workorder_id=".$workorder_row['workorder_id'];
        $dba->Query($SQL);
        lm_info(gettext("The activity has been modified."));
      //      check_workorder_to_close($workorder_row['workorder_id'],$_POST['workorder_work_end_date']." ".$_POST['workorder_work_end_time']);
                  check_workorder_to_close($workorder_row['workorder_id']);

    
        }
        else
        lm_error(gettext("Failed to modify activity ").$SQL." ".$dba->err_msg);

        }
}





else if((isset($_GET['new']) && isset($_SESSION['ADD_WORK'])) || (isset($_GET['modify']) && isset($_SESSION['MODIFY_WORK'])) ){


if (isset($_GET["workorder_id"]) && $_GET["workorder_id"]>0)
    $SQL="SELECT workorder_id,asset_id,workrequest_id,work_details_required FROM workorders WHERE workorder_id=".(int) $_GET["workorder_id"];
$workorder_row=$dba->getRow($SQL);
 if (LM_DEBUG)
        error_log($SQL,0); 
echo "<div class=\"card\">";


         echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\" id=\"work_form\" name=\"work_form\">";

    echo "<div class=\"card-header\">\n";
        if (isset($_GET['new']))        
        echo "<strong>".gettext("Add activity")."</strong>\n";
        
        $SQL="SELECT sum(TIME_TO_SEC(workorder_worktime))/3600 as sum_work FROM workorder_works WHERE workorder_works.deleted<>1 AND workorder_id=".(int) $_GET["workorder_id"]." GROUP BY workorder_id";
        $row=$dba->getRow($SQL);
        if (isset($row['sum_work']) && $row['sum_work']>0)
        echo "<p><strong>".gettext("Working hours by this time:")." ".round($row['sum_work'],1)." ".gettext("hours")."</strong></p>";
        else
        echo "<p>".gettext("There was no work by this time.")."</p>";
        if (isset($_GET['modify']) && isset($_GET["workorder_work_id"]) && $_GET["workorder_work_id"]>0){        
            echo "<strong>".gettext("Modify activity")."</strong>\n";
            
            $SQL="SELECT * FROM workorder_works WHERE workorder_work_id='".(int) $_GET['workorder_work_id']."'";
            $row_mod=$dba->getRow($SQL);
            if (LM_DEBUG)
            error_log($SQL,0);
        }
       
        echo "<p>".gettext("Task(s):")."</p><ul>";
      
        echo "<li>";
        $k="";
        $n="";
       
        foreach ($asset_path=get_whole_path("asset",$asset_id=$workorder_row['asset_id'],1) as $k){
            if ($n=="") // the first element is the main asset_id -> ignore it
            $n=" ";
            else
            $n.=$k."-><wbr>";
        }
        
        echo substr($n,0,-7).": ";
        echo get_task_from_id("workorder",$workorder_row['workorder_id']);
        if ($workorder_row['workrequest_id']>0) //there might be workorders without workrequest
            {
            $SQL1="SELECT repetitive,counter_id FROM workrequests WHERE workrequest_id='".$workorder_row['workrequest_id']."'";
            $row1=$dba->getRow($SQL1);
            if (LM_DEBUG)
            error_log($SQL1,0); 
                if ($row1['repetitive']>1 && $row1['counter_id']>0) //we must record the counter value when the work has finished
                {
                $SQL2="SELECT counters.counter_id,counter_value,asset_id,counter_unit FROM counters LEFT JOIN counter_values ON counters.counter_id=counter_values.counter_id WHERE counters.counter_id='".$row1['counter_id']."' ORDER BY counter_value DESC LIMIT 1";
                if (LM_DEBUG)
                    error_log($SQL2,0);

                $row2=$dba->getRow($SQL2);
                    if (isset(${"counter_".$row2['counter_id']}))
                    echo "(".$row2['counter_value'].") <INPUT TYPE='text' disabled size='6'>";
                    else
                    {
                    echo "<br/>".gettext("Counter value")."(".get_asset_name_from_id($row2['asset_id'],$lang)."): <INPUT TYPE='text' onChange=\"if (this.value<".$row2['counter_value'].") {alert('".gettext("The value must be greater or equal to ").$row2['counter_value']."');\n document.getElementById('submit_button').disabled=true;\n}else document.getElementById('submit_button').disabled=false\" VALUE='' id='counter_".$row2['counter_id']."' name='counter_".$row2['counter_id']."' size='6'>";
                    echo " ".get_unit_from_id($row2['counter_unit']);
                    ${"counter_".$row2['counter_id']}=$row2['counter_value'];
                    if (!isset($counter_array_for_javascript))
                    $counter_array_for_javascript=$row2['counter_value'];
                    else
                    $counter_array_for_javascript.=",".$row2['counter_value'];
                    }
                }
            }
        echo "</li>\n";    
       
        echo "</ul>";
    echo "</div>\n";
echo "<div class=\"card-body card-block\">";
   if ($_SESSION['user_level']<3) {// the boss can administrate other user's work        
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"workorder_user_id\" class=\" form-control-label\">".gettext("Employee:")."</label></div>";

    echo "<div class=\"col-12 col-md-3\">";
    echo "<select name=\"workorder_user_id\" id=\"workorder_user_id\" class=\"form-control\" required onChange=\"
    ajax_call('show_worktimebar',document.getElementById('workorder_work_start_date').value,this.value,'','','".URL."index.php','for_ajaxcall');\n
    check_time_period();\n
    \">\n";
    $SQL="SELECT user_id,firstname,surname FROM users WHERE active=1 AND user_level<4";
    $SQL.=" ORDER BY surname";
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
    if (FIRSTNAME_IS_FIRST)
    {
    echo "<option value=\"".$row["user_id"]."\"";
    if (isset($_GET['modify']) && $row_mod['workorder_user_id']==$row['user_id'])
    echo " selected";
    echo ">".$row["firstname"]." ".$row["surname"]."</option>\n";
    }
    else{
    echo "<option value=\"".$row["user_id"]."\"";
    if (isset($_GET['modify']) && $row_mod['workorder_user_id']==$row['user_id'])
    echo " selected";
    echo ">".$row["surname"]." ".$row["firstname"]."</option>\n";
    }
    }
    echo "</select></div></div>";
  }else
  echo "<input type='hidden' name='workorder_user_id' id='workorder_user_id' value='".$_SESSION['user_id']."'>\n";

   if (isset($_GET["workorder_id"]) && (int) $_GET["workorder_id"]>0){
            $SQL="SELECT workorder_partner_id FROM workorders WHERE workorder_id='".(int) $_GET["workorder_id"]."'";
            $row=$dba->getRow($SQL);
            if ($row['workorder_partner_id']>0 && $_SESSION['user_level']<3){
    echo "<div class=\"row form-group\">\n";
    echo "<div class=\"col col-md-2\"><label for=\"workorder_partner_id\" class=\"form-control-label\">".gettext("Work with partner:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">\n";
        
            echo "<INPUT TYPE='checkbox' name='workorder_partner_id' id='workorder_partner_id' value='".$row['workorder_partner_id']."'";
            if (isset($_GET['modify']) && $row_mod['workorder_partner_id']>0)
            echo " checked='true'";
            echo ">";
            echo "<span> ".get_partner_name_from_id($row['workorder_partner_id'])."</span>\n</div></div>\n";
            }else
            echo "<input type='hidden' name='workorder_partner_id' id='workorder_partner_id' value='0'>\n";
            
        }
        echo "<div class=\"row form-group\">\n";
        
        echo "<div class=\"col col-md-2\"><label for=\"workorder_work_start_date\" class=\"form-control-label\">".gettext("Start time:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">";
        
        echo "<input type=\"date\" id=\"workorder_work_start_date\" name=\"workorder_work_start_date\" onChange=\"
    
ajax_call('show_worktimebar',document.getElementById('workorder_work_start_date').value,document.getElementById('workorder_user_id').value,'','','".URL."index.php','for_ajaxcall');\n

      var c=0; if (c==0){\n
            c=1;\n
            document.getElementById('workorder_work_end_date').value=this.value;\n
            
        };\ncheck_time_period();\n
        \" value=\"";
        
        if (isset($_GET['new'])){
        
                
        echo date($lang_date_format_for_input);
        
        }
        else if (isset($_GET['modify']))
        echo date($lang_date_format_for_input, strtotime($row_mod['workorder_work_start_time']));
        echo "\">\n";
        
        echo "<input type=\"time\" onChange=\"check_time_period();\" id=\"workorder_work_start_time\" name=\"workorder_work_start_time\" value=\"";
        
        if (isset($_GET['modify']))
        echo date("H:i", strtotime($row_mod['workorder_work_start_time']));
        else if (isset($_GET['new'])){
          $SQL="SELECT workorder_work_end_time FROM workorder_works WHERE workorder_user_id=".$_SESSION['user_id']." ORDER BY workorder_work_end_time DESC LIMIT 0,1";
        
        $row=$dba->getRow($SQL);
        if (!empty($row))
        echo date("H:i", strtotime($row['workorder_work_end_time']));
       
        
        }else
        echo date("H:i");
        echo "\">";
        echo "</div>\n";
        //echo "</div>\n";

        
        //echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"workorder_work_end_date\" class=\"form-control-label\">".gettext("End time:")."</label></div>\n";
        echo "<div class=\"col-12 col-md-3\">\n";
        echo "<input type=\"date\" onChange=\"check_time_period();\" id=\"workorder_work_end_date\" name=\"workorder_work_end_date\" value=\"";
        
        if (isset($_GET['new']))
        echo date($lang_date_format_for_input);
        else if (isset($_GET['modify']))
        echo date($lang_date_format_for_input, strtotime($row_mod['workorder_work_end_time']));
        echo "\">";
        
        echo "<input type=\"time\" onChange=\"check_time_period();\" id=\"workorder_work_end_time\" name=\"workorder_work_end_time\" value=\"";
         if (isset($_GET['new']))
        echo date("H:i");
        else if (isset($_GET['modify']))
        echo date("H:i", strtotime($row_mod['workorder_work_end_time']));
        echo "\">";
        echo "</div>\n</div>\n";
        
    echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\">\n";
            echo "<label for=\"workorder_status\" class=\" form-control-label\">".gettext("Unplanned shutdown").":</label>";
        echo "</div>\n";

    echo "<div class=\"col col-md-3\">\n";
        echo "<select name=\"unplanned_shutdown\" id=\"unplanned_shutdown\" class=\"form-control\" >";
    echo "<option value='0'";
    if (isset($_GET['modify']) && $row_mod['unplanned_shutdown']==0)
    echo " selected";
    echo ">".gettext("No")."\n";
    echo "<option value='1'";
    if (isset($_GET['modify']) && $row_mod['unplanned_shutdown']==1)
    echo " selected";
    echo ">".gettext("Yes")."\n";
    echo "</select>\n";    
    echo "</div>\n</div>";
    
    echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\">\n";
            echo "<label for=\"workorder_status\" class=\" form-control-label\">".gettext("After work machine can run").":</label>";
        echo "</div>\n";

    echo "<div class=\"col col-md-3\">\n";
        echo "<select name=\"after_work_machine_can_run\" id=\"after_work_machine_can_run\" class=\"form-control\" >";
    echo "<option value='1'";
    if (isset($_GET['modify']) && $row_mod['after_work_machine_can_run']==1)
    echo " selected";
    echo ">".gettext("Yes")."\n";
    echo "<option value='0'";
    if (isset($_GET['modify']) && $row_mod['after_work_machine_can_run']==0)
    echo " selected";
    echo ">".gettext("No")."\n";
    echo "</select>\n";    
    echo "</div>\n</div>";
    
    echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\">\n";
            echo "<label for=\"workorder_status\" class=\" form-control-label\">".gettext("Status:")."</label>";
        echo "</div>\n";

    echo "<div class=\"col col-md-3\">\n";
        echo "<select name=\"workorder_status\" id=\"workorder_status\" class=\"form-control\" ";
        echo "onChange=\"var counter_array=[".$counter_array_for_javascript."];var i=0;var n=0;\n";
        echo "for (const v of counter_array) {\n";
        echo "if (document.getElementById('counter_'+v).value>0)\n";
        echo "n++;\n";
        echo "else{i++;\n";
        echo "document.getElementById('submit_button').disabled=true;}\n";
        echo "};\n";
        echo "if (i>0) alert()";
        echo "\">\n";
     foreach($workorder_statuses as $id => $status)
     { //$workorder_status from config/lm-settings.php
        if ($id<5)
        { //the 5th is the "deleted"
        echo "<option value=\"".++$id."\"";
        if (isset($_GET['modify']) && $row_mod['workorder_status']==$id)
        echo " selected";
        echo ">".$status."</option>\n";
        }
     }
            echo "</select>\n";
            echo "</div>\n";
        
    echo "</div>\n"; //row form-group
    
    if ($_SESSION['CAN_WRITE_LANG1']){
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"workorder_work_".LANG1."\" class=\" form-control-label\">".gettext("Activity:")." </label></div>";
    echo "<div class=\"col col-md-7\">\n";
    echo "<div id='worktext_lenght'></div>";
    echo " <textarea name=\"workorder_work_".LANG1."\" id=\"workorder_work_".LANG1."\" rows=\"4\""; 
    if ($workorder_row['work_details_required']==1)
    echo " placeholder=\"".gettext("work details required")."\" required";
    echo " class=\"form-control\" onKeyup=\"document.getElementById('worktext_lenght').innerHTML='".gettext('Characters left: ')."'+(".$dba->get_max_fieldlength('workorder_works','workorder_work_'.LANG1)."-this.value.length)\">";
         if (isset($_GET['modify']))
    echo $row_mod['workorder_work_'.LANG1];

    echo "</textarea>\n"; 
    echo "</div></div>\n";
    }
    
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2'])){
    
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"workorder_work_".LANG2."\" class=\" form-control-label\">".gettext("Activity (").LANG2."): </label></div>";
    echo "<div class=\"col col-md-7\">\n";
    echo "<div id='worktext_".LANG2."_lenght'></div>";
    
    echo " <textarea name=\"workorder_work_".LANG2."\" id=\"workorder_work_".LANG2."\" rows=\"4\""; 
    if ($workorder_row['work_details_required']==1)
    echo " placeholder=\"".gettext("work details required")."\" required";
    echo " class=\"form-control\" onKeyup=\"document.getElementById('worktext_".LANG2."_lenght').innerHTML='".gettext('Characters left: ')."'+(".$dba->get_max_fieldlength('workorder_works','workorder_work_'.LANG2)."-this.value.length)\">";
         if (isset($_GET['modify']))
    echo $row_mod['workorder_work_'.LANG2];

    echo "</textarea>\n"; 
    echo "</div></div>\n";
    
    
    }
    
    echo "<input type=\"hidden\" name=\"main_asset_id\" id=\"main_asset_id\" value=\"".$asset_path[0]."\">";
    echo "<input type=\"hidden\" name=\"asset_id\" id=\"asset_id\" value=\"".$asset_id."\">";
    
    
     if (isset($_GET['modify'])){
        echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"works\">";
        echo "<input type=\"hidden\" name=\"modify\" id=\"modify\" value=\"1\">";
        echo "<input type=\"hidden\" name=\"workorder_work_id\" id=\"workorder_work_id\" value=\"". (int) $_GET['workorder_work_id']."\">";
        
    }
    if (isset($_GET['new'])){
    echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"workorders\">";
    echo "<input type=\"hidden\" name=\"new\" id=\"new\" value=\"1\">";}
        
    echo "<input type=\"hidden\" name=\"workorder_id\" id=\"workorder_id\" value=\"";
    if (isset($_GET['modify']))
    echo $row_mod['workorder_id'];
    else if (isset($_GET['workorder_id']) && $_GET['workorder_id']>0)
    echo (int) $_GET['workorder_id'];
    else 
    lm_die("Missing workorder_id!");
    echo "\">";
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
    
   
    
//echo "</div>"; 
        
      echo "<div class=\"card-footer\"\>";
      echo "<div id='ajax_button'>";
      if (isset($_GET['modify'])){
       echo "<button type=\"submit\" id='submit_button' class=\"btn btn-primary btn-sm\">";
    echo "<i class=\"fa fa-dot-circle-o\"></i>";
    echo gettext("Submit");
    echo " </button>\n";
    }
      //<button type=\"submit\" id='submit_button' class=\"btn btn-primary btn-sm\">
      echo "</div>\n";
    
    
    
    
    //echo "<button type=\"reset\" onReset=\"is_it_valid_time_period()\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i>".gettext("Reset")." </button></div>\n";
          
    echo "</form>\n";
echo "</div>\n";
echo "<script>check_time_period();\n";

echo "$(\"#work_form\").validate({
  rules: {";
  if ($_SESSION['CAN_WRITE_LANG1'])
  echo "
    workorder_work_".LANG1.": {
      maxlength: ".$dba->get_max_fieldlength('workorder_works','workorder_work_'.LANG1)."
    }";
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    echo "workorder_work_".LANG2.": {
      maxlength: ".$dba->get_max_fieldlength('workorder_works','workorder_work_'.LANG2)."
    }";
echo "  }
})\n";
echo "</script>\n";

if (isset($_SESSION['SEE_WORKS']))
{
$SQL="SELECT workorders.workorder_id,workorder_user_id";

$SQL.=",workorder_work_".$lang.",workorder_".$lang.",workorder_short_".$lang;

$SQL.=",workorder_work_start_time,workorder_work_end_time FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id WHERE workorder_works.deleted<>1 AND workorders.asset_id =".$asset_id." ORDER BY workorder_work_end_time DESC LIMIT 0,5";

$result=$dba->Select($SQL);
if ($dba->affectedRows()>0){
echo "<strong>".gettext("Last 5 activities...")."</strong>";
echo "<table class='table table-striped table-bordered'>\n";
foreach($result as $row){
echo "<tr><td>";
echo date($lang_date_format." H:i", strtotime($row['workorder_work_start_time']))." - ";
if (date("Y.m.d", strtotime($row['workorder_work_start_time']))==date("Y.m.d", strtotime($row['workorder_work_end_time'])))
echo date("H:i", strtotime($row['workorder_work_end_time']))."</td>";
else
echo date($lang_date_format." H:i", strtotime($row['workorder_work_end_time']))."</td>";
echo "<td> ".get_username_from_id($row['workorder_user_id'])."</td>";
if ($row['workorder_'.$lang]!="")
echo "<td title='".$row['workorder_'.$lang]."'>".$row['workorder_short_'.$lang];
else
echo "<td>".$row['workorder_short_'.$lang];

echo "</td>";
if ($row['workorder_work_'.$lang]==mb_substr($row['workorder_work_'.$lang],0,30))
echo "<td>".$row['workorder_work_'.$lang];
else
echo "<td title='".$row['workorder_work_'.$lang]."'>".mb_substr($row['workorder_work_'.$lang],0,30)."...";
echo "</td></tr>\n";
}
echo "</table>\n";
}
}

require(INCLUDES_PATH."workorder_consumption.php");

}

if (isset($_SESSION['SEE_WORKS'])){
$there_no_partner_at_all=true;
?>
<div class="card">
<div class="card-header">
<?php
echo "<h2 style='display:inline;'>".gettext("Works")." </h2>";
?>
</div>
<div class="card-body">
<table id="work_table" class="table table-striped table-bordered">
<thead>
<tr>

<?php 
echo "<th></th><th>".gettext("Start")."</th>";
echo "<th>".gettext("End")."</th>";
if (isset($_GET["main_asset_id"])){
$main_asset_id=lm_isset_int('main_asset_id');
        if ($main_asset_id>0)
    $_SESSION['main_asset_id']=$main_asset_id;
    else if (isset($_GET["main_asset_id"]) && $_GET["main_asset_id"]=='all')
    unset($_SESSION['main_asset_id']);
}

if (!lm_isset_int('asset_id')>0 || (lm_isset_int('asset_id')>0 && isset($_POST['new']))){
        echo "<th";
        if (isset($_SESSION['main_asset_id']) && $_SESSION['main_asset_id']>0)
        echo " STYLE=\"background-color:orange\"";
        echo ">".gettext("Asset");
        $S="SELECT users_assets FROM users WHERE user_id=".$_SESSION['user_id'];
        $r=$dba->getRow($S);
        $users_assets=json_decode($r['users_assets'],true);
        if (!empty($users_assets))
        {
        $SQL="SELECT asset_id, asset_name_".$lang." FROM assets";
        $SQL.=" WHERE asset_id IN ('".join("','",$users_assets)."')";
        $SQL.=" ORDER BY asset_name_".$lang;
          
            $result1=$dba->Select($SQL);
            }
            echo " <select name=\"fmain_asset_id\" id=\"fmain_asset_id\" class=\"form-control\"";
                    echo " onChange=\"{if (this.value!='')
                    location.href='index.php?page=works&main_asset_id='+this.value;
                    else
                    location.href='index.php?page=works'};\"";
                    echo " style='display:inline;width:200px;'>\n";
            echo "<option value='all'>".gettext("All assets");
            if (!empty($result1)){
            foreach($result1 as $row1){
            echo "<option value='".$row1['asset_id']."'";
            if (isset($_SESSION['main_asset_id']) && $row1['asset_id']==$_SESSION['main_asset_id'] )
            echo " selected";
            echo ">";
            if ($row1['asset_id']==0)
            echo gettext("Refurbish");
            else
            echo $row1['asset_name_'.$lang]."\n";
            }}
            echo "</select>\n"; 

        echo "</th>";
}
//if ($_SESSION['user_level']<3 || isset($_GET['user_id']))
//{
if (isset($_GET['workorder_user_id']) && (int) $_GET['workorder_user_id']>=0)
$_SESSION['workorder_user_id']=(int) $_GET['workorder_user_id'];

echo "<th>";
echo "<select name=\"work_user_id\" id=\"work_user_id\" class=\"form-control\" required onChange=\"location.href='index.php?page=works&workorder_user_id='+this.value\">\n";
    

    $SQL="SELECT user_id,firstname,surname FROM users WHERE active=1 AND user_level<4";
    $SQL.=" ORDER BY surname";
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"0\">".gettext("All")."</option>\n";
    foreach ($result as $row){
    if (FIRSTNAME_IS_FIRST)
    {
    echo "<option value=\"".$row["user_id"]."\"";
    if (isset($_SESSION['workorder_user_id']) && $_SESSION['workorder_user_id']==$row['user_id'])
    echo " selected";
    echo ">".$row["firstname"]." ".$row["surname"]."</option>\n";
    }
    else{
    echo "<option value=\"".$row["user_id"]."\"";
    if (isset($_SESSION['workorder_user_id']) && $_SESSION['workorder_user_id']==$row['user_id'])
    echo " selected";
    echo ">".$row["surname"]." ".$row["firstname"]."</option>\n";
    }
    }
    echo "</select>";
  



echo "</th>";
echo "<th>".gettext("Partner")."</th>";
//}
echo "<th>".gettext("Work")."</th><th>".gettext("Work done")."</th></tr>";

echo "</thead>";
echo "<tbody>";

$SQL="SELECT workorder_works.workorder_id,workorder_works.workorder_status, workorder_work_id,workorder_works.main_asset_id,workorder_works.asset_id,workorder_work_start_time,workorder_work_end_time,workorder_work_".$lang.",workorder_works.workorder_user_id,workorder_works.workorder_partner_id,workorder_short_".$lang;

if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']) && $_SESSION['user_level']<3)
$SQL.=",workorder_work_".LANG1.",workorder_work_".LANG2;

$SQL.=" FROM workorder_works LEFT JOIN workorders ON workorders.workorder_id=workorder_works.workorder_id WHERE workorder_works.deleted<>1";


if (!empty($users_assets))
$SQL.=" AND workorders.main_asset_id IN ('".join("','",$users_assets)."')";


if (isset($_SESSION['main_asset_id']) && $_SESSION['main_asset_id']>=0)
$SQL.=" AND workorder_works.main_asset_id='".$_SESSION['main_asset_id']."'";

//if ($_SESSION['user_level']>2)
//$SQL.=" AND workorder_user_id=".(int) $_SESSION['user_id'];
if (isset($_SESSION['workorder_user_id']) && $_SESSION['workorder_user_id']>0)
$SQL.=" AND workorder_user_id=".(int) $_SESSION['workorder_user_id'];

$SQL.=" ORDER BY workorder_work_end_time DESC";
$result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;
$from=($pagenumber-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);

$now=new datetime('now');
if (!empty($result)){
foreach ($result as $row){

        echo "<tr>\n";
        echo "<td ";
        if (5==$row['workorder_status'])
                        echo " style=\"background-color:#b3f2b3;\"";
        echo "><div class='d-flex justify-content-between'>".++$from;
        echo " <a href=\"javascript:ajax_call('show_workorder_detail',".$row["workorder_id"].",'','','','".URL."index.php','for_ajaxcall')\" title=\"show workorder details\"><i class=\"fa fa-info-circle\"></i></a> ";
 
    $allow_to_modify_date = new DateTime($row['workorder_work_start_time']); // Y-m-d
    $allow_to_modify_date->add(new DateInterval('P'.DAYS_ALLOW_TO_MODIFY_WORKS.'D'));
    
        if (isset($_SESSION["MODIFY_WORK"]) && ($allow_to_modify_date>$now || $_SESSION['user_level']<3)){
         echo "<a href=\"index.php?page=works&modify=1&workorder_work_id=".$row['workorder_work_id']."&workorder_id=".$row['workorder_id'];
        
         echo "\" title=\"".gettext("alter work")."\"> <i class=\"fa fa-wrench\"></i></a> ";
         }
         if (isset($_SESSION['TAKE_PRODUCT_FROM_STOCK']))
                        echo "<a href=\"javascript:ajax_call('product_to_workorder','','','','".$row["workorder_id"]."','".URL."index.php','for_ajaxcall')\" title=\"".gettext("Product to workorder")."\"> <i class=\"fa fa-cart-plus\" style='color:red'></i></a> ";
          
        if (isset($_SESSION["DELETE_WORK"]) && (($row['workorder_user_id']==$_SESSION['user_id'] && $allow_to_modify_date>$now) || $_SESSION['user_level']<3) ){
         echo "<a href=\"javascript:
         var a=confirm('".gettext("You are about to delete a work. Are you sure?")."');
                if (a==true)
         location.href='index.php?page=works&delete=1&workorder_work_id=".$row['workorder_work_id']."&valid=".$_SESSION['tit_id']."'\"><i class=\"fa fa-trash\"></i></a>";
         
         }              
             
echo "</div></td>";
echo "<td>";
if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']) && $_SESSION['user_level']<3 && !empty($row['workorder_work_'.LANG1]) && empty($row['workorder_work_'.LANG2]))
echo " * ";//translation needed
echo date($lang_date_format." H:i", strtotime($row['workorder_work_start_time']))."</td>";
if (date("Y.m.d", strtotime($row['workorder_work_start_time']))==date("Y.m.d", strtotime($row['workorder_work_end_time'])))
echo "<td>".date("H:i", strtotime($row['workorder_work_end_time']))."</td>";
else
echo "<td>".date($lang_date_format." H:i", strtotime($row['workorder_work_end_time']))."</td>";
if (!lm_isset_int('asset_id')>0 || (lm_isset_int('asset_id')>0 && isset($_POST['new'])))
    {
    echo "<td>";
    if ($row['asset_id']>0)
        {
        $n="";
        foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
        if ($n=="") // the first element is the main asset_id -> ignore it
        $n=" ";
        else
        $n.=$k."-><wbr>";}
        
        if ($n!="")
        echo substr($n,0,-7);
        }
    else {
    $SQL="SELECT product_id_to_refurbish FROM workorders WHERE workorder_id=".$row['workorder_id'];
    $row1=$dba->getRow($SQL);
    if ($row1['product_id_to_refurbish']>0)
    echo get_product_name_from_id($row1['product_id_to_refurbish'],$lang);
    }
    echo "</td>\n";
    }
//if ($_SESSION['user_level']<3 || isset($_GET['user_id'])){
    echo "<td>".get_username_from_id($row["workorder_user_id"])."</td>"; 
    echo "<td>";
    if ($row["workorder_partner_id"]>0)
    {
    $there_no_partner_at_all=false;
    echo get_partner_name_from_id($row["workorder_partner_id"]);
    }
    echo "</td>"; 
 // }
 echo "<td><a";
   if (isset($_SESSION["MODIFY_WORK"]) && ($allow_to_modify_date>$now || $_SESSION['user_level']<3)){
         echo " href=\"index.php?page=works&modify=1&workorder_work_id=".$row['workorder_work_id']."&workorder_id=".$row['workorder_id'];
         echo "\" title=\"".gettext("alter work")."\"";
         }
 echo ">".$row['workorder_short_'.$lang]."</td>";
 echo "<td>".$row['workorder_work_'.$lang]."</td>";
 echo "</tr>\n";
}}
echo "</tbody></table>";

include(INCLUDES_PATH."pagination.php");
    
    echo "<script>\n";?>
    $( document ).ready(function() {
    
    <?php 
    if ($there_no_partner_at_all){
     echo "$('#work_table td:nth-child(6)').hide();";
    echo "$('#work_table th:nth-child(6)').hide();";
     }
    
    if ($_SESSION['user_level']>2)
    echo "ajax_call('show_worktimebar',document.work_form.workorder_work_start_date.value,document.work_form.workorder_user_id.value,'modify','','".URL."index.php','for_ajaxcall');";
    echo "})</script>\n";
   
    echo "</div>";//card
}
else
echo gettext("You have no permission!");
?> 
