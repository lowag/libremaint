<?php

if (isset($_POST['real_stock_quantity']) && $_SESSION['STOCK-TAKING'] && is_it_valid_submit()){

$SQL="SELECT product_id,stock_quantity,stock_location_id FROM stock WHERE stock_id='".(int) $_POST['stock_id']."'";
$row=$dba->getRow($SQL);
$SQL="UPDATE stock SET stock_quantity='".floatval($_POST['real_stock_quantity'])."', inventory_time=NOW(), inventory_user_id='".$_SESSION['user_id']."' ,stock_place='".$dba->escapeStr($_POST['stock_place'])."' WHERE stock_id='".(int) $_POST['stock_id']."'";
if (!$dba->Query($SQL))
    lm_die("Error: ".$SQL);
    $diff= $row['stock_quantity']-floatval($_POST['real_stock_quantity']);
    
    if ($diff>0){
    
    $SQL="INSERT INTO stock_movements (product_id,from_stock_location_id,to_stock_location_id,stock_movement_quantity) ";
$SQL.="VALUES (".$row['product_id'].",".$row['stock_location_id'].",0,".$diff.")";
$dba->Query($SQL);

    
    }else if ($diff<0){
    
    $SQL="INSERT INTO stock_movements (product_id,to_stock_location_id,from_stock_location_id,stock_movement_quantity) ";
    $SQL.="VALUES (".$row['product_id'].",".$row['stock_location_id'].",0,".abs($diff).")";

    $dba->Query($SQL);
    
    }
$SQL="INSERT INTO stocktaking (user_id,product_id,stock_id,quantity,stocktaking_time) VALUES ('".$_SESSION['user_id']."','".$row['product_id']."','".(int) $_POST['stock_id']."','".floatval($_POST['real_stock_quantity'])."',NOW())";
 if (!$dba->Query($SQL))
    lm_die("Error:".$SQL." ".$dba->err_msg);
 }
else if (isset($_POST['modify_min_stock_quantity']) && is_it_valid_submit() && $_POST['stock_id']>0){

$SQL="UPDATE stock SET min_stock_quantity=".floatval($_POST['min_stock_quantity'])." WHERE stock_id=".(int) $_POST['stock_id'];
$result=$dba->Query($SQL);

}



else if (isset($_POST['product_moving_from_stock_to_stock']) && is_it_valid_submit()){
$sum_quantity=0;
$mov_category_id=get_category_id_from_id((int) $_POST['product_id']);
$mov_subcategory_id=get_subcategory_id_from_id((int) $_POST['product_id']);

foreach ($_POST as $key=>$value){
if (strstr($key,"quantity_") && $value>0 && (int) $_POST['product_id']>0){
$stock_place=$dba->escapeStr(explode("quantity_","$key")[1]);
$SQL="UPDATE stock SET stock_quantity=stock_quantity-".floatval($value)." WHERE product_id=".(int) $_POST['product_id']." AND stock_location_id=".(int) $_POST['stock_location_id']." AND stock_place='".$stock_place."'";
$dba->Query($SQL);
if (LM_DEBUG)
    error_log($SQL,0);
$SQL="SELECT stock_id FROM stock WHERE product_id=".(int) $_POST['product_id']." AND stock_location_id=".(int) $_POST['dest_stock_location_id']." AND stock_place='".$dba->escapeStr($_POST['dest_stock_place'])."'";
$row=$dba->getRow($SQL);
if (LM_DEBUG)
    error_log($SQL,0);
if ($dba->affectedRows()==1){
$SQL="UPDATE stock SET stock_quantity=stock_quantity+".floatval($value)." WHERE product_id=".(int) $_POST['product_id']." AND stock_location_id=".(int) $_POST['dest_stock_location_id']." AND stock_place='".$dba->escapeStr($_POST['dest_stock_place'])."'";
}else{
$SQL="INSERT INTO stock (product_category_id,product_subcategory_id,stock_location_id,stock_place,product_id,stock_quantity,item_created) VALUES ";
$SQL.="(".$mov_category_id.",".$mov_subcategory_id.",".(int) $_POST['dest_stock_location_id'].",'".$dba->escapeStr($_POST['dest_stock_place'])."',".(int) $_POST['product_id'].",".floatval($value).",NOW())";

}
$dba->Query($SQL);
$sum_quantity+=floatval($value);
if (LM_DEBUG)
    error_log($SQL,0);
}//if (strstr($key,"quantity_") && $value>0){
}//foreach
$SQL="INSERT INTO stock_movements (product_id,from_stock_location_id,to_stock_location_id,stock_movement_quantity) ";
$SQL.="VALUES (".(int) $_POST['product_id'].",".(int) $_POST['stock_location_id'].",".(int) $_POST['dest_stock_location_id'].",".$sum_quantity.")";
$dba->Query($SQL);
if (LM_DEBUG)
    error_log($SQL,0);
lm_info("The moving was succesfull.");




}


