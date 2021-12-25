<div id='for_ajaxcall'>
</div>
<?php
if (isset($_POST['copy_product_id']) && $_POST["copy_product_id"]>0 && isset($_POST['product_id']) && $_POST["product_id"]>0&& is_it_valid_submit()){
$SQL="SELECT * FROM products WHERE product_id=".(int) $_POST["copy_product_id"];
$copy_from=$dba->getRow($SQL);
$SQL="SELECT * FROM products WHERE product_id=".(int) $_POST["product_id"];
$copy_to=$dba->getRow($SQL);
$first_empty_info_field="";
$there_such_info_file=false;

foreach($copy_from as $key=>$value){
if (strstr($key,"info_file_id") && $value>0)
                    {$i=1; //number of info_file_idxx
                    foreach($copy_to as $key2=>$value2){
                    if (strstr($key2,"info_file_id")){
                    $i++;
                    if ($first_empty_info_field=="" && ($value2==0 || $value2==""))
                    $first_empty_info_field=$key2;
                    
                    if ($value2==$value)
                    $there_such_info_file=true;
                    }}
                    
                    if ($there_such_info_file==false){
                        if ($first_empty_info_field!=""){//there is an empty field
                        $SQL="UPDATE products SET ".$first_empty_info_field."=".$value." WHERE product_id=".(int) $_POST['product_id'];
                        $dba->Query($SQL);
                        }else{// we need create a new info field
                        $SQL="ALTER TABLE products add column info_file_id".$i." smallint(6) UNSIGNED";
                        $result = $dba->Query($SQL);
                        if (LM_DEBUG)
                        error_log($SQL,0);
                        $SQL="ALTER TABLE products ADD INDEX (info_file_id".$i.")";
                        $result = $dba->Query($SQL);
                        $SQL="UPDATE products SET info_file_id".$i."=".$value." WHERE product_id=".(int) $_POST['product_id'];
                        $dba->Query($SQL);    
                        
                        }
                        
                    
                    }
                    
                    
                    }

}

}


if (isset($_POST['product_id']) && $_POST["product_id"]>0 && isset($_FILES['info_file_name']['tmp_name']) ){ //it is from the new file form
 
$table="products";
$id=$_POST["product_id"];
$id_column="product_id";
    require(INCLUDES_PATH."file_upload.php"); 

}

else if (isset($_POST['page']) && (isset($_POST["new_name_".LANG1]) && !empty($_POST["new_name_".LANG1])) || (isset($_POST["new_name_".LANG2]) && !empty($_POST["new_name_".LANG2]))){ //it is from the rename asset form
    $SQL="UPDATE products SET ";
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.="product_type_".LANG1."='".$dba->escapeStr($_POST["new_name_".LANG1])."'";
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    $SQL.=",product_type_".LANG2."='".$dba->escapeStr($_POST["new_name_".LANG2])."'";
   
    $SQL.=" WHERE product_id='".$_POST["product_id"]."'";
    if (LM_DEBUG)
        error_log($SQL,0); 
    if ($dba->Query($SQL))
        echo "<div class=\"card\">".gettext("The product type has been renamed.")."</div>";
        else
        echo "<div class=\"card\">".gettext("Failed to rename product ").$dba->err_msg."</div>";
}



