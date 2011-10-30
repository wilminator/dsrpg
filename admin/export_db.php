<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','RESET_DB','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

//Check for admin permissions
$reset_db=check_permission($userid,'dse','RESET_DB');
if($reset_db===false)
    {
    header("Location: ../");
    exit;
    }

if(count($_POST)>0)
    {
    $data=array();
    if(array_key_exists('items',$_POST))
        {
        require_once INCLUDE_DIR.'item_store.php';
        $item_store=new ITEM_STORE;
        $data['items']=$item_store->get_all_items();
        }

    if(array_key_exists('abilities',$_POST))
        {
        require_once INCLUDE_DIR.'ability_store.php';
        $ability_store=new ABILITY_STORE;
        $data['abilities']=$ability_store->get_all_abilities();
        }

    if(array_key_exists('personalities',$_POST))
        {
        require_once INCLUDE_DIR.'personality_store.php';
        $personality_store=new PERSONALITY_STORE;
        $data['personalities']=$personality_store->get_all_personalities();
        }

    if(array_key_exists('jobs',$_POST))
        {
        require_once INCLUDE_DIR.'job_store.php';
        $job_store=new JOB_STORE;
        $data['jobs']=$job_store->get_all_jobs();
        }

    if(array_key_exists('monsters',$_POST))
        {
        require_once INCLUDE_DIR.'monster_store.php';
        $monster_store=new MONSTER_STORE;
        $data['monsters']=$monster_store->get_all_monsters();
        }

    if(array_key_exists('heroes',$_POST))
        {
        require_once INCLUDE_DIR.'team_store.php';
        $team_store=new TEAM_STORE;
        $data['teams']=$team_store->get_all_teams(true);
        require_once INCLUDE_DIR.'character_store.php';
        $character_store=new CHARACTER_STORE;
        $data['characters']=$character_store->get_all_characters();
        }

    if(array_key_exists('fights',$_POST))
        {
        require_once INCLUDE_DIR.'fight_store.php';
        $fight_store=new FIGHT_STORE;
        $data['fights']=$fight_store->get_all_fights();
        }

    $data=serialize($data);

    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename=\"dse_dump\"");
    header("Content-length:".(string)(strlen($data)));
    echo $data;
    exit;
    }
?>
<table>
  <tr><th>
    <form method="post">
      <table>
        <caption><b>Select the data to export:</b></caption>
        <tr>
          <td><input name="items" type="checkbox" value="1">Items</td>
          <td><input name="abilities" type="checkbox" value="1">Abilities</td>
          <td><input name="jobs" type="checkbox" value="1">jobs</td>
          <td><input name="monsters" type="checkbox" value="1">Monsters</td>
        </tr>
        <tr>
          <td><input name="personalities" type="checkbox" value="1">Personalities</td>
          <td><input name="heroes" type="checkbox" value="1">Heroes (Player Data)</td>
          <td><input name="fights" type="checkbox" value="1">Fights (Stored Fight Records)</td>
        </tr>
      </table>
    <input type="submit" value="Export the selected data">
    </form>
    <p><a href="./">Return to the previous menu.</a></p>
  </th></tr>
</table>