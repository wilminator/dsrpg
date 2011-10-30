<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_JOB','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'job_store.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';

//Get the job id number. 0 is a new job.
$jobindex=$_REQUEST['job'];     

$job_store=new JOB_STORE;

if(isset($_GET['confirm']) && $_GET['confirm']=="YES")
    {
    //delete that job in the databse
    $job_store->delete_job($jobindex);
    header ('Location: jobs.php');
    exit;
    }

//Snag the name
$jobs=&$job_store->get_all_jobs();
$name=$jobs[$jobindex]->name;

?>
<table>
  <tr><th>
    <table>
      <caption><b>Are you sure you want to delete the <?php echo $name; ?>?</b></caption>
      <tr>
        <td>
          <?php
          echo "<a href=\"delete_job.php?job=$jobindex&confirm=YES\">Yes, delete this job.</a>";
          ?>
        </td>
        <td><a href="jobs.php">Do not delete this.</a></td>
      </tr>
    </table>
  </th></tr>
</table>

