<div id='for_ajaxcall'>
</div>
<?php
if (isset($_POST['partner_name']) && isset($_POST['new']) &&  is_it_valid_submit()){           
$SQL="INSERT INTO partners (partner_name,partner_address,partner_tags,contact1_title,contact1_firstname_is_first,contact1_firstname,contact1_surname,contact1_phone,contact1_email,contact1_position,partner_created) VALUES";
$SQL.="('".$dba->escapeStr($_POST['partner_name'])."',";
$SQL.="'".$dba->escapeStr($_POST['partner_address'])."',";
$SQL.="'".$dba->escapeStr($_POST['partner_tags'])."',";
$SQL.="'".(int) $_POST['contact1_title']."',";
$SQL.="'".(int) $_POST['contact1_firstname_is_first']."',";
$SQL.="'".$dba->escapeStr($_POST['contact1_firstname'])."',";
$SQL.="'".$dba->escapeStr($_POST['contact1_surname'])."',";
$SQL.="'".$dba->escapeStr($_POST['contact1_phone'])."',";
$SQL.="'".$dba->escapeStr($_POST['contact1_email'])."',";
$SQL.="'".$dba->escapeStr($_POST['contact1_position'])."',";
$SQL.="NOW())";


if ($dba->Query($SQL))
        lm_info(gettext("The new partner has been saved."));
        else
        echo lm_error(gettext("Failed to save new partner.".$dba->err_msg));
if (LM_DEBUG)
error_log($SQL,0);
}

else if (isset($_POST['partner_id']) && $_POST['partner_id']>0 && $_SESSION['user_level']<3 && is_it_valid_submit())
{
$SQL="UPDATE partners SET ";
$SQL.="partner_name='".$dba->escapeStr($_POST['partner_name'])."',";

$SQL.="partner_address='".$dba->escapeStr($_POST['partner_address'])."',";
$SQL.="partner_tags='".$dba->escapeStr($_POST['partner_tags'])."'";
$i=1;
while (isset($_POST['contact'.$i.'_title']))
{
$SQL.=",contact".$i."_title='".(int) $_POST["contact".$i."_title"]."',";
$SQL.="contact".$i."_firstname_is_first='".(int) $_POST["contact".$i."_firstname_is_first"]."',";

$SQL.="contact".$i."_firstname='".$dba->escapeStr($_POST['contact'.$i.'_firstname'])."',";
$SQL.="contact".$i."_surname='".$dba->escapeStr($_POST['contact'.$i.'_surname'])."',";
$SQL.="contact".$i."_position='".$dba->escapeStr($_POST['contact'.$i.'_position'])."',";
$SQL.="contact".$i."_phone='".$dba->escapeStr($_POST['contact'.$i.'_phone'])."',";
$SQL.="contact".$i."_email='".$dba->escapeStr($_POST['contact'.$i.'_email'])."'";
$i++;
}
$i--;
$SQL.=" WHERE partner_id='".(int) $_POST['partner_id']."'";
$SQL1="SHOW COLUMNS FROM partners LIKE '%contact".$i."%'";
$res1=$dba->Select($SQL1);
if (empty($res1))//need to create new fields
{
$SQL1="ALTER TABLE `libremaint`.`partners` 
ADD COLUMN `contact".$i."_surname` VARCHAR(45) NULL DEFAULT NULL AFTER `partner_tags`,
ADD COLUMN `contact".$i."_firstname` VARCHAR(45) NULL DEFAULT NULL AFTER `contact1_surname`,
ADD COLUMN `contact".$i."_firstname_is_first` TINYINT(1) UNSIGNED NULL DEFAULT NULL AFTER `contact".$i."_firstname`,
ADD COLUMN `contact".$i."_title` TINYINT(1) NULL DEFAULT NULL AFTER `contact1_firstname_is_first`,
ADD COLUMN `contact".$i."_email` VARCHAR(45) NULL DEFAULT NULL AFTER `contact1_title`,
ADD COLUMN `contact".$i."_phone` VARCHAR(20) NULL DEFAULT NULL AFTER `contact1_email`,
ADD COLUMN `contact".$i."_position` VARCHAR(45) NULL DEFAULT NULL AFTER `contact1_phone`";
if ($dba->Query($SQL1))
        lm_info(gettext("The new columns has been added."));
        else
        lm_error(gettext("Failed to add new columns").$dba->err_msg." ".$SQL1);
}



if ($dba->Query($SQL))
        lm_info(gettext("The new partner has been modified."));
        else
        lm_error(gettext("Failed to modify new partner ").$dba->err_msg." ".$SQL);
if (LM_DEBUG)
error_log($SQL,0);

} 

