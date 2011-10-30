<?php
function html_loader()
    {
    echo <<<EOD
<div id="loader" class="screen">
    <p id="loader_message">&nbsp;</p>
    <div class="waitbar">
        <div id="loader_percent" class="waitdone"></div>
        <div id="loader_number" class="waitnumb">&nbsp;</div>
    </div>
</div>

EOD;
    }
?>