<?php require_once INCLUDE_DIR."item.php"; $GLOBALS["items"]=unserialize(<<<EOD
a:11:{i:1;O:4:"ITEM":22:{s:4:"name";s:11:"Laser Lance";s:5:"price";s:3:"225";s:6:"effect";s:1:"0";s:11:"use_targets";s:2:"-2";s:4:"base";s:1:"0";s:5:"added";s:1:"0";s:9:"attribute";s:1:"0";s:7:"one_use";b:0;s:10:"equip_type";a:2:{i:0;s:5:"lhand";i:1;s:5:"rhand";}s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:25;s:8:"Accuracy";d:20;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:-5;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";s:1:"0";s:12:"attack_count";s:1:"1";s:16:"attack_attribute";s:1:"0";s:9:"ammo_type";s:0:"";s:11:"description";s:42:"A typical lance with a laser as the blade.";s:8:"menu_pic";s:9:"sword.png";s:17:"fight_effect_type";s:5:"close";s:22:"fight_impact_animation";s:13:"static_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:1:{i:0;s:9:"slash.gif";}s:6:"sounds";a:1:{i:0;s:15:"blobs_slash.mp3";}s:5:"times";a:2:{i:0;s:3:"100";i:1;s:3:"300";}}s:20:"use_impact_animation";s:13:"static_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:1:{i:0;s:0:"";}s:6:"sounds";a:1:{i:0;s:0:"";}s:5:"times";a:2:{i:0;s:0:"";i:1;s:0:"";}}}i:2;O:4:"ITEM":22:{s:4:"name";s:9:"Spark Rod";s:5:"price";s:3:"125";s:6:"effect";s:1:"0";s:11:"use_targets";s:2:"-2";s:4:"base";s:1:"0";s:5:"added";s:1:"0";s:9:"attribute";s:1:"0";s:7:"one_use";b:0;s:10:"equip_type";a:1:{i:0;s:4:"hand";}s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:10;s:8:"Accuracy";d:13;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:-3;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";s:1:"0";s:12:"attack_count";s:1:"1";s:16:"attack_attribute";s:1:"0";s:9:"ammo_type";s:0:"";s:11:"description";s:68:"A rod that electrocutes whatever it hits.  A magician's cattle prod.";s:8:"menu_pic";s:9:"sword.png";s:17:"fight_effect_type";s:5:"close";s:22:"fight_impact_animation";s:24:"static_individual_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:1:{i:0;s:13:"explosion.gif";}s:6:"sounds";a:1:{i:0;s:15:"blobs_punch.mp3";}s:5:"times";a:2:{i:0;s:1:"0";i:1;s:3:"500";}}s:20:"use_impact_animation";s:13:"static_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:1:{i:0;s:0:"";}s:6:"sounds";a:1:{i:0;s:0:"";}s:5:"times";a:2:{i:0;s:0:"";i:1;s:0:"";}}}i:3;O:4:"ITEM":22:{s:4:"name";s:12:"Wraith Touch";s:5:"price";s:4:"2000";s:6:"effect";s:1:"0";s:11:"use_targets";s:2:"-2";s:4:"base";s:1:"0";s:5:"added";s:1:"0";s:9:"attribute";s:1:"0";s:7:"one_use";b:0;s:10:"equip_type";a:1:{i:0;s:4:"hand";}s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:4;s:8:"Accuracy";d:5;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:25;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:1;s:8:"Accuracy";d:2;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";s:1:"0";s:12:"attack_count";s:1:"1";s:16:"attack_attribute";s:1:"0";s:9:"ammo_type";s:0:"";s:11:"description";s:53:"The touch of a banchee that causes whither and decay.";s:8:"menu_pic";s:9:"sword.png";s:17:"fight_effect_type";s:5:"shoot";s:22:"fight_impact_animation";s:13:"static_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:1:{i:0;s:9:"slash.gif";}s:6:"sounds";a:1:{i:0;s:10:"Sword3.mp3";}s:5:"times";a:2:{i:0;s:3:"100";i:1;s:3:"300";}}s:20:"use_impact_animation";s:13:"static_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:1:{i:0;s:0:"";}s:6:"sounds";a:1:{i:0;s:0:"";}s:5:"times";a:2:{i:0;s:0:"";i:1;s:0:"";}}}i:4;O:4:"ITEM":22:{s:4:"name";s:11:"M16A2 Rifle";s:5:"price";s:4:"6200";s:6:"effect";s:1:"0";s:11:"use_targets";s:2:"-2";s:4:"base";s:1:"0";s:5:"added";s:1:"0";s:9:"attribute";s:1:"0";s:7:"one_use";b:0;s:10:"equip_type";a:2:{i:0;s:5:"lhand";i:1;s:5:"rhand";}s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:50;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:20;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:-100;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";s:1:"0";s:12:"attack_count";s:1:"3";s:16:"attack_attribute";s:1:"0";s:9:"ammo_type";s:12:"5.62mm Round";s:11:"description";s:73:"The standard issue weapon for the USMC. Uses 5.62mm rounds as ammunition.";s:8:"menu_pic";s:12:"m16rifle.png";s:17:"fight_effect_type";s:5:"throw";s:22:"fight_impact_animation";s:17:"ranged_arc_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:3:{i:0;s:9:"blood.png";i:1;s:0:"";i:2;s:0:"";}s:6:"sounds";a:2:{i:0;s:9:"Blow2.mp3";i:1;s:0:"";}s:5:"times";a:5:{i:0;s:2:"25";i:1;s:3:"100";i:2;s:1:"0";i:3;s:2:"30";i:4;s:1:"1";}}s:20:"use_impact_animation";s:13:"static_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:1:{i:0;s:0:"";}s:6:"sounds";a:1:{i:0;s:0:"";}s:5:"times";a:2:{i:0;s:0:"";i:1;s:0:"";}}}i:5;O:4:"ITEM":22:{s:4:"name";s:10:"Small Bomb";s:5:"price";s:2:"25";s:6:"effect";s:1:"0";s:11:"use_targets";s:2:"-2";s:4:"base";s:1:"0";s:5:"added";s:1:"0";s:9:"attribute";s:1:"0";s:7:"one_use";b:0;s:10:"equip_type";a:2:{i:0;s:4:"hand";i:1;s:4:"ammo";}s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:15;s:8:"Accuracy";d:150;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:-20;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:-100;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";s:1:"1";s:12:"attack_count";s:1:"1";s:16:"attack_attribute";s:1:"0";s:9:"ammo_type";s:0:"";s:11:"description";s:43:"A small bomb made from household materials.";s:8:"menu_pic";s:11:"bigbomb.png";s:17:"fight_effect_type";s:5:"close";s:22:"fight_impact_animation";s:17:"ranged_arc_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:3:{i:0;s:8:"bomb.gif";i:1;s:13:"explosion.gif";i:2;s:13:"explosion.gif";}s:6:"sounds";a:2:{i:0;s:10:"Flame4.mp3";i:1;s:12:"fireball.mp3";}s:5:"times";a:5:{i:0;s:2:"15";i:1;s:3:"100";i:2;s:3:"0.1";i:3;s:2:"30";i:4;s:2:"70";}}s:20:"use_impact_animation";s:17:"ranged_arc_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:3:{i:0;s:8:"bomb.gif";i:1;s:13:"explosion.gif";i:2;s:13:"explosion.gif";}s:6:"sounds";a:2:{i:0;s:10:"Flame4.mp3";i:1;s:12:"fireball.mp3";}s:5:"times";a:5:{i:0;s:2:"15";i:1;s:3:"100";i:2;s:3:"0.1";i:3;s:2:"15";i:4;s:2:"70";}}}i:6;O:4:"ITEM":22:{s:4:"name";s:17:"5.62mm Ball Round";s:5:"price";s:1:"3";s:6:"effect";s:1:"0";s:11:"use_targets";s:2:"-2";s:4:"base";s:1:"0";s:5:"added";s:1:"0";s:9:"attribute";s:1:"0";s:7:"one_use";b:0;s:10:"equip_type";a:1:{i:0;s:4:"ammo";}s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:300;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";s:1:"0";s:12:"attack_count";s:1:"1";s:16:"attack_attribute";s:1:"0";s:9:"ammo_type";s:12:"5.62mm Round";s:11:"description";s:37:"5.62mm standard anti-infantry rounds.";s:8:"menu_pic";s:12:"556round.png";s:17:"fight_effect_type";s:5:"close";s:22:"fight_impact_animation";s:17:"ranged_arc_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:3:{i:0;s:9:"blood.png";i:1;s:0:"";i:2;s:0:"";}s:6:"sounds";a:2:{i:0;s:0:"";i:1;s:0:"";}s:5:"times";a:5:{i:0;s:1:"1";i:1;s:3:"100";i:2;s:1:"0";i:3;s:2:"30";i:4;s:1:"1";}}s:20:"use_impact_animation";s:17:"ranged_arc_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:3:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";}s:6:"sounds";a:2:{i:0;s:0:"";i:1;s:0:"";}s:5:"times";a:5:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";i:4;s:0:"";}}}i:7;O:4:"ITEM":22:{s:4:"name";s:11:"Medical Kit";s:5:"price";s:2:"15";s:6:"effect";s:1:"1";s:11:"use_targets";s:1:"0";s:4:"base";s:2:"50";s:5:"added";s:2:"20";s:9:"attribute";s:1:"0";s:7:"one_use";b:1;s:10:"equip_type";N;s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";s:1:"0";s:12:"attack_count";s:1:"0";s:16:"attack_attribute";s:1:"0";s:9:"ammo_type";s:0:"";s:11:"description";s:49:"A first aid kit capable of restoring about 60 HP.";s:8:"menu_pic";s:10:"medkit.png";s:17:"fight_effect_type";s:5:"close";s:22:"fight_impact_animation";s:13:"static_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:1:{i:0;s:0:"";}s:6:"sounds";a:1:{i:0;s:0:"";}s:5:"times";a:2:{i:0;s:0:"";i:1;s:0:"";}}s:20:"use_impact_animation";s:13:"static_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:1:{i:0;s:8:"heal.gif";}s:6:"sounds";a:1:{i:0;s:9:"Item2.mp3";}s:5:"times";a:2:{i:0;s:3:"100";i:1;s:3:"300";}}}i:8;O:4:"ITEM":22:{s:4:"name";s:11:"9mm Baretta";s:5:"price";s:4:"3500";s:6:"effect";s:1:"0";s:11:"use_targets";s:2:"-2";s:4:"base";s:1:"0";s:5:"added";s:1:"0";s:9:"attribute";s:1:"0";s:7:"one_use";b:0;s:10:"equip_type";a:1:{i:0;s:4:"hand";}s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:20;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:40;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:-100;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";s:1:"0";s:12:"attack_count";s:1:"4";s:16:"attack_attribute";s:1:"0";s:9:"ammo_type";s:9:"9mm Round";s:11:"description";s:59:"Standard issue pistol for the USMC.  Light and easy to use.";s:8:"menu_pic";s:8:"fire.png";s:17:"fight_effect_type";s:5:"shoot";s:22:"fight_impact_animation";s:17:"ranged_arc_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:3:{i:0;s:9:"blood.png";i:1;s:0:"";i:2;s:0:"";}s:6:"sounds";a:2:{i:0;s:9:"Blow3.mp3";i:1;s:0:"";}s:5:"times";a:5:{i:0;s:2:"45";i:1;s:3:"100";i:2;s:1:"0";i:3;s:2:"30";i:4;s:1:"1";}}s:20:"use_impact_animation";s:17:"ranged_arc_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:3:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";}s:6:"sounds";a:2:{i:0;s:0:"";i:1;s:0:"";}s:5:"times";a:5:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";i:4;s:0:"";}}}i:9;O:4:"ITEM":22:{s:4:"name";s:10:"9mm Bullet";s:5:"price";s:1:"2";s:6:"effect";s:1:"0";s:11:"use_targets";s:2:"-2";s:4:"base";s:1:"0";s:5:"added";s:1:"0";s:9:"attribute";s:1:"0";s:7:"one_use";b:0;s:10:"equip_type";a:1:{i:0;s:4:"ammo";}s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:200;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";s:1:"0";s:12:"attack_count";s:1:"1";s:16:"attack_attribute";s:1:"0";s:9:"ammo_type";s:9:"9mm Round";s:11:"description";s:18:"Normal 9mm bullet.";s:8:"menu_pic";s:11:"bignone.png";s:17:"fight_effect_type";s:5:"close";s:22:"fight_impact_animation";s:17:"ranged_arc_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:3:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";}s:6:"sounds";a:3:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";}s:5:"times";a:5:{i:0;s:1:"1";i:1;s:3:"100";i:2;s:1:"0";i:3;s:2:"30";i:4;s:1:"1";}}s:20:"use_impact_animation";s:17:"ranged_arc_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:1:{i:0;s:0:"";}s:6:"sounds";a:1:{i:0;s:0:"";}s:5:"times";a:1:{i:0;s:0:"";}}}i:10;O:4:"ITEM":22:{s:4:"name";s:11:"Kabar Knife";s:5:"price";s:3:"525";s:6:"effect";s:1:"0";s:11:"use_targets";s:2:"-2";s:4:"base";s:1:"0";s:5:"added";s:1:"0";s:9:"attribute";s:1:"0";s:7:"one_use";b:0;s:10:"equip_type";a:1:{i:0;s:4:"hand";}s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:15;s:8:"Accuracy";d:35;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:20;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";s:1:"0";s:12:"attack_count";s:1:"1";s:16:"attack_attribute";s:1:"0";s:9:"ammo_type";s:0:"";s:11:"description";s:52:"A sharp, trusty hunter's knife used to gut monsters.";s:8:"menu_pic";s:9:"fight.png";s:17:"fight_effect_type";s:5:"close";s:22:"fight_impact_animation";s:17:"ranged_arc_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:3:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";}s:6:"sounds";a:3:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";}s:5:"times";a:5:{i:0;s:1:"1";i:1;s:3:"100";i:2;s:1:"0";i:3;s:2:"30";i:4;s:1:"1";}}s:20:"use_impact_animation";s:17:"ranged_arc_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:1:{i:0;s:0:"";}s:6:"sounds";a:1:{i:0;s:0:"";}s:5:"times";a:1:{i:0;s:0:"";}}}i:0;O:4:"ITEM":22:{s:4:"name";s:7:"Nothing";s:5:"price";i:0;s:6:"effect";i:0;s:11:"use_targets";i:-2;s:4:"base";i:0;s:5:"added";i:0;s:9:"attribute";i:0;s:7:"one_use";b:0;s:10:"equip_type";a:0:{}s:7:"statinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:11:"statpercinc";a:10:{s:2:"HP";d:0;s:2:"MP";d:0;s:5:"Speed";d:0;s:8:"Accuracy";d:0;s:8:"Strength";d:0;s:5:"Dodge";d:0;s:5:"Block";d:0;s:5:"Power";d:0;s:10:"Resistance";d:0;s:5:"Focus";d:0;}s:7:"targets";i:0;s:12:"attack_count";i:1;s:16:"attack_attribute";i:0;s:9:"ammo_type";s:0:"";s:11:"description";s:0:"";s:8:"menu_pic";s:11:"bignone.png";s:17:"fight_effect_type";s:5:"close";s:22:"fight_impact_animation";s:24:"static_individual_impact";s:17:"fight_impact_data";a:3:{s:6:"images";a:1:{i:0;s:9:"punch.gif";}s:6:"sounds";a:1:{i:0;s:0:"";}s:5:"times";a:2:{i:0;i:100;i:1;i:300;}}s:20:"use_impact_animation";s:24:"static_individual_impact";s:15:"use_impact_data";a:3:{s:6:"images";a:1:{i:0;s:0:"";}s:6:"sounds";a:1:{i:0;s:0:"";}s:5:"times";a:2:{i:0;i:100;i:1;i:300;}}}}
EOD
);
$GLOBALS["items_js"]='[
  {\'name\':\'Nothing\',\'price\':0,\'effect\':0,\'use_targets\':-2,\'base\':0,\'added\':0,\'attribute\':0,\'one_use\':false,
    equip_type:[],
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':0,\'attack_count\':1,\'attack_attribute\':0,\'ammo_type\':\'\',\'description\':\'\',\'menu_pic\':\'bignone.png\',\'fight_effect_type\':\'close\',\'fight_impact_animation\':\'static_individual_impact\',
    fight_impact_data:{
      images:[\'punch.gif\'],
      sounds:[\'\'],
      times:[100,300]
    }
  ,\'use_impact_animation\':\'static_individual_impact\',
    use_impact_data:{
      images:[\'\'],
      sounds:[\'\'],
      times:[100,300]
    }
  },
  {\'name\':\'Laser Lance\',\'price\':225,\'effect\':0,\'use_targets\':-2,\'base\':0,\'added\':0,\'attribute\':0,\'one_use\':false,
    equip_type:[\'lhand\',\'rhand\'],
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':25,\'Accuracy\':20,\'Strength\':0,\'Dodge\':0,\'Block\':-5,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':0,\'attack_count\':1,\'attack_attribute\':0,\'ammo_type\':\'\',\'description\':\'A typical lance with a laser as the blade.\',\'menu_pic\':\'sword.png\',\'fight_effect_type\':\'close\',\'fight_impact_animation\':\'static_impact\',
    fight_impact_data:{
      images:[\'slash.gif\'],
      sounds:[\'blobs_slash.mp3\'],
      times:[100,300]
    }
  ,\'use_impact_animation\':\'static_impact\',
    use_impact_data:{
      images:[\'\'],
      sounds:[\'\'],
      times:[\'\',\'\']
    }
  },
  {\'name\':\'Spark Rod\',\'price\':125,\'effect\':0,\'use_targets\':-2,\'base\':0,\'added\':0,\'attribute\':0,\'one_use\':false,
    equip_type:[\'hand\'],
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':10,\'Accuracy\':13,\'Strength\':0,\'Dodge\':0,\'Block\':-3,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':0,\'attack_count\':1,\'attack_attribute\':0,\'ammo_type\':\'\',\'description\':\'A rod that electrocutes whatever it hits.  A magician\\\'s cattle prod.\',\'menu_pic\':\'sword.png\',\'fight_effect_type\':\'close\',\'fight_impact_animation\':\'static_individual_impact\',
    fight_impact_data:{
      images:[\'explosion.gif\'],
      sounds:[\'blobs_punch.mp3\'],
      times:[0,500]
    }
  ,\'use_impact_animation\':\'static_impact\',
    use_impact_data:{
      images:[\'\'],
      sounds:[\'\'],
      times:[\'\',\'\']
    }
  },
  {\'name\':\'Wraith Touch\',\'price\':2000,\'effect\':0,\'use_targets\':-2,\'base\':0,\'added\':0,\'attribute\':0,\'one_use\':false,
    equip_type:[\'hand\'],
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':4,\'Accuracy\':5,\'Strength\':0,\'Dodge\':0,\'Block\':25,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':1,\'Accuracy\':2,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':0,\'attack_count\':1,\'attack_attribute\':0,\'ammo_type\':\'\',\'description\':\'The touch of a banchee that causes whither and decay.\',\'menu_pic\':\'sword.png\',\'fight_effect_type\':\'shoot\',\'fight_impact_animation\':\'static_impact\',
    fight_impact_data:{
      images:[\'slash.gif\'],
      sounds:[\'Sword3.mp3\'],
      times:[100,300]
    }
  ,\'use_impact_animation\':\'static_impact\',
    use_impact_data:{
      images:[\'\'],
      sounds:[\'\'],
      times:[\'\',\'\']
    }
  },
  {\'name\':\'M16A2 Rifle\',\'price\':6200,\'effect\':0,\'use_targets\':-2,\'base\':0,\'added\':0,\'attribute\':0,\'one_use\':false,
    equip_type:[\'lhand\',\'rhand\'],
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':50,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':20,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':-100,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':0,\'attack_count\':3,\'attack_attribute\':0,\'ammo_type\':\'5.62mm Round\',\'description\':\'The standard issue weapon for the USMC. Uses 5.62mm rounds as ammunition.\',\'menu_pic\':\'m16rifle.png\',\'fight_effect_type\':\'throw\',\'fight_impact_animation\':\'ranged_arc_impact\',
    fight_impact_data:{
      images:[\'blood.png\',\'\',\'\'],
      sounds:[\'Blow2.mp3\',\'\'],
      times:[25,100,0,30,1]
    }
  ,\'use_impact_animation\':\'static_impact\',
    use_impact_data:{
      images:[\'\'],
      sounds:[\'\'],
      times:[\'\',\'\']
    }
  },
  {\'name\':\'Small Bomb\',\'price\':25,\'effect\':0,\'use_targets\':-2,\'base\':0,\'added\':0,\'attribute\':0,\'one_use\':false,
    equip_type:[\'hand\',\'ammo\'],
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':15,\'Accuracy\':150,\'Strength\':0,\'Dodge\':0,\'Block\':-20,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':-100,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':1,\'attack_count\':1,\'attack_attribute\':0,\'ammo_type\':\'\',\'description\':\'A small bomb made from household materials.\',\'menu_pic\':\'bigbomb.png\',\'fight_effect_type\':\'close\',\'fight_impact_animation\':\'ranged_arc_impact\',
    fight_impact_data:{
      images:[\'bomb.gif\',\'explosion.gif\',\'explosion.gif\'],
      sounds:[\'Flame4.mp3\',\'fireball.mp3\'],
      times:[15,100,0.1,30,70]
    }
  ,\'use_impact_animation\':\'ranged_arc_impact\',
    use_impact_data:{
      images:[\'bomb.gif\',\'explosion.gif\',\'explosion.gif\'],
      sounds:[\'Flame4.mp3\',\'fireball.mp3\'],
      times:[15,100,0.1,15,70]
    }
  },
  {\'name\':\'5.62mm Ball Round\',\'price\':3,\'effect\':0,\'use_targets\':-2,\'base\':0,\'added\':0,\'attribute\':0,\'one_use\':false,
    equip_type:[\'ammo\'],
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':300,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':0,\'attack_count\':1,\'attack_attribute\':0,\'ammo_type\':\'5.62mm Round\',\'description\':\'5.62mm standard anti-infantry rounds.\',\'menu_pic\':\'556round.png\',\'fight_effect_type\':\'close\',\'fight_impact_animation\':\'ranged_arc_impact\',
    fight_impact_data:{
      images:[\'blood.png\',\'\',\'\'],
      sounds:[\'\',\'\'],
      times:[1,100,0,30,1]
    }
  ,\'use_impact_animation\':\'ranged_arc_impact\',
    use_impact_data:{
      images:[\'\',\'\',\'\'],
      sounds:[\'\',\'\'],
      times:[\'\',\'\',\'\',\'\',\'\']
    }
  },
  {\'name\':\'Medical Kit\',\'price\':15,\'effect\':1,\'use_targets\':0,\'base\':50,\'added\':20,\'attribute\':0,\'one_use\':true,\'equip_type\':null,
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':0,\'attack_count\':0,\'attack_attribute\':0,\'ammo_type\':\'\',\'description\':\'A first aid kit capable of restoring about 60 HP.\',\'menu_pic\':\'medkit.png\',\'fight_effect_type\':\'close\',\'fight_impact_animation\':\'static_impact\',
    fight_impact_data:{
      images:[\'\'],
      sounds:[\'\'],
      times:[\'\',\'\']
    }
  ,\'use_impact_animation\':\'static_impact\',
    use_impact_data:{
      images:[\'heal.gif\'],
      sounds:[\'Item2.mp3\'],
      times:[100,300]
    }
  },
  {\'name\':\'9mm Baretta\',\'price\':3500,\'effect\':0,\'use_targets\':-2,\'base\':0,\'added\':0,\'attribute\':0,\'one_use\':false,
    equip_type:[\'hand\'],
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':20,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':40,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':-100,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':0,\'attack_count\':4,\'attack_attribute\':0,\'ammo_type\':\'9mm Round\',\'description\':\'Standard issue pistol for the USMC.  Light and easy to use.\',\'menu_pic\':\'fire.png\',\'fight_effect_type\':\'shoot\',\'fight_impact_animation\':\'ranged_arc_impact\',
    fight_impact_data:{
      images:[\'blood.png\',\'\',\'\'],
      sounds:[\'Blow3.mp3\',\'\'],
      times:[45,100,0,30,1]
    }
  ,\'use_impact_animation\':\'ranged_arc_impact\',
    use_impact_data:{
      images:[\'\',\'\',\'\'],
      sounds:[\'\',\'\'],
      times:[\'\',\'\',\'\',\'\',\'\']
    }
  },
  {\'name\':\'9mm Bullet\',\'price\':2,\'effect\':0,\'use_targets\':-2,\'base\':0,\'added\':0,\'attribute\':0,\'one_use\':false,
    equip_type:[\'ammo\'],
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':200,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':0,\'attack_count\':1,\'attack_attribute\':0,\'ammo_type\':\'9mm Round\',\'description\':\'Normal 9mm bullet.\',\'menu_pic\':\'bignone.png\',\'fight_effect_type\':\'close\',\'fight_impact_animation\':\'ranged_arc_impact\',
    fight_impact_data:{
      images:[\'\',\'\',\'\'],
      sounds:[\'\',\'\',\'\'],
      times:[1,100,0,30,1]
    }
  ,\'use_impact_animation\':\'ranged_arc_impact\',
    use_impact_data:{
      images:[\'\'],
      sounds:[\'\'],
      times:[\'\']
    }
  },
  {\'name\':\'Kabar Knife\',\'price\':525,\'effect\':0,\'use_targets\':-2,\'base\':0,\'added\':0,\'attribute\':0,\'one_use\':false,
    equip_type:[\'hand\'],
    statinc:{\'HP\':0,\'MP\':0,\'Speed\':15,\'Accuracy\':35,\'Strength\':0,\'Dodge\':0,\'Block\':20,\'Power\':0,\'Resistance\':0,\'Focus\':0},
    statpercinc:{\'HP\':0,\'MP\':0,\'Speed\':0,\'Accuracy\':0,\'Strength\':0,\'Dodge\':0,\'Block\':0,\'Power\':0,\'Resistance\':0,\'Focus\':0}
  ,\'targets\':0,\'attack_count\':1,\'attack_attribute\':0,\'ammo_type\':\'\',\'description\':\'A sharp, trusty hunter\\\'s knife used to gut monsters.\',\'menu_pic\':\'fight.png\',\'fight_effect_type\':\'close\',\'fight_impact_animation\':\'ranged_arc_impact\',
    fight_impact_data:{
      images:[\'\',\'\',\'\'],
      sounds:[\'\',\'\',\'\'],
      times:[1,100,0,30,1]
    }
  ,\'use_impact_animation\':\'ranged_arc_impact\',
    use_impact_data:{
      images:[\'\'],
      sounds:[\'\'],
      times:[\'\']
    }
  }
]'; ?>