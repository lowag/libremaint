
<?php
if (isset($_POST['connection_category_en']) && is_it_valid_submit() && isset($_SESSION['ADD_NEW_CONNECTION_TYPE'])){

$SQL="INSERT INTO connection_categories (connection_category_en,connection_category_".$lang.") VALUES ";
$SQL.="('".$dba->escapeStr($_POST['connection_category_en'])."',";
$SQL.="'".$dba->escapeStr($_POST['connection_category_'.$lang])."'";
$SQL.=")";


if ($dba->Query($SQL))
        lm_info(gettext("The new connection category has been saved."));
                else
        lm_info(gettext("Failed to save new connection category."));
if (LM_DEBUG)
error_log($SQL,0);
}

if (isset($_GET["new"]) ){
?>
<div class="card">
<div class="card-header">
<strong><?php echo gettext("New connection type");?></strong>
</div><?php //card header ?>
<div class="card-body card-block">
<form action="index.php" id="conn_form" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php
if (!isset($_SESSION['ADD_NEW_CONNECTION_TYPE']))
lm_die(gettext("You have no permission!"));
    
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"connection_category_en\" class=\"form-control-label\">".gettext("Connection category en:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"connection_category_en\" name=\"connection_category_en\" placeholder=\"".gettext("connection category en")."\" class=\"form-control\" required></div>\n";
echo "</div>";

if ($lang!="en"){
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"connection_category_".$lang."\" class=\"form-control-label\">".gettext("Connection category:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"connection_category_".$lang."\" name=\"connection_category_".$lang."\" placeholder=\"".gettext("connection category")."\" class=\"form-control\" required></div>\n";
echo "</div>";
}
echo "<INPUT TYPE=\"hidden\" name=\"page\" id=\"page\" value=\"connection_types\">";
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i> Submit </button>\n";
echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> Reset </button></div>\n";
echo "</form></div>";

echo "<script>\n";
echo "$(\"#conn_form\").validate()\n";
echo "</script>\n";
}

if (isset($_SESSION['SEE_CONNECTION_TYPE'])){
$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;
$from=1;
$SQL="SELECT connection_category_en,connection_category_".$lang." FROM connection_categories";
$result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
$from=($pagenumber-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log("page:".$pagenumber." ".$SQL,0);

?>
<div id='for_ajaxcall'>
</div>
<div class="card-body">
<table id="bootstrap-data-table" class="table table-striped table-bordered">
<thead>
<tr>
<th></th>
<?php echo "<th>".gettext("Connection category")." en </th><th>".gettext("Connection category")."</th></tr>";
?>
</thead>
<tbody>
<?php
foreach ($result as $row)
{
$from++;
echo "<tr><td>".$from;

 
echo "</td><td>".$row['connection_category_en']."</td>\n";
echo "<td>".$row['connection_category_'.$lang]."</td>\n";
echo "</tr>\n";

}
echo "</tbody></table></div>";
include(INCLUDES_PATH."pagination.php");
}
else
echo gettext("You have no permission!");
?>




