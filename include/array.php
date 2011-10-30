<?php

/**
 * array.php
 * PHP Array handling functions
 * @version 0.0.1
 * @copyright 2003 The Wilminator
 **/

 /*********************************************
 * array_strip
 * 
 * array_strip runs through recursive arrays 
 * looking for objects or values based on 
 * specified criteria.  It then returns these
 * values in recursive arrays that mirror the 
 * original array.  This function is meant to
 * copy object values or array values out of 
 * a recursive array. $format is an eval'd
 * expression that either looks like an index
 * or an object variable reference, IE "[0]"
 * or "->name". Variables can be passed in as
 * hashes.
 * 
 * IE- You have a two dimensional array of
 * customer objects that have a name property.
 * Using array_strip, you can create a two
 * dimensional array of just names.
 * ******************************************/
function array_strip($array,$format,$variables=null)
    {
    //var_dump($array);
    $result=array();
    if(is_array($variables))
        extract($variables);
    foreach($array as $key=>$unit)
        if(is_array($unit))
            $result[$key]=array_strip($unit,$format,$variables);
        else
            {
            //echo "return \$unit$format;\n";
            $result[$key]=eval("return \$unit$format;");
            }
    //var_dump($result);
    return $result;
    }

function create_array($keys,$values,$nv)
    {
    $retval=array();
    if(is_array($keys))
        {
        foreach($keys as $index=>$key)
            if(isset($values[$index]))
                {
                if (is_numeric($values[$index]))
                    {
                    $retval[$key]=$values[$index]+0.0;
                    }
                else
                    {
                    $retval[$key]=$values[$index];
                    }
                }
            else
                $retval[$key]=$nv;
        }
    else
        log_error("Bogus array.");
    return $retval;
    }
?>