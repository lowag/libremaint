<?php
header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache'); 
    header('expires: 0');
    //header("Access-Control-Allow-Origin: *");	
    include_once( dirname(__FILE__) . '/functions.php' );
	include_once( dirname(__FILE__) . '/lm-load.php' );
	


	

	
 $req_page=lm_isset_str('page');
 
 if ($req_page=='')
    $req_page="index";
	
	//echo "xxxxx".$req_page;
	if ( !isset($lm_did_header) ) {
	$lm_did_header = true;
	?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Libremaint</title>
    <meta name="description" content="Libremaint">
    <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php //  <script src="https://code.jquery.com/jquery-latest.min.js"></script>
  echo "<link rel=\"icon\" type=\"image/png\" href=\"".URL."favicon.ico\">";
      echo "<script src=\"".VENDORS_LOC."jquery/dist/jquery.min.js\"></script>\n";
echo "<script src=\"".VENDORS_LOC."jquery-ui/jquery-ui.min.js\"></script>\n";

  ?>

<?php
    echo "<link rel=\"stylesheet\" href=\"".VENDORS_LOC."bootstrap/dist/css/bootstrap.min.css\">\n";
    if ($req_page=="assets" || $req_page=="locations" || $req_page=="categories" || $req_page=="notifications")
    {
    echo "<link rel=\"stylesheet\" href=\"".VENDORS_LOC."fancytree/dist/skin-win8/ui.fancytree.css\">\n";
    echo "<script src=\"".VENDORS_LOC."fancytree/src/jquery.fancytree.js\"></script>\n";
    echo "<script src=\"".VENDORS_LOC."fancytree/src/jquery.fancytree.filter.js\"></script>\n";
    echo "<script src=\"".INCLUDES_LOC."for_fancytree.js\"></script>\n";

    }
    
    echo "<script src=\"".VENDORS_LOC."chart.js/dist/Chart.min.js\"></script>\n";

    
    echo "<script src=\"".VENDORS_LOC."popper.js/dist/umd/popper.min.js\"></script>\n";

  //  echo "<script src=\"".VENDORS_LOC."/jquery/dist/jquery.min.js\"></script>\n";
        echo "<script src=\"".VENDORS_LOC."bootstrap/dist/js/bootstrap.min.js\"></script>\n";
    echo "<link rel=\"stylesheet\" href=\"".VENDORS_LOC."font-awesome/css/font-awesome.min.css\">\n";
    
    echo "<link rel=\"stylesheet\" href=\"".VENDORS_LOC."themify-icons/css/themify-icons.css\">\n";
    echo "<link rel=\"stylesheet\" href=\"".VENDORS_LOC."flag-icon-css/css/flag-icon.min.css\">\n";
    echo "<link rel=\"stylesheet\" href=\"".VENDORS_LOC."selectFX/css/cs-skin-elastic.css\">\n";
 //   echo "<link rel=\"stylesheet\" href=\"".VENDORS_LOC."/jqvmap/dist/jqvmap.min.css\">\n";
    echo "<link rel=\"stylesheet\" href=\"".CSS_LOC."css/style.css\">\n";
 echo "<link rel=\"stylesheet\" href=\"".CSS_LOC."css/tree_style.css\">\n";
    
    echo "<link rel='stylesheet' href=\"".VENDORS_LOC."lightbox/ekko-lightbox.min.css\">\n";
echo "<script src=\"".VENDORS_LOC."lightbox/ekko-lightbox.min.js\"></script>\n";
echo "<link rel=\"stylesheet\" href=\"".CSS_LOC."css/open_sans.css\">\n";
 echo "<STYLE>.table-hover> tbody> tr:hover{
    background-color:yellow;
}</STYLE>";
   ?>
   


 <?php
//we need validation when create new <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

if (isset($_GET["new"]) || isset($_GET['modify']) || isset($_GET['into_stock']))
{
echo "<script src=\"".VENDORS_LOC."jquery-validation/dist/jquery.validate.min.js\"></script>";
if ($lang!="en" && file_exists(VENDORS_PATH."jquery-validation/dist/localization/messages_".$lang.".js"))
echo "<script src=\"".VENDORS_LOC."jquery-validation/dist/localization/messages_".$lang.".js\"></script>";
}

?>
<script>

function ajax_call(param1,param2,param3,param4,param5,url,div_name)
{
   $.ajax({

     type: "GET",
     url: url,
     data: { ajax: 1, param1: param1, param2: param2, param3: param3,param4: param4,param5: param5 } , // appears as $_GET['id'] @ your backend side
     success: function(data) {
     
          $('#'+div_name).html(data);
        window.scrollTo(0,0);
     }

   });

}
</script>
</head>
<?php
}

?>

