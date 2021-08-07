<body>
<?php
?>
 <!-- Left Panel -->

    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="./"><img src="images/logo.png" alt="Logo"></a>
                <a class="navbar-brand hidden" href="./"><img src="images/logo2.png" alt="Logo"></a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active">
                        <a href="index.php"> <i class="menu-icon fa fa-dashboard"></i>
			<?php echo (gettext("Mainboard"));?> </a>
                    </li>
<?php 

#$pages array from config/lm-settings.php 
foreach ($pages as $page){
if (isset($_SESSION[$page[2]])){
echo "<li class=\"menu-item-has-children dropdown\" >\n";

echo "<a href=\"index.php?page=".$page[1]."\" class=\"dropdown-toggle\""; 
if ($req_page==$page[1]){
echo " data-toggle=\"dropdown\"";

}
echo " aria-haspopup=\"true\" aria-expanded=\"false\" id=\"".$page[1]."\"> <i class=\"menu-icon\">".$page[3]."</i>";
if ($req_page==$page[1]){
    echo "<span style=\"color:orange \">";
    echo  ucfirst($page[0]);
    echo "</span>";
    }else
    echo  ucfirst($page[0]);
echo "</a>\n";

    if ($req_page==$page[1]){
    echo "<ul class=\"sub-menu children dropdown-menu\">\n";
    echo "<li><i class=\"fa fa-puzzle-piece\"></i><a href=\"index.php?page=".$page[1]."\">\n";
    if ($req_page==$page[1]){
    echo "<span style=\"color:orange \">";
    echo $page[0];
    echo "</span>\n";
    }else
    echo $page[0];
    echo "\n</a></li>\n";
    
    if (isset($page[6]) && isset($_SESSION[$page[6]])){
    echo "<li><i class=\"fa fa-puzzle-piece\"></i><a href=\"".$page[5]."\">";
    if ($req_page==$page[1]){
    echo "<span style=\"color:orange \">";
    echo $page[4];
    echo "</span>";
    }else
    echo $page[4];
    echo "</a></li>\n";}
    
    if (isset($page[10]) && isset($_SESSION[$page[10]])){
    echo "<li><i class=\"fa fa-puzzle-piece\"></i><a href=\"".$page[9]."\">";
    if ($req_page==$page[1]){
    echo "<span style=\"color:orange \">";
    echo $page[8];
    echo "</span>";
    }else
    echo $page[8];
    echo "</a></li>\n";}
    echo "</ul>\n";
    
    echo "</li>\n";
    }//if (isset($_GET["page"]) && $_GET["page"]==$page)
    }//if ($_SESSION['user_level']>$page[1])
}//foreach
if ($_SESSION['user_level']>3 && $_SESSION['SEE_WORKS']){
echo "<li class=\"menu-item-has-children dropdown\" >\n";
echo "<a href=\"index.php?page=works\" class=\"dropdown-toggle\" aria-haspopup=\"true\" aria-expanded=\"false\" id=\"works\"> <i class=\"menu-icon\">".gettext("WR")."</i>".gettext("Works")."</a>\n";
}               

?>
               

                          
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->

    <!-- Left Panel -->

