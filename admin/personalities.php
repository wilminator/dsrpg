<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_PERSONALITY','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'personality_store.php';

$personality_store=new PERSONALITY_STORE;
$personalities=&$personality_store->get_all_personalities();

echo "
<center>
  <table cellpadding=\"4\" cellspacing=\"0\">
    <tr><td colspan=\"2\"><a href=\"edit_personality.php?personality=0\">Add new personality</a></td><td colspan=\"3\"><a href=\"./\">Return to the admin menu</a></td><tr>
    ";
foreach($personalities as $index=>$personality)
    {
    $output="<tr>"
        ."<td><a href=\"edit_personality.php?personality={$index}\">{$index}</td>"
        ."<td><a href=\"edit_personality.php?personality={$index}\">{$personality->name}</a></td>"
        ."<td><a href=\"edit_personality.php?personality={$index}\"><img style=\"max-width:64px;max-height:64px\" src=\"../".FIGHTER_IMAGES_DIR."{$personality->base_data['images'][0]}\"></a></td>"
        ."<td><a href=\"delete_personality.php?personality={$index}\">delete</a></td>"
        ."</tr>\n";
    echo $output;
    }
echo "
  </table>
</center>";
?>
