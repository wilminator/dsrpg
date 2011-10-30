<?php
require_once INCLUDE_DIR.'personality.php';
require_once INCLUDE_DIR.'mysql.php';

require_once INCLUDE_DIR.'js_rip.php';
    if(!function_exists('clean')) {function clean($a) {return addslashes($a);}}

class PERSONALITY_STORE
    {
    //This constructor (tries to) initialize the personality table.
    function PERSONALITY_STORE($reset=false)
        {

        $result=mysql_do_query("select count(*) from personalities",false);
        if($result===false || ($data=mysql_fetch_row($result))===false || $data[0]==0 || $reset===true)
            {
            //Delete table
            mysql_do_query("DROP TABLE IF EXISTS personalities");
            //Recreate table.
            mysql_do_query("
CREATE TABLE `personalities` (
  `personalityid` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `base_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `base_images` varchar(255) default '',
  `base_sounds` varchar(255) default '',
  `base_times` varchar(255) default '100,100',
  `equip_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `equip_images` varchar(255) default '',
  `equip_sounds` varchar(255) default '',
  `equip_times` varchar(255) default '100,100',
  `flee_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `flee_images` varchar(255) default '',
  `flee_sounds` varchar(255) default '',
  `flee_times` varchar(255) default '100,100',
  `hit_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `hit_images` varchar(255) default '',
  `hit_sounds` varchar(255) default '',
  `hit_times` varchar(255) default '100,100',
  `die_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `die_images` varchar(255) default '',
  `die_sounds` varchar(255) default '',
  `die_times` varchar(255) default '100,100',
  `attack_close_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `attack_close_images` varchar(255) default '',
  `attack_close_sounds` varchar(255) default '',
  `attack_close_times` varchar(255) default '100,100',
  `attack_throw_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `attack_throw_images` varchar(255) default '',
  `attack_throw_sounds` varchar(255) default '',
  `attack_throw_times` varchar(255) default '100,100',
  `attack_shoot_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `attack_shoot_images` varchar(255) default '',
  `attack_shoot_sounds` varchar(255) default '',
  `attack_shoot_times` varchar(255) default '100,100',
  `skill_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `skill_images` varchar(255) default '',
  `skill_sounds` varchar(255) default '',
  `skill_times` varchar(255) default '100,100',
  `spell_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `spell_images` varchar(255) default '',
  `spell_sounds` varchar(255) default '',
  `spell_times` varchar(255) default '100,100',
  `item_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `item_images` varchar(255) default '',
  `item_sounds` varchar(255) default '',
  `item_times` varchar(255) default '100,100',
  `overworld_stand_up` varchar(64) default '',
  `overworld_stand_down` varchar(64) default '',
  `overworld_stand_left` varchar(64) default '',
  `overworld_stand_right` varchar(64) default '',
  `overworld_move_up` varchar(64) default '',
  `overworld_move_down` varchar(64) default '',
  `overworld_move_left` varchar(64) default '',
  `overworld_move_right` varchar(64) default '',
  PRIMARY KEY  (`personalityid`)
) ;");
            }
        }

    function get_personality($index)
        {
        $query="select * from personalities where personalityid=$index";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);
        if($data===false)
            log_error("Personality $index does not exist in the database.");
        $base_images=is_null($data['base_images'])?array():explode(',',$data['base_images']);
        $base_sounds=is_null($data['base_sounds'])?array():explode(',',$data['base_sounds']);
        $base_times=is_null($data['base_times'])?array():explode(',',$data['base_times']);
        $equip_images=is_null($data['equip_images'])?array():explode(',',$data['equip_images']);
        $equip_sounds=is_null($data['equip_sounds'])?array():explode(',',$data['equip_sounds']);
        $equip_times=is_null($data['equip_times'])?array():explode(',',$data['equip_times']);
        $flee_images=is_null($data['flee_images'])?array():explode(',',$data['flee_images']);
        $flee_sounds=is_null($data['flee_sounds'])?array():explode(',',$data['flee_sounds']);
        $flee_times=is_null($data['flee_times'])?array():explode(',',$data['flee_times']);
        $hit_images=is_null($data['hit_images'])?array():explode(',',$data['hit_images']);
        $hit_sounds=is_null($data['hit_sounds'])?array():explode(',',$data['hit_sounds']);
        $hit_times=is_null($data['hit_times'])?array():explode(',',$data['hit_times']);
        $die_images=is_null($data['die_images'])?array():explode(',',$data['die_images']);
        $die_sounds=is_null($data['die_sounds'])?array():explode(',',$data['die_sounds']);
        $die_times=is_null($data['die_times'])?array():explode(',',$data['die_times']);
        $attack_close_images=is_null($data['attack_close_images'])?array():explode(',',$data['attack_close_images']);
        $attack_close_sounds=is_null($data['attack_close_sounds'])?array():explode(',',$data['attack_close_sounds']);
        $attack_close_times=is_null($data['attack_close_times'])?array():explode(',',$data['attack_close_times']);
        $attack_throw_images=is_null($data['attack_throw_images'])?array():explode(',',$data['attack_throw_images']);
        $attack_throw_sounds=is_null($data['attack_throw_sounds'])?array():explode(',',$data['attack_throw_sounds']);
        $attack_throw_times=is_null($data['attack_throw_times'])?array():explode(',',$data['attack_throw_times']);
        $attack_shoot_images=is_null($data['attack_shoot_images'])?array():explode(',',$data['attack_shoot_images']);
        $attack_shoot_sounds=is_null($data['attack_shoot_sounds'])?array():explode(',',$data['attack_shoot_sounds']);
        $attack_shoot_times=is_null($data['attack_shoot_times'])?array():explode(',',$data['attack_shoot_times']);
        $skill_images=is_null($data['skill_images'])?array():explode(',',$data['skill_images']);
        $skill_sounds=is_null($data['skill_sounds'])?array():explode(',',$data['skill_sounds']);
        $skill_times=is_null($data['skill_times'])?array():explode(',',$data['skill_times']);
        $spell_images=is_null($data['spell_images'])?array():explode(',',$data['spell_images']);
        $spell_sounds=is_null($data['spell_sounds'])?array():explode(',',$data['spell_sounds']);
        $spell_times=is_null($data['spell_times'])?array():explode(',',$data['spell_times']);
        $item_images=is_null($data['item_images'])?array():explode(',',$data['item_images']);
        $item_sounds=is_null($data['item_sounds'])?array():explode(',',$data['item_sounds']);
        $item_times=is_null($data['item_times'])?array():explode(',',$data['item_times']);
        $overworld_stand_images=array(
            'up'=>$data['overworld_stand_up'],
            'down'=>$data['overworld_stand_down'],
            'left'=>$data['overworld_stand_left'],
            'right'=>$data['overworld_stand_right']);
        $overworld_move_images=array(
            'up'=>$data['overworld_move_up'],
            'down'=>$data['overworld_move_down'],
            'left'=>$data['overworld_move_left'],
            'right'=>$data['overworld_move_right']);
        return new PERSONALITY(
            $data['name'],
            $data['base_animation'],array('images'=>$base_images,'sounds'=>$base_sounds,'times'=>$base_times),
            $data['equip_animation'],array('images'=>$equip_images,'sounds'=>$equip_sounds,'times'=>$equip_times),
            $data['flee_animation'],array('images'=>$flee_images,'sounds'=>$flee_sounds,'times'=>$flee_times),
            $data['hit_animation'],array('images'=>$hit_images,'sounds'=>$hit_sounds,'times'=>$hit_times),
            $data['die_animation'],array('images'=>$die_images,'sounds'=>$die_sounds,'times'=>$die_times),
            $data['attack_close_animation'],array('images'=>$attack_close_images,'sounds'=>$attack_close_sounds,'times'=>$attack_close_times),
            $data['attack_throw_animation'],array('images'=>$attack_throw_images,'sounds'=>$attack_throw_sounds,'times'=>$attack_throw_times),
            $data['attack_shoot_animation'],array('images'=>$attack_shoot_images,'sounds'=>$attack_shoot_sounds,'times'=>$attack_shoot_times),
            $data['skill_animation'],array('images'=>$skill_images,'sounds'=>$skill_sounds,'times'=>$skill_times),
            $data['spell_animation'],array('images'=>$spell_images,'sounds'=>$spell_sounds,'times'=>$spell_times),
            $data['item_animation'],array('images'=>$item_images,'sounds'=>$item_sounds,'times'=>$item_times),
            $overworld_stand_images,$overworld_move_images);
        }

    function &get_all_personalities()
        {
        $personalities=array();
        $query="select personalityid from personalities";
        $result=mysql_do_query($query);
        while(($data=mysql_fetch_row($result))!==false)
            $personalities[$data[0]]=&$this->get_personality($data[0]);
        return $personalities;
        }

    function set_personality(&$personality,$index)
        {
        $name=mysql_real_escape_string($personality->name);
        $base_animation=mysql_real_escape_string($personality->base_animation);
        $base_images=count($personality->base_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->base_data['images']))."'":'NULL';
        $base_sounds=count($personality->base_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->base_data['sounds']))."'":'NULL';
        $base_times=count($personality->base_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->base_data['times']))."'":'NULL';
        $equip_animation=mysql_real_escape_string($personality->equip_animation);
        $equip_images=count($personality->equip_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->equip_data['images']))."'":'NULL';
        $equip_sounds=count($personality->equip_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->equip_data['sounds']))."'":'NULL';
        $equip_times=count($personality->equip_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->equip_data['times']))."'":'NULL';
        $flee_animation=mysql_real_escape_string($personality->flee_animation);
        $flee_images=count($personality->flee_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->flee_data['images']))."'":'NULL';
        $flee_sounds=count($personality->flee_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->flee_data['sounds']))."'":'NULL';
        $flee_times=count($personality->flee_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->flee_data['times']))."'":'NULL';
        $hit_animation=mysql_real_escape_string($personality->hit_animation);
        $hit_images=count($personality->hit_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->hit_data['images']))."'":'NULL';
        $hit_sounds=count($personality->hit_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->hit_data['sounds']))."'":'NULL';
        $hit_times=count($personality->hit_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->hit_data['times']))."'":'NULL';
        $die_animation=mysql_real_escape_string($personality->die_animation);
        $die_images=count($personality->die_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->die_data['images']))."'":'NULL';
        $die_sounds=count($personality->die_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->die_data['sounds']))."'":'NULL';
        $die_times=count($personality->die_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->die_data['times']))."'":'NULL';
        $attack_close_animation=mysql_real_escape_string($personality->attack_close_animation);
        $attack_close_images=count($personality->attack_close_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->attack_close_data['images']))."'":'NULL';
        $attack_close_sounds=count($personality->attack_close_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->attack_close_data['sounds']))."'":'NULL';
        $attack_close_times=count($personality->attack_close_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->attack_close_data['times']))."'":'NULL';
        $attack_throw_animation=mysql_real_escape_string($personality->attack_throw_animation);
        $attack_throw_images=count($personality->attack_throw_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->attack_throw_data['images']))."'":'NULL';
        $attack_throw_sounds=count($personality->attack_throw_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->attack_throw_data['sounds']))."'":'NULL';
        $attack_throw_times=count($personality->attack_throw_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->attack_throw_data['times']))."'":'NULL';
        $attack_shoot_animation=mysql_real_escape_string($personality->attack_shoot_animation);
        $attack_shoot_images=count($personality->attack_shoot_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->attack_shoot_data['images']))."'":'NULL';
        $attack_shoot_sounds=count($personality->attack_shoot_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->attack_shoot_data['sounds']))."'":'NULL';
        $attack_shoot_times=count($personality->attack_shoot_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->attack_shoot_data['times']))."'":'NULL';
        $skill_animation=mysql_real_escape_string($personality->skill_animation);
        $skill_images=count($personality->skill_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->skill_data['images']))."'":'NULL';
        $skill_sounds=count($personality->skill_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->skill_data['sounds']))."'":'NULL';
        $skill_times=count($personality->skill_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->skill_data['times']))."'":'NULL';
        $spell_animation=mysql_real_escape_string($personality->spell_animation);
        $spell_images=count($personality->spell_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->spell_data['images']))."'":'NULL';
        $spell_sounds=count($personality->spell_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->spell_data['sounds']))."'":'NULL';
        $spell_times=count($personality->spell_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->spell_data['times']))."'":'NULL';
        $item_animation=mysql_real_escape_string($personality->item_animation);
        $item_images=count($personality->item_data['images'])>0?"'".mysql_real_escape_string(implode(',',$personality->item_data['images']))."'":'NULL';
        $item_sounds=count($personality->item_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$personality->item_data['sounds']))."'":'NULL';
        $item_times=count($personality->item_data['times'])>0?"'".mysql_real_escape_string(implode(',',$personality->item_data['times']))."'":'NULL';
        $overworld_stand_up=mysql_real_escape_string($personality->overworld_stand_images['up']);
        $overworld_stand_down=mysql_real_escape_string($personality->overworld_stand_images['down']);
        $overworld_stand_left=mysql_real_escape_string($personality->overworld_stand_images['left']);
        $overworld_stand_right=mysql_real_escape_string($personality->overworld_stand_images['right']);
        $overworld_move_up=mysql_real_escape_string($personality->overworld_move_images['up']);
        $overworld_move_down=mysql_real_escape_string($personality->overworld_move_images['down']);
        $overworld_move_left=mysql_real_escape_string($personality->overworld_move_images['left']);
        $overworld_move_right=mysql_real_escape_string($personality->overworld_move_images['right']);
        $query="
        replace into personalities
            (personalityid,name,
            base_animation,base_images,base_sounds,base_times,
            equip_animation,equip_images,equip_sounds,equip_times,
            flee_animation,flee_images,flee_sounds,flee_times,
            hit_animation,hit_images,hit_sounds,hit_times,
            die_animation,die_images,die_sounds,die_times,
            attack_close_animation,attack_close_images,attack_close_sounds,attack_close_times,
            attack_throw_animation,attack_throw_images,attack_throw_sounds,attack_throw_times,
            attack_shoot_animation,attack_shoot_images,attack_shoot_sounds,attack_shoot_times,
            skill_animation,skill_images,skill_sounds,skill_times,
            spell_animation,spell_images,spell_sounds,spell_times,
            item_animation,item_images,item_sounds,item_times,
            overworld_stand_up,overworld_stand_down,
            overworld_stand_left,overworld_stand_right,
            overworld_move_up,overworld_move_down,
            overworld_move_left,overworld_move_right)
        values
            ($index,'$name',
            '$base_animation',$base_images,$base_sounds,$base_times,
            '$equip_animation',$equip_images,$equip_sounds,$equip_times,
            '$flee_animation',$flee_images,$flee_sounds,$flee_times,
            '$hit_animation',$hit_images,$hit_sounds,$hit_times,
            '$die_animation',$die_images,$die_sounds,$die_times,
            '$attack_close_animation',$attack_close_images,$attack_close_sounds,$attack_close_times,
            '$attack_throw_animation',$attack_throw_images,$attack_throw_sounds,$attack_throw_times,
            '$attack_shoot_animation',$attack_shoot_images,$attack_shoot_sounds,$attack_shoot_times,
            '$skill_animation',$skill_images,$skill_sounds,$skill_times,
            '$spell_animation',$spell_images,$spell_sounds,$spell_times,
            '$item_animation',$item_images,$item_sounds,$item_times,
            '$overworld_stand_up','$overworld_stand_down',
            '$overworld_stand_left','$overworld_stand_right',
            '$overworld_move_up','$overworld_move_down',
            '$overworld_move_left','$overworld_move_right')";
        $result=mysql_do_query($query);
        if($index==0)
            $index=mysql_insert_id();
        return $index;
        }

    function delete_personality($index)
        {
        $query="delete from personalities where personalityid=$index";
        $result=mysql_do_query($query);
        }

    function write_personalities_file()
        {
        $handle=fopen(INCLUDE_DIR.'personalities.php','w');
        if($handle)
            {
            $personalities=$this->get_all_personalities();
            $personality_ser=addslashes(serialize($personalities));
            $personality_ser=serialize($personalities);
            $personality_js=php_data_to_js($personalities);
            fwrite($handle, '<?php require_once INCLUDE_DIR."personality.php"; $GLOBALS["personalities"]=unserialize(<<<EOD
'.$personality_ser.'
EOD
);
$GLOBALS["personalities_js"]=\''.clean($personality_js).'\'; ?>');
            fclose($handle);
            }
        }
    }
?>
