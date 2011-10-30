<?php
require_once INCLUDE_DIR.'js_rip.php';

$GLOBALS['image_stack']=array();

function push_image($filename)
    {
    if(is_array($filename))
        foreach($filename as $file)
            push_image($file);
    else
        {
        if(array_search($filename,$GLOBALS['image_stack'])===false)
            $GLOBALS['image_stack'][]=$filename;
        }
    }

function image_stack_to_js()
    {
    return php_data_to_js($GLOBALS['image_stack']);
    }

function push_image_directory($directory,$basedir='')
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
                    push_image_directory($directory.$file,$basedir);
                else
                    push_image($directory.$file);
                }
        closedir($handle);
        }
    }
?>
