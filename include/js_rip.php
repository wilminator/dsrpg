<?php
/**
js_rip -- A library that provides PHP-Javascript data conversion.
Copyright (C) 2003,2004,2005 Mike Wilmes
This file is the only file in this library.

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public
License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Version 1.0.0    Released 2005-07-31
    Author:
        Michael Wilmes    mwilmes@wilminator.com
    Features:
        Provides two primatry functions that can translate PHP data structures
            to and from anonymous Javascript data structures.
        Supports objects and arrays.  PHP objects and associative arrays will
            become anonymous object structures in Javascript. Javascript
            objects will become PHP associative arrays.
    Does not support:
        Named object conversion.  All PHP objects will be reduced to
            anonymous Javascript objects and Javascript objects will be
            converted into PHP associative arrays.
        Conversion of functions.  PHP functions cannot be exported to
            Javascript and Javascript functions cannot be exported to PHP.
        PHP and Javascript associative arrays must use valid variable names.
        Negative PHP array indicies. Numeric PHP array indices cannot be
            negative and will be truncated.
*/

/* php_data_to_js
Input:
    value (Mixed)
    tab (Optional String)
    indent (Optional String)
Output:
    Return value (String): A javascript formatted representation of value.

This function is the base function for converting a data structure into
Javascript.  If the datatype is an object or an array, then value is forwarded
to php_object_to_js.  The tab and indent are forwarded to php_object_to_js to
nicely indent a data structure.
*/
function php_data_to_js($value,$tab='',$indent='  ',$cr="\n")
    {
    //If value is an array or an object, forward to php_object_to_js.
    if(is_array($value)||is_object($value))
        return php_object_to_js($value,$tab,$indent,$cr);
    //If value is null then output null.
    elseif (is_null($value))
        return "null";
    //If value is true then output true.
    elseif ($value===true)
        return "true";
    //If value is false then output false.
    elseif ($value===false)
        return "false";
    //If value is numeric then output value.
    elseif (is_numeric($value))
        return "$value";
    //The value is assumed to be string. Escape the string and add quotes around it.
    return "'".addslashes($value)."'";
    }

/* php_object_to_js
Input:
    object (Array or Object)
    tab (Optional String)
    indent (Optional String)
Output:
    Return value (String): A javascript formatted representation of value.

This function attempts to convert an object or an associative array into a
Javascript anonymous object.  If the array is deemed to be a non-associative
array (all indices are numeric) then value is forwarded to php_array_to_js.
The string is tabbed using tab, and calls to php_data_to_js for component data
pass tab and indent concatenated to increase the indent of children components.
*/
function php_object_to_js($object,$tab='',$indent='  ',$cr="\n")
    {
    //Do a check if this is an array.
    if(is_array($object))
        {
        //Assume the array is numeric.
        $flag=true;
        //Loop through each index.
        foreach($object as $index=>$value)
            //if this is not numeric, then fail the test.
            if(!is_numeric($index))
                {
                $flag=false;
                break;
                }
        //If we passed, then forward to php_array_to_js.
        if($flag)
            return php_array_to_js($object,$tab,$indent);
        }
    //If the object is empty, then return null.
    if (count($object)==0)
        return "null";
    //Initialize the return value array.
    $retval=array();
    //Initialize a counter.
    $count=0;
    //Iterate through each porperty
    foreach($object as $property=>$value)
        {
        //Increase the counter
        $count++;
        //If value is an array or an object, skip a line before and after.
        if(is_array($value)||is_object($value))
            {
            $data="{$cr}{$tab}{$indent}$property:".php_data_to_js($value,$tab.$indent,$indent)."{$cr}{$tab}";
            }
        //Otherwise just get the value.
        else
            {
            $data="'$property':".php_data_to_js($value);
            }
        //Push the value onto a stack.
        array_push($retval,$data);
        }
    //Create our return string and return it.
    if($cr)
        return "{".preg_replace("/{$cr}\s*,{$cr}/s",",{$cr}",implode(",",$retval))."}";
    return "{".implode(",",$retval)."}";
    }

