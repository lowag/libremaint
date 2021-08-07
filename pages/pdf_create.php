<?php
if (isset($_GET['title']) && $_GET['title']=='create_task_list')
include(INCLUDES_PATH.'pdf_create/task_list.php');
else if(isset($_GET['title']) && $_GET['title']=='work_stat_by_assets' || isset($_POST['start_date']))
{
include(INCLUDES_PATH.'pdf_create/work_stat_by_assets.php');
}
else
lm_die("Something went wrong...");
?>
