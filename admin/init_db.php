<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','RESET_DB','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'job_store.php';
require_once INCLUDE_DIR.'monster_store.php';
require_once INCLUDE_DIR.'ability_store.php';
require_once INCLUDE_DIR.'personality_store.php';
require_once INCLUDE_DIR.'item_store.php';

if(isset($_GET['confirm']))
    {
    switch($_GET['confirm'])
        {
        case "YES":
        case "FILE":
            //delete all tables in the databse
            echo 'Recreating items table.<br>';
            $item_store=new ITEM_STORE(true);
            //Add just the items if need be
            if($_GET['confirm']=="FILE")
                {
                $item_store=new ITEM_STORE;
                echo 'Importing old items into namespace.<br>';
                require_once INCLUDE_DIR.'items.php';
                echo 'Adding old items to table.<br>';
                foreach($GLOBALS['items'] as $index=>$item)
                    if($index!=0)
                        {
                        $item_store->set_item($item,$index);
                        //echo "Added item $index<br>";
                        }
                }
            echo 'Done with items table.<br>';
            //echo "Init'd items.<br>";
            $GLOBALS['items']=$item_store->get_all_items();
            echo 'Recreating abilities table.<br>';
            $ability_store=new ABILITY_STORE(true);
            //echo "Init'd abilities.<br>";
            echo 'Recreating jobs table.<br>';
            $job_store=new JOB_STORE(true);
            echo 'Recreating personalities table.<br>';
            $personality_store=new PERSONALITY_STORE(true);
            //echo "Init'd jobs.<br>";
            echo 'Recreating monsters table.<br>';
            $monster_store=new MONSTER_STORE(true);
            //echo "Init'd monsters.<br>";

            //Add everything else if need be.
            if($_GET['confirm']=="FILE")
                {
                $ability_store=new ABILITY_STORE;
                echo 'Importing old abilities into namespace.<br>';
                require_once INCLUDE_DIR.'abilities.php';
                echo 'Adding old abilities to table.<br>';
                foreach($GLOBALS['abilities'] as $index=>$ability)
                    {
                    if (! in_array($ability->skill_effect_type, array('close','throw','shoot','none'))) $ability->skill_effect_type = 'none';
                    $ability_store->set_ability($ability,$index);
                    //echo "Added ability $index<br>";
                    }

                $job_store=new JOB_STORE;
                echo 'Importing old jobs into namespace.<br>';
                require_once INCLUDE_DIR.'jobs.php';
                echo 'Adding old jobs to table.<br>';
                foreach($GLOBALS['jobs'] as $index=>$job)
                    {
                    $job_store->set_job($job,$index);
                    //echo "Added job $index<br>";
                    }

                $personality_store=new PERSONALITY_STORE;
                echo 'Importing old personalities into namespace.<br>';
                require_once INCLUDE_DIR.'personalities.php';
                echo 'Adding old personalities to table.<br>';
                foreach($GLOBALS['personalities'] as $index=>$personality)
                    {
                    $personality_store->set_personality($personality,$index);
                    //echo "Added job $index<br>";
                    }

                $monster_store=new MONSTER_STORE;
                echo 'Importing old monsters into namespace.<br>';
                require_once INCLUDE_DIR.'monsters.php';
                echo 'Adding old monsters to table.<br>';
                foreach($GLOBALS['monsters'] as $index=>$monster)
                    {
                    $monster_store->set_monster($monster,$index);
                    //echo "Added monster $index<br>";
                    }
                }
            echo 'Done.<br>';
            echo "<a href=\"./\">Return to the previous menu.</a>";
            exit;
        }
    }
?>
<table>
  <tr><th>
    <table>
      <caption><b>Are you sure you want to reinitialize the database?</b></caption>
      <tr>
        <td><a href="init_db.php?confirm=YES">Yes, start all over.</a></td>
        <td><a href="init_db.php?confirm=FILE">Yes, but use the existing variable files.</a></td>
        <td><a href="./">Do not touch a thing!</a></td>
      </tr>
    </table>
  </th></tr>
</table>