else if (isset($_POST['stock_location_id'])){//put product into stock
    $SQL="SELECT stock_id FROM stock WHERE product_id='". (int) $_POST['product_id']."' AND stock_location_id='".(int) $_POST['stock_location_id']."' AND stock_place='".$dba->escapeStr($_POST['stock_place'])."'";
    $row=$dba->getRow($SQL);
    if (1==$dba->affectedRows())
        {
        $SQL="UPDATE stock SET stock_quantity=stock_quantity+".(float) $_POST['quantity'].",min_stock_quantity=".(float) $_POST['min_stock_quantity']." WHERE stock_id='".(int) $row['stock_id']."'";
        }
    else{

    $SQL="INSERT INTO stock (product_id,product_category_id,product_subcategory_id,stock_location_id,stock_place,stock_quantity,min_stock_quantity,item_created) VALUES";
    $SQL.="(".(int) $_POST['product_id'].",";
    $SQL.=(int) $_POST['category_id'].",";
    $SQL.=(int) $_POST['subcategory_id'].",";
    $SQL.=(int) $_POST['stock_location_id'].",";
    $SQL.="'".$dba->escapeStr($_POST['stock_place'])."',";
    $SQL.=(float) $_POST['quantity'].",";
    $SQL.=(float) $_POST['min_stock_quantity'].",";
    $SQL.="NOW())";
    
    }

    if ($dba->Query($SQL))
            echo "<div class=\"card\">".gettext("The new product has been placed to the stock.")."</div>";
            else
            echo "<div class=\"card\">".gettext("Failed to place new product to the stock. ").$dba->err_msg." ".$SQL."</div>";
    if (LM_DEBUG)
    error_log($SQL,0);
    if ($_POST['partner_id']=='new'){
        $SQL="INSERT INTO partners (partner_name,partner_address,partner_created) VALUES ";
        $SQL.="('".$dba->escapeStr($_POST['partner_name'])."','".$dba->escapeStr($_POST["partner_address"])."',NOW())";
        $dba->Query($SQL);
        if (LM_DEBUG)
        error_log($SQL,0);
        $partner_id=$dba->insertedId();
        }else
        $partner_id=(int) $_POST['partner_id'];
$SQL="INSERT INTO stock_movements (from_partner_id,product_id,stock_movement_quantity,to_stock_location_id,stock_movement_time) VALUES ";
$SQL.="('".$partner_id."',";
$SQL.="'".(int) $_POST['product_id']."',";
$SQL.="'".(float) $_POST['quantity']."',";
$SQL.="'".(int) $_POST['stock_location_id']."','";
$SQL.=$dba->escapeStr($_POST['stock_movement_time'])."')";
$dba->Query($SQL);
if (LM_DEBUG)
error_log($SQL,0);
}
else if (isset($_POST['modal_product_type_'.$lang]) && $_POST['modal_product_type_'.$lang]!="" && is_it_valid_submit() && isset($_SESSION['ADD_PRODUCT'])){// add new product from modal_add_new_product_form.php
if (!empty($dba->escapeStr($_POST['modal_new_manufacturer']))){
$SQL="INSERT INTO manufacturers (manufacturer_name) VALUES ('".$dba->escapeStr($_POST['modal_new_manufacturer'])."')";
if ($dba->Query($SQL)){
lm_info(gettext("The new manufacturer has been added."));
$manufacturer_id=$dba->insertedId();
}
else
lm_info(gettext("Failed to save new manufacturer.")." ".$dba->err_msg);
}

    $SQL="INSERT INTO products (category_id,subcategory_id,";
    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
        $SQL.="product_type_".LANG2.",product_properties_".LANG2.",";
    $SQL.="product_type_".$lang.", product_properties_".$lang." , manufacturer_id,product_stockable,quantity_unit,display) VALUES (";
    $SQL.=(int) $_POST['modal_category_id'].",";
    $SQL.=(int) $_POST['modal_subcategory_id'].",";
    if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2']){
        $SQL.="'".$dba->escapeStr($_POST['modal_product_type_'.LANG2])."',";
        $SQL.="'".$dba->escapeStr($_POST['modal_product_properties_'.LANG2])."',";
        }
    $SQL.="'".$dba->escapeStr($_POST['modal_product_type_'.$lang])."',";
    $SQL.="'".$dba->escapeStr($_POST['modal_product_properties_'.$lang])."',";
    if (!empty($_POST['modal_new_manufacturer']))
    $SQL.="'".$manufacturer_id."',";
    else
    $SQL.=(int) $_POST['modal_manufacturer_id'].",";
    $SQL.=(int) $_POST['modal_product_stockable'].",";
    $SQL.=$_POST['modal_quantity_unit'].",";
    $display=0;

    foreach ($_POST["display"] as $key =>$value){
    $display+=$value;
    }
    $SQL.=$display.")";
    if (LM_DEBUG)
            error_log($SQL,0); 
        if ($dba->Query($SQL))
        {
        lm_info(gettext("The new product has been saved."));
            if (isset($_POST['category_id'])){
           $SQL="INSERT INTO stock (product_id,product_category_id,product_subcategory_id,stock_location_id,stock_place,stock_quantity,item_created) VALUES";
            $SQL.="(".(int) $dba->insertedId().",";
            $SQL.=(int) $_POST['category_id'].",";
            $SQL.=(int) $_POST['subcategory_id'].",";
            $SQL.=(int) $_POST['stock_location_id'].",";
            $SQL.="'".$dba->escapeStr($_POST['stock_place'])."',";
            $SQL.=(float) $_POST['quantity'].",";
            $SQL.="NOW())";
            if ($dba->Query($SQL)){
        lm_info(gettext("The product has been placed in stock."));
        }
        if ($_POST['partner_id']=='new'){
        $SQL="INSERT INTO partners (partner_name,partner_address,partner_created) VALUES ";
        $SQL.="('".$dba->escapeStr($_POST['partner_name'])."','".$dba->escapeStr($_POST["partner_address"])."',NOW())";
        $dba->Query($SQL);
        if (LM_DEBUG)
        error_log($SQL,0);
        $partner_id=$dba->insertedId();
        }else
        $partner_id=(int) $_POST['partner_id'];
        $SQL="INSERT INTO stock_movements (from_partner_id,product_id,stock_movement_quantity,to_stock_location_id,stock_movement_time) VALUES ";
        $SQL.="(".$partner_id.",";
        $SQL.=(int) $_POST['product_id'].",";
        $SQL.=(float) $_POST['quantity'].",";
        $SQL.=(int) $_POST['stock_location_id'];
        $SQL.="'".$dba->escapeStr($_POST['stock_movement_time'])." 00:00:00')";
        $dba->Query($SQL);
        if (LM_DEBUG)
error_log($SQL,0);
        }
            else if (isset($_POST['modal_destination'])){
            echo "<script>ajax_call('into_stock','".$dba->insertedId()."','".(int) $_POST['modal_category_id']."','".(int) $_POST['modal_subcategory_id']."','stock','".URL."index.php','for_ajaxcall')</script>";
            }
        }
            else
            lm_info(gettext("Failed to save new product ".$dba->err_msg));


            

}
$category_id=lm_isset_int('category_id'); 

 if ($category_id>0){
 $_SESSION['category_id']=$category_id;
 unset($_SESSION['subcategory_id']);
 unset($_SESSION['stock_location_id']);
 }
 else if (isset($_GET['category_id']) && $_GET['category_id']=='all')
 unset($_SESSION['category_id']);

 $subcategory_id=lm_isset_int('subcategory_id'); 
  if ($subcategory_id>0)
 $_SESSION['subcategory_id']=$subcategory_id;
 else if (isset($_GET['subcategory_id']) && $_GET['subcategory_id']=='all')
 unset($_SESSION['subcategory_id']);
 
 $stock_location_id=lm_isset_int('stock_location_id'); 
  if ($stock_location_id>0)
 $_SESSION['stock_location_id']=$stock_location_id;
 else if (isset($_GET['stock_location_id']) && $_GET['stock_location_id']=='all')
 unset($_SESSION['stock_location_id']);
 
 

