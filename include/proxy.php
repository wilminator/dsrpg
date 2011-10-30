<?php
require_once INCLUDE_DIR.'errorlog.php';

require_once INCLUDE_DIR.'js_rip.php';

function process_data($data)
    {
    //Convert js data to php stuff
    $stuff=js_data_to_php($data,true);
    //If there was not an error, process the stuff
    if(!is_null($stuff))
        {
        /* Debugging Code
        if(count($stuff)>0)
            log_error("data: $data.\nstuff: ".php_data_to_js($stuff));
        */
        //It should be an array.  Itereate through each.
        foreach($stuff as $value)
            {
            //Ensure the function exists.
            if(!function_exists($value[0]))
                //Note this call is to the directory the main script
                //is located in.
                @include "php_js/$value[0].php";
            //If the function exists, then call it.
            if(function_exists($value[0]))
                {
                call_user_func_array($value[0],$value[1]);
                }
            else
                log_error("Could not execute a function named $value[0].");
            }
        }
    }

function queue_response()
    {
    //Ensure that there is at least an empty array to store into.
    if(!array_key_exists('__proxy_values',$GLOBALS))
        $GLOBALS['__proxy_values']=array();
    //Store all arguments as an array for transmission.
    $GLOBALS['__proxy_values'][]=func_get_args();
    }

function write_responses()
    {
    //Ensure that there is at least an empty array to transmit.
    if(!array_key_exists('__proxy_values',$GLOBALS))
        $GLOBALS['__proxy_values']=array();
    //Translate from php to js
    $output=php_data_to_js($GLOBALS['__proxy_values']);
    //Output the js.
    echo rawurlencode($output);
    //Clear the data buffer.
    unset($GLOBALS['__proxy_values']);
    }
?>
