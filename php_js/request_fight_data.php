<?php
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'fight_store.php';

function request_fight_data($fightid=0)
    {
    //Record the error data
    //log_error("Client: $_SESSION[userid]\nRequesting fight data $fightid");

    //If the fight is not in session then bail;
    /* OLD WAY
    if(!isset($_SESSION['player_party'])||!isset($_SESSION['prefight']))
        {
        queue_response('fight_terminate','proc_fight.php');
        return;
        }

    //Create a variable shortcuts.
    $prefight=$_SESSION['prefight'];
    $fight=$_SESSION['fight'];
    $combat_playback=$_SESSION['combat_playback'];
    $sequence=$_SESSION['sequence'];
    */
    //New way!
    $fight_store=new FIGHT_STORE();
    if($fightid==0)
        {
        $result=$fight_store->get_fight($GLOBALS['userid']);
        //If the fight is not in session then bail;
        if($result==false)
            {
            queue_response('fight_terminate','proc_fight.php');
            return;
            }
        extract($result);
        }
    else
        {
        $result=$fight_store->replay_fight($fightid);
        list($prefight,$actions,$fight)=$result;
        $sequence=1;
        $player_party=-1;
        }

    //Recalculate combat actions.
    //!!TEST!! No conversion!
    //$combat_playback=convert_action_list_to_playlist($action_list,$player_party,$fight);
    $combat_playback=$action_list;
    
    //log_error(var_export($prefight,true));
    //log_error(var_export($fight,true));

    //Send the response
    queue_response('receive_fight_data',$sequence,$prefight,$combat_playback,$fight,$timeout);
    }
?>
