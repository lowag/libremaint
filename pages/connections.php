
<?php
if (isset($_POST['new']) && is_it_valid_submit() && isset($_SESSION['ADD_NEW_CONNECTION_TYPE'])){

$SQL="INSERT INTO connections (";

if (isset($_SESSION['CAN_WRITE_LANG1']))
    $SQL.="connection_name_".LANG1.",connection_review_".LANG1.",";

if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    $SQL.="connection_name_".LANG2.",connection_review_".LANG2.",";
    
$SQL.="connection_type,connection_category_id) VALUES (";

if (isset($_SESSION['CAN_WRITE_LANG1'])){
$SQL.="'".$dba->escapeStr($_POST['connection_name_'.LANG1])."',";
$SQL.="'".$dba->escapeStr($_POST['connection_review_'.LANG1])."',";
}

if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2'])){
$SQL.="'".$dba->escapeStr($_POST['connection_name_'.LANG2])."',";
$SQL.="'".$dba->escapeStr($_POST['connection_review_'.LANG2])."',";
}

$SQL.=(int) $_POST['connection_type'].",";
$SQL.=(int) $_POST['connection_category_id'];
$SQL.=")";


if ($dba->Query($SQL))
        lm_info(gettext("The new connection has been saved."));
                else
        lm_info(gettext("Failed to save new connection.".$dba->err_msg));
if (LM_DEBUG)
error_log($SQL,0);
}


if (isset($_POST['modify']) && isset($_POST['connection_id']) && is_it_valid_submit() && isset($_SESSION['ADD_NEW_CONNECTION_TYPE'])){

$SQL="UPDATE connections SET ";

if (isset($_SESSION['CAN_WRITE_LANG1'])){
    $SQL.="connection_name_".LANG1."='".$dba->escapeStr($_POST['connection_name_'.LANG1])."',";
    
    $SQL.="connection_review_".LANG1."='".$dba->escapeStr($_POST['connection_review_'.LANG1])."',";
}


if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
{
    $SQL.="connection_name_".LANG2."='".$dba->escapeStr($_POST['connection_name_'.LANG2])."',";
    $SQL.="connection_review_".LANG2."='".$dba->escapeStr($_POST['connection_review_'.LANG2])."',";
 }
 
$SQL.="connection_type=".(int) $_POST['connection_type'].",";
$SQL.="connection_category_id=".(int) $_POST['connection_category_id'];

$SQL.=" WHERE connection_id=".(int) $_POST['connection_id'];

if ($dba->Query($SQL))
        lm_info(gettext("The connection has been modified."));
                else
        lm_info(gettext("Failed to modify connection.".$dba->err_msg));
if (LM_DEBUG)
error_log($SQL,0);
}




