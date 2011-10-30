<?php
ini_set('display_errors','0');
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';

#Globals
require_once INCLUDE_DIR.'paths.php';
#Prep the game objects.
require_once INCLUDE_DIR.'fight.php';
require_once INCLUDE_DIR.'personalities.php';

#Do LOGIN stuff :)
require_once 'common_auth.php';
$GLOBALS['userid']=is_logged_in();
//If login failed then bail. (Umm... maybe not... anonymous fight viewing.)
if(!$GLOBALS['userid']) exit;
//If on hold then bail.
if(on_hold_in_context($userid,'dse')) exit;

//Load proxy library
require_once INCLUDE_DIR.'proxy.php';

//Remove unneeded slashes
if(get_magic_quotes_gpc())
    $_POST=array_map('stripslashes',$_POST);

//Here we respond to code from the client.
if(isset($_POST['data']))
    process_data($_POST['data']);

//Here we add our code to initiate client activity unsolicited.

//Now we transmit our responses.
write_responses();
?>