if (isset($_GET['into_stock']) && isset($_SESSION['PUT_PRODUCT_INTO_STOCK']))
{

?>
<div id='for_ajaxcall'>

<div class="card">
    
        <?php
       
            echo "<div class=\"row form-group\">";
            echo "<div class=\"col col-md-3\"><label for=\"category_id\" class=\"form-control-label\">".gettext(" Category:")."</label></div>";

            echo "<div class=\"col col-md-2\">";
            echo "<select name=\"category_id\" id=\"category_id\" class=\"form-control\" onChange=\"ajax_call('into_stock','',this.value,0,'stock','".URL."index.php','for_ajaxcall')\">\n";
            $SQL="SELECT category_id,category_name_".$lang." FROM categories WHERE category_parent_id=0";
            $SQL.=" ORDER BY category_name_".$lang;
            if (LM_DEBUG)
            error_log($SQL,0);
            $result=$dba->Select($SQL);
            echo "<option value=\"0\">".gettext("Please select")."</option>\n";
            foreach ($result as $row){
            if ($row["category_name_".$lang]!="")
            echo "<option value=\"".$row["category_id"]."\">".$row["category_name_".$lang]."</option>\n";
            else
            echo "<option value=\"".$row["category_id"]."\">".$row["category_name_en"]."</option>\n";

            }
            echo "</select></div></div>";
        
        
        
    
echo "</div></div>";
}else
echo "<div id='for_ajaxcall'></div>";

