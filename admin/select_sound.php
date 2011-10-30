<html>
<head>
</head>
<body>
<h1>Select a sound</h1>
<a href="javascript:window.top.choose_sound('<?php echo $_GET['key']; ?>','');">Select no sound</a>
<span style="width:100px"></span>
<a href="javascript:window.top.cancel_select();">Cancel (make no changes)</a>
<table border="1">
<?php
$path=$_GET['path'];
$key=$_GET['key'];
$count=0;
$dir = dir("$path");
while (false !== ($file = $dir->read()))
    if($file!='..' && $file!='.')
        {
        if($count%4==0) echo "</tr>\n";
        echo "<td valign=\"center\"><a href=\"javascript:window.top.choose_sound('$key','$file');\">$file</a><img src=\"play.png\" onclick=\"window.top.play_sound('$file');\"></td>";
        $count++;
        if($count%4==0) echo "</tr>\n";
        }
$dir->close();
if($count%4!=0) echo "</tr>\n";
?>
</table>
</body>
</html>