else if (isset($_GET["new"])){
?>
<div class="card">
<div class="card-header">
<strong><?php echo gettext("New partner");?></strong>
</div><?php //card header ?>
<div class="card-body card-block">
<form action="index.php" id="partner_form" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php

    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"partner_name\" class=\" form-control-label\">".gettext("Partner name:")."</label></div>";
    echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"partner_name\" name=\"partner_name\" placeholder=\"".gettext("name")."\" class=\"form-control\" required></div>\n";
    echo "</div>";

    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"partner_address\" class=\" form-control-label\">".gettext("Partner address:")."</label></div>";
    echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"partner_address\" name=\"partner_address\" placeholder=\"".gettext("address")."\" class=\"form-control\"></div>\n";
    echo "</div>";
    
    
    echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"contact1_firstname_is_first\" class=\"form-control-label\">".gettext("First name is first").":</label></div>\n";
         echo "<div class=\"col col-md-3\">";
        echo "<select name=\"contact_firstname_is_first\" id=\"contact_firstname_is_first\">";
        echo "<option value='1'";
        echo ">".gettext("Yes");
        echo "<option value='0'";
        echo ">".gettext("No");
        echo "</options></select>";
        echo "</div></div>\n";
    
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"contact1_title\" class=\" form-control-label\">".gettext("Title:")."</label></div>";

    echo "<div class=\"col col-md-2\">";
    echo "<select name=\"contact1_title\" id=\"contact1_title\" class=\"form-control\">\n";
    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
    $i=0;
    foreach  (TITLES as $title){
    echo "<option value=\"".++$i."\">".$title."</option>\n";
   
    }
    echo "</select></div></div>";
   

echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"contact1_firstname\" class=\"form-control-label\">".gettext("Firstname:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"contact1_firstname\" name=\"contact1_firstname\" placeholder=\"".gettext("Firstname")."\" class=\"form-control\"></div>\n";
echo "</div>";
  
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"contact1_surname\" class=\"form-control-label\">".gettext("Surname:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"contact1_surname\" name=\"contact1_surname\" placeholder=\"".gettext("Surname")."\" class=\"form-control\"></div>\n";
echo "</div>";


echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"contact1_email\" class=\"form-control-label\">".gettext("Email:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"contact1_email\" name=\"contact1_email\" placeholder=\"".gettext("Email")."\" class=\"form-control\"></div>\n";
echo "</div>";

echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"contact1_phone\" class=\"form-control-label\">".gettext("Phone:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><input type=\"text\" id=\"contact1_phone\" name=\"contact1_phone\" placeholder=\"".gettext("Phone")."\" class=\"form-control\"></div>\n";
echo "</div>";

echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"partner tags\" class=\"form-control-label\">".gettext("Partner tags:")."</label></div>\n";
echo "<div class=\"col-8 col-md-6\"><textarea id=\"partner_tags\" name=\"partner_tags\" placeholder=\"".gettext("Partner tags")."\" class=\"form-control\"></textarea></div>\n";
echo "</div>";

echo "<INPUT TYPE=\"hidden\" name=\"page\" id=\"page\" value=\"partners\">";
echo "<INPUT TYPE=\"hidden\" name=\"new\" id=\"new\" value=\"1\">";
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div>\n";
echo "</form></div>";
echo "<script>\n";
echo "$(\"#partner_form\").validate()\n";
echo "</script>\n";
}

$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;
$from=1;
if (isset($_GET['partner_tag']))
$_SESSION['partner_tag']=$dba->escapeStr($_GET['partner_tag']);
if (isset($_GET['del_search_tag']))
unset($_SESSION['partner_tag']);
$SQL="SELECT partner_id,partner_name,contact1_firstname,contact1_surname,contact1_firstname_is_first,contact1_email,contact1_phone,partner_tags FROM partners";
if (isset($_SESSION['partner_tag']))
$SQL.=" WHERE partner_tags LIKE '%".$_SESSION['partner_tag']."%'";
if (isset($_GET['partner_id']) && (int)$_GET['partner_id']>0)
$SQL.=" WHERE partner_id=".(int)$_GET['partner_id'];
$SQL.=" ORDER BY partner_name";
$result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
$from=($pagenumber-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log("page:".$pagenumber." ".$SQL,0);

?>

<div class="card-body">
<table id="bootstrap-data-table" class="table table-striped table-bordered">
<thead>
<tr>
<th></th>
<?php echo "<th>";
if (isset($_SESSION['partner_tag']))
echo " <button type=\"button\" class=\"btn btn-danger btn-sm\" onClick=\"location.href='index.php?page=partners&del_search_tag=1'\">".$_SESSION['partner_tag']." x</button>";


$SQL="SELECT partner_name,partner_id FROM partners ORDER BY partner_name";
$result1=$dba->Select($SQL);
echo " <select name=\"partner_id\" id=\"partner_id\" class=\"form-control\"";
                    echo " onChange=\"{location.href='index.php?page=partners&partner_id='+this.value;
                    };\"";
                    echo " style='display:inline;width:200px;'>\n";
            echo "<option value='all'>".gettext("Contacts");
            if (!empty($result1)){
            foreach($result1 as $row1){
            echo "<option value='".$row1['partner_id']."'";
            if (isset($_GET['partner_id']) && $row1['partner_id']==(int)$_GET['partner_id'] )
            echo " selected";
            echo ">";
            
            echo $row1['partner_name']."\n";
            }}
            echo "</select>\n"; 


echo "</th><th>".gettext("Email")."</th><th>".gettext("Phone")."</th><th>".gettext("Partner tags")."</th></tr>";
?>
</thead>
<tbody>
<?php
if (!empty($result)){
foreach ($result as $row)
{
$from++;
echo "<tr><td>";
echo "<div class=\"user-area dropdown float-right\">\n";
                            
                             echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
                             echo $from;
                             echo " <i class=\"fa fa-bars\"></i>\n";
                             echo "</a>\n";
                             
                             
                             echo "<div class=\"user-menu dropdown-menu\">";
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_partner_detail','".$row['partner_id']."','','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Show details")."</a>";
                            if ($_SESSION['user_level']<3)
                            {
                            echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_partner_detail','".$row['partner_id']."','modify','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Modify")."</a>";
                             }
                             echo "</div>";
    echo "</div>";
   

echo "</td><td onClick=\"ajax_call('show_partner_detail','".$row['partner_id']."','modify','','','".URL."index.php','for_ajaxcall')\">".$row['partner_name']."</td>\n";
if ($row['contact1_firstname_is_first'])
echo "<td>".$row['contact1_firstname']." ".$row['contact1_surname']."</td>\n";
else
echo "<td>".$row['contact1_surname']." ".$row['contact1_firstname']."</td>\n";

echo "<td><a href=\"mailto:".$row['contact1_email']."\">".$row['contact1_email']."</a></td>\n";
echo "<td>".$row['contact1_phone']."</td>\n";
echo "<td>".$row['partner_tags']."</td>\n";
echo "</tr>\n";


}}
echo "</tbody></table></div>";
include(INCLUDES_PATH."pagination.php");

?>