else if (isset($_POST['page']) && isset($_POST["modify_product"])  && is_it_valid_submit()){
$SQL="UPDATE products SET ";

if ($_SESSION['CAN_WRITE_LANG1'])
{
$SQL.="product_type_".LANG1."='".$dba->escapeStr($_POST["product_type_".LANG1])."',";
$SQL.="product_properties_".LANG1."='".$dba->escapeStr($_POST["product_properties_".LANG1])."',";
}
if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
{
$SQL.="product_type_".LANG2."='".$dba->escapeStr($_POST["product_type_".LANG2])."',";
$SQL.="product_properties_".LANG2."='".$dba->escapeStr($_POST["product_properties_".LANG2])."',";
}

$SQL.="manufacturer_id=".(int) $_POST['manufacturer_id'].",";
$SQL.="quantity_unit=".(int) $_POST['quantity_unit'].",";
$SQL.="default_stock_location_id=".(int) $_POST['default_stock_location_id'].",";
$SQL.="product_stockable=".(int) $_POST['product_stockable'].",";
$display=0;
foreach ($_POST["display"] as $key =>$value){
$display+=$value;
}


$SQL.="display=".$display;
$SQL.=" WHERE product_id=".(int) $_POST['product_id']; 
if (LM_DEBUG)
            error_log($SQL,0); 
if ($dba->Query($SQL))
        echo "<div class=\"card\">".gettext("The product has been modified.")."</div>";
        else
        echo "<div class=\"card\">".gettext("Failed to modify product ").$dba->err_msg."</div>";


}


else if (isset($_POST['page']) && isset($_POST["new_product"])  && is_it_valid_submit()){
    $SQL="INSERT INTO products (category_id,subcategory_id,";
    
    if ($_SESSION['CAN_WRITE_LANG1'])
    $SQL.="product_type_".LANG1.",product_properties_".LANG1.",";
    
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    $SQL.="product_type_".LANG2.",product_properties_".LANG2.",";
    
    $SQL.="manufacturer_id,quantity_unit,default_stock_location_id ,product_stockable,display) VALUES (";
    $SQL.=(int) $_POST['category_id'].",";
    $SQL.=(int) $_POST['subcategory_id'].",";
    
    if ($_SESSION['CAN_WRITE_LANG1']){
    $SQL.="'".$dba->escapeStr($_POST['product_type_'.LANG1])."',";
    $SQL.="'".$dba->escapeStr($_POST['product_type_'.LANG1])."',";
    }
    if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
    {
    $SQL.="'".$dba->escapeStr($_POST['product_properties_'.LANG2])."',";
    $SQL.="'".$dba->escapeStr($_POST['product_properties_'.LANG2])."',";
    }
    $SQL.=(int) $_POST['manufacturer_id'].",";
    $SQL.=(int) $_POST['quantity_unit'].",";
    $SQL.=(int) $_POST['default_stock_location_id'].",";
    $SQL.=(int) $_POST['product_stockable'].",";
    $display=0;

    foreach ($_POST["display"] as $key =>$value){
    $display+=$value;
    }
    $SQL.=$display.")";
    if (LM_DEBUG)
            error_log($SQL,0); 
        if ($dba->Query($SQL)){
             if ((int)$_POST['product_stockable']==2){//unique product
            $SQL="INSERT INTO stock (product_id,product_category_id,product_subcategory_id,stock_location_id,stock_location_asset_id,stock_location_partner_id,stock_quantity,item_created) VALUES ";
            $SQL.="(".$dba->insertedId().",".(int) $_POST['category_id'].",".(int) $_POST['subcategory_id'].",0,0,0,1,now())";
            if (LM_DEBUG)
            error_log($SQL,0); 
            $dba->Query($SQL);
            }
            
            echo "<div class=\"card\">".gettext("The new product has been saved.")."</div>";
            }
            else
            echo "<div class=\"alert  alert-success alert-dismissible fade show\" role=\"alert\">".gettext("Failed to save new product ").$dba->err_msg." ".$SQL."</div>";

}
else if (isset($_POST['product_id']) && isset($_POST["connection_id"])){// add new connection from
$SQL="SELECT * FROM products WHERE product_id='".$_POST["product_id"]."'";
if (LM_DEBUG)
            error_log($SQL,0); 
$row=$dba->getRow($SQL);
$has_written=false;
$conn_number=0;
foreach($row as $key=>$value) //has it already append?
    {
       	if (strstr($key,"connection_id") && $value==$_POST["connection_id"])
       	{
       	$has_written=true;
       	}
    }   	

foreach($row as $key=>$value)
    {
       	if (strstr($key,"connection_id"))
       	{
       	$conn_number++;
       	if(0==$value && !$has_written){
        $SQL="UPDATE products SET ".$key."='".(int) $_POST['connection_id']."', connection_type".substr($key,13)."=".(int) $_POST['connection_type']." WHERE product_id='".(int) $_POST['product_id']."'";
        if (LM_DEBUG)
            error_log($SQL,0); 
        if ($dba->Query($SQL)){
            echo "<div class=\"card\">".gettext("The connection has been attached to the product.")."</div>";
            $has_written=true;
            }
            else
            echo "<div class=\"card\">".gettext("Failed to attach connection to the product ").$dba->err_msg."</div>";
        }}
    }

if (!$has_written) //there was no empty "connection"
    {
    $SQL="ALTER TABLE products ADD COLUMN connection_id".++$conn_number." SMALLINT(3) UNSIGNED NULL,ADD COLUMN connection_type".$conn_number." SMALLINT(3) UNSIGNED NULL";
    if (LM_DEBUG)
            error_log($SQL,0); 
        if ($dba->Query($SQL))
        {
        $SQL="UPDATE products SET connection_id".$conn_number."='".$_POST['connection_id']."',connection_type".$conn_number."='".$_POST['connection_type']."' WHERE product_id='".$_POST['product_id']."'";
        if (LM_DEBUG)
            error_log($SQL,0); 
        if ($dba->Query($SQL)){
            echo "<div class=\"card\">".gettext("The connection has been attached to the product.")."</div>";
            $has_written=true;
            
            }
            else
            echo "<div class=\"card\">".gettext("Failed to attach connection to the product ").$dba->err_msg."</div>";
        
        }else
        echo "<div class=\"card\">".gettext("Something went wrong").$dba->err_msg."</div>";
    }
    
    
}