if (isset($_GET["new"]) || isset($_GET["modify"]) ){
?>
<div class="card">
<div class="card-header">
<strong><?php echo gettext("New connection");?></strong>
</div><?php //card header ?>
<div class="card-body card-block">
<form action="index.php" id="conn_form" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php
if (!isset($_SESSION['ADD_NEW_CONNECTION_TYPE']))
lm_die(gettext("You have no permission!"));
if (isset($_GET["modify"]))
{
$SQL="SELECT * FROM connections WHERE connection_id=".(int) $_GET['connection_id'];
$connection_row=$dba->getRow($SQL);
}


echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"connection_category_id\" class=\"form-control-label\">".gettext("Connection category:")."</label></div>\n";
        echo "<div class=\"col-8 col-md-6\">";
        echo "<select name=\"connection_category_id\" id=\"connection_category_id\" class=\"form-control\"";
        echo " required>\n";
        $SQL="SELECT connection_category_".$lang.", connection_category_id FROM connection_categories ORDER BY connection_category_".$lang;
        if (LM_DEBUG)
        error_log($SQL,0);
        $result=$dba->Select($SQL);
        echo "<option value=\"\">".gettext("Please select")."</option>\n";
        foreach ($result as $row)
        {
            echo "<option value=\"".$row["connection_category_id"]."\"";
           if (isset($_GET["modify"]) && $connection_row['connection_category_id']==$row["connection_category_id"])
           echo " selected";
            echo ">".$row["connection_category_".$lang]."</option>\n";
        }
        echo "</select></div></div>";

if ($_SESSION['CAN_WRITE_LANG1']){        
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"connection_name_".LANG1."\" class=\"form-control-label\">".gettext("Connection name").LANG1.":</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"connection_name_".LANG1."\" name=\"connection_name_".LANG1."\" placeholder=\"".gettext("connection name")." ".LANG1."\" class=\"form-control\"";

if (isset($_GET["modify"]))
echo " VALUE=\"".$connection_row['connection_name_'.LANG1]."\"";

echo " required></div>\n";
echo "</div>";
}


if (isset($_SESSION['CAN_WRITE_LANG2']) && LANG2_AS_SECOND_LANG){
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"connection_name_".LANG2."\" class=\"form-control-label\">".gettext("Connection name:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"connection_name_".LANG2."\" name=\"connection_name_".LANG2."\" placeholder=\"".gettext("connection name")."\" class=\"form-control\"";

if (isset($_GET["modify"]))
echo " VALUE=\"".$connection_row['connection_name_'.LANG2]."\"";

echo " required></div>\n";
echo "</div>";
}
echo "<div class=\"row form-group\">\n";
                echo "<div class=\"col col-md-2\"><label for=\"connection_type\" class=\"form-control-label\">".gettext("Connection type:")."</label></div>\n";
                echo "<div class=\"col col-md-2\">";
                echo "<select name=\"connection_type\" id=\"connection_type\" class=\"form-control\">\n";
                $i=0;
                //$connection_types from lm-settings.php
                foreach ($connection_types as $connection_type){
                echo "<option value=\"".++$i."\"";
                if (isset($_GET["modify"]) && $connection_row['connection_type']==$connection_type)
           echo " selected";
                echo ">".$connection_type."</option>\n";
                }                
                echo "</select>";
                echo "</div>\n";
echo "</div>\n";

if ($_SESSION['CAN_WRITE_LANG1']){
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"connection_review_".LANG1."\" class=\"form-control-label\">".gettext("Connection review").":</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"connection_review_".LANG1."\" name=\"connection_review_".LANG1."\" placeholder=\"".gettext("connection review")." ".LANG1."\" class=\"form-control\"";
if (isset($_GET["modify"]))
echo " VALUE=\"".$connection_row['connection_review_'.LANG1]."\"";

echo "></div>\n";
echo "</div>";
}

if (isset($_SESSION['CAN_WRITE_LANG2']) && LANG2_AS_SECOND_LANG){
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"connection_review_".LANG2."\" class=\"form-control-label\">".gettext("Connection review:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"connection_review_".LANG2."\" name=\"connection_review_".LANG2."\" placeholder=\"".gettext("connection review")."\" class=\"form-control\"";
if (isset($_GET["modify"]))
echo " VALUE=\"".$connection_row['connection_review_'.LANG2]."\"";

echo "></div>\n";
echo "</div>";
}

echo "<INPUT TYPE=\"hidden\" name=\"page\" id=\"page\" value=\"connections\">";
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

if (isset($_GET['new']))
echo "<input type=\"hidden\" name=\"new\" id=\"new\" value=\"1\">";
else if (isset($_GET['modify'])){
echo "<input type=\"hidden\" name=\"modify\" id=\"modify\" value=\"1\">";
echo "<input type=\"hidden\" name=\"connection_id\" id=\"connection_id\" value=\"".(int) $_GET['connection_id']."\">";
}

echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i> Submit </button>\n";
echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> Reset </button></div>\n";
echo "</form></div>";
echo "<script>\n";
echo "$(\"#conn_form\").validate()\n";
echo "</script>\n";
}

if (isset($_SESSION['SEE_CONNECTION_TYPE'])){
if (isset($_GET['connection_category_id']) && $_GET['connection_category_id']>0)
$_SESSION['connection_category_id']=(int) $_GET['connection_category_id'];
else if (isset($_GET['connection_category_id']) && $_GET['connection_category_id']==0)
unset($_SESSION['connection_category_id']);
$pagenumber=lm_isset_int('pagenumber');

?>
<div id='for_ajaxcall'>
</div>
<div class="card-body">
<table id="bootstrap-data-table" class="table table-striped table-bordered">
<thead>
<tr>
<th></th>
<?php 


echo "<th>";
echo "<SELECT name='connection_id' id='connection_id' onChange=\"location.href='index.php?page=connections&connection_category_id='+this.value\">";
echo "<OPTION VALUE='0'>".gettext("All connection");
$SQL="SELECT connection_category_id, connection_category_".$lang." FROM connection_categories";
$result=$dba->Select($SQL);
foreach($result as $row){
echo "<OPTION VALUE='".$row['connection_category_id']."'";
if (isset($_SESSION['connection_category_id']) && $row['connection_category_id']==$_SESSION['connection_category_id'])
echo " selected";
echo ">".$row['connection_category_'.$lang];

}
echo "</SELECT>";
echo "</th><th>".gettext("Connection name")."</th>";
echo "<th>".gettext("Connection type")."</th>";
echo "<th>".gettext("Connection review")."</th></tr>";

?>
</thead>
<tbody>
<?php
if ($pagenumber<1)
$pagenumber=1;
$from=1;
$SQL="SELECT connection_id,connection_category_".$lang.",connection_name_".$lang.",connection_review_".$lang.",connection_type FROM connections LEFT JOIN connection_categories ON connection_categories.connection_category_id=connections.connection_category_id";
if (isset($_SESSION['connection_category_id']) && $_SESSION['connection_category_id']>0)
$SQL.=" WHERE connections.connection_category_id=".$_SESSION['connection_category_id'];
$SQL.=" ORDER BY connection_category_".$lang;
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
echo "<tr><td>";
echo "<div class=\"user-area dropdown float-right\">\n";
                            
                             echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
                             echo $from;
                             echo "</a>\n";
                             
                            echo "<div class=\"user-menu dropdown-menu\">";

                             if (isset($_SESSION["SEE_CONNECTION_OF_PRODUCT"]) && isset($_SESSION["SEE_CONNECTION_OF_ASSET"])){
                            echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_assets_products_with_this_connection','".$row['connection_id']."','','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Show assets, product with this connection")."</a>";}
                             
                             if (isset($_SESSION['ADD_NEW_CONNECTION_TYPE']))
                            {
                            echo "<a class=\"nav-link\" href=\"index.php?page=connections&connection_id=".$row['connection_id']."&modify=1\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Modify connection")."</a>";
                            
                            }
                             echo "</div>";
    echo "</div>";
 
echo "</td><td>".$row['connection_category_'.$lang]."</td>\n";
echo "<td>".$row['connection_name_'.$lang]."</td>\n";
echo "<td>";
if ($row['connection_type']<3)
echo gettext("Male-female");
else
echo gettext("Same");
echo "</td>\n";

echo "<td>".$row['connection_review_'.$lang]."</td>\n";
echo "</tr>\n";

}}
echo "</tbody></table></div>";
include(INCLUDES_PATH."pagination.php");
}
else
echo gettext("You have no permission!");
?>


