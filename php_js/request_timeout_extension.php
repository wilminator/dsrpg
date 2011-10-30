<?php
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'fight_store.php';

function request_timeout_extension()
    {
    //Record the error data
    //log_error("Client: $_SESSION[userid]\nRequesting timeout extension",100);

    $fight_store=new FIGHT_STORE();
    //Get fight data
    $result=$fight_store->get_fight($GLOBALS['userid'],$_SESSION['fightid']);
    extract($result);
    if($timeout<-5*60)
        {
        //This guy is five minutes late and the party hasn't started. Give him a break.
        //Give him half the initial time to prep.
        $timeout=($fight->count()+3)/2;
        //Update the timeout.
        $fight_store->update_timeout($fight,$timeout);
        //Return the number of *seconds* before expiry.
        queue_response('update_timerbox',$timeout*60);
        }
    if($timeout<1*60&&$timeout>0) //Between one minute and timeout
        {
        //This guy has a slow client. Give him a break.
        //Give him 2 minutes to prep.
        $timeout=2;
        //Update the timeout.
        $fight_store->update_timeout($fight,$timeout);
        //Return the number of *seconds* before expiry.
        queue_response('update_timerbox',$timeout*60);
        }
    }
?>