?>



<div class="card-body">



<table id="stock-table" class="table table-striped table-bordered">
<thead>
<tr>

<?php 
echo "<th>"; 
echo "<div class=\"dropdown for-notification\">";

?>
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="notification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo gettext("S"); ?>
                                
                            </button>
                            <div class="dropdown-menu" aria-labelledby="notification">
                                <a class="dropdown-item media bg-flat-color-10" href="index.php?page=stock&only_in_stock=0">
                                <i class="fa fa-warning"></i>
                               
                                <p><?php echo gettext("All"); ?></p>
                            </a>
                            
                            
                                <a class="dropdown-item media bg-flat-color-4" href="index.php?page=stock&only_in_stock=1">
                                <i class="fa fa-warning"></i>
                               
                                <p><?php echo gettext("Only in stock"); ?></p>
                                </a>
                            
                            
                                <a class="dropdown-item media bg-flat-color-6" href="index.php?page=stock&only_in_stock=2">
                                    <i class="fa fa-warning"></i>
                                
                                <p><?php echo gettext("Orderable"); ?></p>
                                </a>
                            </div>

                            </div>
                        <?php
                        
echo "</th>";

if (isset($_GET['product_id']) && (int) $_GET['product_id']>0)
            {
            $category_id=get_category_id_from_id((int) $_GET['product_id']);
            $subcategory_id=get_subcategory_id_from_id((int) $_GET['product_id']);
                        }
            else
            {
            $category_id=0;
            $subcategory_id=0;
            }
    
