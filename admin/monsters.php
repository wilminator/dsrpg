<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_MONSTER','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'monster_store.php';
require_once INCLUDE_DIR.'effects.php';

$monster_store=new MONSTER_STORE;
$monsters=&$monster_store->get_all_monsters();

if (key_exists('sort',$_GET))
    {
    if ($_GET['sort']=='name')
        uasort($monsters, create_function('$a,$b','return strcmp($a->name, $b->name);'));
    
    if ($_GET['sort']=='pxp')
        uasort($monsters, create_function('$a,$b','return $a->pxp - $b->pxp;'));
    }

echo "
<center>
  <table cellpadding=\"4\" cellspacing=\"0\">
    <tr><td colspan=\"2\"><a href=\"edit_monster.php?monster=0\">Add new monster</a></td><td colspan=\"3\"><a href=\"./\">Return to the admin menu</a></td><tr>
    <tr><th align=\"left\"><a href=\"monsters.php\">ID</a></th><th align=\"left\"><a href=\"monsters.php?sort=name\">Name</a></th><th align=\"left\" colspan = \"2\"><a href=\"monsters.php?sort=pxp\">PXP</a></th><tr>
    ";
foreach($monsters as $index=>$monster)
    if($index!=0)
        {
        $output="<tr>"
            ."<td><a href=\"edit_monster.php?monster={$index}\">{$index}</td>"
            ."<td><a href=\"edit_monster.php?monster={$index}\">{$monster->name}</a></td>"
            ."<td>".$monster->describe_stats()."</td>"
            ."<td><a href=\"delete_monster.php?monster={$index}\">delete</a></td>"
            ."</tr>\n";
        echo $output;
        }
echo "
  </table>
</center>";
?>
