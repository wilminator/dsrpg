<?php
function detect_browser()
    {
    if(!isset($GLOBALS['browser']))
        {
        $GLOBALS['browser']=(boolean)strstr($_SERVER['HTTP_USER_AGENT'],'MSIE');
        }
    return $GLOBALS['browser'];
    }

function ie_browser()
    {
    $browser=detect_browser();
    return ($browser=='MSIE');
    }
/*
function ie_fix_image_url($url)
    {
    return (ie_browser()?$GLOBALS['ie_fix_image'].$url:$url);
    }
*/
?>