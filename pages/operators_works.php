<script>
function add_to_array(){
var checkboxes = document.querySelectorAll('input[name="workrequest_id[]"]');
var workrequest_ids=[];

checkboxes.forEach(e => { 
    if (e.checked){
    workrequest_ids.push(e.value);
     }
 })
document.getElementById("workrequest_ids").value=JSON.stringify(workrequest_ids);

 }
 
function is_any_work_checked(){
var checkboxes = document.querySelectorAll('input[name="workrequest_id[]"]');
var i=0;
checkboxes.forEach(e => { 
    if (e.checked){
    i++;
    }
 })
if (i>0)
return true;
else
{
alert('<?php echo (gettext("There is no any work checked!"));?>');
return false;
}
} 
</script>
<?php 
$SQL="SELECT users_assets FROM users WHERE user_id=".$_SESSION['user_id'];
$row=$dba->getRow($SQL);
if (!empty($row['users_assets']))
$users_assets=json_decode($row['users_assets'],true);

if (isset($_GET['delete']) && isset($_SESSION['DELETE_OPERATOR_WORK']) && is_it_valid_submit()){
$SQL="SELECT operator_user_id,workrequest_id FROM operator_works WHERE operator_work_id=".(int) $_GET['operator_work_id'];
$row=$dba->getRow($SQL);

if ($row['operator_user_id']==$_SESSION['user_id'])
    {
    $SQL="UPDATE operator_works SET deleted=1 WHERE operator_work_id=".(int) $_GET['operator_work_id'];
    $dba->Query($SQL);
    $SQL="SELECT operator_user_id,operator_work_date FROM operator_works WHERE workrequest_id=".$row['workrequest_id']." AND deleted<>1 ORDER BY operator_work_date DESC LIMIT 0,1";
    $row1=$dba->getRow($SQL);
    $SQL="UPDATE workrequests SET last_ready_date='".$row1['operator_work_date']."', last_ready_user_id=".$row1['operator_user_id'].",workrequest_status=1 WHERE workrequest_id=".$row['workrequest_id'];
            $dba->Query($SQL);
    
    }
}

if (isset($_POST['new']) && isset($_SESSION['RECORD_OPERATOR_WORK']) && is_it_valid_submit()){// add new work form 
$date_now = new DateTime();
$date_work=DateTime::createFromFormat($lang_date_format.' h:i',$_POST['operator_work_date']." ".$_POST['operator_work_time']);

$recorded=0;
if ($date_work<=$date_now) //not the future
{
     foreach (json_decode($_POST["workrequest_ids"],true) as $workrequest_id)
            {
    
    $SQL="SELECT * FROM workrequests WHERE workrequest_id=".$workrequest_id;
    $workrequest_row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
    $SQL="INSERT INTO operator_works (workrequest_id, operator_work_time,main_asset_id,asset_id,operator_user_id";

    
    $SQL.=") VALUES ";
    $SQL.="(";
    $SQL.=$workrequest_id.",";
    $SQL.="'".$dba->escapeStr($_POST['operator_work_date'])." ".$dba->escapeStr($_POST['operator_work_time'])."',";
    $SQL.=$workrequest_row['main_asset_id'].",";
    $SQL.=$workrequest_row['asset_id'].",";
    
    $SQL.=(int) $_SESSION['user_id'].")";
   
   
    
if (LM_DEBUG)
error_log($SQL,0);
    if ($dba->Query($SQL))
            {
            $SQL="UPDATE workrequests SET workrequest_status=3, last_ready_date='".$_POST['operator_work_date']."', last_ready_user_id=".$_SESSION['user_id']." WHERE workrequest_id=".$workrequest_id;
            $dba->Query($SQL);
            if (LM_DEBUG)
            error_log($SQL,0);
            $recorded++;
                        
            }
            else
            lm_error(gettext("Failed to record activity.").$SQL." ".$dba->err_msg);
        }
            }
            else
            lm_error(gettext("Failed to record activity (the time you given is in the future)"));
        
if ($recorded>0)
lm_info($recorded." ".gettext("activity has been recorded."));
        }


