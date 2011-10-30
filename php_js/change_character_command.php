<?php
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'fight_store.php';

if(!function_exists('json_encode'))
    {
    require_once INCLUDE_DIR.'js_rip.php';
    }

function change_character_command($party,$group,$character,$action,$current_sequence)
    {
    $fight_store=new FIGHT_STORE();
    $result=$fight_store->get_fight($GLOBALS['userid']);
    //If there is no active fight then bail;
    if($result==false)
        {
        log_error("HACK:\nAttempt to terminate a fight while not part of a fight\nClient: $_SESSION[userid]\nProcessing fight commands",100);
        return;
        }
    extract($result);

    //Update the actions of the character
    $result=$fight->set_PC_action($party,$group,$character,$teamid,$action);
    //Assign fight back to session to ensure containment.
    $fight_store->update_fight_action($fight,$current_sequence);
    //Record the error data
    //log_error("Client: $_SESSION[userid]\nChanging action for PC: $party;$group;$character\nTeam:$_SESSION[teamid]\n".json_encode($action)."\nResult:".json_encode($result),100);
    }
?>
