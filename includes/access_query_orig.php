<?php
$a_prime_number=?????;
$ok_remainder=?????;
$nok_remainder=?????;

if (isset($_POST['access_query']) && (int) $_POST['ep']>0 && isset($_POST['ucid'])) {
$file = fopen(INCLUDES_PATH."entry.log", "a") or die("Unable to open file!");
fwrite($file,date("Y-m-d H:i:s")." ");
fwrite($file,$dba->escapeStr($_POST['ucid'])." ");

$SQL="SELECT user_id FROM users WHERE users_card_id='".$dba->escapeStr($_POST['ucid'])."'";
$row=$dba->getRow($SQL);
fwrite($file,$SQL."\n");

if ($row['user_id']>0)
{
    $SQL="SELECT assets_entry_users,entry_point FROM assets WHERE asset_id=".(int) $_POST['ep'];
    $row1=$dba->getRow($SQL);
    fwrite($file,$SQL);
    $assets_entry_users=json_decode($row1['assets_entry_users'],true); 
    if ($row1['entry_point'] && in_array($row['user_id'],$assets_entry_users))
    {
    echo rand(300,3000)*$a_prime_number+$ok_remainder;
    fwrite($file,"OK\n\n");
    }else
    {
    echo rand(300,3000)*$a_prime_number+$nok_remainder;
    fwrite($file,"NOK\n\n");
    }
   fclose($file);         
}
else
echo rand(300,3000)*$a_prime_number+$nok_remainder;
$dba->disconnect();
}else
echo rand(300,3000)*$a_prime_number+$nok_remainder;
?>
