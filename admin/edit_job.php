<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_JOB','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'functions.php';
require_once INCLUDE_DIR.'job_store.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';

if(get_magic_quotes_gpc())
    $_POST=array_map('stripslashes',$_POST);

if(!function_exists('php_data_to_js'))
    {
    require_once INCLUDE_DIR.'js_rip.php';
    }

$job_store=new JOB_STORE;

if(isset($_POST['OP'])&&$_POST['OP']=="Update Database")
    {
    //Get the job id number. 0 is a new job.
    $jobindex=$_REQUEST['job'];

    //Get the ability count
    $ability_count=$_POST['ability_count'];
    $abilities=array();
    
    for($count=0;$count<=$ability_count;$count++)
        if(!isset($_POST["del_ability_$count"])
            && $_POST["ability_level_$count"]>0)
            $abilities[]=array('ability'=>$_POST["ability_$count"],
                'level'=>$_POST["ability_level_$count"]);

    //Make a new job
    $this_job=new JOB(
        $_POST['name'],$_POST['need'],
        array($_POST['HP'],$_POST['MP'],$_POST['Speed'],$_POST['Accuracy'],$_POST['Strength'],$_POST['Dodge'],
            $_POST['Block'],$_POST['Power'],$_POST['Resistance'],$_POST['Focus']),
        $abilities);

    //update that job in the databse
    $jobindex=$job_store->set_job($this_job,$jobindex);
    }
else
    $jobindex=$_REQUEST['job'];

$jobs=&$job_store->get_all_jobs();
if($jobindex>0)
    $job=&$jobs[$jobindex];
else
    $job=new JOB(
        'New Job',100,
        array(12.00,6.00,8.00,8.00,8.00,7.50,8.00,8.00,8.00,8.00),
        array());

//Setup HTML display variables.
$ability_list=array(0=>'');
$result=mysql_do_query("select abilityid,name,description,mp_used from abilities order by name");
while($data=mysql_fetch_assoc($result))
    {
    $ability_list[$data['abilityid']]="$data[name] ($data[mp_used])";
    $ability_desc[$data['abilityid']]=$data['description'];
    }

//Default input box size
$def_size=array('size'=>7);
//Default input box size
$def_textarea=array('rows'=>3,'cols'=>50);
?>
<script>
abilities=<?php echo php_data_to_js($ability_desc); ?>;

function show_ability_description(object)
    {
    var ability=object.value;
    document.form_data.ability_desc.value=abilities[ability];
    }

personalities=<?php echo php_data_to_js($personality_pic); ?>;

function show_personality(object)
    {
    var ability=object.value;
    document.form_data.pic.src=personalities[ability];
    }
</script>
<form method="post" action="edit_job.php">
<table>
  <tr><th>
    <table>
      <tr>
        <?php
        $tindex=$jobindex;
        do  {
           	$tindex--;
            } while($tindex>0 && !array_key_exists($tindex,$jobs));
        if ($tindex>0)
            echo "<td><a href=\"edit_job.php?job=$tindex\">Previous job</a></td>"
        ?>
        <td><a href="jobs.php">Return to job list</a></td>
        <?php
        echo "<td><a href=\"delete_job.php?job=$jobindex\">Delete this job</a></td>"
        ?>
        <td><a href="edit_job.php?job=0">New job</a></td>
        <?php
        $tindex=$jobindex;
        $keys=array_keys($jobs);
        sort($keys);
        end($keys);
        $last=end($keys);
        do  {
           	$tindex++;
            } while($tindex<=$last && !array_key_exists($tindex,$jobs));
        if ($tindex<=$last)
            echo "<td><a href=\"edit_job.php?job=$tindex\">Next job</a></td>"
        ?>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Job Title</th>
        <td>
          <?php make_input('name',$job->name); ?>
          <?php make_input('job',$jobindex,array('type'=>'hidden')); ?>
        </td>
        <th>XP Need</th>
        <td>
          <?php make_input('need',$job->need,$def_size); ?>
        </td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
  <tr><th>
    <table>
      <?php
      echo "<tr>\n";
      foreach($job->upgrade as $key=>$value)
          echo "<th>$key</th>\n";
      echo "</tr>\n<tr>\n";
      foreach($job->upgrade as $key=>$value)
          {
          echo "<th>";
          make_input($key,$job->upgrade[$key],$def_size);
          echo "</th>\n";
          }
      echo "</tr>\n";
      ?>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Ability</th>
        <th>Level Earned</th>
        <th>Delete?</th>
        <th>Desc</th>
      </tr>
      <?php
        for($count=0;$count<count($job->abilities);$count++)
            {
            echo "      <tr>\n        <td>";
            make_select("ability_$count",$job->abilities[$count]['ability'],$ability_list);
            echo "</td>\n        <td>";
            make_input("ability_level_$count",$job->abilities[$count]['level'],$def_size);
            echo "</td>\n        <td>";
            make_checkbox("del_ability_$count",false);
            echo "</td>\n        <td>";
            echo $ability_desc[$job->abilities[$count]['ability']];
            echo "</td>\n      </tr>\n";

            }
        ?>
      <tr>
        <td><?php make_select("ability_$count",'',$ability_list); ?></td>
        <td><?php make_input("ability_level_$count",0,$def_size); ?></td>
        <td><?php make_input("ability_count",count($job->abilities),array('type'=>'hidden')); ?></td>
        <td><?php make_textarea("ability_desc",'',$def_textarea); ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <td><input type="submit" name="OP" value="Update Database"></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <?php
        $tindex=$jobindex;
        do  {
           	$tindex--;
            } while($tindex>0 && !array_key_exists($tindex,$jobs));
        if ($tindex>0)
            echo "<td><a href=\"edit_job.php?job=$tindex\">Previous job</a></td>"
        ?>
        <td><a href="jobs.php">Return to job list</a></td>
        <?php
        echo "<td><a href=\"delete_job.php?job=$jobindex\">Delete this job</a></td>"
        ?>
        <td><a href="edit_job.php?job=0">New job</a></td>
        <?php
        $tindex=$jobindex;
        $keys=array_keys($jobs);
        sort($keys);
        end($keys);
        $last=end($keys);
        do  {
           	$tindex++;
            } while($tindex<=$last && !array_key_exists($tindex,$jobs));
        if ($tindex<=$last)
            echo "<td><a href=\"edit_job.php?job=$tindex\">Next job</a></td>"
        ?>
      </tr>
    </table>
  </th></tr>
</table>
</form>
