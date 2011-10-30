<?php
$GLOBALS['css_stack']=array();

function push_css($filename)
    {
    if(is_array($filename))
        foreach($filename as $file)
            push_css($file);
    else
        {
        if(array_search($filename,$GLOBALS['css_stack'])===false)
            $GLOBALS['css_stack'][]=$filename;
        }    
    }

function html_css($directory='')
    {
    if($directory!='' && substr($directory,-1)!='/')
        $directory.='/';
    foreach($GLOBALS['css_stack'] as $filename)
        echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"$directory$filename\">\n";
    /*
    echo "<style type=\"text/css\" rel=\"stylesheet\">\n";
    foreach($GLOBALS['css_stack'] as $filename)
        readfile ("$directory$filename");
    echo "</style>\n";
    */
    }


function push_css_directory($directory,$basedir='')
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
                    push_css_directory($directory.$file,$basedir);
                else
                    push_css($directory.$file);
                }
        closedir($handle);
        }
    }
?>