?>

<div id='for_ajaxcall'>
</div>
<?php




if((isset($_GET['new']) && isset($_SESSION['RECORD_OPERATOR_WORK']))){

  
echo "<div class=\"card\">";


         echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\" class=\"form-horizontal\" id=\"work_form\" name=\"work_form\" onSubmit=\"return is_any_work_checked()\">";

    echo "<div class=\"card-header\">\n";
             
        echo "<strong>".gettext("Add activity")."</strong>\n";
        if (isset($_GET['main_asset_id']) && (int) $_GET['main_asset_id']>0)
        $main_asset_id=(int) $_GET['main_asset_id'];
    
        
 
    echo "</div>\n";
echo "<div class=\"card-body card-block\">";
   
  echo "<input type='hidden' name='operator_user_id' id='operator_user_id' value='".$_SESSION['user_id']."'>\n";

   echo "<div class=\"row form-group\">\n";
        
        echo "<div class=\"col col-md-2\"><label for=\"operator_work_date\" class=\"form-control-label\">".gettext("Finished time").":</label></div>\n";
        echo "<div class=\"col-12 col-md-5\">";
        
        echo "<input type=\"date\" id=\"operator_work_date\" name=\"operator_work_date\" value=\"";
        echo date($lang_date_format_for_input);
        echo "\">\n";
        echo "<input type=\"time\" id=\"operator_work_time\" name=\"operator_work_time\" value=\"";
        echo date("h:i");
        echo "\">\n";
        echo "</div></div>\n";
  
  echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"main_asset_id\" class=\" form-control-label\">".gettext("Asset:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-3\">";
        echo "<select name=\"main_asset_id\" id=\"main_asset_id\" class=\"form-control\" required";
        echo " onChange=\"location.href='index.php?page=operators_works&new=1&valid=".$_SESSION['tit_id']."&main_asset_id='+this.value\"";
        echo ">\n";
        echo "<option value=''>".gettext("Select an asset!")."</option>\n";
        $SQL="SELECT DISTINCT main_asset_id FROM workrequests WHERE for_operators=1";
        $SQL.=" AND main_asset_id IN ('".join("','",$users_assets)."')";
        $result=$dba->Select($SQL);
        if (LM_DEBUG)
            error_log($SQL,0);
        foreach($result as $row) 
        {
       
        echo "<option value=\"".$row['main_asset_id']."\"";
        if (isset($main_asset_id) && $row['main_asset_id']==$main_asset_id )
        echo " selected";
        
        echo ">".get_asset_name_from_id($row['main_asset_id'],$lang)."</option>\n";
        }
        echo "</select>\n";
    echo "</div>";
echo "</div>";
  
  
  if (isset($_GET['main_asset_id']) && in_array($_GET['main_asset_id'], $users_assets))
  {
  $SQL="SELECT workrequests.asset_id,workrequest_short_".$lang.",workrequest_".$lang.",workrequest_id,last_ready_date,last_ready_user_id,asset_name_".$lang." FROM workrequests LEFT JOIN assets ON workrequests.asset_id=assets.asset_id WHERE for_operators=1 AND main_asset_id=".$main_asset_id." AND workrequest_status<>4 ORDER BY asset_name_".$lang;
  $result=$dba->Select($SQL);

  if ($dba->affectedRows()>0){
  echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"main_asset_id\" class=\" form-control-label\">".gettext("Tasks").":</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-6\">";
    
    foreach ($result as $row){
    $SQL="SELECT asset_parent_id FROM assets WHERE asset_id=".$row['asset_id'];
    $row1=$dba->getRow($SQL);
    echo "<INPUT TYPE='checkbox' name='workrequest_id[]' value='".$row['workrequest_id']."' onChange=\"add_to_array()\"> ";
    if ($row1['asset_parent_id']>0)
    echo get_asset_name_from_id($row1['asset_parent_id'],$lang)." > ";
    echo get_asset_name_from_id($row['asset_id'],$lang).": ".$row['workrequest_short_'.$lang];
    if ($row['last_ready_user_id']>0)
    echo " (".get_username_from_id($row['last_ready_user_id'])." / ".date($lang_date_format, strtotime($row['last_ready_date'])).")";
    echo "<br/>";
    }
   // echo "</div>";
//echo "</div>";
   } 
  else
  echo gettext("There is no task for this asset.");
  
  
  
  
  }
  
  
    echo "<input type=\"hidden\" name=\"workrequest_ids\" id=\"workrequest_ids\">";

    
    
    
     if (isset($_GET['modify'])){
        echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"operators_works\">";
        echo "<input type=\"hidden\" name=\"modify\" id=\"modify\" value=\"1\">";
        echo "<input type=\"hidden\" name=\"operator_work_id\" id=\"operator_work_id\" value=\"". (int) $_GET['operator_work_id']."\">";
        
    }
    if (isset($_GET['new'])){
    echo "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"operators_works\">";
    echo "<input type=\"hidden\" name=\"new\" id=\"new\" value=\"1\">";}
        
    
    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";
    
    echo "</div></div>\n";
    
//echo "</div>"; 
        
      echo "<div class=\"card-footer\"\>";
      echo "<div id='ajax_button'>";
     
       echo "<button type=\"submit\" id='submit_button' class=\"btn btn-primary btn-sm\">";
    echo "<i class=\"fa fa-dot-circle-o\"></i>";
    echo gettext("Submit");
    echo " </button>\n";
      //<button type=\"submit\" id='submit_button' class=\"btn btn-primary btn-sm\">
      echo "</div>\n";
    
    
    
    
    //echo "<button type=\"reset\" onReset=\"is_it_valid_time_period()\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i>".gettext("Reset")." </button></div>\n";
          
    echo "</form>\n";
echo "</div>\n";



}

if (isset($_SESSION['SEE_OPERATORS_WORKS'])){
$there_no_partner_at_all=true;
?>
<div class="card">
<div class="card-header">
<?php
echo "<h2 style='display:inline;'>".gettext("Operator works")." </h2>";
?>
</div>
<div class="card-body">
<table id="work_table" class="table table-striped table-bordered">
<thead>
<tr>

<?php 
echo "<th></th><th>".gettext("Date")."</th>";


$main_asset_id=lm_isset_int('main_asset_id');
        if ($main_asset_id>0)
    $_SESSION['main_asset_id']=$main_asset_id;
    else if (isset($_GET["main_asset_id"]) && $_GET["main_asset_id"]=='all')
    unset($_SESSION['main_asset_id']);
    
if (!lm_isset_int('asset_id')>0 || (lm_isset_int('asset_id')>0 && isset($_POST['new']))){
        echo "<th";
        if (isset($_SESSION['main_asset_id']) && $_SESSION['main_asset_id']>0)
        echo " STYLE=\"background-color:orange\"";
        echo ">".gettext("Asset");
        

        
            echo " <select name=\"fmain_asset_id\" id=\"fmain_asset_id\" class=\"form-control\"";
                    echo " onChange=\"{if (this.value!='')
                    location.href='index.php?page=operators_works&main_asset_id='+this.value;
                    else
                    location.href='index.php?page=operators_works'};\"";
                    echo " style='display:inline;width:200px;'>\n";
            echo "<option value='all'>".gettext("All assets");
            foreach($users_assets as $key=>$value){
            echo "<option value='".$value."'";
            if (isset($_SESSION['main_asset_id']) && $value==$_SESSION['main_asset_id'] )
            echo " selected";
            echo ">";
            echo get_asset_name_from_id($value,$lang)."\n";
            }
            echo "</select>\n"; 

        echo "</th>";
}

if (isset($_GET['operator_user_id']) && (int) $_GET['operator_user_id']>=0)
$_SESSION['operator_user_id']=(int) $_GET['operator_user_id'];

echo "<th>";
echo "<select name=\"work_user_id\" id=\"work_user_id\" class=\"form-control\" required onChange=\"location.href='index.php?page=operators_works&operator_user_id='+this.value\">\n";
    $SQL="SELECT user_id,firstname,surname FROM users WHERE user_level=4 AND active=1";
    $SQL.=" ORDER BY surname";
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"0\">".gettext("All")."</option>\n";
    foreach ($result as $row){
    if (FIRSTNAME_IS_FIRST)
    {
    echo "<option value=\"".$row["user_id"]."\"";
    if (isset($_SESSION['operator_user_id']) && $_SESSION['operator_user_id']==$row['user_id'])
    echo " selected";
    echo ">".$row["firstname"]." ".$row["surname"]."</option>\n";
    }
    else{
    echo "<option value=\"".$row["user_id"]."\"";
    if (isset($_GET['operator_user_id']) && $_SESSION['operator_user_id']==$row['user_id'])
    echo " selected";
    echo ">".$row["surname"]." ".$row["firstname"]."</option>\n";
    }
    }
    echo "</select>";
echo "</th>";

echo "<th>".gettext("Note")."</th></tr>";

echo "</thead>";
echo "<tbody>";
if (!empty($users_assets))
{
$SQL="SELECT operator_works.workrequest_id, operator_work_id,operator_works.main_asset_id,operator_works.asset_id,operator_work_time,operator_works.operator_user_id,operator_work,workrequest_".$lang.",workrequest_short_".$lang." FROM operator_works LEFT JOIN workrequests ON workrequests.workrequest_id=operator_works.workrequest_id WHERE deleted<>1";

if (isset($_SESSION['main_asset_id']) && $_SESSION['main_asset_id']>=0)
$SQL.=" AND operator_works.main_asset_id='".$_SESSION['main_asset_id']."'";
else
$SQL.=" AND operator_works.main_asset_id IN ('".join("','",$users_assets)."')";

$SQL.=" ORDER BY operator_work_time DESC";
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
if ($number_all>0){
foreach ($result as $row){

        echo "<tr>\n";
        echo "<td ";
        echo "><div class='d-flex justify-content-between'>".++$from;
        if ($row['workrequest_'.$lang]!="")
        echo " <a href=\"javascript:ajax_call('show_workrequest_detail',".$row["workrequest_id"].",'','','','".URL."index.php','for_ajaxcall')\" title=\"show workrequest details\"><i class=\"fa fa-info-circle\"></i></a> ";
$allow_to_modify_date = new DateTime($row['operator_work_time']); // Y-m-d
    $allow_to_modify_date->add(new DateInterval('P'.DAYS_ALLOW_TO_MODIFY_WORKS.'D'));
    
        if (isset($_SESSION["DELETE_OPERATOR_WORK"]) && $row['operator_user_id']==$_SESSION['user_id'] && $allow_to_modify_date>$now){
         echo "<a href=\"javascript:
         var a=confirm('".gettext("You are about to delete a work. Are you sure?")."');
                if (a==true)
         location.href='index.php?page=operators_works&delete=1&operator_work_id=".$row['operator_work_id']."&valid=".$_SESSION['tit_id']."'\"><i class=\"fa fa-trash\"></i></a>";
         
         }
    
                          
echo "</div></td>";
echo "<td>".date($lang_date_format." h:i", strtotime($row['operator_work_time']))."</td>";

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
  
    echo "</td>\n";
    }
    
    echo "<td>".get_username_from_id($row["operator_user_id"])."</td>"; 
    
 echo "<td>".$row['operator_work']."</td>";
 echo "</tr>\n";
}}}
echo "</tbody></table>";

include(INCLUDES_PATH."pagination.php");
   
    echo "</div>";//card
}
else
echo gettext("You have no permission!");
?> 
