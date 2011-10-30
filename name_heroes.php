<?php
#Prep the game objects.
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';
#Make personalities
require_once INCLUDE_DIR.'team.php';
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'personalities.php';
require_once INCLUDE_DIR.'jobs.php';

session_start();

#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_membership_access('dse','/auth/login.php','index.php');
redirect_on_hold($userid,'dse','on_hold.php','../index.php');

//Check for team object
if(!isset($_SESSION['team']))
    {
    header("Location: name_team.php");
    exit;
    }

#Check for responses
if(isset($_POST['GO_BACK']))
    {
    header("Location: name_team.php?team_size=$_GET[team_size]");
    exit;
    }
if(isset($_POST['MAKE_TEAM']))
    {
    $team=$_SESSION['team'];
    unset($_SESSION['team']);
    for($count=0;$count<$_GET['team_size'];$count++)
        {
        $character=hero($_POST["name$count"],$_POST["job$count"],1,$_POST["personality$count"]);
        $team->add($character);
        }
    $team->playerid=$userid;
    $team->store_team();
    header("Location: pick_team.php");
    exit;
    }

//Make web objects
$team_name=$_SESSION['team']->name;
$team_size=$_GET['team_size'];

#Job list
foreach($GLOBALS['jobs'] as $index=>$job)
    $job_list[$index]=$job->name;
asort($job_list);

#Personality List
foreach($GLOBALS['personalities'] as $index=>$personality)
    {
    $personality_list[$index]=$personality->name;
    $personality_pic[$index]=FIGHTER_IMAGES_DIR.$personality->base_data['images'][0];
    }
asort($personality_list);
reset($personality_list);
$personalityid=key($personality_list);
?>
<html>
<head>
<title>Choose Your Heroes</title>
<script>
personalities=<?php echo php_data_to_js($personality_pic); ?>;

function show_personality(object,id)
    {
    var ability=object.value;
    var pic=document.getElementById(id);
    pic.src=personalities[ability];
    }
</script>
</head>
<body onload="document.form_data.name0.focus();">
<h1>Choose your new team members</h1>
<form name="form_data" method="post">
<table>
<tr><th>Team Name</th><td><?php echo $team_name; ?></td></tr>
<tr><th>Team Size</th><td><?php echo $team_size; ?></td></tr>
<tr>
  <th>&nbsp;</th>
  <th>Hero Name</th>
  <th>Hero Job</th>
  <th>Hero Looks Like</th>
</tr>
    <?php
    for($count=0;$count<$_GET['team_size'];$count++)
        {
        $ordinal=$count+1;
        ?>
      <tr>
        <th>Hero <?php echo $ordinal; ?></th>
        <td><?php make_input("name$count",''); ?></td>
        <td><?php make_select("job$count",'',$job_list); ?></td>
        <td><?php make_select("personality$count",'',$personality_list,array('onkeyup'=>"show_personality(this,'pic$count');",'onclick'=>"show_personality(this,'pic$count');")); ?></td>
        <td>
          <img id="pic<?php echo $count; ?>" src="<?php echo $personality_pic[$personalityid]; ?>">
        </td>
      </tr>
        <?php
        }
    ?>
<tr><td colspan="2" align="center"><input type="submit" name="MAKE_TEAM" value="Make them into a team!"></td></tr>
<tr><td colspan="2" align="center"><input type="submit" name="GO_BACK" value="Return to the previous screen"></td></tr>
</table>
</body>
</html>
