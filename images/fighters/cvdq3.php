<?php
//Avenger-Attack.gif
foreach (glob("*-Attack.gif") as $filename)
    {
    $im = imagecreatefromgif($filename);
    $newname = substr($filename, 0, -11) . '.png';
    if ($im) 
        {
        echo "$filename => $newname<br>";
        imagepng($im, $newname, 9);
        imagedestroy($im);
        }
    }
?>