<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_ABILITY','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'ability_store.php';
require_once INCLUDE_DIR.'effects.php';

$ability_store=new ability_STORE;
$abilities=&$ability_store->get_all_abilities();

echo "
<center>
  <table cellpadding=\"4\" cellspacing=\"0\">
    <tr><td colspan=\"2\"><a href=\"edit_ability.php?ability=0\">Add new ability</a></td><td colspan=\"2\"><a href=\"./\">Return to the admin menu</a></td><tr>
    ";
foreach($abilities as $index=>$ability)
    if($index!=0)
        {
        $output="<tr>"
            ."<td><a href=\"edit_ability.php?ability={$index}\">{$index}</td>"
            ."<td><a href=\"edit_ability.php?ability={$index}\">{$ability->name}</a></td>"
            ."<td>".$ability->describe_use()."</td>"
            ."<td><a href=\"delete_ability.php?ability={$index}\">delete</a></td>"
            ."</tr>\n";
        echo $output;
        }
echo "
  </table>
</center>";
?>
