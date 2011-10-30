<?php
require_once INCLUDE_DIR.'js_rip.php';

function abilities_to_js()
    {
    return php_data_to_js($GLOBALS['abilities']);
    }

class ABILITY
    {
    var $name;
    var $effect;
    var $type;
    var $mp_used;
    var $targets;//-1=group,-2=party
    var $base;
    var $added;
    var $attribute;
    var $description;
    var $menu_pic;
    var $skill_effect_type;
    var $impact_animation;
    var $impact_data;

    function ability($name,$type,$effect,$base,$added,$attribute,$mp,$targets,
        $description,$menu_pic,$skill_effect_type,$impact_animation,$impact_data)
        {
        $this->name=$name;
        $this->type=$type;
        $this->effect=$effect;
        $this->base=$base;
        $this->added=$added;
        $this->attribute=$attribute;
        $this->mp_used=$mp;
        $this->targets=$targets;
        $this->description=$description;
        $this->menu_pic=$menu_pic;
        $this->skill_effect_type=$skill_effect_type;
        $this->impact_animation=$impact_animation;
        $this->impact_data=$impact_data;
        }

    function describe_use()
        {
        return ($this->type==0?'Spell. ':'Skill. ')
            .describe_range($this->targets)
            ." "
            .describe_effect($this->effect,$this->base,$this->added,$this->attribute);
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
    }
?>
