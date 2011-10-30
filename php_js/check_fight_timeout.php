<?php
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'fight_store.php';
if(!function_exists('json_encode'))
    {
    require_once INCLUDE_DIR.'js_rip.php';
    }

function check_fight_timeout($sequence_number)
    {
    //Record the error data
    //log_error("Client: $_SESSION[userid]\nChecking fight timeout\n".json_encode($_SESSION),100);

    $fight_store=new FIGHT_STORE();
    //Check expiry and task assignment
    $expired=$fight_store->has_timed_out($_SESSION['fightid'],$sequence_number);
    extract($expired);
    if(is_null($expired))
        {
        //We need to reload the fight.
        //Get fight data
        $result=$fight_store->get_fight($GLOBALS['userid'],$_SESSION['fightid']);
        extract($result);
        //$combat_playback=convert_action_list_to_playlist($action_list,$player_party,$fight);
        queue_response('receive_fight_data',$sequence,$prefight,$action_list,$fight,$timeout);
        }
    elseif($expired==false)
        {
        //We are not ready yet.
        queue_response('wait_for_fight_timeout',$timeout);
        }
    else
        {
        //We are the chosen ones- process this fight!
        //Get the fight data
        $result=$fight_store->get_fight($GLOBALS['userid'],$_SESSION['fightid']);
        extract($result);
        //Process a turn of combat
        $action_list=process_fight(&$fight,&$sequence,$players);
        //Store the results
        $result=$fight_store->update_fight($prefight,$action_list,$fight,$sequence);
        extract($result);
        if($result==false)
            log_error("Did not record the fight.");
        //Convert the action log for javascript
        $combat_playback=convert_action_list_to_playlist($action_list,$player_party,$fight);
        //Send the response
        queue_response('receive_fight_data',$sequence,$prefight,$combat_playback,$fight,$timeout);
        }
    }
?>
