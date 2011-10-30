<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_membership_access('dse','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

//Check for admin permissions
$edit_items=check_permission($userid,'dse','EDIT_ITEMS');
$edit_ability=check_permission($userid,'dse','EDIT_ABILITY');
$edit_monster=check_permission($userid,'dse','EDIT_MONSTER');
$edit_job=check_permission($userid,'dse','EDIT_JOB');
$reset_db=check_permission($userid,'dse','RESET_DB');
if(($edit_items||$edit_ability||$edit_job||$edit_monster)===false)
    {
    header("Location: ../");
    exit;
    }

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'job_store.php';
require_once INCLUDE_DIR.'ability_store.php';
require_once INCLUDE_DIR.'item_store.php';
require_once INCLUDE_DIR.'monster_store.php';
require_once INCLUDE_DIR.'personality_store.php';

if(isset($_GET['confirm']) && $_GET['confirm']=="YES")
    {
    //delete all tables in the databse
    $item_store=new ITEM_STORE();
    $item_store->write_items_file();
    $GLOBALS['items']=$item_store->get_all_items();
    $ability_store=new ABILITY_STORE();
    $ability_store->write_abilities_file();
    $job_store=new JOB_STORE();
    $job_store->write_jobs_file();
    $personality_store=new PERSONALITY_STORE();
    $personality_store->write_personalities_file();
    $monster_store=new MONSTER_STORE();
    $monster_store->write_monsters_file();
    header ('Location: ./');
    exit;
    }
?>
<table>
  <tr><th>
    <table>
      <caption><b>Are you sure you want to reinitialize the game variable files?</b></caption>
      <tr>
        <td><a href="init_vars.php?confirm=YES">Yes, commit the files.</a></td>
        <td><a href="./">Do not touch a thing!</a></td>
      </tr>
    </table>
  </th></tr>
</table>
