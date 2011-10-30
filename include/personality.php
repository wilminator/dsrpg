<?php
require_once INCLUDE_DIR.'images.php';

class PERSONALITY
    {
    var $name;
    var $base_animation;
    var $base_data;
    var $equip_animation;
    var $equip_data;
    var $flee_animation;
    var $flee_data;
    var $hit_animation;
    var $hit_data;
    var $die_animation;
    var $die_data;
    var $attack_close_animation;
    var $attack_close_data;
    var $attack_throw_animation;
    var $attack_throw_data;
    var $attack_shoot_animation;
    var $attack_shoot_data;
    var $skill_animation;
    var $skill_data;
    var $spell_animation;
    var $spell_data;
    var $item_animation;
    var $item_data;
    var $overworld_stand_images;
    var $overworld_move_images;

    function PERSONALITY(
        $name,
        $base_animation,$base_data,
        $equip_animation,$equip_data,
        $flee_animation,$flee_data,
        $hit_animation,$hit_data,
        $die_animation,$die_data,
        $attack_close_animation,$attack_close_data,
        $attack_throw_animation,$attack_throw_data,
        $attack_shoot_animation,$attack_shoot_data,
        $skill_animation,$skill_data,
        $spell_animation,$spell_data,
        $item_animation,$item_data,
        $overworld_stand_images,$overworld_move_images
        )
        {
        $this->name=$name;
        $this->base_animation=$base_animation;
        $this->base_data=$base_data;
        $this->equip_animation=$equip_animation;
        $this->equip_data=$equip_data;
        $this->flee_animation=$flee_animation;
        $this->flee_data=$flee_data;
        $this->hit_animation=$hit_animation;
        $this->hit_data=$hit_data;
        $this->die_animation=$die_animation;
        $this->die_data=$die_data;
        $this->attack_close_animation=$attack_close_animation;
        $this->attack_close_data=$attack_close_data;
        $this->attack_throw_animation=$attack_throw_animation;
        $this->attack_throw_data=$attack_throw_data;
        $this->attack_shoot_animation=$attack_shoot_animation;
        $this->attack_shoot_data=$attack_shoot_data;
        $this->skill_animation=$skill_animation;
        $this->skill_data=$skill_data;
        $this->spell_animation=$spell_animation;
        $this->spell_data=$spell_data;
        $this->item_animation=$item_animation;
        $this->item_data=$item_data;
        $this->overworld_stand_images=$overworld_stand_images;
        $this->overworld_move_images=$overworld_move_images;
        }

    function push_data()
        {
        foreach($this->base_data['images'] as $image) push_image($image);
        foreach($this->equip_data['images'] as $image) push_image($image);
        foreach($this->flee_data['images'] as $image) push_image($image);
        foreach($this->hit_data['images'] as $image) push_image($image);
        foreach($this->die_data['images'] as $image) push_image($image);
        foreach($this->attack_close_data['images'] as $image) push_image($image);
        foreach($this->attack_throw_data['images'] as $image) push_image($image);
        foreach($this->attack_shoot_data['images'] as $image) push_image($image);
        foreach($this->skill_data['images'] as $image) push_image($image);
        foreach($this->spell_data['images'] as $image) push_image($image);
        foreach($this->item_data['images'] as $image) push_image($image);
        }
    }
?>