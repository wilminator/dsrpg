<?php
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'fight_store.php';

function finish_fight()
    {
    //Record the error data
    //log_error("Client: $_SESSION[userid]\nTerminating fight\nTeam:$_SESSION[teamid]",100);

    /*
    //If the fight is not in session then bail;
    if(!isset($_SESSION['prefight']))
        {
        log_error("HACK:\nAttempt to terminate a fight while not part of a fight\nClient: $_SESSION[userid]\nTerminating fight\nTeam:$_SESSION[teamid]",100);
        return;
        }

    //Create a variable shortcuts.
    $fight=$_SESSION['fight'];
    $player_party=$_SESSION['player_party'];
    */
    $fight_store=new FIGHT_STORE();
    $result=$fight_store->get_fight($GLOBALS['userid'],$_SESSION['fightid']);
    //If there is no active fight then bail;
    if($result==false)
        {
        log_error("Attempt to terminate a fight while not part of a fight\nClient: $_SESSION[userid]\nProcessing fight commands\nTeam:$_SESSION[teamid]",100);
        queue_response('fight_terminate','pick_team.php');
        unset($_SESSION['fightid']);
        return;
        }
    extract($result);

    //If both parties are alive, then instruct the client to reload the data
    $imdead=$fight->test_own_party_dead($player_party);
    $theyredead=$fight->test_other_parties_dead($player_party);
    if ($imdead==false&&$theyredead==false)
        {
        log_error("Client: $_SESSION[userid]\nCan't terminate fight- both parties are still alive\nTeam:$_SESSION[teamid]",100);
        queue_response('request_fight_data');
        return;
        }

    /*
    //Purge fight data (except $fight, for now.  That is how PXP is calculated.
    unset($_SESSION['prefight']);
    unset($_SESSION['combat_playback']);
    unset($_SESSION['sequence']);
    unset($_SESSION['js_stack']);
    */
    //Direct client to switch to the map (temp hack- js will change pages)
    //queue_response('switch_to_map');
    queue_response('fight_terminate','proc_fight.php');
    }
?>
