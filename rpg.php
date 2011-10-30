<?php
//Tell where the includes are.
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';

//Prepare the error trapping functions
require_once INCLUDE_DIR.'errorlog.php';

$dummy=null;

#Globals
require_once INCLUDE_DIR.'paths.php';
#HTML scritps
require_once INCLUDE_DIR.'html/html_fight.php';
#Prep the game objects.
require_once INCLUDE_DIR.'fight.php';
require_once INCLUDE_DIR.'fight_store.php';

require_once INCLUDE_DIR.'jobs.php';
require_once INCLUDE_DIR.'items.php';
require_once INCLUDE_DIR.'abilities.php';
require_once INCLUDE_DIR.'personalities.php';
#AI for the monsters
require_once INCLUDE_DIR.'ai.php';
#We may need the javascript file stack.
require_once INCLUDE_DIR.'html/html_javascript.php';

#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_login('/auth/login.php');
$auth=get_auth();
$auth->add_membership($userid,'dse');
redirect_on_hold($userid,'dse','on_hold.php','../index.php');

//Check for party completeness
/* OLD WAY
if(!isset($_SESSION['prefight']))
    {
    header('Location: proc_fight.php');
    exit;
    }
*/
//New way!
if(isset($_SESSION['fightid']))
    $fightid=$_SESSION['fightid'];
else
    $fightid=0;
$fight_store=new FIGHT_STORE();
$result=$fight_store->get_fight($userid,$fightid);
if($result==false)
    {
    header('Location: pick_team.php');
    exit;
    }
extract($result);
if($active=="no")
    {
    header('Location: proc_fight.php');
    exit;
    }

if (is_null($player_party)) {
    echo 'There is no player party.<br>';
    require_once INCLUDE_DIR.'team_store.php';
    $team_store = new TEAM_STORE();
    foreach ($fight->parties as $party)
        {
        foreach ($party->teams as $id=>$members)
            {
            $team = $team_store->get_team($id);
            var_dump($team);
            echo '<hr>';
            if ($team->playerid == $userid)
                {
                $player_party = $party;
                $teamid = $id;
                }
            }
        }
    if (is_null($player_party))
        {
        var_dump($result);
        exit;
        }
}

$_SESSION['fightid']=$fightid;

html_combat($fightid, $fight, $player_party, $teamid);
?>