/* php_array_to_js
Input:
    object (Array)
    tab (Optional String)
    indent (Optional String)
Output:
    Return value (String): A javascript formatted representation of value.

This function attempts to convert an numerically indexed array into a
Javascript array.  Any missing numerical indicies are filled in as null. The
string is tabbed using tab, and calls to php_data_to_js for component data
pass tab and indent concatenated to increase the indent of children components.
*/
function php_array_to_js($object,$tab='',$indent='  ',$cr="\n")
    {
    //If the array is empty, then output an empty array.
    if(count($object)==0)
        return '[]';
    //Initialize the return value array.
    $retval=array();
    //Determine the maximum .
    $max_index=max(array_keys($object));
    //Initialize the return value array.
    for($index=0;$index<=$max_index;$index++)
        {
        //If the index is present, then record its value.
        if(array_key_exists($index,$object))
            $value=$object[$index];
        //Otherwise assume null to keep indices correct.
        else
            $value=null;
        //If value is an array or an object, skip a line before and after.
        if(is_array($value)||is_object($value))
            {
            $data="{$cr}{$tab}{$indent}".php_data_to_js($value,$tab.$indent,$indent)."{$cr}{$tab}";
            }
        //Otherwise just get the value.
        else
            {
            $data=php_data_to_js($value);
            }
        //Push the value onto a stack.
        array_push($retval,$data);
        }
    //Create our return string and return it.
    if($cr)
        return "[".preg_replace("/{$cr}\s*,{$cr}/s",",{$cr}",implode(",",$retval))."]";
    return "[".implode(",",$retval)."]";
    }

/* js_data_to_php
Input:
    data (String)
Output:
    Return value (Mixed): A PHP data structure containing the vaules in the
        javascript formatted string.

This function is the base function for converting a data structure from
Javascript into PHP.  If the string begins with an opening brace or single
quote, then the end is checked to ensure that the data structure is closed
there.  If not, then null is returned.  If so, then the data structure is
parsed out and its components are parsed out and into a PHP data structure.
Otherwise the string is assumed to be a numeric literal, boolean literal, or
null, and the apporpriate PHP value is generated and returned.
*/
function js_data_to_php($data)
    {
    //Trim whitespace off the incoming data
    $data=trim($data);
    //Look for opening braces and quotes.
    switch($data[0])
        {
        case '[': //Array
            //Check for the closing brace.
            if (substr($data,-1)!=']')
                return null;
            //Initialize the processing array.
            $retval=array();
            //Get the components.
            $parts=comma_slice_javascript(substr($data,1,-1));
            //If we have no components then return null.
            if(is_null($parts))
                return null;
            //Iterate through each part, converting it along the way.
            foreach($parts as $value)
                $retval[]=js_data_to_php($value);
            //Return our data structure
            return $retval;
        case '{': //Object
            //Check for the closing brace.
            if (substr($data,-1)!='}')
                return null;
            //Initialize the processing array.
            $retval=array();
            //Get the components.
            $parts=comma_slice_javascript(substr($data,1,-1));
            //If we have no components then return null.
            if(is_null($parts))
                return null;
            //Iterate through each part, converting it along the way.
            foreach($parts as $value)
                {
                //Split the part at the first colon. The first piece is the
                //property name, the second is the property value.
                $pieces=explode(':',$value,2);
                $pieces[0]=trim($pieces[0]);
                if(substr($pieces[0],0,1)=='"' ||substr($pieces[0],0,1)=="'")
                    $retval[js_data_to_php($pieces[0])]=js_data_to_php($pieces[1]);
                else
                    $retval[$pieces[0]]=js_data_to_php($pieces[1]);
                }
            //Return our data structure
            return $retval;
        case "'": //String with single quote
            //Check for the closing quote.
            if (substr($data,-1)!="'")
                return null;
            //Return our data structure
            return substr($data,1,-1);
        case '"': //String with double quote
            //Check for the closing quote.
            if (substr($data,-1)!='"')
                return null;
            //Return our data structure
            return substr($data,1,-1);
        }
    //Look for literals
    switch($data)
        {
        //True literal
        case 'true':
            return true;
        //False literal
        case 'false':
            return false;
        //Null literal
        case 'null':
            return null;
        }
    //Assume an integer literal
    return (int)$data;
    }

