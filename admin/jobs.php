<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_JOB','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'job_store.php';
require_once INCLUDE_DIR.'effects.php';

$job_store=new job_STORE;
$jobs=&$job_store->get_all_jobs();

echo "
<center>
  <table cellpadding=\"4\" cellspacing=\"0\">
    <tr><td colspan=\"2\"><a href=\"edit_job.php?job=0\">Add new job</a></td><td colspan=\"3\"><a href=\"./\">Return to the admin menu</a></td><tr>
    ";
foreach($jobs as $index=>$job)
    if($index!=0)
        {
        $output="<tr>"
            ."<td><a href=\"edit_job.php?job={$index}\">{$index}</td>"
            ."<td><a href=\"edit_job.php?job={$index}\">{$job->name}</a></td>"
            ."<td>".$job->describe_stats()."</td>"
            ."<td><a href=\"delete_job.php?job={$index}\">delete</a></td>"
            ."</tr>\n";
        echo $output;
        }
echo "
  </table>
</center>";
?>
