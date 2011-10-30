<?php
#Prep the game objects.
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';
require_once INCLUDE_DIR.'fight.php';
require_once INCLUDE_DIR.'fight_store.php';
#Make items
require_once INCLUDE_DIR.'items.php';
#Make abilities
require_once INCLUDE_DIR.'abilities.php';
#Make jobs
require_once INCLUDE_DIR."jobs.php";
#Make personalities
require_once INCLUDE_DIR.'personalities.php';

#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_membership_access('dse','/auth/login.php','index.php');
redirect_on_hold($userid,'dse','on_hold.php','../index.php');


if(isset($_SESSION['fightid']))
    {
    $fight_store=new FIGHT_STORE();
    $result=$fight_store->get_fight($userid,$_SESSION['fightid']);
    extract($result);
    echo "Got a fight.<br>";
    }
else
    {
    $active="no";
    $seen="yes";
    unset($_SESSION['fightid']);
    header('Location: pick_team.php');
    exit;
    }


/*if($active!="no")
    {
    unset($_SESSION['fightid']);
    header('Location: pick_team.php');
    exit;
    }
*/

//OK, now process health benefits.
$party=&$fight->parties[$player_party];
$eparty=&$fight->parties[1-$player_party];
$epxp=$eparty->get_pxp();
$pxp=$party->get_pxp();

/*
#Fake the funk- distribute XP
$party_size=0;
foreach(array_keys($party->groups) as $gindex)
    $party_size+=$party->groups[$gindex]->count();

foreach(array_keys($party->groups) as $gindex)
    foreach(array_keys($party->groups[$gindex]->characters) as $cindex)
        {
        $character=&$party->groups[$gindex]->characters[$cindex];
        if($character->current['HP']==0)
            $character->exp+=floor($epxp/$party_size/4);
        else
            $character->exp+=floor($epxp/$party_size);
        $character->level();
        }
#End faking the funk
*/


//Distribute the xp tree
//echo php_data_to_js($fight).'<br><br>';
$left=$fight->process_exp_tree();
//echo php_data_to_js($fight).'<br><br>';
foreach(array_keys($party->groups) as $gindex)
    foreach(array_keys($party->groups[$gindex]->characters) as $cindex)
        {
        $character=&$party->groups[$gindex]->characters[$cindex];
        $diff=$character->exp-$character->old_exp;
        echo "{$character->name} has earned $diff XP.<br>\n";
        if($character->jobid>0)
            {
            $result=$character->level();
            foreach($result['data'] as $line)
                echo "$line<br>\n";
            }
        }
//echo "There are $left parties left.<br>";

$ratio=$epxp/4.0/$pxp;
foreach(array_keys($party->groups) as $gindex)
    foreach(array_keys($party->groups[$gindex]->characters) as $cindex)
        {
        $character=&$party->groups[$gindex]->characters[$cindex];
        if($character->current['HP']==0)
            $character->current['HP']+=floor($character->base['HP']*$ratio*2);
        else
            {
            $character->current['HP']+=floor($character->base['HP']*$ratio);
            $character->current['MP']+=floor($character->base['MP']*$ratio*2);
            }
        if($character->current['HP']>$character->base['HP'])
            $character->current['HP']=$character->base['HP'];
        if($character->current['MP']>$character->base['MP'])
            $character->current['MP']=$character->base['MP'];
        }
if(count($party->teams)>0)
    $party->store_party();

#Fake the funk- distribute gold
$gold=0;
foreach(array_keys($eparty->groups) as $gindex)
    foreach(array_keys($eparty->groups[$gindex]->characters) as $cindex)
        if($eparty->groups[$gindex]->characters[$cindex]->is_dead())
            $gold+=$eparty->groups[$gindex]->characters[$cindex]->gold;

if(count($party->teams)>0)
    $take=$party->distribute_gold($gold);
else
    $take=0;
echo "Each team received $take gold.<br>\n";
#End faking the funk

unset($_SESSION['fightid']);
?>
<p><a href="setup_fight.php">Return to the team selection menu.</a></p>