echo "<th>".gettext("Product");
 $SQL="SELECT stock.product_category_id,category_name_".$lang." FROM stock LEFT JOIN categories on categories.category_id=stock.product_category_id WHERE stock_quantity>0 group by product_category_id";
    $result=$dba->Select($SQL);
    if (LM_DEBUG)
    error_log($SQL,0);
    echo "<select name=\"category_id\" id=\"category_id\" class=\"form-control\"";
            echo " onChange=\"location.href='index.php?page=stock&category_id='+this.value\"";
            echo " style='display:inline;width:200px;position:relative;left:20px'>\n";
    echo "<option value='all'>".gettext("All categories");
    foreach($result as $row){
    echo "<option value='".$row['product_category_id']."'";
    if ((isset($_SESSION['category_id']) && $row['product_category_id']==$_SESSION['category_id']) || ($category_id>0 && $category_id==$row['product_category_id']))
    echo " selected";
    echo ">".$row['category_name_'.$lang]."\n";
    }
    echo "</select>\n"; 
    
    if (isset($_SESSION['category_id']) && $_SESSION['category_id']>0)
    {
    $SQL="SELECT distinct(stock.product_subcategory_id) as category_id,category_name_".$lang." FROM stock LEFT JOIN categories on categories.category_id=stock.product_subcategory_id WHERE stock_quantity>0";
    
    $SQL.=" AND stock.product_category_id='".$_SESSION['category_id']."' ORDER BY category_name_".$lang;
  
    $result=$dba->Select($SQL);
    if (LM_DEBUG)
    error_log($SQL,0);
    if ($dba->affectedRows()>0)
    {
    echo " <select name=\"subcategory_id\" id=\"subcategory_id\" class=\"form-control\"";
            echo " onChange=\"location.href='index.php?page=stock&category_id=".$_SESSION['category_id']."&subcategory_id='+this.value\"";
            echo " style='display:inline;width:150px;'>\n";
    echo "<option value='all'>".gettext("All categories");
    foreach($result as $row){
    echo "<option value='".$row['category_id']."'";
    if ((isset($_SESSION['subcategory_id']) && $row['category_id']==$_SESSION['subcategory_id']) || $subcategory_id==$row['category_id'])
    echo " selected";
    echo ">".$row['category_name_'.$lang]."\n";
    }
    echo "</select>\n"; 
    }
    }
    echo "</th>";
echo "<th><SELECT name='stock_location_id' id='stock_location_id' class=\"form-control\"";
echo " onChange=\"location.href='index.php?page=stock&stock_location_id='+this.value\"";
echo ">";
echo "<option value='all'>".gettext("ALL location");
$SQL="SELECT distinct(stock_location_id),location_name_".$lang." FROM stock LEFT JOIN locations ON locations.location_id=stock.stock_location_id WHERE stock_location_id>0 AND stock_quantity>0 ";
if (isset($_SESSION['category_id']))
$SQL.="AND product_category_id=".$_SESSION['category_id'];

$SQL.=" ORDER BY location_name_".$lang;
$result=$dba->Select($SQL);
if ($dba->affectedRows()>0)
{
foreach ($result as $row){
    echo "<option value='".$row['stock_location_id']."'";
    if (isset($_SESSION['stock_location_id']) && $row['stock_location_id']==$_SESSION['stock_location_id'])
    echo " selected";
    echo ">".$row['location_name_'.$lang];
}}
echo "</options></select>";
echo "</th>";
echo "<th>".gettext("Quantity")."</th>";

echo "</tr>";
?>
</thead>
<tbody>
<?php

$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;
if ($pagenumber>0)
$_SESSION['pagenumber']=$pagenumber;
else
unset($_SESSION['pagenumber']);

