<?php
require_once INCLUDE_DIR.'paths.php';

if(!function_exists('json_encode'))
    {
    require_once INCLUDE_DIR.'js_rip.php';
    }

function client_error($message,$data)
    {
    /*
    message is a message sent by the client
    data is a datastructure containing the bad data
    */
    //Record the error data
    log_error("Client: $_SESSION[userid]\nMessage: $message\n".json_encode($data),100);
    }
?>
