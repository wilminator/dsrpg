<?php require_once INCLUDE_DIR."ability.php"; $GLOBALS["abilities"]=unserialize(<<<EOD
a:10:{i:1;O:7:"ABILITY":13:{s:4:"name";s:18:"Electric Shockwave";s:6:"effect";s:1:"2";s:4:"type";s:1:"0";s:7:"mp_used";s:2:"17";s:7:"targets";s:2:"-1";s:4:"base";s:2:"70";s:5:"added";s:2:"55";s:9:"attribute";s:1:"5";s:11:"description";s:81:"Causes an arc of electricity to run trough one group of enemies for about 100 HP.";s:8:"menu_pic";s:13:"shockwave.png";s:17:"skill_effect_type";s:4:"none";s:16:"impact_animation";s:13:"static_impact";s:11:"impact_data";a:3:{s:6:"images";a:1:{i:0;s:20:"lightening_storm.gif";}s:6:"sounds";a:1:{i:0;s:16:"electrocute3.mp3";}s:5:"times";a:2:{i:0;s:4:"1500";i:1;s:4:"2500";}}}i:2;O:7:"ABILITY":13:{s:4:"name";s:12:"Endless Wail";s:6:"effect";s:1:"2";s:4:"type";s:1:"1";s:7:"mp_used";s:2:"37";s:7:"targets";s:2:"-2";s:4:"base";s:2:"60";s:5:"added";s:2:"30";s:9:"attribute";s:1:"0";s:11:"description";s:72:"This agonizing wail torments all enemies, causing about 75 HP of damage.";s:8:"menu_pic";s:8:"wail.png";s:17:"skill_effect_type";s:4:"none";s:16:"impact_animation";s:13:"static_impact";s:11:"impact_data";a:3:{s:6:"images";a:1:{i:0;s:0:"";}s:6:"sounds";a:1:{i:0;s:7:"Cow.mp3";}s:5:"times";a:2:{i:0;s:3:"500";i:1;s:4:"1000";}}}i:3;O:7:"ABILITY":13:{s:4:"name";s:4:"Heal";s:6:"effect";s:1:"1";s:4:"type";s:1:"0";s:7:"mp_used";s:1:"5";s:7:"targets";s:1:"0";s:4:"base";s:2:"25";s:5:"added";s:2:"15";s:9:"attribute";s:1:"0";s:11:"description";s:31:"Heals one ally for about 30 HP.";s:8:"menu_pic";s:8:"heal.png";s:17:"skill_effect_type";s:4:"none";s:16:"impact_animation";s:24:"static_individual_impact";s:11:"impact_data";a:3:{s:6:"images";a:1:{i:0;s:8:"heal.gif";}s:6:"sounds";a:1:{i:0;s:9:"Holy2.mp3";}s:5:"times";a:2:{i:0;s:3:"700";i:1;s:3:"900";}}}i:4;O:7:"ABILITY":13:{s:4:"name";s:8:"Healsome";s:6:"effect";s:1:"1";s:4:"type";s:1:"0";s:7:"mp_used";s:2:"12";s:7:"targets";s:1:"0";s:4:"base";s:2:"80";s:5:"added";s:2:"60";s:9:"attribute";s:1:"0";s:11:"description";s:32:"Heals one ally for about 110 HP.";s:8:"menu_pic";s:12:"healsome.png";s:17:"skill_effect_type";s:4:"none";s:16:"impact_animation";s:24:"static_individual_impact";s:11:"impact_data";a:3:{s:6:"images";a:1:{i:0;s:8:"heal.gif";}s:6:"sounds";a:1:{i:0;s:9:"Holy2.mp3";}s:5:"times";a:2:{i:0;s:3:"700";i:1;s:3:"900";}}}i:5;O:7:"ABILITY":13:{s:4:"name";s:4:"Life";s:6:"effect";s:1:"1";s:4:"type";s:1:"0";s:7:"mp_used";s:2:"14";s:7:"targets";s:2:"-1";s:4:"base";s:2:"20";s:5:"added";s:2:"15";s:9:"attribute";s:1:"0";s:11:"description";s:32:"Heals one group for about 25 HP.";s:8:"menu_pic";s:8:"life.png";s:17:"skill_effect_type";s:4:"none";s:16:"impact_animation";s:24:"static_individual_impact";s:11:"impact_data";a:3:{s:6:"images";a:1:{i:0;s:8:"heal.gif";}s:6:"sounds";a:1:{i:0;s:9:"Holy8.mp3";}s:5:"times";a:2:{i:0;s:3:"700";i:1;s:3:"900";}}}i:6;O:7:"ABILITY":13:{s:4:"name";s:8:"Fireball";s:6:"effect";s:1:"2";s:4:"type";s:1:"0";s:7:"mp_used";s:1:"6";s:7:"targets";s:1:"0";s:4:"base";s:2:"30";s:5:"added";s:2:"20";s:9:"attribute";s:1:"1";s:11:"description";s:68:"Throws a small ball of fire causing about 40 HP damage to one enemy.";s:8:"menu_pic";s:12:"fireball.png";s:17:"skill_effect_type";s:4:"none";s:16:"impact_animation";s:17:"ranged_arc_impact";s:11:"impact_data";a:3:{s:6:"images";a:3:{i:0;s:12:"fireball.png";i:1;s:13:"explosion.gif";i:2;s:18:"small_fireball.png";}s:6:"sounds";a:2:{i:0;s:10:"Flame4.mp3";i:1;s:10:"Flame1.mp3";}s:5:"times";a:5:{i:0;s:2:"15";i:1;s:4:"1000";i:2;s:4:"0.25";i:3;s:2:"30";i:4;s:2:"50";}}}i:7;O:7:"ABILITY":13:{s:4:"name";s:8:"Firebomb";s:6:"effect";s:1:"2";s:4:"type";s:1:"0";s:7:"mp_used";s:2:"10";s:7:"targets";s:1:"1";s:4:"base";s:2:"30";s:5:"added";s:2:"15";s:9:"attribute";s:1:"1";s:11:"description";s:78:"A single ball of fire explodes causing about 35 HP damage up to three enemies.";s:8:"menu_pic";s:12:"firebomb.png";s:17:"skill_effect_type";s:4:"none";s:16:"impact_animation";s:17:"ranged_arc_impact";s:11:"impact_data";a:3:{s:6:"images";a:3:{i:0;s:12:"fireball.png";i:1;s:13:"explosion.gif";i:2;s:18:"small_fireball.png";}s:6:"sounds";a:2:{i:0;s:10:"Flame4.mp3";i:1;s:10:"Flame1.mp3";}s:5:"times";a:5:{i:0;s:2:"15";i:1;s:4:"1000";i:2;s:4:"0.25";i:3;s:2:"30";i:4;s:2:"70";}}}i:8;O:7:"ABILITY":13:{s:4:"name";s:9:"Fire Nuke";s:6:"effect";s:1:"2";s:4:"type";s:1:"0";s:7:"mp_used";s:2:"32";s:7:"targets";s:2:"-2";s:4:"base";s:2:"80";s:5:"added";s:2:"40";s:9:"attribute";s:1:"1";s:11:"description";s:116:"A huge fireball explodes in the center of the target party, causing about 100 HP damage to each person in the party.";s:8:"menu_pic";s:12:"firenuke.png";s:17:"skill_effect_type";s:4:"none";s:16:"impact_animation";s:18:"slide_right_impact";s:11:"impact_data";a:3:{s:6:"images";a:1:{i:0;s:17:"huge_fireball.png";}s:6:"sounds";a:1:{i:0;s:10:"Flame7.mp3";}s:5:"times";a:2:{i:0;s:4:"2000";i:1;s:2:"25";}}}i:9;O:7:"ABILITY":13:{s:4:"name";s:9:"Fireballs";s:6:"effect";s:1:"2";s:4:"type";s:1:"0";s:7:"mp_used";s:2:"20";s:7:"targets";s:2:"-1";s:4:"base";s:2:"45";s:5:"added";s:2:"30";s:9:"attribute";s:1:"1";s:11:"description";s:93:"Many fireballs leap from the caster's fingers causing about 60 HP damage to the target group.";s:8:"menu_pic";s:12:"fireball.png";s:17:"skill_effect_type";s:4:"none";s:16:"impact_animation";s:12:"multi_impact";s:11:"impact_data";a:3:{s:6:"images";a:3:{i:0;s:18:"small_fireball.png";i:1;s:13:"explosion.gif";i:2;s:0:"";}s:6:"sounds";a:2:{i:0;s:10:"Flame4.mp3";i:1;s:10:"Flame1.mp3";}s:5:"times";a:4:{i:0;s:2:"15";i:1;s:3:"500";i:2;s:2:"50";i:3;s:0:"";}}}i:10;O:7:"ABILITY":13:{s:4:"name";s:10:"Hot-dukin'";s:6:"effect";s:1:"2";s:4:"type";s:1:"1";s:7:"mp_used";s:1:"9";s:7:"targets";s:1:"4";s:4:"base";s:2:"30";s:5:"added";s:2:"50";s:9:"attribute";s:1:"1";s:11:"description";s:109:"Street fighters eat your hearts out! Many fireballs pummel a target plus four on either side for about 55 HP.";s:8:"menu_pic";s:12:"fireball.png";s:17:"skill_effect_type";s:5:"throw";s:16:"impact_animation";s:12:"multi_impact";s:11:"impact_data";a:3:{s:6:"images";a:3:{i:0;s:18:"small_fireball.png";i:1;s:13:"explosion.gif";i:2;s:0:"";}s:6:"sounds";a:2:{i:0;s:10:"Flame2.mp3";i:1;s:0:"";}s:5:"times";a:4:{i:0;s:2:"30";i:1;s:4:"1000";i:2;s:2:"70";i:3;s:1:"1";}}}}
EOD
); $GLOBALS["abilities_js"]='[null,
  {\'name\':\'Electric Shockwave\',\'effect\':2,\'type\':0,\'mp_used\':17,\'targets\':-1,\'base\':70,\'added\':55,\'attribute\':5,\'description\':\'Causes an arc of electricity to run trough one group of enemies for about 100 HP.\',\'menu_pic\':\'shockwave.png\',\'skill_effect_type\':\'none\',\'impact_animation\':\'static_impact\',
    impact_data:{
      images:[\'lightening_storm.gif\'],
      sounds:[\'electrocute3.mp3\'],
      times:[1500,2500]
    }
  },
  {\'name\':\'Endless Wail\',\'effect\':2,\'type\':1,\'mp_used\':37,\'targets\':-2,\'base\':60,\'added\':30,\'attribute\':0,\'description\':\'This agonizing wail torments all enemies, causing about 75 HP of damage.\',\'menu_pic\':\'wail.png\',\'skill_effect_type\':\'none\',\'impact_animation\':\'static_impact\',
    impact_data:{
      images:[\'\'],
      sounds:[\'Cow.mp3\'],
      times:[500,1000]
    }
  },
  {\'name\':\'Heal\',\'effect\':1,\'type\':0,\'mp_used\':5,\'targets\':0,\'base\':25,\'added\':15,\'attribute\':0,\'description\':\'Heals one ally for about 30 HP.\',\'menu_pic\':\'heal.png\',\'skill_effect_type\':\'none\',\'impact_animation\':\'static_individual_impact\',
    impact_data:{
      images:[\'heal.gif\'],
      sounds:[\'Holy2.mp3\'],
      times:[700,900]
    }
  },
  {\'name\':\'Healsome\',\'effect\':1,\'type\':0,\'mp_used\':12,\'targets\':0,\'base\':80,\'added\':60,\'attribute\':0,\'description\':\'Heals one ally for about 110 HP.\',\'menu_pic\':\'healsome.png\',\'skill_effect_type\':\'none\',\'impact_animation\':\'static_individual_impact\',
    impact_data:{
      images:[\'heal.gif\'],
      sounds:[\'Holy2.mp3\'],
      times:[700,900]
    }
  },
  {\'name\':\'Life\',\'effect\':1,\'type\':0,\'mp_used\':14,\'targets\':-1,\'base\':20,\'added\':15,\'attribute\':0,\'description\':\'Heals one group for about 25 HP.\',\'menu_pic\':\'life.png\',\'skill_effect_type\':\'none\',\'impact_animation\':\'static_individual_impact\',
    impact_data:{
      images:[\'heal.gif\'],
      sounds:[\'Holy8.mp3\'],
      times:[700,900]
    }
  },
  {\'name\':\'Fireball\',\'effect\':2,\'type\':0,\'mp_used\':6,\'targets\':0,\'base\':30,\'added\':20,\'attribute\':1,\'description\':\'Throws a small ball of fire causing about 40 HP damage to one enemy.\',\'menu_pic\':\'fireball.png\',\'skill_effect_type\':\'none\',\'impact_animation\':\'ranged_arc_impact\',
    impact_data:{
      images:[\'fireball.png\',\'explosion.gif\',\'small_fireball.png\'],
      sounds:[\'Flame4.mp3\',\'Flame1.mp3\'],
      times:[15,1000,0.25,30,50]
    }
  },
  {\'name\':\'Firebomb\',\'effect\':2,\'type\':0,\'mp_used\':10,\'targets\':1,\'base\':30,\'added\':15,\'attribute\':1,\'description\':\'A single ball of fire explodes causing about 35 HP damage up to three enemies.\',\'menu_pic\':\'firebomb.png\',\'skill_effect_type\':\'none\',\'impact_animation\':\'ranged_arc_impact\',
    impact_data:{
      images:[\'fireball.png\',\'explosion.gif\',\'small_fireball.png\'],
      sounds:[\'Flame4.mp3\',\'Flame1.mp3\'],
      times:[15,1000,0.25,30,70]
    }
  },
  {\'name\':\'Fire Nuke\',\'effect\':2,\'type\':0,\'mp_used\':32,\'targets\':-2,\'base\':80,\'added\':40,\'attribute\':1,\'description\':\'A huge fireball explodes in the center of the target party, causing about 100 HP damage to each person in the party.\',\'menu_pic\':\'firenuke.png\',\'skill_effect_type\':\'none\',\'impact_animation\':\'slide_right_impact\',
    impact_data:{
      images:[\'huge_fireball.png\'],
      sounds:[\'Flame7.mp3\'],
      times:[2000,25]
    }
  },
  {\'name\':\'Fireballs\',\'effect\':2,\'type\':0,\'mp_used\':20,\'targets\':-1,\'base\':45,\'added\':30,\'attribute\':1,\'description\':\'Many fireballs leap from the caster\\\'s fingers causing about 60 HP damage to the target group.\',\'menu_pic\':\'fireball.png\',\'skill_effect_type\':\'none\',\'impact_animation\':\'multi_impact\',
    impact_data:{
      images:[\'small_fireball.png\',\'explosion.gif\',\'\'],
      sounds:[\'Flame4.mp3\',\'Flame1.mp3\'],
      times:[15,500,50,\'\']
    }
  },
  {\'name\':\'Hot-dukin\\\'\',\'effect\':2,\'type\':1,\'mp_used\':9,\'targets\':4,\'base\':30,\'added\':50,\'attribute\':1,\'description\':\'Street fighters eat your hearts out! Many fireballs pummel a target plus four on either side for about 55 HP.\',\'menu_pic\':\'fireball.png\',\'skill_effect_type\':\'throw\',\'impact_animation\':\'multi_impact\',
    impact_data:{
      images:[\'small_fireball.png\',\'explosion.gif\',\'\'],
      sounds:[\'Flame2.mp3\',\'\'],
      times:[30,1000,70,1]
    }
  }
]'; ?>