$only_in_stock=lm_isset_int('only_in_stock');
if ($only_in_stock>=0)
$_SESSION['only_in_stock']=$only_in_stock;

$SQL="SELECT stock_id,stock.product_id,stock_location_id,stock_location_asset_id,stock_location_partner_id,stock_place,stock_quantity,products.info_file_id1,product_stockable,min_stock_quantity, inventory_time, inventory_user_id FROM stock LEFT JOIN products on products.product_id=stock.product_id WHERE 1=1";

if (isset($_GET['product_id']) && (int)$_GET['product_id']>0){
$SQL.=" AND stock.product_id=".(int) $_GET['product_id'];
}else{
if (isset($_SESSION['category_id']) && $_SESSION['category_id']>0)
$SQL.=" AND stock.product_category_id='".$_SESSION['category_id']."'";
if (isset($_SESSION['subcategory_id']) && $_SESSION['subcategory_id']>0)
$SQL.=" AND stock.product_subcategory_id='".$_SESSION['subcategory_id']."'";

if (isset($_SESSION['stock_location_id']) && $_SESSION['stock_location_id']>0)
$SQL.=" AND stock_location_id='".$_SESSION['stock_location_id']."'";

if ($_SESSION['only_in_stock']==1)
$SQL.=" AND stock_quantity>0";

if ($_SESSION['only_in_stock']==2)
$SQL.=" AND stock_quantity<=min_stock_quantity AND min_stock_quantity>0";


$SQL.=" ORDER BY product_type_".$lang;

}

$result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
if (LM_DEBUG)
    error_log($SQL,0);
