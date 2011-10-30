<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_ABILITY','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'html/html_javascript.php';

if(get_magic_quotes_gpc())
    $_POST=array_map('stripslashes',$_POST);

require_once INCLUDE_DIR.'js_rip.php';

//If the data has been returned to us, then process it and move on.
if(array_key_exists('descjs',$_POST))
    {
    $descjs=$_POST['descjs'];
    $_SESSION['animationjs']=$descjs;
    $desc_array=js_data_to_php($descjs);
    ksort($desc_array);
    $_SESSION['animations']=$desc_array;
    header("Location: $_GET[return]?ability=$_GET[ability]&item=$_GET[item]&personality=$_GET[personality]");
    exit;
    }
//Otherwise get the js data.
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Please wait...</title>
<?php
    //display the script tags.
    push_js_directory('','../'.JAVASCRIPT_DIR);
    html_js('../'.JAVASCRIPT_DIR);

echo "
<script type=\"text/javascript\">
//public constants
var javadir='".JAVA_DIR."';
var images='".IMAGES_DIR."';
var fighter_images='".FIGHTER_IMAGES_DIR."';
var menu_images='".MENU_IMAGES_DIR."';
var ability_images='".ABILITY_IMAGES_DIR."';
var item_images='".ITEM_IMAGES_DIR."';
var effect_images='".EFFECT_IMAGES_DIR."';
var sound='".SOUND_DIR."';
var music='".MUSIC_DIR."';
";
?>

function generate()
    {
    var retval={};
    for (var index in animations)
        retval[index]=animations[index].prototype.description();
    document.formdata.descjs.value=encode_data_type(retval);
    document.formdata.submit();
    return true;
    }
</script>
</head>
<body onload="return generate();">
<form name="formdata" method="post">
<input name="descjs">
</form
</body>
</html>
