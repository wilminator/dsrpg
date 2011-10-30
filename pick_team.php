<?php
#Prep the game objects.
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'team_store.php';

require_once INCLUDE_DIR.'jobs.php';
require_once INCLUDE_DIR.'personalities.php';
//require_once INCLUDE_DIR.'personalities.php';
//require_once INCLUDE_DIR.'personalities.php';

#Get local function library
require_once 'functions.php';
#Load fight definitions
require_once INCLUDE_DIR.'fight.php';

#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_login('/auth/login.php');
add_context_membership($userid,'dse');
redirect_on_hold($userid,'dse','on_hold.php','../index.php');

#OK, new direction
//If there is a fight in progress, jump to it.
/*
if(isset($_SESSION['prefight']))
    {
    header('Location: rpg.php');
    exit;
    }
*/
//New way!
require_once INCLUDE_DIR.'fight_store.php';

$fight_store=new FIGHT_STORE();
$result=$fight_store->get_fight($userid);

if($result!=false)
    {
    header('Location: rpg.php');
    exit;
    }

//Check for admin permissions
$edit_items=check_permission($userid,'dse','EDIT_ITEM');
$edit_ability=check_permission($userid,'dse','EDIT_ABILITY');
$edit_monster=check_permission($userid,'dse','EDIT_MONSTER');
$edit_job=check_permission($userid,'dse','EDIT_JOB');
$edit_personality=check_permission($userid,'dse','EDIT_PERSONALITY');
$reset_db=check_permission($userid,'dse','RESET_DB');
#$=check_permission($userid,'dse','');

$team_store=new TEAM_STORE();
$teams=$team_store->get_all_teams_by_playerid($userid);
unset($_SESSION['party']);
?>
<html>
<head>
<title>Pick A Team To Play</title>
</head>
<body>
<h1>Pick a team to play</h1>
<table><tr>
<td><a href="../../">Return to the game info page</a></td>
<?php
if(count($teams)<3)
    echo "<td><a href=\"name_team.php\">Create a new team</a></td>\n";
if($edit_items||$edit_ability||$edit_job||$edit_monster||$edit_personality)
    echo '<td><a href="admin/">Go to the game admin menu</a></td>';
?>
</tr></table>
<table>
<?php
foreach(array_keys($teams) as $index)
    {
    echo "<tr><td>";
    display_team($teams[$index],FIGHTER_IMAGES_DIR);
    echo "</td><td>
<table height=\"100%\">
  <tr><td><a href=\"setup_fight.php?teamid={$teams[$index]->teamid}\">Play this team</a></td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr><td><a href=\"delete_team.php?teamid={$teams[$index]->teamid}\">Delete this team</a></td></tr>
  <tr><td>&nbsp;</td></tr>
</table></td></tr>";
    }
?>
</table>