if (isset($_GET['new']) || isset($_GET['modify'])){
?>

<div class="card">
<div class="card-header">
<strong><?php
if (isset($_GET['modify'])){
echo gettext("Modify product");
$SQL="SELECT * FROM products WHERE product_id='". (int) $_GET['product_id']."'";
$row_orig=$dba->getRow($SQL);
}
else
echo gettext("New product");?></strong>
</div><?php //card header ?>
<div class="card-body card-block">
<form action="index.php" id="product_form" method="post" enctype="multipart/form-data" class="form-horizontal">

<?php
//if ($_GET["new"]=="category" && isset($_GET["parent_id"]) && ($_GET["parent_id"]>0)){
//$SQL="SELECT category_name_$lang FROM categories WHERE category_id='".$_GET["parent_id"]."'";


    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\"><label for=\"category_id\" class=\"form-control-label\">".gettext(" Category:")."</label></div>";

    echo "<div class=\"col col-md-2\">";
    if (isset($_GET['new']))
    {
    echo "<select name=\"category_id\" id=\"category_id\" class=\"form-control\" onChange=\"ajax_call('products',this.value,0,'','','".URL."index.php','subcategory')\" required>\n";
    $SQL="SELECT category_id,category_name_".$lang." FROM categories WHERE category_parent_id=0";
    $SQL.=" ORDER BY category_name_".$lang;
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
    if ($row["category_name_".$lang]!="")
    echo "<option value=\"".$row["category_id"]."\">".$row["category_name_".$lang]."</option>\n";
     }
    echo "</select>";
    } else 
    echo get_category_name_from_id($row_orig['category_id'],$lang);
    echo "</div></div>";
  
  
