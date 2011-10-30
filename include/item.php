<?php
require_once INCLUDE_DIR.'array.php';
require_once INCLUDE_DIR.'constants.php';

class ITEM{
    var $name;
    var $price;
    var $effect;
    var $use_targets;
    var $base;
    var $added;
    var $attribute;
    var $one_use;
    var $equip_type;
    var $statinc;
    var $statpercinc;
    var $targets;
    var $attack_count;
    var $attack_attribute;
    var $ammo_type;
    var $description;
    var $menu_pic;
    var $fight_effect_type;
    var $fight_impact_animation;
    var $fight_impact_data;
    var $use_impact_animation;
    var $use_impact_data;

    function ITEM($name,$price,$effect,$use_targets,$base,$added,$attribute,
        $one_use,$equip_type,$statinc,$statpercinc,$targets,$atkcnt,
        $attack_attribute,$ammo_type,$description,$menu_pic,
        $fight_effect_type,$fight_impact_animation,$fight_impact_data,
        $use_impact_animation,$use_impact_data)
        {
        $this->name=$name;
        $this->price=$price;
        $this->effect=$effect;
        $this->use_targets=$use_targets;
        $this->base=$base;
        $this->added=$added;
        $this->attribute=$attribute;
        $this->one_use=$one_use;
        $this->equip_type=$equip_type;
        $this->statinc=create_array($GLOBALS['character_stats'],$statinc,0);
        $this->statpercinc=create_array($GLOBALS['character_stats'],$statpercinc,0);
        $this->targets=$targets;
        $this->attack_count=$atkcnt;
        $this->attack_attribute=$attack_attribute;
        $this->ammo_type=$ammo_type;
        $this->description=$description;
        $this->menu_pic=$menu_pic;
        $this->fight_effect_type=$fight_effect_type;
        $this->fight_impact_animation=$fight_impact_animation;
        $this->fight_impact_data=$fight_impact_data;
        $this->use_impact_animation=$use_impact_animation;
        $this->use_impact_data=$use_impact_data;
        }

    function describe_use()
        {
        $response= describe_range($this->targets)
            ." "
            .describe_effect($this->effect,$this->base,$this->added,$this->attribute);
        if($this->one_use)
            $response.=' Disappears when used as an item.';
        else
            $response.=' Never disappears when used as an item.';
        return $response;
        }

    function describe_equip()
        {
        if(is_null($this->equip_type))
            return "Not equippable.";
        $response='';
        if(in_array('lhand',$this->equip_type)&&in_array('rhand',$this->equip_type)&&in_array('rammo',$this->equip_type))
            $response="Two handed expendable weapon.";
        if(in_array('lhand',$this->equip_type)&&in_array('rhand',$this->equip_type)&&$this->ammo_type!='')
            $response="Two handed weapon needing '{$this->ammo_type}' ammunition.";
        if(in_array('lhand',$this->equip_type)&&in_array('rhand',$this->equip_type))
            $response="Two handed weapon.";
        elseif(in_array('hand',$this->equip_type)&&in_array('ammo',$this->equip_type))
            $response="Single handed expendable weapon.";
        elseif(in_array('hand',$this->equip_type)&&$this->ammo_type!='')
            $response="Single handed weapon needing '{$this->ammo_type}' ammunition.";
        elseif(in_array('hand',$this->equip_type))
            $response="Single handed weapon.";
        elseif(in_array('arm',$this->equip_type))
            $response="Protective item that is worn on the arm.";
        elseif(in_array('body',$this->equip_type))
            $response="Body armor.";
        elseif(in_array('head',$this->equip_type))
            $response="Head gear.";
        elseif(in_array('back',$this->equip_type))
            $response="Accessory that covers the back.";
        elseif(in_array('feet',$this->equip_type))
            $response="Footwear.";
        elseif(in_array('ammo',$this->equip_type))
            $response="Ammunition for weapons needing '{$this->ammo_type}' type ammunition.";
        if($response!='')
            $response.=" ".$this->describe_stats();
        else
            $response.="Not equippable.";
        return $response;
        }

    function describe_stats()
        {
        global $attributes;

        $response=array();
        foreach($this->statinc as $index=>$inc)
            {
            $percinc=$this->statpercinc[$index];
            if($inc!=0 || $percinc!=0)
                {
                $output=$index;
                if($percinc!=0)
                    {
                    if($percinc>0) $percinc='+'.$percinc;
                    $output.=" $percinc%";
                    }
                if($inc!=0)
                    {
                    if($inc>0) $inc='+'.$inc;
                    $output.=" $inc";
                    }
                $response[]=$output;
                }
            }
        $atk_cnt=($this->attack_count>1?"Attacks {$this->attack_count} times per turn. ":'');
        return "{$attributes[$this->attack_attribute]} based. ".$atk_cnt.implode(', ',$response);
        }

    function is_bad()
        {
        return is_effect_bad($this->effect);
        }

    function useable_in_field()
        {
        return field_effect($this->effect);
        }

    function useable_in_combat()
        {
        return combat_effect($this->effect);
        }
        
    function get_max_effect()
        {
        get_max_effect($this->effect,$this->base,$this->added,$this->attribute);
        }
    }
?>