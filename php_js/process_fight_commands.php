<?php
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'fight_store.php';

function process_fight_commands($sequence_number)
    {
    //Record the error data
    //log_error("Client: $_SESSION[userid]\nProcessing fight commands",100);

    $fight_store=new FIGHT_STORE();
    //Get the fight data
    $result=$fight_store->get_fight($GLOBALS['userid'],$_SESSION['fightid']);
    //If there is no active fight then bail;
    if($result==false)
        {
        log_error("HACK:\nTrying to advance a fight while not part of a fight\nClient: $GLOBALS[userid]",100);
        queue_response('fight_terminate','pick_team.php');
        return;
        }
    extract($result);
    if($active=="no")
        {
        log_error("HACK:\nTrying to advance a fight that has finished\nClient: $GLOBALS[userid]",100);
        queue_response('fight_terminate','pick_team.php');
        exit;
        }
    //If sequence_number is less than sequence, then this is an update to
    //a prior part of the fight.  Ignore and send a refresh instead.
    if($sequence_number<$sequence)
        {
        //Send the response
        $combat_playback=$action_list;
        queue_response('receive_fight_data',$sequence,$prefight,$combat_playback,$fight,$timeout);
        return;
        }
    //Update prefight.
    $prefight=unserialize(serialize($fight));
    //OK, update the sequence number.
    $result=$fight_store->update_player($GLOBALS['userid'],$sequence_number);
    //If result is true, then we have locked out everyone else and will process the fight.
    if($result)
        {
        //Process a turn of combat
        $action_list=process_fight(&$fight,&$sequence,$players);
        //Store the results
        $result=$fight_store->update_fight($prefight,$action_list,$fight,$sequence);
        extract($result);
        if($result==false)
            log_error("Did not record the fight.");
        //Convert the action log for javascript
        //!!TEST!! No conversion!
        //$combat_playback=convert_action_list_to_playlist($action_list,$player_party,$fight);
        $combat_playback=$action_list;
        //Send the response
        queue_response('receive_fight_data',$sequence,$prefight,$combat_playback,$fight,$timeout);
        }
    else
        queue_response('wait_for_fight_timeout');
    }
?>