echo "<div id=\"subcategory\">";
if (isset($_GET['modify']) && $row_orig['subcategory_id']>0)
{
echo get_category_name_from_id($row_orig['subcategory_id'],$lang);
}
echo "</div>";
  
 
 if ($_SESSION['CAN_WRITE_LANG1'])
{ 
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"product_type_".LANG1."\" class=\"form-control-label\">".gettext("Product type:")."</label></div>\n";
echo "<div class=\"col-12 col-md-10\"><input type=\"text\" id=\"product_type_".LANG1."\" name=\"product_type_".LANG1."\" placeholder=\"".gettext("product type")."\" class=\"form-control\"";
if (isset($_GET['modify']))
echo " value='".$row_orig['product_type_'.LANG1]."'";
echo " required>";
echo "<small class=\"form-text text-muted\">".gettext("product type")."</small></div>\n";
echo "</div>";
}

if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
{
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"product_type_".CAN_WRITE_LANG2."\" class=\"form-control-label\">".gettext("Product type (").LANG2."): </label></div>\n";
echo "<div class=\"col-12 col-md-10\"><input type=\"text\" id=\"product_type_".LANG2."\" name=\"product_type_".LANG2."\" placeholder=\"".gettext("product type")."\" class=\"form-control\"";
if (isset($_GET['modify']))
echo " value='".$row_orig['product_type_'.LANG2]."'";
echo " required><small class=\"form-text text-muted\">".gettext("product type")."</small></div>\n";
echo "</div>";

}



echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"manufacturer_id\" class=\"form-control-label\">".gettext("Manufacturer:")."</label></div>\n";
echo "<div class=\"col col-md-2\">";
    echo "<select name=\"manufacturer_id\" id=\"manufacturer_id\" class=\"form-control\">\n";
    $SQL="SELECT manufacturer_id,manufacturer_name FROM manufacturers ORDER BY manufacturer_name";
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
    echo "<option value=\"".$row["manufacturer_id"]."\"";
    if (isset($_GET['modify']) && $row['manufacturer_id']==$row_orig['manufacturer_id'])
    echo " selected";
    echo ">".$row["manufacturer_name"]."</option>\n";
    }
    echo "</select></div>\n";
echo "</div>";


if ($_SESSION['CAN_WRITE_LANG1'])
{
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"product_properties_".LANG1."\" class=\"form-control-label\">".gettext("Product properties:")."</label></div>\n";
echo "<div class=\"col-12 col-md-10\"><input type=\"text\" id=\"product_properties_".LANG1."\" name=\"product_properties_".LANG1."\" placeholder=\"".gettext("product properties")."\" class=\"form-control\"";
if (isset($_GET['modify']))
    echo " value='".$row_orig['product_properties_'.LANG1]."'";
echo "><small class=\"form-text text-muted\">".gettext("product properties")."</small></div>\n";
echo "</div>";
}


if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
{
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"product_properties_".LANG2."\" class=\"form-control-label\">".gettext("Product properties (").LANG2."): </label></div>\n";
echo "<div class=\"col-12 col-md-10\"><input type=\"text\" id=\"product_properties_".LANG2."\" name=\"product_properties_".LANG2."\" placeholder=\"".gettext("product properties ").LANG2."\" class=\"form-control\"";
if (isset($_GET['modify']))
    echo " value='".$row_orig['product_properties_'.LANG2]."'";
echo "><small class=\"form-text text-muted\">".gettext("product properties ").LANG2."</small></div>\n";
echo "</div>";
}


echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-2\"><label for=\"display\" class=\"form-control-label\">".gettext("Display:")."</label></div>\n";
echo "<div class=\"col-12 col-md-10\">";
if (isset($_GET['modify']))
$d=$row_orig['display'];

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"1\"";
if (isset($_GET['modify']) && (($d >> 0) & 1))
echo " checked";
echo "> ".gettext("Category")."\n";

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"2\"";
if (isset($_GET['modify']) && (($d >> 1) & 1))
echo " checked";
echo "> ".gettext("Subcategory")."\n";

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"4\"";
if (isset($_GET['modify']) && (($d >> 2) & 1))
echo " checked";
echo "> ".gettext("Type")."\n";

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"8\"";
if (isset($_GET['modify']) && (($d >> 3) & 1))
echo " checked";
echo "> ".gettext("Manufacturer")."\n";

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"16\"";
if (isset($_GET['modify']) && (($d >> 4) & 1))
echo " checked";
echo "> ".gettext("Properties")."\n";

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"32\"";
if (isset($_GET['modify']) && (($d >> 5) & 1))
echo " checked";
echo "> ".gettext("subcat. is the first")."\n";

