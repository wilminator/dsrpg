<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_ABILITY','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'ability_store.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';

//Get the ability id number. 0 is a new ability.
$abilityindex=$_REQUEST['ability'];     

$ability_store=new ABILITY_STORE;

if(isset($_GET['confirm']) && $_GET['confirm']=="YES")
    {
    //delete that ability in the databse
    $ability_store->delete_ability($abilityindex);
    header ('Location: abilities.php');
    exit;
    }

//Snag the name
$abilities=&$ability_store->get_all_abilities();
$name=$abilities[$abilityindex]->name;

?>
<table>
  <tr><th>
    <table>
      <caption><b>Are you sure you want to delete the <?php echo $name; ?>?</b></caption>
      <tr>
        <td>
          <?php
          echo "<a href=\"delete_ability.php?ability=$abilityindex&confirm=YES\">Yes, delete this ability.</a>";
          ?>
        </td>
        <td><a href="abilities.php">Do not delete this.</a></td>
      </tr>
    </table>
  </th></tr>
</table>

