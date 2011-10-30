<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_ITEM','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'item_store.php';
require_once INCLUDE_DIR.'effects.php';

$item_store=new ITEM_STORE;
$items=&$item_store->get_all_items();

echo "
<center>
  <table cellpadding=\"4\" cellspacing=\"0\">
    <tr><td colspan=\"2\"><a href=\"edit_item.php?item=0\">Add new item</a></td><td colspan=\"3\"><a href=\"./\">Return to the admin menu</a></td><tr>
    ";
foreach($items as $index=>$item)
    if($index!=0)
        {
        $output="<tr>"
            ."<td><a href=\"edit_item.php?item={$index}\">{$index}</td>"
            ."<td><a href=\"edit_item.php?item={$index}\">{$item->name}</a></td>"
            ."<td>{$item->price} gold</td>"
            ."<td>".$item->describe_use()."</td>"
            ."<td>".$item->describe_equip()."</td>"
            ."<td><a href=\"delete_item.php?item={$index}\">delete</a></td>"
            ."</tr>\n";
        echo $output;
        }
echo "
  </table>
</center>";
?>