echo "</div>\n";
echo "</div>";




echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"quantity_unit\" class=\"form-control-label\">".gettext("Unit:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-1\">";
        echo "<select name=\"quantity_unit\" id=\"quantity_unit\" class=\"form-control\" >";
    $SQL="SELECT unit_id,unit_".$lang." FROM units ORDER BY unit_".$lang;
    $result=$dba->Select($SQL);
    if (LM_DEBUG)
        error_log($SQL,0);
    foreach ($result as $row){    
        echo "<option value=\"".$row['unit_id']."\" ";
        if (isset($_GET['modify']) && $row_orig['quantity_unit']==$row['unit_id'])
    echo "selected";
        echo ">".$row['unit_'.$lang]."</option>\n";
        }
        echo "</select>\n";
    echo "</div>";
echo "</div>"; 


echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-2\">\n";
        echo "<label for=\"product_stockable\" class=\"form-control-label\">".gettext("Stockable:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col col-md-2\">";
        echo "<select name=\"product_stockable\" id=\"product_stockable\" class=\"form-control\" >";
      
        echo "<option value=\"1\" ";
        if (isset($_GET['modify']) && 1==$row_orig['product_stockable'])
            echo "selected";
        echo ">".gettext("Yes")."</option>\n";
        
        echo "<option value=\"2\" ";
        if (isset($_GET['modify']) && 2==$row_orig['product_stockable'])
            echo "selected";
        echo ">".gettext("Yes, but unique")."</option>\n";
        
        echo "<option value=\"3\" ";
        if (isset($_GET['modify']) && 3==$row_orig['product_stockable'])
            echo "selected";
        echo ">".gettext("No")."</option>\n";

        echo "</select>\n";
    echo "</div>";
echo "</div>";

echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-2\"><label for=\"default_stock_location_id\" class=\"form-control-label\">";
        echo gettext("Default stock:")."</label></div>\n";
        echo "<div class=\"col col-md-2\">\n";
        echo "<select id=\"default_stock_location_id\" name=\"default_stock_location_id\" class=\"form-control\">";

        $SQL="SELECT location_name_".$lang.", location_id FROM locations WHERE set_as_stock=1 ORDER BY location_name_".$lang;
        $result=$dba->Select($SQL);
        echo "<option value=\"0\">".gettext("Please select")."</option>\n";
        foreach ($result as $row){
        echo "<option value=\"".$row["location_id"]."\"";
        if (isset($_GET['modify']) && $row_orig['default_stock_location_id']==$row['location_id'])
            echo "selected";
        echo ">".$row["location_name_".$lang]."</option>\n";
        }
        echo "</select></div></div>\n";

        ?>


</div><?php //card-body card-block  ?>
<div class="card-footer">
<button type="submit" class="btn btn-primary btn-sm">
<i class="fa fa-dot-circle-o"></i><?php echo gettext(" Submit ");?>
</button>
<button type="reset" class="btn btn-danger btn-sm">
<i class="fa fa-ban"></i><?php echo gettext(" Reset ");?>
</button>
</div>
<input type="hidden" name="page" id="page" value="products">
<?php
echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">";

if (isset($_GET['modify']))
{
echo "<INPUT TYPE=\"hidden\" name=\"modify_product\" id=\"modify_product\" VALUE=\"1\">";
echo "<INPUT TYPE=\"hidden\" name=\"product_id\" id=\"product_id\" VALUE=\"".(int) $_GET["product_id"]."\">";
}else
echo "<INPUT TYPE=\"hidden\" name=\"new_product\" id=\"new_product\" VALUE=\"1\">";

?>
</form>
</div><?php //card  
echo "<script>\n";
echo "$(\"#product_form\").validate({
  rules: {";
  if (LANG2_AS_SECOND_LANG && isset($_SESSION['CAN_WRITE_LANG2']))
{
  echo  "product_type_".LANG2.": {
        required: true,
        maxlength: ".$dba->get_max_fieldlength('products','product_type_'.LANG2)."
    }
    ,product_properties_".LANG2.": {
        maxlength: ".$dba->get_max_fieldlength('products','product_properties_'.LANG2)."
    }";}
    
    if ($_SESSION['CAN_WRITE_LANG1'])
    {
    echo ",product_type_".LANG1.": {
        required: true,
        maxlength: ".$dba->get_max_fieldlength('products','product_type_'.LANG1)."
    }
    ,product_properties_".$lang.": {
        maxlength: ".$dba->get_max_fieldlength('products','product_properties_'.LANG1)."
    }";
    }
  echo "}
})\n";
echo "</script>\n";
}//if (isset($_GET["new"]))

