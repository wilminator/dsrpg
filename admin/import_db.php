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


if(count($_FILES)>0)
    {
    $filename=$_FILES['datafile']['tmp_name'];
    if(!is_uploaded_file($filename))
        {
        echo 'Problem with file.';
        exit;
        }
    $data=null;
    $filedata=file_get_contents($filename);
    //var_dump($filedata);

    require_once INCLUDE_DIR.'item.php';
    require_once INCLUDE_DIR.'ability.php';
    require_once INCLUDE_DIR.'personality.php';
    require_once INCLUDE_DIR.'monster.php';
    require_once INCLUDE_DIR.'job.php';
    require_once INCLUDE_DIR.'character.php';
    require_once INCLUDE_DIR.'group.php';
    require_once INCLUDE_DIR.'party.php';
    require_once INCLUDE_DIR.'fight.php';

    $data=@unserialize($filedata);
    //var_dump($data);
    //exit;
    if(array_key_exists('items',$data))
        {
        require_once INCLUDE_DIR.'item_store.php';
        $item_store=new ITEM_STORE(true);
        foreach($data['items'] as $index=>$item)
            if($index!=0)
                {
                $item_store->set_item($item,$index);
                }
        echo "Uploaded ".count($data['items'])." items.<br>";
        }

    if(array_key_exists('abilities',$data))
        {
        require_once INCLUDE_DIR.'ability_store.php';
        $ability_store=new ABILITY_STORE(true);
        foreach($data['abilities'] as $index=>$ability)
            {
            $ability_store->set_ability($ability,$index);
            }
        echo "Uploaded ".count($data['abilities'])." abilities.<br>";
        }

    if(array_key_exists('jobs',$data))
        {
        require_once INCLUDE_DIR.'job_store.php';
        $job_store=new JOB_STORE(true);
        foreach($data['jobs'] as $index=>$job)
            {
            $job_store->set_job($job,$index);
            }
        echo "Uploaded ".count($data['jobs'])." jobs.<br>";
        }

    if(array_key_exists('monsters',$data))
        {
        require_once INCLUDE_DIR.'monster_store.php';
        $monster_store=new MONSTER_STORE(true);
        foreach($data['monsters'] as $index=>$monster)
            {
            $monster_store->set_monster($monster,$index);
            }
        echo "Uploaded ".count($data['monsters'])." monsters.<br>";
        }

    if(array_key_exists('characters',$data))
        {
        require_once INCLUDE_DIR.'character_store.php';
        $character_store=new CHARACTER_STORE(true);
        foreach($data['characters'] as $index=>$character)
            {
            $character_store->set_character($character);
            }
        echo "Uploaded ".count($data['characters'])." characters.<br>";
        }

    if(array_key_exists('teams',$data))
        {
        require_once INCLUDE_DIR.'team_store.php';
        $team_store=new TEAM_STORE(true);
        foreach($data['teams'] as $index=>$team)
            {
            $team_store->set_team($team,true);
            }
        echo "Uploaded ".count($data['teams'])." teams.<br>";
        }

    if(array_key_exists('fights',$data))
        {
        require_once INCLUDE_DIR.'fight_store.php';
        $fight_store=new FIGHT_STORE(true);
        foreach($data['fights'] as $index=>$fight)
            {
            $fight_store->set_fight_data($index,$fight);
            }
        echo "Uploaded ".count($data['fights'])." fights.<br>";
        }

    echo "<a href=\"./\">Return to the previous menu.</a>";
    exit;
    }
?>
<table>
  <tr><th>
    <form enctype="multipart/form-data" method="post">
      <table>
        <caption><b>Select the data to import:</b></caption>
        <tr>
          <td><input name="datafile" type="file"></td>
        </tr>
      </table>
    <input type="submit" value="Import the selected file">
    </form>
    <p><a href="./">Return to the previous menu.</a></p>
  </th></tr>
</table>