<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_PERSONALITY','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'personality_store.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';

//Get the personality id number. 0 is a new personality.
$personalityindex=$_REQUEST['personality'];     

$personality_store=new PERSONALITY_STORE;

if(isset($_GET['confirm']) && $_GET['confirm']=="YES")
    {
    //delete that personality in the databse
    $personality_store->delete_personality($personalityindex);
    header ('Location: personalities.php');
    exit;
    }

//Snag the name
$personalities=&$personality_store->get_all_personalities();
$name=$personalities[$personalityindex]->name;

?>
<table>
  <tr><th>
    <table>
      <caption><b>Are you sure you want to delete <?php echo $name; ?>?</b></caption>
      <tr>
        <td>
          <?php
          echo "<a href=\"delete_personality.php?personality=$personalityindex&confirm=YES\">Yes, delete this personality.</a>";
          ?>
        </td>
        <td><a href="personalities.php">Do not delete this.</a></td>
      </tr>
    </table>
  </th></tr>
</table>