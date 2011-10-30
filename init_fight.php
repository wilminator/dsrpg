<?php
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';

#Prep the game objects.
require_once INCLUDE_DIR.'fight.php';
require_once INCLUDE_DIR.'monsters.php';
require_once INCLUDE_DIR.'fight_store.php';
require_once INCLUDE_DIR.'personalities.php';
require_once INCLUDE_DIR.'ai.php';
require_once INCLUDE_DIR.'paths.php';

#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_login('/auth/login.php');
$auth=get_auth();
$auth->add_membership($userid,'dse');
redirect_on_hold($userid,'dse','on_hold.php','../index.php');

#If all the data we need isn't here, then load in the filler data.
if(!isset($_SESSION['party']))
    {
    header('Location: setup_fight.php');
    exit;
    }

#Define the background and music.
$background='forest.png';
$music='battle_2.mp3';

$choices = array();
$dir = dir(MUSIC_DIR);
while (false !== ($file = $dir->read()))
    if($file!='..' && $file!='.')
        {
        $choices[]=$file;
        }
$music = $choices[mt_rand(0,count($choices) - 1)];

#Make the fight.
$fight=new FIGHT($background,$music);

#Add the hero party.
$party=$_SESSION['party'];
$fight->add($party);

#Generate the monster party.
//$monster_store=new MONSTER_STORE;
$pxp=$party->get_pxp();
$seed = mt_rand(0, min(array($pxp/2, 1000)));
$qty = round(18 + pow($seed - 500, 3) * 18 / (500 * 500 * 500));
if ($qty<1) $qty=1;

$monster_party = create_monster_party($pxp, count($party->get_character_list(null)), $qty);

$fight->add($monster_party);

#Setup ally and enemy links.
$fight->parties[0]->enemies[]=1;
$fight->parties[1]->enemies[]=0;
$fight->parties[0]->allies[]=0;
$fight->parties[1]->allies[]=1;

#Check to see which party we will play as now.
if (isset($_GET['eparty']))
    {
    $player_party=1;
    $_SESSION['teamid']=-1;
    //Give player-controlled monster party default AI actions.
    $party=$fight->parties[$player_party] ;
    foreach($party->groups as $grpnumber=>$group)
        foreach($group->characters as $chrnumber=>$hero)
            if(!$fight->parties[$player_party]->groups[$grpnumber]->characters[$chrnumber]->is_dead())
                {
                #Hey, let's try the AI!
                $enemy=&$fight->parties[$player_party]->groups[$grpnumber]->characters[$chrnumber];
                $mycmd=ai($fight,$player_party,$grpnumber,$chrnumber);
                list($cmd,$use,$pty,$grp,$chr)=explode(',',$mycmd);
                $enemy->command=$cmd;
                $enemy->using=$use;
                $enemy->target=array($pty,$grp,$chr);
                }
    }
else
    {
    $player_party=0;

    $hero_party=&$fight->parties[$player_party];
    $monster_party=&$fight->parties[1-$player_party];

    //See which enemies are alive.
    $live_heroes=array();
    foreach($monster_party->groups as $grpnumber=>$group)
        foreach($group->characters as $chrnumber=>$hero)
            {
            $true_hero=&$monster_party->groups[$grpnumber]->characters[$chrnumber];
            if(!$true_hero->is_dead())
                $live_heroes[$grpnumber][]=$chrnumber;
            }

    //Target some live enemies.
    foreach($hero_party->groups as $grpnumber=>$group)
        foreach($group->characters as $chrnumber=>$hero)
            {
            $true_hero=&$hero_party->groups[$grpnumber]->characters[$chrnumber];
            $grp=array_rand($live_heroes);
            $chr=$live_heroes[$grp][array_rand($live_heroes[$grp])];
            $true_hero->command=1;
            $true_hero->using=0;
            $true_hero->target=array(1-$player_party,$grp,$chr);
            }
    }

/* OK!!! STORE THE FIGHT IN THE DB!!!
#Save to session.
//Save the fight.
$_SESSION['fight']=$fight;
//Create a dummy prefight.
$_SESSION['prefight']=$fight;
//Create a empty combat playback.
$_SESSION['combat_playback']=array();
//Set the sequence to 0.
$_SESSION['sequence']=0;
//Set an empty js file stack.
$_SESSION['js_stack']=array();
//Set the player party.
$_SESSION['player_party']=$player_party;
*/
unset($_SESSION['party']);
unset($_SESSION['team']);

//Create an array of the player's parties
//$playerid,$teamid,$player_party
$players[]=array('playerid'=>$_SESSION['userid'],'teamid'=>$_SESSION['teamid'],'player_party'=>$player_party);

//Store the fight data in the DB.
$fight_store=new FIGHT_STORE();
$result = $fight_store->init_fight($fight,$players);
if ($result==false) 
    {
    echo 'Failure creating fight data.';
    }
else
    {
    $_SESSION['fightid'] = $result;
    header('Location: rpg.php');
    }
?>