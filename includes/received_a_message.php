<?php
if ((isset($_POST['received_message1']) && (int) $_POST['sensor_id1']>0) || (int) $_POST['user_id']>0 || isset($_POST['operating'])) {


$post="";
foreach($_POST as $key => $value) {
$post.=$key.":".$value."\n";
}
$file = fopen(INCLUDES_PATH."post.log", "a") or die("Unable to open file!");
fwrite($file, $post);
fclose($file);
$i=1;
if (!isset($_POST['operating'])){

while (isset($_POST['sensor_id'.$i])){
    $need_to_insert=true;
    
            if ((int) $_POST['message_type'.$i]==4) //online: we need need to update only the last seen time
            {
            $SQL="SELECT message_id FROM received_messages WHERE sensor_id=".(int) $_POST['sensor_id'.$i]." AND message_type=4";
            $row=$dba->getRow($SQL);
            if (LM_DEBUG)
            error_log($SQL,0);
            if (!empty($row) && $row['message_id']>0)
            {
            $SQL="UPDATE received_messages SET message_time=now(),user_id_who_checked=0,ip='".$dba->escapeStr($_POST['ip'])."'";
            if (isset($_POST['sensor_value'.$i]))
            $SQL.=",sensor_value=".(float)$_POST['sensor_value'.$i];
            $SQL.=" WHERE message_id=".$row['message_id'];
            $dba->Query($SQL);
            if (LM_DEBUG)
            error_log($SQL,0);
            $need_to_insert=false;
            }

            }
            $SQL="SELECT user_id_who_checked FROM received_messages WHERE sensor_id=".(int) $_POST['sensor_id'.$i]." AND message_type=<3 ORDER BY message_id DESC LIMIT 0,1";
            $row=$dba->getRow($SQL);
            if ($row['user_id_who_checked']>0)
            $need_to_insert=true;
            else
            $need_to_insert=false;
            
            $SQL="INSERT INTO received_messages (message_type,sensor_id,user_id,received_message,user_id_who_checked,ip";
            if (isset($_POST['sensor_value'.$i]))
            $SQL.=",sensor_value";
            $SQL.=") VALUES (";
            $SQL.=(int) $_POST['message_type'.$i].",".(int) $_POST['sensor_id'.$i].",".(int) $_POST["user_id".$i].",".(int) $_POST["received_message".$i].",0,'".$dba->escapeStr($_POST['ip'])."'";
            if (isset($_POST['sensor_value'.$i]))
            $SQL.=",".(float)$_POST['sensor_value'.$i];
            $SQL.=")";
            if ($need_to_insert)
            $result = $dba->Query($SQL);
        
            
            $SQL="SELECT asset_id,min_sensor_value,max_sensor_value FROM iot_sensors WHERE sensor_id=".(int) $_POST['sensor_id'.$i];
            $row=$dba->getRow($SQL);
            $asset_id=$row['asset_id'];
            if ((isset($_POST['sensor_value'.$i]) && $_POST['sensor_value'.$i]>$row['max_sensor_value']) || $row['max_sensor_value']==NULL){
            $SQL="UPDATE iot_sensors SET max_sensor_value=".(float) $_POST['sensor_value'.$i].",max_sensor_value_time=NOW() WHERE sensor_id=".(int) $_POST['sensor_id'.$i];
            $result = $dba->Query($SQL);
            }
            if ((isset($_POST['sensor_value'.$i]) && $_POST['sensor_value'.$i]<$row['min_sensor_value']) || $row['min_sensor_value']==NULL){
            $SQL="UPDATE iot_sensors SET min_sensor_value=".(float) $_POST['sensor_value'.$i].",min_sensor_value_time=NOW() WHERE sensor_id=".(int) $_POST['sensor_id'.$i];
            $result = $dba->Query($SQL);
            }
            if ((int) $_POST['message_type'.$i]<3) //on error or warning we should notify somebody
                if($asset_id>0){
                
                $sender=get_asset_name_from_id($asset_id,'hu');
                $SQL="SELECT assets_users FROM assets where asset_id=".get_whole_path_ids('asset',$asset_id,1)[0];
                if (LM_DEBUG)
                error_log($SQL,0); 
                $row=$dba->getRow($SQL);
                if(!empty($row))
                {
                $users_to_message=json_decode($row['assets_users'],true);
                
                
                foreach ($users_to_message as $user){
                $SQL="SELECT user_level,telegram_chat_id FROM users WHERE user_id=".$user;
                $row=$dba->getRow($SQL);
                if ($row['user_level']<3 && !empty($row['telegram_chat_id'])){// we need to notify only the leaders
                $SQL="INSERT INTO telegram_messages (user_id,sensor_id,received_message,sensor_value,notification_id) VALUES (".$user.",".(int) $_POST['sensor_id'.$i].",".(int) $_POST["received_message".$i].",".(float)$_POST['sensor_value'.$i].",0)";
                $dba->Query($SQL);
                if (LM_DEBUG)
                error_log($SQL,0); 
                }
                }
                
                if(is_user_working($user))
                send_telegram_messages();
                }
                }
$i++;                
}        //while (isset($_POST['asset_id'.$i]))
}//cooling
else
{
$i=1;
while (isset($_POST['operating'.$i])){
if ((int) $_POST['operating'.$i]==1){

$SQL="INSERT INTO operatings (`start_time`,`asset_id`) VALUES (NOW(),".(int)$_POST['asset_id'.$i].")";
$dba->Query($SQL);

}
else if ((int) $_POST['operating'.$i]==0)
{
$SQL="SELECT operating_id,end_time FROM operatings WHERE asset_id=".(int)$_POST['asset_id'].$i." AND end_time is null ORDER BY start_time DESC LIMIT 0,1";
$row=$dba->getRow($SQL);
    if (!empty($row)){
    $SQL="UPDATE operatings SET `end_time`=NOW() WHERE operating_id=".$row['operating_id'];
    $dba->Query($SQL);
    }
}
$i++;
}
}
        if(isset($_POST['user_id']) && $_POST['user_id']>0)
        $sender=get_user_name_from_id($_POST["user_id"]);

        
    
if (LM_DEBUG)
error_log("received message: ".$SQL,0);
$dba->disconnect();
}
?>
