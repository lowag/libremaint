

<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
      
        <h4 class="modal-title w-100 font-weight-bold"><?php gettext("New product");?></h4>
       <button type="button" class="close" data-dismiss="modal">&times;</button>       
      </div>
      <form action="index.php" method="POST" name="new_product_modal_form" enctype="multipart/form-data" class="form-horizontal">
      <div class="modal-body mx-3">
       
    <?php    
    echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-3\"><label for=\"modal_category_id\" class=\"form-control-label\">".gettext(" Category:")."</label></div>";
    if (isset($_GET['param3'])){
    echo get_category_name_from_id($_GET['param3'],$lang);
    echo "<input type='hidden' id='modal_category_id' name='modal_category_id' value='".$_GET['param3']."'>";

    }
    else{
    echo "<div class=\"col col-md-8\">";
    echo "<select name=\"modal_category_id\" id=\"modal_category_id\" class=\"form-control\" onChange=\"ajax_call('products',this.value,0,'','".$_GET['param5']."','".URL."index.php','modal_subcategory_id')\">\n";
    $SQL="SELECT category_id,category_name_".$lang." FROM categories WHERE category_parent_id=0";
    $SQL.=" ORDER BY category_name_".$lang;
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
    foreach ($result as $row){
    
    echo "<option value=\"".$row["category_id"]."\" ";
    if (isset($_GET['param3']) && $_GET['param3']==$row["category_id"])
    echo "selected";
    
    if ($row["category_name_".$lang]!="")
    echo ">".$row["category_name_".$lang]."</option>\n";
    else
    echo ">".$row["category_name_en"]."</option>\n";

    }
    echo "</select></div>\n";
    }
    echo "</div>\n";

 if (isset($_GET['param3'])){
 echo "<div class=\"row form-group\" id='subcategory_div'>";
    echo "<div class=\"col col-md-3\"><label for=\"modal_subcategory_id\" class=\"form-control-label\">".gettext(" Subcategory:")."</label></div>";
    echo "<div class=\"col col-md-8\">";
    echo "<select name=\"modal_subcategory_id\" id=\"modal_subcategory_id\" class=\"form-control\"";
    //echo " onChange=\"ajax_call('products',this.value,0,'','','".URL."index.php','subcategory')\"";
    echo ">\n";
    $SQL="SELECT category_id,category_name_".$lang.",category_name_en FROM categories WHERE category_parent_id='".(int) $_GET['param3']."'";
    $SQL.=" ORDER BY category_name_".$lang;
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
    if (!empty($result)){
    foreach ($result as $row){
    
    echo "<option value=\"".$row["category_id"]."\" ";
    if (isset($_GET['param4']) && $_GET['param4']==$row["category_id"])
    echo "selected";
    
    if ($row["category_name_".$lang]!="")
    echo ">".$row["category_name_".$lang]."</option>\n";
    else
    echo ">".$row["category_name_en"]."</option>\n";
    }
    }
    echo "</select></div>\n";

    echo "</div>\n"; 
 }else 
echo "<div id=\"modal_subcategory_id\"></div>";
if ($_SESSION['CAN_WRITE_LANG1'])
{
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"modal_product_type_".LANG1."\" class=\"form-control-label\">".gettext("Product type:")."</label></div>\n";
echo "<div class=\"col-11 col-md-8\">\n";
echo "<input type=\"text\" id=\"modal_product_type_".LANG1."\" name=\"modal_product_type_".LANG1."\" placeholder=\"".gettext("product type")."\" class=\"form-control\" onkeyup=\"ajax_call('search','product_type_".LANG1."','products',this.value,'modal_product_type_".LANG1."','".URL."index.php','livesearch')\">\n";
echo "<div id='livesearch'></div>\n";
echo "<small class=\"form-text text-muted\">".gettext("product type")."</small></div>\n";
echo "</div>\n";
}

if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
{
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"modal_product_type_".LANG2."\" class=\"form-control-label\">".gettext("Product type ").LANG2."):</label></div>\n";
echo "<div class=\"col-11 col-md-8\">\n";
echo "<input type=\"text\" id=\"modal_product_type_".LANG2."\" name=\"modal_product_type_".LANG2."\" placeholder=\"".gettext("product type")." ".LANG2."\" class=\"form-control\" onkeyup=\"ajax_call('search','product_type_".LANG2."','products',this.value,'modal_product_type_".LANG2."','".URL."index.php','livesearch_".LANG2."')\">\n";
echo "<div id='livesearch_".LANG2."'></div>\n";
echo "<small class=\"form-text text-muted\">".gettext("product type").LANG2."</small></div>\n";
echo "</div>\n";
}

echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"modal_manufacturer_id\" class=\"form-control-label\">".gettext("Manufacturer:")."</label></div>\n";

echo "<div class=\"col-11 col-md-8\">";
    echo "<select name=\"modal_manufacturer_id\" id=\"modal_manufacturer_id\" class=\"form-control\"";
    echo " onChange=\"if (this.value=='new')\n";
    echo " document.getElementById('modal_new_manufacturer_r').style.display='block';\n";
    echo "else{\n";
    echo " document.getElementById('modal_new_manufacturer_r').style.display='none';\n";
    echo "document.getElementById('modal_new_manufacturer').value='';\n";
    echo "}";
    echo "\"";
    echo ">\n";
    $SQL="SELECT manufacturer_id,manufacturer_name FROM manufacturers ORDER BY manufacturer_name";
    if (LM_DEBUG)
    error_log($SQL,0);
    $result=$dba->Select($SQL);
    echo "<option value=\"0\">".gettext("Please select")."</option>\n";
    echo "<option value=\"new\">".gettext("New")."</option>\n";
    foreach ($result as $row){
    echo "<option value=\"".$row["manufacturer_id"]."\">".$row["manufacturer_name"]."</option>\n";
    }
    echo "</select></div>\n";