?>


<div class="card-body">
<table id="bootstrap-data-table" class="table table-striped table-bordered">
<thead>
<tr>
<?php 
echo "<th></th><th>";
$category_id=lm_isset_int('category_id');  
if ($category_id>0){
$_SESSION['category_id']=$category_id;
unset($_SESSION['subcategory_id']);
}
$subcategory_id=lm_isset_int('subcategory_id');
if ($subcategory_id>0)
$_SESSION['subcategory_id']=$subcategory_id;
$SQL="SELECT category_id,category_name_".$lang." FROM categories WHERE category_parent_id=0 ORDER BY category_name_".$lang;
$result=$dba->Select($SQL);

if (LM_DEBUG)
    error_log($SQL,0);
    echo " <select name=\"category_id\" id=\"category_id\" class=\"form-control\"";
            echo " onChange=\"location.href='index.php?page=products&category_id='+this.value\"";
            echo " style='display:inline;width:250px;'>\n";
    echo "<option value='0'>".gettext("All categories");
    foreach($result as $row){
    echo "<option value='".$row['category_id']."'";
    if ($row['category_id']==$_SESSION['category_id'])
    echo " selected";
    echo ">".$row['category_name_'.$lang]."\n";
    }
    echo "</select>\n"; 
    
    if (isset($_SESSION['category_id']) && $_SESSION['category_id']>0)
    {
    $SQL="SELECT category_id, category_name_".$lang." FROM categories WHERE category_parent_id='".$_SESSION['category_id']."' ORDER BY category_name_".$lang;
    $result=$dba->Select($SQL);
    if (LM_DEBUG)
    error_log($SQL,0);
    if ($dba->affectedRows()>0)
    {
    echo " <select name=\"subcategory_id\" id=\"subcategory_id\" class=\"form-control\"";
            echo " onChange=\"location.href='index.php?page=products&category_id=".$category_id."&subcategory_id='+this.value\"";
            echo " style='display:inline;width:200px;'>\n";
    echo "<option value='0'>".gettext("All categories");
    foreach($result as $row){
    echo "<option value='".$row['category_id']."'";
    if (isset($_SESSION['subcategory_id']) && $row['category_id']==$_SESSION['subcategory_id'])
    echo " selected";
    echo ">".$row['category_name_'.$lang]."\n";
    }
    echo "</select>\n"; 
    }
    }


echo gettext("Description");
echo "</th>";
echo "<th>".gettext("Quantity unit")."</th></tr>";

?>
</thead>
<tbody>
<?php
$pagenumber=lm_isset_int('pagenumber');
if ($pagenumber<1)
$pagenumber=1;

