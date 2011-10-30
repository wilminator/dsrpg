<?php
function html_code()
    {
    echo <<<EOD
    <textarea id="code" style="z-index:255"></textarea>
EOD;
    }

function html_jukebox()
    {
    return;
    $java_dir=JAVA_DIR;
    echo <<<EOD
    <applet name="Jukebox" id="Jukebox" code="Jukebox.class"  archive="{$java_dir}Jukebox.jar" width="700" height="20" style="visibility:hidden"></applet>
EOD;
    }
?>