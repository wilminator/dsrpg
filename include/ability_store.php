<?php
require_once INCLUDE_DIR.'ability.php';
require_once INCLUDE_DIR.'mysql.php';

require_once INCLUDE_DIR.'js_rip.php';
    if(!function_exists('clean')) {function clean($a) {return addslashes($a);}}

class ABILITY_STORE
    {
    //This constructor (tries to) initialize the ability table.
    function ABILITY_STORE($reset=false)
        {
        $result=mysql_do_query("select count(*) from abilities",false);
        if($result===false || ($data=mysql_fetch_row($result))===false || $data[0]==0 || $reset===true)
            {
            //Delete table
            mysql_do_query("DROP TABLE IF EXISTS abilities");
            //Recreate table.
            mysql_do_query("
CREATE TABLE `abilities` (
  `abilityid` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `type` tinyint(4) unsigned NOT NULL default '0',
  `effect` tinyint(4) unsigned NOT NULL default '0',
  `base` int(11) NOT NULL default '0',
  `added` int(11) NOT NULL default '0',
  `attribute` tinyint(4) unsigned NOT NULL default '0',
  `MP_used` int(11) unsigned NOT NULL default '0',
  `targets` tinyint(4) NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  `menu_pic` varchar(64) NOT NULL default 'bignone.png',
  `skill_effect_type` enum('close','throw','shoot','none') NOT NULL default 'none',
  `impact_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `impact_images` varchar(255) NULL default '',
  `impact_sounds` varchar(255) NULL default '',
  `impact_times` varchar(255) NULL default '100,100',
  PRIMARY KEY  (`abilityid`)
) ;");
            }
        }

    function get_ability($index)
        {
        $query="select * from abilities where abilityid=$index";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);
        if($data===false)
            log_error("Ability $index does not exist in the database.");
        $impact_images=is_null($data['impact_images'])?array():explode(',',$data['impact_images']);
        $impact_sounds=is_null($data['impact_sounds'])?array():explode(',',$data['impact_sounds']);
        $impact_times=is_null($data['impact_times'])?array():explode(',',$data['impact_times']);
        return new ABILITY($data['name'],$data['type'],$data['effect'],$data['base'],$data['added']
            ,$data['attribute'],$data['MP_used'],$data['targets'],$data['description']
            ,$data['menu_pic'],$data['skill_effect_type'],$data['impact_animation']
            ,array('images'=>$impact_images,'sounds'=>$impact_sounds,'times'=>$impact_times));
        }

    function &get_all_abilities()
        {
        $abilities=array();
        $query="select abilityid from abilities";
        $result=mysql_do_query($query);
        while(($data=mysql_fetch_row($result))!==false)
            $abilities[$data[0]]=&$this->get_ability($data[0]);
        return $abilities;
        }

    function set_ability(&$ability,$index)
        {
        $name=mysql_real_escape_string($ability->name);
        $description=mysql_real_escape_string($ability->description);
        $impact_images=count($ability->impact_data['images'])>0?"'".mysql_real_escape_string(implode(',',$ability->impact_data['images']))."'":'NULL';
        $impact_sounds=count($ability->impact_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$ability->impact_data['sounds']))."'":'NULL';
        $impact_times=count($ability->impact_data['times'])>0?"'".mysql_real_escape_string(implode(',',$ability->impact_data['times']))."'":'NULL';
        $query="
        replace into abilities
            (abilityid,name,type,effect,base,added,attribute,MP_used,targets,description,menu_pic,skill_effect_type,impact_animation,impact_images,impact_sounds,impact_times)
        values
            ($index,'$name',{$ability->type},{$ability->effect},{$ability->base},{$ability->added},
            {$ability->attribute},{$ability->mp_used},{$ability->targets},'$description',
            '{$ability->menu_pic}','{$ability->skill_effect_type}','{$ability->impact_animation}',
            {$impact_images},{$impact_sounds},{$impact_times})";
        $result=mysql_do_query($query);
        if($index==0)
            $index=mysql_insert_id();
        return $index;
        }

    function delete_ability($index)
        {
        mysql_do_query("delete from abilities where abilityid=$index");
        mysql_do_query("delete from job_abilities where abilityid=$index");
        }

    function write_abilities_file()
        {
        $handle=fopen(INCLUDE_DIR.'abilities.php','w');
        if($handle)
            {
            $abilities=$this->get_all_abilities();
            $ability_ser=serialize($abilities);
            $ability_js=php_data_to_js($abilities);
            fwrite($handle, '<?php require_once INCLUDE_DIR."ability.php"; $GLOBALS["abilities"]=unserialize(<<<EOD
'.$ability_ser.'
EOD
); $GLOBALS["abilities_js"]=\''.clean($ability_js).'\'; ?>');
            fclose($handle);
            }
        }
    }
?>
