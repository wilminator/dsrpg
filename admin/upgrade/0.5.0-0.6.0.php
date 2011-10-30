<?php
define ('INCLUDE_DIR','../../include/');

require_once INCLUDE_DIR.'mysql.php';

$db1 = 'wilminat_dse_0_5_0';
$db2 = MYSQL_DB;

// Test to see if the new schema exsits.
$exists = false;
$result=mysql_do_query("show databases",false);
while ($data=mysql_fetch_row($result))
    {
    if ($data[0]==$db2)
        {
        echo "$db2 already exists.<br>";
        $exists = true;
        }
    }

// Create the new db, if needed.
if(!$exists && mysql_do_query("create database `$db2`")===false)
    {
    echo "Could not create $db2.  Do you have the required permissions?";
    exit;
    }



// Prep initial copies of the new tables.
require_once INCLUDE_DIR.'ability_store.php';
$as = new ABILITY_STORE(true);
$as->write_abilities_file();

require_once INCLUDE_DIR.'job_store.php';
$js = new JOB_STORE(true);
$js->write_jobs_file();

require_once INCLUDE_DIR.'personality_store.php';
$ps = new PERSONALITY_STORE(true);
$ps->write_personalities_file();

require_once INCLUDE_DIR.'item_store.php';
$is = new ITEM_STORE(true);
$is->write_items_file();

require_once INCLUDE_DIR.'character_store.php';
$cs = new CHARACTER_STORE(true);

require_once INCLUDE_DIR.'team_store.php';
$ts = new TEAM_STORE(true);

require_once INCLUDE_DIR.'monster_store.php';
$ms = new MONSTER_STORE(true);
$ms->write_monsters_file();

require_once INCLUDE_DIR.'fight_store.php';
$fs = new FIGHT_STORE(true);

// Copy all the tables over.
$result=mysql_do_query("show tables from `$db1`",false);
while ($data=mysql_fetch_row($result))
    {
    $table = $data[0];
    if (mysql_do_query("TRUNCATE TABLE `$db2`.`$table`",false)===false)
        {
        echo "Could not truncate $table.  Do you have the required permissions?";
        exit;
        }
    $result2=mysql_do_query("desc `$db1`.`$table`",false);
    $cols = array();
    while ($data=mysql_fetch_row($result2))
        $cols[] = "`{$data[0]}`";
    $cols = implode($cols, ',');
    echo "$cols<br>";
    if (mysql_do_query("REPLACE INTO `$db2`.`$table` ($cols) SELECT $cols FROM `$db1`.`$table`",false)===false)
        {
        echo "Could not copy data in $table from $db1 to $db2.  Do you have the required permissions?";
        exit;
        }
    echo "Copied $table from $db1 to $db2.<br>";
    }

//Now make specific updates.


echo "Done.";
?>