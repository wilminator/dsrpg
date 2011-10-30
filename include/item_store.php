<?php
require_once INCLUDE_DIR.'item.php';
require_once INCLUDE_DIR.'mysql.php';

require_once INCLUDE_DIR.'js_rip.php';
    if(!function_exists('clean')) {function clean($a) {return addslashes($a);}}

class ITEM_STORE
    {
    //This constructor (tries to) initialize the item table.
    function ITEM_STORE($reset=false)
        {

        $result=mysql_do_query("select count(*) from items",false);
        if($result===false || ($data=mysql_fetch_row($result))===false || $data[0]==0 || $reset===true)
            {
            //Delete table
            mysql_do_query("DROP TABLE IF EXISTS items");
            //Recreate table.
            mysql_do_query("
CREATE TABLE `items` (
  `itemid` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `price` int(11) NOT NULL default '0',
  `effect` tinyint(4) unsigned NOT NULL default '0',
  `use_targets` tinyint(4) NOT NULL default '0',
  `base` int(11) NOT NULL default '0',
  `added` int(11) NOT NULL default '0',
  `attribute` tinyint(4) unsigned NOT NULL default '0',
  `one_use` enum('true','false') NOT NULL default 'true',
  `equip_type` varchar(64) NOT NULL default '',
  `inc_HP` int(11) NOT NULL default '0',
  `inc_MP` int(11) NOT NULL default '0',
  `inc_ACC` int(11) NOT NULL default '0',
  `inc_STR` int(11) NOT NULL default '0',
  `inc_DOD` int(11) NOT NULL default '0',
  `inc_BLO` int(11) NOT NULL default '0',
  `inc_SPD` int(11) NOT NULL default '0',
  `inc_POW` int(11) NOT NULL default '0',
  `inc_RES` int(11) NOT NULL default '0',
  `inc_FOC` int(11) NOT NULL default '0',
  `percinc_HP` int(11) NOT NULL default '0',
  `percinc_MP` int(11) NOT NULL default '0',
  `percinc_ACC` int(11) NOT NULL default '0',
  `percinc_STR` int(11) NOT NULL default '0',
  `percinc_DOD` int(11) NOT NULL default '0',
  `percinc_BLO` int(11) NOT NULL default '0',
  `percinc_SPD` int(11) NOT NULL default '0',
  `percinc_POW` int(11) NOT NULL default '0',
  `percinc_RES` int(11) NOT NULL default '0',
  `percinc_FOC` int(11) NOT NULL default '0',
  `targets` tinyint(4) NOT NULL default '0',
  `attack_count` tinyint(4) unsigned NOT NULL default '0',
  `attack_attribute` tinyint(4) unsigned NOT NULL default '0',
  `ammo_type` varchar(32) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `menu_pic` varchar(64) NOT NULL default 'bignone.png',
  `fight_effect_type` enum('close','throw','shoot','none') NOT NULL default 'none',
  `fight_impact_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `fight_impact_images` varchar(255) default '',
  `fight_impact_sounds` varchar(255) default '',
  `fight_impact_times` varchar(255) default '100,100',
  `use_impact_animation` varchar(64) NOT NULL default 'static_individual_impact',
  `use_impact_images` varchar(255) default '',
  `use_impact_sounds` varchar(255) default '',
  `use_impact_times` varchar(255) default '100,100',
  PRIMARY KEY  (`itemid`)
) ;");
            }
        }

    function get_item($index)
        {
        $query="select * from items where itemid=$index";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);
        if($data===false)
            log_error("Item $index does not exist in the database.");
        if($data['equip_type']=='')
            $equip_type=null;
        else
            $equip_type=explode(',',$data['equip_type']);
        $one_use=($data['one_use']=='true');
        $fight_impact_images=is_null($data['fight_impact_images'])?array():explode(',',$data['fight_impact_images']);
        $fight_impact_sounds=is_null($data['fight_impact_sounds'])?array():explode(',',$data['fight_impact_sounds']);
        $fight_impact_times=is_null($data['fight_impact_times'])?array():explode(',',$data['fight_impact_times']);
        $use_impact_images=is_null($data['use_impact_images'])?array():explode(',',$data['use_impact_images']);
        $use_impact_sounds=is_null($data['use_impact_sounds'])?array():explode(',',$data['use_impact_sounds']);
        $use_impact_times=is_null($data['use_impact_times'])?array():explode(',',$data['use_impact_times']);
        return new ITEM(
            $data['name'],$data['price'],$data['effect'],$data['use_targets'],$data['base'],$data['added'],$data['attribute'],$one_use,$equip_type,
            array($data['inc_HP'],$data['inc_MP'],$data['inc_SPD'],$data['inc_ACC'],$data['inc_STR'],
                $data['inc_DOD'],$data['inc_BLO'],$data['inc_POW'],$data['inc_RES'],$data['inc_FOC']),
            array($data['percinc_HP'],$data['percinc_MP'],$data['percinc_SPD'],$data['percinc_ACC'],$data['percinc_STR'],
                $data['percinc_DOD'],$data['percinc_BLO'],$data['percinc_POW'],$data['percinc_RES'],$data['percinc_FOC']),
            $data['targets'],$data['attack_count'],$data['attack_attribute'],$data['ammo_type'],$data['description'],
            $data['menu_pic'],
            $data['fight_effect_type'],$data['fight_impact_animation'],
            array('images'=>$fight_impact_images,'sounds'=>$fight_impact_sounds,'times'=>$fight_impact_times),
            $data['use_impact_animation'],
            array('images'=>$use_impact_images,'sounds'=>$use_impact_sounds,'times'=>$use_impact_times));
        }

    function &get_all_items()
        {
        $items=array();
        $query="select itemid from items";
        $result=mysql_do_query($query);
        while(($data=mysql_fetch_row($result))!==false)
            $items[$data[0]]=&$this->get_item($data[0]);

        #Must add this entry manually.
        $items[0]=new ITEM(
            "Nothing"          ,     0,         0,         -2,
            0,    0,        0,false  ,array()               ,
            array(  0,  0,  0,  0,  0,  0,  0,  0,  0,  0),
            array(  0,  0,  0,  0,  0,  0,  0,  0,  0,  0),
            0,           1,       0,''                 ,
            "",'bignone.png','close',
            'static_individual_impact',array('images'=>array('punch.gif'),'sounds'=>array(''),'times'=>array(100,300)),
            'static_individual_impact',array('images'=>array(''),'sounds'=>array(''),'times'=>array(100,300))
            );
        return $items;
        }

    function set_item(&$item,$index)
        {
        $name=mysql_real_escape_string($item->name);
        if(is_null($item->equip_type))
            $equip_type='';
        else
            $equip_type=implode(',',$item->equip_type);
        $one_use=($item->one_use?'true':'false');
        $ammo_type=mysql_real_escape_string($item->ammo_type);
        $description=mysql_real_escape_string($item->description);
        $fight_impact_images=count($item->fight_impact_data['images'])>0?"'".mysql_real_escape_string(implode(',',$item->fight_impact_data['images']))."'":'NULL';
        $fight_impact_sounds=count($item->fight_impact_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$item->fight_impact_data['sounds']))."'":'NULL';
        $fight_impact_times=count($item->fight_impact_data['times'])>0?"'".mysql_real_escape_string(implode(',',$item->fight_impact_data['times']))."'":'NULL';
        $use_impact_images=count($item->use_impact_data['images'])>0?"'".mysql_real_escape_string(implode(',',$item->use_impact_data['images']))."'":'NULL';
        $use_impact_sounds=count($item->use_impact_data['sounds'])>0?"'".mysql_real_escape_string(implode(',',$item->use_impact_data['sounds']))."'":'NULL';
        $use_impact_times=count($item->use_impact_data['times'])>0?"'".mysql_real_escape_string(implode(',',$item->use_impact_data['times']))."'":'NULL';
        $query="
        replace into items
            (itemid,name,price,effect,use_targets,base,added,attribute,one_use,equip_type,
            inc_HP,inc_MP,inc_ACC,inc_STR,inc_DOD,inc_BLO,inc_SPD,inc_POW,inc_RES,inc_FOC,
            percinc_HP,percinc_MP,percinc_ACC,percinc_STR,percinc_DOD,percinc_BLO,percinc_SPD,percinc_POW,percinc_RES,percinc_FOC,
            targets,attack_count,attack_attribute,ammo_type,description,menu_pic,fight_effect_type,
            fight_impact_animation,fight_impact_images,fight_impact_sounds,fight_impact_times,
            use_impact_animation,use_impact_images,use_impact_sounds,use_impact_times)
        values
            ($index,'$name',{$item->price},{$item->effect},{$item->use_targets},{$item->base},{$item->added},{$item->attribute},'$one_use','$equip_type',
            {$item->statinc['HP']},{$item->statinc['MP']},{$item->statinc['Accuracy']},{$item->statinc['Strength']},{$item->statinc['Dodge']},
                {$item->statinc['Block']},{$item->statinc['Speed']},{$item->statinc['Power']},{$item->statinc['Resistance']},{$item->statinc['Focus']},
            {$item->statpercinc['HP']},{$item->statpercinc['MP']},{$item->statpercinc['Accuracy']},{$item->statpercinc['Strength']},{$item->statpercinc['Dodge']},
                {$item->statpercinc['Block']},{$item->statpercinc['Speed']},{$item->statpercinc['Power']},{$item->statpercinc['Resistance']},{$item->statpercinc['Focus']},
            {$item->targets},{$item->attack_count},{$item->attack_attribute},'$ammo_type','$description',
            '{$item->menu_pic}','{$item->fight_effect_type}',
            '{$item->fight_impact_animation}',{$fight_impact_images},{$fight_impact_sounds},{$fight_impact_times},
            '{$item->use_impact_animation}',{$use_impact_images},{$use_impact_sounds},{$use_impact_times})";
        $result=mysql_do_query($query);
        if($index==0)
            $index=mysql_insert_id();
        return $index;
        }

    function delete_item($index)
        {
        $query="delete from items where itemid=$index";
        $result=mysql_do_query($query);
        }

    function write_items_file()
        {
        $handle=fopen(INCLUDE_DIR.'items.php','w');
        if($handle)
            {
            $items=$this->get_all_items();
            $item_ser=addslashes(serialize($items));
            $item_ser=serialize($items);
            $item_js=php_data_to_js($items);
            fwrite($handle, '<?php require_once INCLUDE_DIR."item.php"; $GLOBALS["items"]=unserialize(<<<EOD
'.$item_ser.'
EOD
);
$GLOBALS["items_js"]=\''.clean($item_js).'\'; ?>');
            fclose($handle);
            }
        }
    }
?>
