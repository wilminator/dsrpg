<?php
#Prep the game objects.
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';

require_once INCLUDE_DIR.'team.php';
require_once INCLUDE_DIR.'party.php';
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'items.php';
require_once INCLUDE_DIR.'personalities.php';
require_once INCLUDE_DIR.'abilities.php';
require_once INCLUDE_DIR.'jobs.php';

#Include local libraries
require_once 'functions.php';

session_start();

#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_membership_access('dse','/auth/login.php','index.php');
redirect_on_hold($userid,'dse','on_hold.php','../index.php');

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
$fight_as_enemy=check_permission($userid,'dse','PLAY_AGAINST');

#Check for a cancel
if(isset($_POST['CANCEL']))
    {
    unset($_SESSION['party']);
    header('Location: pick_team.php');
    exit;
    }

#Check for shopping
if(isset($_POST['SHOP']))
    {
    header('Location: store.php');
    exit;
    }

#Check for action
if(isset($_POST['ACTION']))
    {
    header('Location: hero_use_thing.php');
    exit;
    }

#If all the data we need isn't here, then load in the filler data.
if(!isset($_SESSION['party']))
    {
    if(isset($_SESSION['teamid']))
        $teamid = $_SESSION['teamid'];
    if(isset($_GET['teamid']))
        $teamid = $_GET['teamid'];
    if(isset($teamid))
        {
        require_once INCLUDE_DIR.'team_store.php';
        $team_store=new TEAM_STORE();
        $team=$team_store->get_team($teamid);
        if($team->playerid==$userid)
            {
            $party=new PARTY();
            $party->add_team($team);
            $_SESSION['party']=$party;
            $_SESSION['team']=$team;
            $_SESSION['teamid']=$team->teamid;
            }
        }
    if(!isset($_SESSION['party']))
        {
        header('Location: pick_team.php');
        exit;
        }
    }

#Generate a temporary hero party.
$party=$_SESSION['party'];
$team=$_SESSION['team'];

#Set a blank error message
$error='';

#Check for resting
if(isset($_POST['INN']))
    {
    if($team->gold>=round(sqrt($team->get_pxp())))
        {
        $team->gold-=round(sqrt($team->get_pxp()));
        $error='Your team has rested.';
        foreach(array_keys($team->characters) as $index)
            {
            $character=&$team->characters[$index];
            $character->current['HP']=$character->base['HP'];
            $character->current['MP']=$character->base['MP'];
            }
        foreach(array_keys($party->groups) as $group)
            foreach(array_keys($party->groups[$group]->characters) as $index)
                {
                $character=&$party->groups[$group]->characters[$index];
                $character->current['HP']=$character->base['HP'];
                $character->current['MP']=$character->base['MP'];
                }
        $team->store_team();
        }
    else
        $error="You do not have enough gold.";
    }

#Check for a move
if(isset($_GET['direction']))
    {
    $group=$_GET['group'];
    $position=$_GET['position'];
    switch($_GET['direction'])
        {
        case 'l':
            if($position>0)
                $party->move_character($group,$position,$group,$position-1);
            break;
        case 'r':
            if($position<GROUP_MAX_COUNT)
                $party->move_character($group,$position,$group,$position+1);
            break;
        case 'd':
            if($group<PARTY_MAX_COUNT)
                {
                $new_group=$group+1;
                while($new_group<PARTY_MAX_COUNT
                    && isset($party->groups[$new_group])
                    && $party->groups[$new_group]->count()>=GROUP_MAX_COUNT)
                    $new_group++;
                if($new_group<PARTY_MAX_COUNT)
                    $party->move_character($group,$position,$new_group,$position);
                }
            break;
        case 'u':
            if($group>0)
                {
                $new_group=$group-1;
                while($new_group>=0
                    && isset($party->groups[$new_group])
                    && $party->groups[$new_group]->count()>=GROUP_MAX_COUNT)
                    $new_group--;
                if($new_group>=0)
                    $party->move_character($group,$position,$new_group,$position);
                }
            break;
        }
    }

#Check for character selection
if(isset($_GET['equip']))
    {
    header("Location: setup_equip.php?group=$_GET[group]&position=$_GET[position]");
    exit;
    }

#If this is a continue command, then goto the init_fight page.
if(isset($_POST['DO_FIGHT']))
    {
    header("Location: init_fight.php");
    exit;
    }

#If this is a continue command, then goto the init_fight page.
if(isset($_POST['DO_MONSTER']))
    {
    header("Location: init_fight.php?eparty");
    exit;
    }

$pxp = $team->get_pxp();
echo "<p><b>Team:</b> {$team->name} <b>Gold:</b> {$team->gold} <b>PXP:</b> {$pxp}</p>";
if($error)
    echo "<p><font color=\"red\">$error</font></p>\n";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"8\">\n";
foreach(array_keys($party->groups) as $gindex)
    {
    echo "<tr>\n";
    foreach(array_keys($party->groups[$gindex]->characters) as $cindex)
        {
        echo "<td>\n";
        display_hero_tile($party,$party->groups[$gindex]->characters[$cindex],$gindex,$cindex,FIGHTER_IMAGES_DIR,MENU_IMAGES_DIR);
        echo "</td>\n";
        }
    echo "</tr>\n";
    }
echo "</table>\n";

?>

<p>
Click on hero to change equipment.<br>
Click on an arrow next to a hero to rearrange your party layout.<br>
Click on a button below to use items or skills, or cast spells, visit an inn to recover, go shopping for items, or to go fight (or die trying).<br></p>
<form method="post" action="setup_fight.php">
  <table>
    <tr>
      <td><input type="submit" name="ACTION" value="Use Spell, Skill, or Item"></td>
      <td><input type="submit" name="INN" value="Visit an inn for <?php echo round(sqrt($team->get_pxp())); ?> gold"></td>
      <td><input type="submit" name="SHOP" value="Go Shopping"></td>
      <td><input type="submit" name="DO_FIGHT" value="Fight With Party"></td>
    </tr>
<?php
if($fight_as_enemy)
    echo "<tr><td><input type=\"submit\" name=\"DO_MONSTER\" value=\"Fight Against Party\"></td></tr>";
?>
    <tr>
      <td><input type="submit" name="CANCEL" value="Go Back"></td>
    </tr>
  </table>
</form>