$SQL="Select * FROM products LEFT JOIN categories ON products.category_id=categories.category_id";
if (isset($_SESSION['category_id']) && $_SESSION['category_id']>0){
$SQL.=" WHERE products.category_id=".$_SESSION['category_id'];}

if (isset($_SESSION['subcategory_id']) && $_SESSION['subcategory_id']>0)
$SQL.=" AND subcategory_id='".$_SESSION['subcategory_id']."'";

$SQL.=" ORDER BY category_name_".$lang.",product_type_".$lang;
$result_all=$dba->Select($SQL);
$number_all=$dba->affectedRows();
$from=($pagenumber-1)*ROWS_PER_PAGE;
$SQL.=" limit $from,".ROWS_PER_PAGE;
$result=$dba->Select($SQL);

if (LM_DEBUG)
error_log($SQL,0);
if ($number_all>0){
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
                             
                             if (isset($_SESSION["SEE_FILE_OF_PRODUCT"]) && $row['info_file_id1']>0){
                            
                            echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_info_files','".$row['product_id']."','products','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Show files")."</a>";
                                                        
                             
                             }
                            if ($_SESSION["MODIFY_PRODUCT"])
                              {
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('rename','".$row['product_id']."','products','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Rename product")."</a>";
                             
                              echo "<a href=\"index.php?page=products&modify=1&product_id=".$row['product_id']."\" title=\"".gettext("add new activity")."\"> <i class=\"fa fa-clock-o\" style='color:blue'></i> ";
                             echo gettext("Modify product")."</a>";
                             
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('handle_connection','".$row['product_id']."','products','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Handle connection")."</a>";
                             }
                             
                             if (isset($_SESSION['ADD_FILE_TO_PRODUCT'])){
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('add_file',".$row['product_id'].",'products','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ".gettext("Add file")."</a>";
                             }
                             
                             if (isset($_SESSION['ADD_FILE_TO_PRODUCT'])){
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('copy_info_file',".$row['product_id'].",'products','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ".gettext("Copy info file")."</a>";
                             }
                             
                             if (isset($_SESSION['SEE_ASSETS'])){
                             echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_assets_with_this_product',".$row['product_id'].",'products','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ".gettext("Show assets with this")."</a>";
                             }
                               if (isset($_SESSION["SEE_PRODUCT_MOVING"])){
                            echo "<a class=\"nav-link\" href=\"javascript:ajax_call('show_stock_movements',".$row['product_id'].",'','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-user\"></i> ";
                             echo gettext("Stock movements")."</a>";
                             
                             }
                             echo "</div>";
    echo "</div>";
    if (isset($_SESSION["SEE_FILE_OF_PRODUCT"]) && $row['info_file_id1']>0){
     echo "<a href=\"javascript:ajax_call('show_info_files','".$row['product_id']."','products','','','".URL."index.php','for_ajaxcall')\"><i class=\"fa fa-info\"></i> ";
    echo "</a>";
    }
    echo "</td>\n";
    echo "<td>";
    echo get_product_name_from_id($row["product_id"],$lang);
    /*
    if ($row['subcategory_id']>0)
    echo ucfirst(get_category_name_from_id($row['subcategory_id'],$lang))." ";
    echo $row['category_name_'.$lang]." ";
    echo $row['product_type_'.$lang]." ";
//     echo get_manufacturer_name_from_id($row['manufacturer_id'])." ";
    echo $row['product_properties_'.$lang]."
    */
    echo "<mark>".Luhn($row['product_id'])."</mark></td>";
    echo "<td>".get_unit_from_id($row['quantity_unit'])."</td>";
    echo "</tr>";
    }
}else
echo "<tr><td colspan=4>".gettext("No match.")."</td></tr>";
?>

</tbody>
</table>
</div>
<?php
include(INCLUDES_PATH."pagination.php");
?>
