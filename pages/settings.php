<?php
if (!empty($_POST['new_password']) && $_POST['new_password']==$_POST['new_password_rep'] && is_it_valid_submit())
{
$SQL="SELECT password FROM users WHERE user_id=".$_SESSION['user_id'];
$row=$dba->getRow($SQL);
if (password_verify($_POST['old_password'],$row['password']))
{
$SQL="UPDATE users SET password='".password_hash($_POST['new_password'],PASSWORD_DEFAULT)."' WHERE user_id='".$_SESSION['user_id']."'";

if ($dba->Query($SQL))
lm_info(gettext("The password have been updated."));
else
lm_info(gettext("Failed to updated password."));
}
}
?>

<div class="card">
<div class="card-header">
<?php
echo gettext("Change password");
?>
</div>
<div class="card-body card-block">
<form action="index.php" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php
echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"old_password\" class=\"form-control-label\">".gettext("Old password:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-3\">";
        echo "<input type=\"password\" name=\"old_password\" id=\"old_password\" class=\"form-control\" >";
    echo "</div>";
echo "</div>";    

echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"new_password\" class=\"form-control-label\">".gettext("New password:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-3\">";
        echo "<input type='password' name=\"new_password\" id=\"new_password\" class=\"form-control\" >";
    echo "</div>";
echo "</div>";

echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"new_password_rep\" class=\"form-control-label\">".gettext("Repeat new password:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-3\">";
        echo "<input type='password' name=\"new_password_rep\" id=\"new_password_rep\" class=\"form-control\" >";
    echo "</div>";
echo "</div>"; 
echo "</div>";

echo "<INPUT TYPE=\"hidden\" name=\"page\" id=\"page\" VALUE=\"settings\">";
echo "<INPUT TYPE=\"hidden\" name=\"valid\" id=\"valid\" VALUE=\"".$_SESSION['tit_id']."\">";

echo "<div class=\"card-footer\"><button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i> ".gettext("Submit")." </button>\n";
echo "<button type=\"reset\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> ".gettext("Reset")." </button></div>\n";
echo "</form></div>";