echo "</div>";

echo "<div id='modal_new_manufacturer_r' style='display:none;'>\n";
echo "<div class=\"row form-group\">\n";
echo "<div class=\"col col-md-3\"><label for=\"modal_new_manufacturer\" class=\"form-control-label\">".gettext("New manufacturer:")."</label></div>\n";
echo "<div class=\"col-11 col-md-8\"><input type=\"text\" id=\"modal_new_manufacturer\" name=\"modal_new_manufacturer\" placeholder=\"".gettext("new manufacturer")."\" class=\"form-control\"><small class=\"form-text text-muted\">".gettext("new manufacturer")."</small></div>\n";
echo "</div>\n";
echo "</div>\n";

if ($_SESSION['CAN_WRITE_LANG1']){
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"modal_product_properties_".LANG1."\" class=\"form-control-label\">".gettext("Product properties:")."</label></div>\n";
echo "<div class=\"col-11 col-md-8\"><input type=\"text\" id=\"modal_product_properties_".LANG1."\" name=\"modal_product_properties_".LANG1."\" placeholder=\"".gettext("product properties")."\" class=\"form-control\"><small class=\"form-text text-muted\">".gettext("product properties")."</small></div>\n";
echo "</div>";
}


if (LANG2_AS_SECOND_LANG && $_SESSION['CAN_WRITE_LANG2'])
{
echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"modal_product_properties_".LANG2."\" class=\"form-control-label\">".gettext("Product properties")." ".LANG2.":</label></div>\n";
echo "<div class=\"col-11 col-md-8\"><input type=\"text\" id=\"modal_product_properties_".LANG2."\" name=\"modal_product_properties_".LANG2."\" placeholder=\"".gettext("product properties")." ".LANG2."\" class=\"form-control\"><small class=\"form-text text-muted\">".gettext("product properties")." ".LANG2."</small></div>\n";
echo "</div>";
}



echo "<div class=\"row form-group\">";
echo "<div class=\"col col-md-3\"><label for=\"display\" class=\"form-control-label\">".gettext("Display:")."</label></div>\n";
echo "<div class=\"col-11 col-md-8\">";

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"1\"";
echo "> ".gettext("Category")."\n";

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"2\"";
echo "> ".gettext("Subcategory")."\n";

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"4\"";
echo "> ".gettext("Type")."\n";

echo "<br/><INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"8\"";
echo "> ".gettext("Manufacturer")."\n";

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"16\"";
echo "> ".gettext("Properties")."\n";

echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"display[]\" VALUE=\"32\"";
echo "> ".gettext("subcat. is the first")."\n";

echo "</div>\n";
echo "</div>";




echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-3\">\n";
        echo "<label for=\"modal_quantity_unit\" class=\"form-control-label\">".gettext("Unit:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col-11 col-md-5\">";
        echo "<select name=\"modal_quantity_unit\" id=\"modal_quantity_unit\" class=\"form-control\" >";
    $SQL="SELECT unit_id,unit_".$lang." FROM units ORDER BY unit_".$lang;
    $result=$dba->Select($SQL);
    if (LM_DEBUG)
        error_log($SQL,0);
    foreach ($result as $row){    
        echo "<option value=\"".$row['unit_id']."\" >".$row['unit_'.$lang]."</option>\n";
        }
        echo "</select>\n";
    echo "</div></div>\n";
    
    
    
echo "<div class=\"row form-group\">";
    echo "<div class=\"col col-md-3\">\n";
        echo "<label for=\"modal_product_stockable\" class=\"form-control-label\">".gettext("Stockable:")."</label>";
    echo "</div>\n";

    echo "<div class=\"col-11 col-md-8\">";
        echo "<select name=\"modal_product_stockable\" id=\"modal_product_stockable\" class=\"form-control\" >";
      
        echo "<option value=\"1\" >".gettext("Yes")."</option>\n";
        echo "<option value=\"2\" >".gettext("Yes, but unique")."</option>\n";
        echo "<option value=\"3\" >".gettext("No")."</option>\n";

        echo "</select>\n";
    echo "</div>";
echo "</div>"; 


echo "<div class=\"row form-group\">\n";
        echo "<div class=\"col col-md-3\"><label for=\"default_stock_location_id\" class=\"form-control-label\">";
        echo gettext("Default stock:")."</label></div>\n";
        echo "<div class=\"col-11 col-md-8\">\n";
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

    echo "<input type=\"hidden\" name=\"valid\" id=\"valid\" value=\"".$_SESSION["tit_id"]."\">\n";
    echo "<input type='hidden' id='page' name='page' value='".$_GET['param5']."'>";
    if ($_GET['param5']=='assets') //it is from the assets page and we need asset_id to attach the new product
    echo "<input type='hidden' id='modal_asset_id' name='modal_asset_id' value='".$_GET['param2']."'>\n";
    if ($_GET['param1']=="into_stock")// it is from into_stock ajax_call.php
    echo "<input type='hidden' id='modal_destination' name='modal_destination' value='into_stock'>\n";

echo "</div>\n"; 
     
echo "<div class=\"modal-footer d-flex justify-content-center\">";
echo "<button type=\"submit\" class=\"btn btn-primary btn-sm\">\n";
echo "<i class=\"fa fa-dot-circle-o\"></i>".gettext(" Submit ")."</button></form>";
?> 
</div> 
        
     </div>
  </div>
</div>
