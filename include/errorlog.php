<?php
define('DEFAULT_ERROR_LOG','errorlog.txt');

$displayErrors_bool = false;
$fileErrors_bool = true;

function log_error($message)
    {
    set_error_handler('handleDebug');
    trigger_error($message,E_USER_NOTICE);
    restore_error_handler();
    }

set_error_handler('handleError');

function fixArgs($args,$touched=array()) {
    $fixedArgs=array();
    foreach($args as $key=>$fakeArg)
        {
        $arg=&$args[$key];
        $type=gettype($arg);
        switch($type)
            {
            case 'boolean':
                if($arg===true)
                    $fixedArgs[]='boolean (TRUE)';
                else
                    $fixedArgs[]='boolean (FALSE)';
                break;
            case 'integer':
            case 'double':
                $fixedArgs[]="$type ($arg)";
                break;
            case 'string':
                $fixedArgs[]="$type \"$arg\"";
                break;
            case 'resource':
                $fixedArgs[]="$type (".get_resource_type($arg).")";
                break;
            case 'array':
                $fixedArgs[]="$type";
                break;
            case 'object':
                $fixedArgs[]="$type (".get_class($arg).")";
                // {".get_object_vars($arg)."}";
                break;
            case 'NULL':
                $fixedArgs[]='NULL';
                break;
            default:
                $fixedArgs[]="Unknown type: $type";
                break;
            }
        }
    return implode(',',$fixedArgs);
}

function handleDebug($errorNo_int, $errorMsg_str, $file_str, $line_str) {
    $errorHTML_str = generateErrorString($errorNo_int, $errorMsg_str, $file_str, $line_str, 2);

    $handle=fopen(DEFAULT_ERROR_LOG,'a');
	fwrite($handle,str_replace('<br />',"\n",$errorHTML_str)."\n");
	fclose($handle);
	return true;
}

function handleError($errorNo_int, $errorMsg_str, $file_str, $line_str) {
	global $displayErrors_bool, $fileErrors_bool;

	if (!($errorNo_int & error_reporting())) {
		return false;
	}

	$errorType_str = array (
		1    => "Php Error",
		2    => "Php Warning",
		4    => "Parsing Error",
		8    => "Php Notice",
		16   => "Core Error",
		32   => "Core Warning",
		64   => "Compile Error",
		128  => "Compile Warning",
		256  => "Php User Error",
		512  => "Php User Warning",
		1024 => "Php User Notice"
	);

    $errorHTML_str = generateErrorString($errorNo_int, $errorMsg_str, $file_str, $line_str);

	if ($fileErrors_bool) {
        $handle=fopen(DEFAULT_ERROR_LOG,'a');
		fwrite($handle,str_replace('<br />',"\n",$errorHTML_str)."\n");
		fclose($handle);
		}
	if ($displayErrors_bool) {
        echo $errorHTML_str.'<br />';
	}

	if($errorNo_int & (E_ALL^E_USER_NOTICE^E_NOTICE^E_WARNING)) {
        //die('');
    }
}

function generateErrorString($errorNo_int, $errorMsg_str, $file_str, $line_str, $unroll_int=0) {
    // Get backtrace
    $backtrace = debug_backtrace();

    //Set fix flag to fix file and line number
    $fixflag_bool=($unroll_int>0);

    //Unset call to debug_print_backtrace
    //Add 2 to remove call to generateErrorString and debug_backtrace
    $unroll_int+=2;
    while($unroll_int>0)
        {
        $unroll_int--;
        $data=array_shift($backtrace);
        //$calls[]='Unwinding error '.var_export(array_shift($backtrace),true);
        if($fixflag_bool)
            {
            $file_str=(array_key_exists('file',$data)?$data['file']:'unknown');
            $line_str=(array_key_exists('line',$data)?$data['line']:'???');
            }
        }

    //Set the initial message line
    $date=date('Y-m-d H:i:s');
    $calls = array();
	$calls[] = "($date) $errorMsg_str in file $file_str line $line_str";

    // Iterate backtrace
    foreach ($backtrace as $i => $call) {
        if(isset($call['file'])) {
            $location = $call['file'] . ':' . $call['line'];
        } else {
            $location = 'unknown';
        }
        $function = (isset($call['class'])) ?
            $call['class'] . '.' . $call['function'] :
            $call['function'];

        $params = '';
        if (isset($call['args'])) {
            //$params = implode(', ', $call['args']);
            $params = fixArgs($call['args']);
        }

        $calls[] = sprintf('#%d  %s(%s) called at [%s]',
            $i,
            $function,
            $params,
            $location);
    }
    $calls[] = '-------------------------------------';

    return implode("<br />", $calls);
}
?>