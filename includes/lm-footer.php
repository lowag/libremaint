<?php
$dba->disconnect();
    // it needs for assets.php don't delete:
    echo "<script src=\"".VENDORS_LOC."jquery/dist/jquery.min.js\"></script>\n";
   // echo "<script src=\"".VENDORS_LOC."bootstrap/dist/js/bootstrap.min.js\"></script>";
    echo "<script src=\"".CSS_LOC."js/main.js\"></script>\n";
 //   echo "<script src=\"".VENDORS_LOC."chart.js/dist/Chart.bundle.min.js\"></script>";
echo "<script src=\"".CSS_LOC."js/dashboard.js\"></script>\n";
 //   echo "<script src=\"".CSS_LOC."js/widgets.js\"></script>";
//    echo "<script src=\"".VENDORS_LOC."jqvmap/dist/jquery.vmap.min.js\"></script>";
 //   echo "<script src=\"".VENDORS_LOC."jqvmap/examples/js/jquery.vmap.sampledata.js\"></script>";
  //  echo "<script src=\"".VENDORS_LOC."jqvmap/dist/maps/jquery.vmap.world.js\"></script>";

 if ($valid_page){// from lm-body.php
 echo "<script>$(\"#".$req_page."\").dropdown('toggle');\n";//make the
 echo "$(\"#".$req_page."\").on ('click', function (e) {  e.stopPropagation();})</script>\n";

 
 } 
  
  
?><script>


$(document).on("click", '[data-toggle="lightbox"]', function(event) {
  event.preventDefault();
  $(this).ekkoLightbox();
});


</script>
</body>

</html>
