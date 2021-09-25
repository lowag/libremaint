<?php
if (!$id>0 || empty($table))
lm_die("Missing id or table name!".__FILE__." ".__LINE__);
function remove_accent($str) {  
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');  
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');  
        return str_replace($a, $b, $str);  
    }  
      
    function slugString($str) {  
        return strtolower(preg_replace(array('/[^a-zA-Z0-9_ -]/', '/[ -]+/',  '/[_-]+/', '/^-|-$/'), array('', '-', '-', ''), remove_accent($str)));  
    }  
    

$upload_errors=array(
        0=>gettext("There is no error, the file uploaded with success"),
        1=>gettext("The uploaded file exceeds the upload_max_filesize directive in php.ini"),
        2=>gettext("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"),
        3=>gettext("The uploaded file was only partially uploaded"),
        4=>gettext("No file was uploaded"),
        6=>gettext("Missing a temporary folder"),
        7=>gettext("Failed to write file to disk")
); 
$errors="";
 //$v=filesize($_FILES['info_file_name']['error'][0]);
foreach($_FILES['info_file_name']['tmp_name'] as $key => $tmp_name ){
	

  
 if ($_FILES['info_file_name']['error'][$key]>0)
  {
  $errors.=$upload_errors[$_FILES['info_file_name']['error'][$key]]."<br/>";
  
   }else{
 $filename = $key.$_FILES['info_file_name']['name'][$key];  
$info = pathinfo($filename);
if (strtolower($info['extension']=="jpeg"))
$info['extension']="jpg";
$filename =  basename($filename,'.'.$info['extension']);
//remove all characters from the file name other than letters, numbers, hyphens and underscores
//$filename = preg_replace("/[^A-Za-z0-9_-]/", "", $filename);
$filename=preg_replace("/[^a-z0-9_-]/i", "", $filename); 
if ($info['extension']!=""){
$filename.=".".strtolower($info['extension']);

if(move_uploaded_file($_FILES['info_file_name']['tmp_name'][$key], TMP_PATH.$filename)) {
//copy(TMP_PATH.$filename, $info_dir.$filename);
$finfo = new finfo(FILEINFO_MIME);
if (filesize(TMP_PATH.$filename)<MAX_INFO_FILE_SIZE){
//$type = $finfo->file(TMP_PATH.$filename);
//$mime = substr($type, 0, strpos($type, ';'));
$mime=mime_content_type(TMP_PATH.$filename);
if (in_array($mime,ACCEPTED_FILE_TYPES)){
      $sha=sha1_file(TMP_PATH.$filename);
      $SQL="SELECT info_file_id FROM info_files WHERE info_file_sha='".$sha."'";
      if (LM_DEBUG)
error_log($SQL,0);
if (!empty($sha))
      $row = $dba->getRow($SQL);
      $info_file_id=$row['info_file_id'];
      if ($info_file_id>0){
//there has been added th file earlier, so we have to find the first empty "info_file_id".$i column
$SQL="SELECT * from ".$table." WHERE ".$id_column."='".$id."'";
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0); 
$i=1;//the next "info_file_id" column
$n="a";//the first empty place 
$added_earlier="no";//there has been added the file earlier if the value is 1
//print_r($row);
//echo $SQL;
$i=1;
    foreach ($row as $key=>$value){
    if (strstr($key,"info_file_id")){
    $i++;
    }
//echo $key."--->".$value."||";
    if (strstr($key,"info_file_id") && $n=="a" && ($value==0 || $value=="")){
    $n=$key;}
    if (strstr($key,"info_file_id") && $value==$info_file_id)
    $added_earlier=1;
    }
//echo "n: ".$n." there have been earlier: ".$added_earlier;
    if ($n=="a" && $added_earlier=="no"){
//there is no empty column
    $SQL="ALTER TABLE ".$table." add column info_file_id".$i." smallint(6) UNSIGNED";
    $result = $dba->Query($SQL);
if (LM_DEBUG)
error_log($SQL,0);
    $SQL="ALTER TABLE ".$table." ADD INDEX (info_file_id".$i.")";
    $result = $dba->Query($SQL);
    if (LM_DEBUG)
error_log($SQL,0);
    $SQL="UPDATE ".$table." SET info_file_id".$i."='".$row['info_file_id']."' WHERE ".$id_column."='".$id."'";
    $result = $dba->Query($SQL);
    if (LM_DEBUG)
error_log($SQL,0);
    }
//$n=="";
    else if ($added_earlier=="no"){
    $SQL3="UPDATE ".$table." SET ".$n."='".$info_file_id."' WHERE ".$id_column."='".$id."'";
    $result = $dba->Query($SQL3);
    if (LM_DEBUG)
error_log($SQL3,0);
    }
//echo $SQL;
//echo "SQL3:".$SQL3;   
    $notice_board=gettext("This file has been uploaded earlier.");
    if ($added_earlier==1)  
  $notice_board.=gettext("This file has been added earlier.");


}else{
//there is no such file regarding its content

if ($table=="assets"){
  $newfilename=mb_substr(get_asset_name_from_id($id,$lang), 0, 15,"utf8")."_".get_random_string(5);}
else if ($table=="locations"){
  $newfilename=mb_substr(get_location_name_from_id($id,$lang), 0, 15,"utf8")."_".get_random_string(5);}
else if ($table=="products"){
  $newfilename=mb_substr(get_product_name_from_id($id,$lang), 0, 15,"utf8")."_".get_random_string(5);}
else if ($table=="workorders"){
$SQL="SELECT main_asset_id FROM workorders WHERE workorder_id='".$id."'";
$row=$dba->getRow($SQL);
  $newfilename="workorder_".$id."_".get_asset_name_from_id($row['main_asset_id'],$lang)."_".get_random_string(5);}
else if ($table=="works"){
$SQL="SELECT main_asset_id FROM workorder_works WHERE workorder_id='".$id."'";
$row=$dba->getRow($SQL);
$newfilename="workorder_work_".$id."_".get_asset_name_from_id($row['main_asset_id'],$lang)."_".get_random_string(5);}
else if ($table=="stock_movements"){
$SQL="SELECT product_id FROM stock_movements WHERE stock_movement_id='".(int) $id."'";
  $row=$dba->getRow($SQL);
  $newfilename="move_".mb_substr(get_product_name_from_id($row['product_id'],$lang), 0, 15,"utf8")."_".get_random_string(5);}
else if ($table=="pinboard"){
$SQL="SELECT pin_short_".$lang." FROM pinboard WHERE pin_id='".(int) $id."'";
  $row=$dba->getRow($SQL);
  $newfilename=mb_substr($row['pin_short_'.$lang], 0, 15,"utf8")."_".get_random_string(5);}

else
    $newfilename=$filename."_".get_random_string(5);
    
    
  //$newfilename = preg_replace("/[^A-Za-z0-9_-]/", "", $newfilename);
  $newfilename=slugString($newfilename);
  $k=1;
  $pnewfilename=$newfilename;

if ($mime=="image/jpeg" || $mime=="application/pdf" || $mime=="image/png"  || $mime=="image/gif"){//for these types we have to make thumbnails

//if there is a same file name we have to make a different one  
  while (file_exists(INFO_THUMB_PATH."small_".$pnewfilename.".".strtolower($info['extension'])))
  {
  $pnewfilename=$newfilename."_".$k;
  $k++;
  }
  }
else{
while (file_exists(INFO_PATH.$pnewfilename.".".strtolower($info['extension'])))
  {
  $pnewfilename=$newfilename."_".$k;
  $k++;
  }
}
$thumbnail = INFO_THUMB_PATH."small_".$pnewfilename.".".strtolower($info['extension']);
$newfilename=$pnewfilename.".".strtolower($info['extension']);
if (LM_DEBUG)
error_log("filename:".$thumbnail."  newfilename:".$newfilename.__line__,0);
  if (!copy(TMP_PATH.$filename, INFO_PATH.$newfilename)) {
      echo gettext("File copying was not successful: ").$newfilename."\n";
  }else{
copy(INFO_PATH.$newfilename,TMP_PATH.$newfilename);

$p=INFO_PATH.$newfilename;
if (LM_DEBUG)
error_log("filename:".$p,0);
if ($mime=="image/jpeg" || $mime=="image/png" || $mime=="image/gif"){
$data = getimagesize($p);
$width = $data[0];

img_resize(THUMB_IMG_SIZE, $thumbnail, $p);

if ($width>IMG_SIZE)
img_resize(IMG_SIZE, $p, $p);

}
else if ($mime=="application/pdf"){
$im = new imagick($p.'[0]');
$imageprops = $im->getImageGeometry();

$im->resizeImage(THUMB_IMG_SIZE,$imageprops['height']/$imageprops['width']*THUMB_IMG_SIZE,Imagick::FILTER_LANCZOS,1);
$im->setImageFormat('jpeg');

$im->writeImage (substr($thumbnail,0,-3)."jpg"); 
}


  $SQL="INSERT INTO info_files (info_file_name,info_file_sha,info_file_review_en,info_file_review_".$lang.",req_user_level,upload_time,uploaded_by,confidential) VALUES ('".$newfilename."','".$sha."','".$dba->escapeStr($_POST["info_file_review_en"])."','".$dba->escapeStr($_POST["info_file_review_".$lang])."',".(int) $_POST["req_user_level"].",now(),".$_SESSION['user_id'].",".(int) $_POST['confidential'].")";
  $result = $dba->Query($SQL);
  if (LM_DEBUG)
error_log($SQL,0);
  $info_file_id = $dba->insertedId();
$n="a";
$SQL="SELECT * from ".$dba->escapeStr($table)." WHERE ".$dba->escapeStr($id_column)."='".(int) $id."'";
$row=$dba->getRow($SQL);
if (LM_DEBUG)
error_log($SQL,0);
$i=1;
foreach ($row as $key=>$value){
    if (strstr($key,"info_file_id")){
    $i++;
    }
    if (strstr($key,"info_file_id") && $n=="a" && ($value==0 || $value==""))
    $n=$key;
    }
    if ($n=="a"){
    $SQL="ALTER TABLE ".$dba->escapeStr($table)." add column info_file_id".$i." smallint(6) UNSIGNED";
    $result = $dba->Query($SQL);
    if (LM_DEBUG)
error_log($SQL,0);
    $SQL="ALTER TABLE ".$dba->escapeStr($table)." ADD INDEX (info_file_id".$i.")";
    $result = $dba->Query($SQL);
    if (LM_DEBUG)
error_log($SQL,0);
    $SQL="UPDATE ".$dba->escapeStr($table)." SET info_file_id".$i."='".(int) $info_file_id."' WHERE ".$id_column."='".(int) $id."'";
    $result = $dba->Query($SQL);
    if (LM_DEBUG)
error_log($SQL,0);
    }//$n=="";
    else{
    $SQL2="UPDATE ".$dba->escapeStr($table)." SET ".$n."='".(int) $info_file_id."' WHERE ".$dba->escapeStr($id_column)."='".(int) $id."'";
    $result = $dba->Query($SQL2);
    if (LM_DEBUG)
    error_log($SQL,0);
    }//echo "SQL2.:".$SQL2;
//echo "SQL1.:".$SQL;
if (touch(INFO_PATH."there_was_a_file_upload.txt")) //we should make backup
lm_info(gettext("File uploading was successful"));
     }

}//end:there is no such file regarding its content

}//valid mime
else{
echo gettext("Invalid file type! ".$mime);
unlink(TMP_PATH.$filename);
}//invalid mime

}//jó fájlméret
else
lm_info(gettext("File size was too large!"));

     }else{
lm_info(gettext("There was an error during the operation! ".$sizemax." ".$postmax." hh".$v));
}}
else{
lm_info(gettext("There is no file extension!"));

}
}//end:foreach($_FILES['info_file_name']['tmp_name'] as $key => $tmp_name ){
}//end: if ($_FILES['info_file_name']['size'][$key]>0)
if ($errors!="")
lm_info($errors);

?>
