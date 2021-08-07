<div id="right-panel" class="right-panel"><!--ok-->
        <header id="header" class="header">
            <div class="header-menu">
                <div class="col-sm-7">
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
                    <div class="header-left">
               <?php 
 if ($req_page=="partners"){
               ?>
 <button class="search-trigger"><i class="fa fa-search"></i></button>
 <div class="form-inline"><!--ok-->
 <form class="search-form">
 <input class="form-control mr-sm-2" type="text" placeholder="Search ..." aria-label="Search" id="search" name="search" onfocus="this.value = this.value;" onkeydown="
            this.onkeydown=function(e){
    if(e.keyCode==13){
    event.preventDefault();
if (this.value.length>3)
location.href='index.php?page=partners&partner_tag='+(this.value);
}}
            " value="<?php 
            if (isset($_GET['partner_tag']))
            echo $_GET['partner_tag'];
            ?>">
                                <button class="search-close" type="submit"><i class="fa fa-close"></i></button>
                            </form>
                        </div><!--id="form-inline"-->
                    
<?php

}
$SQL="SELECT users_assets FROM users WHERE user_id=".$_SESSION['user_id'];
$row=$dba->getRow($SQL);
if (!empty($row['users_assets']))
$users_assets=json_decode($row['users_assets'],true);

if ($_SESSION['user_level']<4 && IOT_SUPPORT){
if (!empty($users_assets))
{
$SQL="SELECT * FROM received_messages LEFT JOIN iot_sensors ON received_messages.sensor_id=iot_sensors.sensor_id WHERE 1=1";
$SQL.=" AND main_asset_id IN (";
$need_a_comma=false;
foreach ($users_assets as $key=>$value){
if ($need_a_comma)
$SQL.=",";
$need_a_comma=true;
$SQL.=$value;

}
$SQL.=")";

$SQL.=" AND message_type>2";
$SQL.=" AND user_id_who_checked=0 LIMIT 0,10";
$result=$dba->Select($SQL);
$message_number=$dba->affectedRows();

                           

?>
                        
                        <div class="dropdown for-message"><!--ok-->
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="message" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell">
<?php
                                echo '<span class="count bg-danger">'.$message_number.'</span>';
?></i></button>
                            <div class="dropdown-menu" aria-labelledby="notification"><!--ok-->
<?php                               
echo '<p class="red">'.gettext('You have ').$message_number." ".gettext(' message').'</p>';
if ($message_number>0){
foreach ($result as $row){
$SQL="SELECT message_".$lang." FROM messages WHERE message_id=".(int) $row["received_message"];
    $row1=$dba->getRow($SQL);

echo '<a class="dropdown-item media bg-flat-color-1" href="index.php?page=messages">';
                                echo '<i class="fa fa-check"></i><p>';
                                $n="";
                foreach (get_whole_path("asset",$row['asset_id'],1) as $k){
                if ($n=="") // the first element is the main asset_id -> ignore it
                $n=" ";
                else
                $n.=$k."-><wbr>";}

                if ($n!="")
                echo substr($n,0,-7).': '.$row1['message_'.$lang];
                                
                                echo '</p></a>';

}
}
?>                           </div><!--dropdown menu-->
                        </div><!--dropdown for message-->
<?php 
}
}

if (OPERATOR_NOTIFICATIONS_SUPPORT && isset($users_assets) && $_SESSION['user_level']<3){
$SQL="SELECT notification_short_".$lang.",main_asset_id FROM notifications WHERE  notification_status=1";
$SQL.=" AND main_asset_id IN ('".join("','",$users_assets)."')";

$result=$dba->Select($SQL);
$not_number=$dba->affectedRows();
if ($not_number>0){
?>
                        <div class="dropdown for-notification"><!--ok-->
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="notification"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ti-email"></i>
                                <span class="count bg-primary"><?php echo $not_number; ?></span>
                            </button>
 <div class="dropdown-menu" aria-labelledby="notification"><!--ok-->
<?php                               
echo '<p class="red">'.gettext('You have')." ".$not_number." ".gettext(' notification').'</p>';

foreach ($result as $row){

echo '<a class="dropdown-item media bg-flat-color-1" href="index.php?page=notifications">';
                                echo '<i class="fa fa-check"></i><p>';
                          
                echo get_asset_name_from_id($row['main_asset_id'],$lang).': '.$row['notification_short_'.$lang];
                                
                                echo '</p></a>';

}

?>                           </div><!--dropdown menu-->                           
                        </div><!--dropdown for notification-->
                    
 <?php
 }
 }
if ($req_page=="stock"){
echo "<script src=\"".INCLUDES_LOC."luhn.js\"></script>\n";

 echo gettext("Product id").": <INPUT TYPE='text' autocomplete='off' name='prod_id' id='prod_id' VALUE='";

if (isset($_GET['product_id'])){
echo luhn($_GET['product_id']);
}
echo "' SIZE='3' onKeyPress=\"this.onkeydown=function(e){
    if(e.keyCode==13){
    event.preventDefault();
if (Validate(this.value))
location.href='index.php?page=stock&product_id='+(this.value).substring(0,this.value.length-1);
else
alert ('".gettext("Wrong number! Check it!")."');
   return false; 
    }
}\">";

}
?>                   
                </div></div>

                <div class="col-sm-5"><!--->
                    <div class="user-area dropdown float-right"><!--ok-->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php
                            if (is_user_working($_SESSION['user_id']))
                            {
                            send_telegram_messages();
                            echo "<strong>".gettext("Logged as:")." ".$_SESSION['username']."</strong>";
                            }
                            else
                            echo gettext("Logged as:")." ".$_SESSION['username'];?>
                        </a>
                        <div class="user-menu dropdown-menu"><!--ok-->
<?php 
if (isset($_SESSION['MODIFY_USER'])){
?>
                        
                            <a class="nav-link" href="index.php?page=users&modify=1"><i class="fa fa-user"></i><?php echo gettext("My Profile");?></a>
<?php } ?>
                            <a class="nav-link" href="index.php?page=settings"><i class="fa fa-cog"></i> <?php echo gettext("Change password");?></a>

                            <?php echo "<a class=\"nav-link\" href=\"".URL."index.php?logout\"><i class=\"fa fa-power-off\"></i>".gettext("Logout")."</a>";?>
                        </div><!--id="user-menu dropdown-menu"-->
                    </div><!--user-area-dropdown -->
                    <div class="language-select dropdown" id="language-select"><!--ok-->
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown"  id="language" aria-haspopup="true" aria-expanded="true">
                            <i class="flag-icon flag-icon-<?php if ($lang=="en") echo "gb"; else echo $lang;?>"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="language"><!--ok-->
                            <div class="dropdown-item"><!--ok-->
                              <a href="index.php?lang=hu"><span class="flag-icon flag-icon-hu"></span></a>
                            </div><!--dropdown-item-->
                            
                            <div class="dropdown-item"><!--ok-->
                              <a href="index.php?lang=en"><i class="flag-icon flag-icon-gb"></i></a>
                            </div><!--dropdown-item-->
                            
                        </div><!--dropdown-menu-->
                    </div><!--id=language-select-->

                </div><!--id="col-sm5"-->
            </div><!--id="right-panel"-->

        </header><!-- /header -->
        <!-- Header-->
<?php


?>
