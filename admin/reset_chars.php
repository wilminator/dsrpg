<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_membership_access('dse','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

//Check for admin permissions
$reset_db=check_permission($userid,'dse','RESET_DB');
if($reset_db===false)
    {
    header("Location: ../");
    exit;
    }

define ('INCLUDE_DIR','../include/');
require_once INCLUDE_DIR.'character_store.php';

if(isset($_GET['confirm']) && $_GET['confirm']=="YES")
    {
    echo 'Regenerating characters.<br>';
    require_once INCLUDE_DIR.'jobs.php';
    require_once INCLUDE_DIR.'items.php';
    require_once INCLUDE_DIR.'abilities.php';
    require_once INCLUDE_DIR.'personalities.php';
    //regenerate all PCs.
    $char_store=new CHARACTER_STORE();
    $chars=$char_store->get_all_characters();
    foreach(array_keys($chars) as $index)
        {
        echo "Processing character #$index {$chars[$index]->name} XP={$chars[$index]->exp}<br>\n";
        $results=$chars[$index]->regenerate_stats();
        //var_dump($results);
        echo implode('<br>',$results['data']).'<br>';
        $char_store->set_character($chars[$index]);
        }
    //header ('Location: ./');
    echo "<a href=\"./\">Return to the menu.</a>";
    exit;
    }
?>
<table>
  <tr><th>
    <table>
      <caption><b>Are you sure you want to regenerate all player characters in the game?  They will not lose any items, gold, or experience, but may gain or lose abilities.</b></caption>
      <tr>
        <td>
          <?php
          echo "<a href=\"reset_chars.php?confirm=YES\">Yes, regenerate all PCs.</a>";
          ?>
        </td>
        <td><a href="./">Do not touch a thing!</a></td>
      </tr>
    </table>
  </th></tr>
</table>
