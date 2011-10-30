<?php
$GLOBALS['javascript_stack']=array();

function push_js($filename)
    {
    if ($filename=='') return;
    if(is_array($filename))
        foreach($filename as $file)
            push_js($file);
    else
        {
        if(array_search($filename,$GLOBALS['javascript_stack'])===false)
            array_unshift($GLOBALS['javascript_stack'],$filename);
        }
    }
    
function push_js_directory($directory,$basedir='')
    {
    if($basedir!='' && substr($basedir,-1)!='/')
        $basedir.='/';
    if(substr($directory,-1)=='/')
        $directory=substr($directory,0,-1);
    $handle=opendir($basedir.$directory);
    if($directory!='')
        $directory.='/';
    if($handle)
        {
        while($file=readdir($handle))
            if($file!='.' && $file!='..')
                {
                if(is_dir($basedir.$directory.$file))
                    push_js_directory($directory.$file,$basedir);
                else
                    push_js($directory.$file);
                }
        closedir($handle);
        }
    }

function html_js($directory='')
    {
    if($directory!='' && substr($directory,-1)!='/')
        $directory.='/';
    foreach($GLOBALS['javascript_stack'] as $filename)
        echo "<script type=\"text/javascript\" src=\"$directory$filename\"></script>\n";
    /*
    //echo "<script type=\"text/javascript\">\n";
    foreach($GLOBALS['javascript_stack'] as $filename)
        {
        echo "//START filename=$filename\n";
        readfile ("$directory$filename");
        echo "//*END* filename=$filename\n";
        }
    //echo "</script>\n";
    */
    }

function get_js_stack()
    {
    return $GLOBALS['javascript_stack'];
    }
?>