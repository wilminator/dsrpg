<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_ITEM','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'item_store.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';

//Get the item id number. 0 is a new item.
$itemindex=$_REQUEST['item'];     

$item_store=new ITEM_STORE;

if(isset($_GET['confirm']) && $_GET['confirm']=="YES")
    {
    //delete that item in the databse
    $item_store->delete_item($itemindex);
    header ('Location: items.php');
    exit;
    }

//Snag the name
$items=&$item_store->get_all_items();
$name=$items[$itemindex]->name;

?>
<table>
  <tr><th>
    <table>
      <caption><b>Are you sure you want to delete the <?php echo $name; ?>?</b></caption>
      <tr>
        <td>
          <?php
          echo "<a href=\"delete_item.php?item=$itemindex&confirm=YES\">Yes, delete this item.</a>";
          ?>
        </td>
        <td><a href="items.php">Do not delete this.</a></td>
      </tr>
    </table>
  </th></tr>
</table>