/* comma_slice_javascript
Input:
    string (String)
Output:
    Return value (Array): An array of strings parsed out from around commas
        not contained withing braces or quotes.

This function is used by js_data_to_php to splice out comma separated values
in Javascript arrays and objects.  It returns an array of the top level
components.
*/
function comma_slice_javascript($string)
    {
    //Initialize the return array.
    $retval=array();
    //Set the string pointer
    $pos=0;
    //Set the pointer for the last seen comma
    $comma=0;
    //Make a colon counter
    $colon=0;
    //look while pos points to the string
    while($pos<strlen($string))
        {
        //Check for braces and quotes.
        switch($string[$pos])
            {
            case '[':
                //Look for the entire Javascript array.
                $result=produce_run('[',']',substr($string,$pos));
                //If we can't get the array then return null.
                if(!$result)
                    {
                    log_error( "No run for [");
                    return null;
                    }
                //Advance pos the length of the array.
                $pos+=strlen($result);
                break;
            case '{':
                //Look for the entire Javascript object.
                $result=produce_run('{','}',substr($string,$pos));
                //If we can't get the object then return null.
                if(!$result)
                    {
                    log_error( "No run for {");
                    return null;
                    }
                //Advance pos the length of the object.
                $pos+=strlen($result);
                break;
            case "'":
                //Look for the second single quote.
                do  {
                    $pos=strpos($string,"'",$pos+1);
                    //Loop while the single quote is preceded by a slash.
                    } while ($pos && $string[$pos-1]=="\\");
                //If there is no matching quote, then return null.
                if(!$pos)
                    {
                    log_error( "missing end '");
                    return null;
                    }
                //Increment $pos to where a comma should be.
                $pos++;
                break;
            case '"':
                //Look for the second double quote.
                do  {
                    $pos=strpos($string,'"',$pos+1);
                    //Loop while the double quote is preceded by a slash.
                    } while ($pos && $string[$pos-1]=="\\");
                //If there is no matching quote, then return null.
                if(!$pos)
                    {
                    log_error( 'missing end "');
                    return null;
                    }
                //Increment $pos to where a comma should be.
                $pos++;
                break;
            default:
                $startpos=$pos;
                //Look for the next comma
                $pos=strpos($string,",",$startpos+1);
                //Look for colon, too.
                $pos2=strpos($string,":",$startpos+1);
                //Take the smaller of the two if both are not false
                if($pos2!==false)
                    {
                    $pos=($pos!==false && $pos<$pos2)?$pos:$pos2;
                    }

                //If one cannot be found, advance pos to the end of the string.
                if(!$pos)
                    $pos=strlen($string);
                break;
            }
        //Sanity check- bypass all whitespace.
        while($pos<strlen($string) && preg_match("/\s/",$string[$pos]))
            $pos++;
        //If we are not past the end of the string and we are not looking at a
        //comma, then fail.
        if($pos<strlen($string) && $string[$pos]!=',' && $string[$pos]!=':')
            {
            log_error( "not seeing comma or colon at $pos in $string looking at ".$string[$pos]);
            return null;
            }
        if($pos<strlen($string) && $string[$pos]==':')
            {
            $colon++;
            if ($colon>1)
                {
                log_error( "Too many unquoted colons at $pos in $string looking at ".substr($string,0,$pos));
                return null;
                }
            //Move pos past the colon
            $pos++;
            }
        else
            {
            //Break off everything between commas.
            $retval[]=substr($string,$comma,$pos-$comma);
            //Move pos past the comma
            $pos++;
            //Set comma to just past the comma.
            $comma=$pos;
            //Reset the colon count
            $colon=0;
            }
        };
    //Return the data structure/
    return $retval;
    }

/* produce_run
Input:
    open_token (String)
    close_token (String)
    string (String)
Output:
    Return value (String or null): An string containing the first opening and
        matching closing brace contained in string or null indicating an error.

This function is used by comma_splice_javascript to determine the length of a
complete Javascript array or anonymous object to ensure proper comma
parsing.  A string containing the first opening and matching closing brace
contained in string is returned if the structure is properly formed.  This
string may contain nested data structures containing the same braces.  If a
matching closing brace cannot be found, then the function returns null to
indicate an error.
*/
function produce_run($open_token,$close_token,$string)
    {
    //A token counter
    $tokens=0;
    //Our position in string
    $pos=0;
    do  {
        //Look for tokens
        switch($string[$pos])
            {
            //If we found an open token increment our token counter.
            case $open_token:
                $tokens++;
                break;
            //If we find a close token decrement the counter.
            case $close_token:
                $tokens--;
                break;
            }
        //Advance the string pointer
        $pos++;
        //Loop while we are still in the string and the token counter is
        //greater than zero.
        } while($tokens>0 && $pos<strlen($string));
    //If the token counter is not zero, then fail.
    if ($tokens>0)
        return null;
    //Return the entire data structure.
    return substr($string,0,$pos);
    }
?>
