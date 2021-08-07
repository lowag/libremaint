
<?php
//$number_all $pagenumber
if (!isset($number_all))
$number_all=$dba->affectedRows();


$pagemax=intval($number_all/ROWS_PER_PAGE);
if ($number_all%ROWS_PER_PAGE)
$pagemax++;
//echo $pagemax;


if ($pagemax>1) //there are more then 1 page
{	

echo "<nav aria-label=\"pagination\">\n";
echo "<ul class=\"pagination\">\n";
	$t="/&pagenumber=".$pagenumber."/";
	//$q=$_SERVER['PHP_SELF']."?".preg_replace($t,'',$_SERVER['QUERY_STRING']);
	$page=lm_isset_str('page');
	$q=URL."index.php?page=".$page;
//	echo $q;
	//echo "<table><tr><td>";
	if ($pagenumber>1)
	{	
	$prev=$pagenumber-1;	
     echo "<li class=\"page-item\">\n";
     echo "<a class=\"page-link\" href=\"$q&pagenumber=$prev\" tabindex=\"-1\">".gettext("Previous")."</a>\n";
    echo "</li>\n";

	}
	
	
	$old=1;
	while ($old<$pagemax+1)
	{
	if ($old<10 || $old>$pagenumber-3 && $old<$pagenumber+3 || $old>$pagemax-10)
	{
	$pont=0;	
	if ($old==$pagenumber)
	{
	 echo "<li class=\"page-item active\">\n";
     echo "<a class=\"page-link\" href=\"".$q."&pagenumber=".$old."\">".$old." <span class=\"sr-only\">".gettext("(current)")."</span></a>\n";
    echo "</li>\n";
	}
	else
	echo "<li class=\"page-item\"><a class=\"page-link\" href=\"".$q."&pagenumber=$old\">".$old."</a></li>\n";
   	
	}
	else
	{
	if ($pont<3)	
	echo "<li class=\"page-item\">.</li>\n";
  	$pont++;
	}	
	$old++;	
	}
	
	if ($pagenumber!=$pagemax)
    {
    $next=++$pagenumber;
    echo "<li class=\"page-item\">\n";
    echo "<a class=\"page-link\" href=\"".$q."&pagenumber=".$next."\">".gettext("Next")."</a>\n";
    echo "</li>\n";
    echo "</ul>\n";
    echo "</nav>\n";
    }

}

?>
