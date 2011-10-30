<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_MONSTER','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');


define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'monster_store.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';

//Get the monster id number. 0 is a new monster.
$monsterindex=$_REQUEST['monster'];     

$monster_store=new MONSTER_STORE;

if(isset($_GET['confirm']) && $_GET['confirm']=="YES")
    {
    //delete that monster in the databse
    $monster_store->delete_monster($monsterindex);
    header ('Location: monsters.php');
    exit;
    }

//Snag the name
$monsters=&$monster_store->get_all_monsters();
$name=$monsters[$monsterindex]->name;

?>
<table>
  <tr><th>
    <table>
      <caption><b>Are you sure you want to delete the <?php echo $name; ?>?</b></caption>
      <tr>
        <td>
          <?php
          echo "<a href=\"delete_monster.php?monster=$monsterindex&confirm=YES\">Yes, delete this monster.</a>";
          ?>
        </td>
        <td><a href="monsters.php">Do not delete this.</a></td>
      </tr>
    </table>
  </th></tr>
</table>

