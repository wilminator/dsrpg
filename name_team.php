<?php
#Prep the game objects.
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';
require_once INCLUDE_DIR.'team_store.php';
require_once INCLUDE_DIR.'constants.php';

session_start();

#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_membership_access('dse','/auth/login.php','index.php');
redirect_on_hold($userid,'dse','on_hold.php','../index.php');

#Check team count
$team_store=new TEAM_STORE();
$teams=$team_store->get_all_teams_by_playerid($userid);
if(count($teams)>=3)
    header('Location: pick_team.php');

#Check for responses
if(isset($_POST['CANCEL']))
    {
    unset($_SESSION['team']);
    header('Location: index.php');
    exit;
    }
if(isset($_POST['NAME_HEROES']))
    {
    $_SESSION['team']=new TEAM();
    $_SESSION['team']->name=$_POST['team_name'];
    header("Location: name_heroes.php?team_size=$_POST[team_size]");
    exit;
    }

//Make web objects
if(isset($_SESSION['team']))
    $team_name=$_SESSION['team']->name;
else
    $team_name='';

$team_size_options=array();
for($index=1;$index<=GROUP_MAX_COUNT;$index++)
    $team_size_options[$index]=$index;

if(isset($_REQUEST['team_name']))
    $team_size=$_REQUEST['team_name'];
else
    $team_size=1;

?>
<html>
<head>
<title>Name Your Team</title>
</head>
<body onload="document.form_data.name.focus();">
<h1>Name your new team</h1>
<form name="form_data" method="post">
<table>
<tr><th>Team Name</th><td><?php make_input("team_name",$team_name); ?></td></tr>
<tr><th>Team Size</th><td><?php make_select('team_size',$team_size,$team_size_options); ?></td></tr>
<tr><td colspan="2" align="center"><input type="submit" name="NAME_HEROES" value="Name this team's heroes"></td></tr>
<tr><td colspan="2" align="center"><input type="submit" name="CANCEL" value="Cancel team creation"></td></tr>
</table>
</body>
</html>