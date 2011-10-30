<html>
<head>
</head>
<body>
<h1>Select an image</h1>
<a href="javascript:window.top.choose_image('<?php echo $_GET['key']; ?>','<?php echo $_GET['path']; ?>','');">Select no image</a>
<span style="width:100px"></span>
<a href="javascript:window.top.cancel_select();">Cancel (make no changes)</a>
<table>
<?php
$path=$_GET['path'];
$key=$_GET['key'];
$count=0;
$dir = dir("../$path");
$files = array();
while (false !== ($file = $dir->read()))
    if($file!='..' && $file!='.')
        $files[] = $file;
$dir->close();

usort($files, 'strcasecmp');

foreach ($files as $file)
    {
    if($count%4==0) echo "</tr>\n";
    echo "<td align=\"center\"><a href=\"javascript:window.top.choose_image('$key','$path','$file');\"><img style=\"max-width:190px;max-height:150px\" src=\"../$path$file\"><br>$file</a></td>";
    $count++;
    if($count%4==0) echo "</tr>\n";
    }
if($count%4!=0) echo "</tr>\n";
?>
</table>
</body>
</html>