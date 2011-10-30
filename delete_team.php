<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_membership_access('dse','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../index.php');

define ('INCLUDE_DIR','include/');

require_once INCLUDE_DIR.'errorlog.php';
require_once INCLUDE_DIR.'team_store.php';

//Get the item id number. 0 is a new item.
$teamindex=$_REQUEST['teamid'];

$team_store=new TEAM_STORE;

if(isset($_GET['confirm']) && $_GET['confirm']=="YES")
    {
    //delete that team in the databse
    $team_store->delete_team($teamindex);
    header ('Location: pick_team.php');
    exit;
    }

//Snag the name
$teams=&$team_store->get_all_teams();
$name=$teams[$teamindex]->name;

?>
<table>
  <tr><th>
    <table>
      <caption><b>Are you sure you want to delete your team <?php echo $name; ?>?</b></caption>
      <tr>
        <td>
          <?php
          echo "<a href=\"delete_team.php?teamid=$teamindex&confirm=YES\">Yes, delete this team.</a>";
          ?>
        </td>
        <td><a href="pick_team.php">Do not delete this team.</a></td>
      </tr>
    </table>
  </th></tr>
</table>