$from=($_SESSION['pagenumber']-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);
if (LM_DEBUG)
error_log($SQL,0);
if (!empty($result)){
$now=new datetime('now');
foreach ($result as $row)
{
    $from++;
    echo "<tr";
        
    if ($row['min_stock_quantity']>0 && $row['stock_quantity']<$row['min_stock_quantity'])
    echo " class=\"bg-flat-color-2\"";
    
    else if ($row['min_stock_quantity']>0 && $row['stock_quantity']==$row['min_stock_quantity']) 
    echo " class=\"bg-flat-color-3\"";
    echo ">\n";
    echo "<td>\n";
    
   if (isset($_SESSION["SEE_FILE_OF_PRODUCT"]) && $row['info_file_id1']>0){
     echo "<a href=\"javascript:ajax_call('show_info_files','".$row['product_id']."','products','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-info\"></i> ";
    echo "</a>";
    }
    echo "<div class=\"user-area dropdown float-right\">\n";
                            
                             echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
                             echo $from;
                             echo " <i class=\"fa fa-bars\"></i>\n";
                             echo "</a>\n";
                             
                            echo "<div class=\"user-menu dropdown-menu\">";

                             if (isset($_SESSION["SEE_FILE_OF_PRODUCT"]) && $row['info_file_id1']>0){
                            echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_info_files','".$row['product_id']."','products','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Show files")."</a>";}
                             if (isset($_SESSION["ADD_PRODUCT_FILE"])){
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('add_file',".$row['product_id'].",'products','stock','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ".gettext("Add file")."</a>";
                             }
                             if (isset($_SESSION['SEE_ASSETS'])){
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_assets_with_this_product',".$row['product_id'].",'products','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ".gettext("Show assets with this")."</a>";
                             }
                             
                             if (isset($_SESSION["SEE_PRODUCT_MOVING"])){
                            echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_stock_movements',".$row['product_id'].",'','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Stock movements")."</a>";
                             
                             }
                             if (isset($_SESSION["ADD_WORKREQUEST"]) && $row['product_stockable']==2 && $row['stock_location_id']>0){
                            echo "<a class=\"nav-link\" href=\"index.php?page=workrequests&new=1&product_id_to_refurbish=".$row['product_id']."\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Refurbish")."</a>";
                             
                             }
                              if (isset($_SESSION["TAKE_PRODUCT_FROM_STOCK"])){
                            echo "<a class=\"nav-link\" href=\"javascript:ajax_call('product_moving_from_stock_to_stock',".$row['product_id'].",".$row['stock_location_id'].",'','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Product moving")."</a>";
                             }
                             if (isset($_SESSION["STOCK-TAKING"])){
                            echo "<a class=\"nav-link\" href=\"javascript:ajax_call('stocktaking',".$row['stock_id'].",'','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Stocktaking")."</a>";
                             }
                             echo "</div>";
    echo "</div>";
    
    echo "</td>\n<td>\n";
    
  
    $green_inventory_day = new DateTime($row['inventory_time']);
    
    $green_inventory_day->add(new DateInterval('P'.GREEN_DAYS_AFTER_INVENTORY.'D'));
   
    $red_inventory_day = new DateTime($row['inventory_time']);
    $red_inventory_day->add(new DateInterval('P'.RED_DAYS_AFTER_INVENTORY.'D'));
   if ($row['stock_location_id']>0){
         echo "<a href=\"javascript:ajax_call('stocktaking',".$row['stock_id'].",'','','','".URL."index.php','for_ajaxcall')\">";
   
     if ($row['inventory_user_id']>0){
        if ($green_inventory_day>$now)
            echo "<i class=\"fa fa-check\" style=\"color:green\" title=\"".gettext("Last stocktaking:")." ".date($lang_date_format." H:i", strtotime($row['inventory_time']))." ".get_username_from_id($row['inventory_user_id'])."\"></i> ";
        else if ($red_inventory_day<$now)
            echo "<i class=\"fa fa-cancel\" style=\"color:red\" title=\"".gettext("Last stocktaking:")." ".date($lang_date_format." H:i", strtotime($row['inventory_time']))." ".get_username_from_id($row['inventory_user_id'])."\"></i> ";
        }else
    echo "<i class=\"fa fa-window-close\" style=\"color:red\" title=\"".gettext("No stocktaking yet")."\"></i> ";
   
    echo "</a>";}
    
    echo get_product_name_from_id($row['product_id'],$lang)." <mark>".Luhn($row['product_id'])."</mark></td>\n";
    
   
    echo "<td>\n";
    if ($row['stock_location_id']>0){
        echo get_location_name_from_id($row['stock_location_id'],$lang);
            if ($row['stock_place']!="")
            echo " / ".$row['stock_place'];
    }
    else if ($row['stock_location_asset_id']>0){
    
    $n="";
    
    foreach (get_whole_path("asset",$row['stock_location_asset_id'],1) as $k){
    if ($n=="") // the first element is the main asset_id -> ignore it
    $n=" ";
    else
    $n.=$k."-><wbr>";}

    if ($n!="")
    echo substr($n,0,-7);
     }
    else if ($row['stock_location_partner_id']>0){
    echo get_partner_name_from_id($row['stock_location_partner_id']);
    
    }
    
    echo "</td>\n";
    $unit=get_quantity_unit_from_product_id($row['product_id']);
     if ($row['product_stockable']==1){
        if (isset($_SESSION["PUT_PRODUCT_INTO_STOCK"]))
        echo "<td onClick=\"ajax_call('modify_min_stock_quantity','".$row['stock_id']."','','','','".URL."index.php','for_ajaxcall')\">";
        else
        echo "<td>";
    echo round($row['stock_quantity'])." ".$unit[0];
    $min_stock_quantity=round($row['min_stock_quantity']);
    echo " (".$min_stock_quantity." ".$unit[0].")";
   }else
   echo "<td>".round($row['stock_quantity'])." ".$unit[0];
   echo "</td>\n</tr>\n";


}
}else
echo "<tr><td colspan='5'>".gettext("There are no such products in stock!")."</td></tr>";
echo "</tbody>\n</table>\n</div>\n";
include(INCLUDES_PATH."pagination.php");
 ?>


