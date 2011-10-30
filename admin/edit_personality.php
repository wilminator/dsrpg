<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_PERSONALITY','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

//Quick look- if $_SESSION['animationjs'] does not exist then jump to get_animations.php
if(!array_key_exists('animationjs',$_SESSION) || !$_SESSION['animationjs'])
    {
    header('Location: get_animations.php?return=edit_personality.php&personality='.$_REQUEST['personality']);
    exit;
    }
$animations=$_SESSION['animations'];
$animationjs=$_SESSION['animationjs'];

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'functions.php';
require_once INCLUDE_DIR.'personality_store.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';

if(get_magic_quotes_gpc())
    $_POST=array_map('stripslashes',$_POST);

if(!function_exists('php_data_to_js'))
    {
    require_once INCLUDE_DIR.'js_rip.php';
    }

$personality_store=new PERSONALITY_STORE;

if(isset($_POST['OP'])&&$_POST['OP']=="Update Database")
    {
    //Get the personality id number. 0 is a new personality.
    $personalityindex=$_REQUEST['personality'];

    log_error(var_export($_POST,true));

    //Prep the special variables.
    $base_animation_data=$animations[$_POST['base_animation']];
    $base_image_count=count($base_animation_data['images']);
    $base_sound_count=count($base_animation_data['sounds']);
    $base_time_count=count($base_animation_data['times']);
    for($index=0;$index<$base_image_count;$index++)
        $base_images[]=$_POST["base_image_$index"];
    for($index=0;$index<$base_sound_count;$index++)
        $base_sounds[]=$_POST["base_sound_$index"];
    for($index=0;$index<$base_time_count;$index++)
        $base_times[]=$_POST["base_time_$index"];

    $equip_animation_data=$animations[$_POST['equip_animation']];
    $equip_image_count=count($equip_animation_data['images']);
    $equip_sound_count=count($equip_animation_data['sounds']);
    $equip_time_count=count($equip_animation_data['times']);
    for($index=0;$index<$equip_image_count;$index++)
        $equip_images[]=$_POST["equip_image_$index"];
    for($index=0;$index<$equip_sound_count;$index++)
        $equip_sounds[]=$_POST["equip_sound_$index"];
    for($index=0;$index<$equip_time_count;$index++)
        $equip_times[]=$_POST["equip_time_$index"];

    $flee_animation_data=$animations[$_POST['flee_animation']];
    $flee_image_count=count($flee_animation_data['images']);
    $flee_sound_count=count($flee_animation_data['sounds']);
    $flee_time_count=count($flee_animation_data['times']);
    for($index=0;$index<$flee_image_count;$index++)
        $flee_images[]=$_POST["flee_image_$index"];
    for($index=0;$index<$flee_sound_count;$index++)
        $flee_sounds[]=$_POST["flee_sound_$index"];
    for($index=0;$index<$flee_time_count;$index++)
        $flee_times[]=$_POST["flee_time_$index"];

    $hit_animation_data=$animations[$_POST['hit_animation']];
    $hit_image_count=count($hit_animation_data['images']);
    $hit_sound_count=count($hit_animation_data['sounds']);
    $hit_time_count=count($hit_animation_data['times']);
    for($index=0;$index<$hit_image_count;$index++)
        $hit_images[]=$_POST["hit_image_$index"];
    for($index=0;$index<$hit_sound_count;$index++)
        $hit_sounds[]=$_POST["hit_sound_$index"];
    for($index=0;$index<$hit_time_count;$index++)
        $hit_times[]=$_POST["hit_time_$index"];

    $die_animation_data=$animations[$_POST['die_animation']];
    $die_image_count=count($die_animation_data['images']);
    $die_sound_count=count($die_animation_data['sounds']);
    $die_time_count=count($die_animation_data['times']);
    for($index=0;$index<$die_image_count;$index++)
        $die_images[]=$_POST["die_image_$index"];
    for($index=0;$index<$die_sound_count;$index++)
        $die_sounds[]=$_POST["die_sound_$index"];
    for($index=0;$index<$die_time_count;$index++)
        $die_times[]=$_POST["die_time_$index"];

    $attack_close_animation_data=$animations[$_POST['attack_close_animation']];
    $attack_close_image_count=count($attack_close_animation_data['images']);
    $attack_close_sound_count=count($attack_close_animation_data['sounds']);
    $attack_close_time_count=count($attack_close_animation_data['times']);
    for($index=0;$index<$attack_close_image_count;$index++)
        $attack_close_images[]=$_POST["attack_close_image_$index"];
    for($index=0;$index<$attack_close_sound_count;$index++)
        $attack_close_sounds[]=$_POST["attack_close_sound_$index"];
    for($index=0;$index<$attack_close_time_count;$index++)
        $attack_close_times[]=$_POST["attack_close_time_$index"];

    $attack_throw_animation_data=$animations[$_POST['attack_throw_animation']];
    $attack_throw_image_count=count($attack_throw_animation_data['images']);
    $attack_throw_sound_count=count($attack_throw_animation_data['sounds']);
    $attack_throw_time_count=count($attack_throw_animation_data['times']);
    for($index=0;$index<$attack_throw_image_count;$index++)
        $attack_throw_images[]=$_POST["attack_throw_image_$index"];
    for($index=0;$index<$attack_throw_sound_count;$index++)
        $attack_throw_sounds[]=$_POST["attack_throw_sound_$index"];
    for($index=0;$index<$attack_throw_time_count;$index++)
        $attack_throw_times[]=$_POST["attack_throw_time_$index"];

    $attack_shoot_animation_data=$animations[$_POST['attack_shoot_animation']];
    $attack_shoot_image_count=count($attack_shoot_animation_data['images']);
    $attack_shoot_sound_count=count($attack_shoot_animation_data['sounds']);
    $attack_shoot_time_count=count($attack_shoot_animation_data['times']);
    for($index=0;$index<$attack_shoot_image_count;$index++)
        $attack_shoot_images[]=$_POST["attack_shoot_image_$index"];
    for($index=0;$index<$attack_shoot_sound_count;$index++)
        $attack_shoot_sounds[]=$_POST["attack_shoot_sound_$index"];
    for($index=0;$index<$attack_shoot_time_count;$index++)
        $attack_shoot_times[]=$_POST["attack_shoot_time_$index"];

    $skill_animation_data=$animations[$_POST['skill_animation']];
    $skill_image_count=count($skill_animation_data['images']);
    $skill_sound_count=count($skill_animation_data['sounds']);
    $skill_time_count=count($skill_animation_data['times']);
    for($index=0;$index<$skill_image_count;$index++)
        $skill_images[]=$_POST["skill_image_$index"];
    for($index=0;$index<$skill_sound_count;$index++)
        $skill_sounds[]=$_POST["skill_sound_$index"];
    for($index=0;$index<$skill_time_count;$index++)
        $skill_times[]=$_POST["skill_time_$index"];

    $spell_animation_data=$animations[$_POST['spell_animation']];
    $spell_image_count=count($spell_animation_data['images']);
    $spell_sound_count=count($spell_animation_data['sounds']);
    $spell_time_count=count($spell_animation_data['times']);
    for($index=0;$index<$spell_image_count;$index++)
        $spell_images[]=$_POST["spell_image_$index"];
    for($index=0;$index<$spell_sound_count;$index++)
        $spell_sounds[]=$_POST["spell_sound_$index"];
    for($index=0;$index<$spell_time_count;$index++)
        $spell_times[]=$_POST["spell_time_$index"];

    $item_animation_data=$animations[$_POST['item_animation']];
    $item_image_count=count($item_animation_data['images']);
    $item_sound_count=count($item_animation_data['sounds']);
    $item_time_count=count($item_animation_data['times']);
    for($index=0;$index<$item_image_count;$index++)
        $item_images[]=$_POST["item_image_$index"];
    for($index=0;$index<$item_sound_count;$index++)
        $item_sounds[]=$_POST["item_sound_$index"];
    for($index=0;$index<$item_time_count;$index++)
        $item_times[]=$_POST["item_time_$index"];

    //Make a new PERSONALITY
    $this_personality=new PERSONALITY(
            $_POST['name'],
            $_POST['base_animation'],array('images'=>$base_images,'sounds'=>$base_sounds,'times'=>$base_times),
            $_POST['equip_animation'],array('images'=>$equip_images,'sounds'=>$equip_sounds,'times'=>$equip_times),
            $_POST['flee_animation'],array('images'=>$flee_images,'sounds'=>$flee_sounds,'times'=>$flee_times),
            $_POST['hit_animation'],array('images'=>$hit_images,'sounds'=>$hit_sounds,'times'=>$hit_times),
            $_POST['die_animation'],array('images'=>$die_images,'sounds'=>$die_sounds,'times'=>$die_times),
            $_POST['attack_close_animation'],array('images'=>$attack_close_images,'sounds'=>$attack_close_sounds,'times'=>$attack_close_times),
            $_POST['attack_throw_animation'],array('images'=>$attack_throw_images,'sounds'=>$attack_throw_sounds,'times'=>$attack_throw_times),
            $_POST['attack_shoot_animation'],array('images'=>$attack_shoot_images,'sounds'=>$attack_shoot_sounds,'times'=>$attack_shoot_times),
            $_POST['skill_animation'],array('images'=>$skill_images,'sounds'=>$skill_sounds,'times'=>$skill_times),
            $_POST['spell_animation'],array('images'=>$spell_images,'sounds'=>$spell_sounds,'times'=>$spell_times),
            $_POST['item_animation'],array('images'=>$item_images,'sounds'=>$item_sounds,'times'=>$item_times));

    //update that personality in the databse
    $personalityindex=$personality_store->set_personality($this_personality,$personalityindex);
    }
else
    $personalityindex=$_REQUEST['personality'];

$personalities=&$personality_store->get_all_personalities();
if($personalityindex>0)
    $personality=&$personalities[$personalityindex];
else
    $personality=new PERSONALITY(
            'New Personality',
            'base_animation',array('images'=>array(''),'sounds'=>array(''),'times'=>array(0)),
            'pose_animation',array('images'=>array(''),'sounds'=>array(''),'times'=>array(500,500)),
            'flee_animation',array('images'=>array(''),'sounds'=>array(''),'times'=>array(0)),
            'pose_animation',array('images'=>array(''),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
            'die_animation',array('images'=>array(''),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
            'attack_close_animation',array('images'=>array('','',''),'sounds'=>array('','',''),'times'=>array(25,500,500)),
            'pose_animation',array('images'=>array(''),'sounds'=>array(''),'times'=>array(500,500)),
            'pose_animation',array('images'=>array(''),'sounds'=>array(''),'times'=>array(500,500)),
            'pose_animation',array('images'=>array(''),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
            'pose_animation',array('images'=>array(''),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
            'pose_animation',array('images'=>array(''),'sounds'=>array(''),'times'=>array(500,500)));

//Setup HTML display variables.

//Default input box size
$def_size=array('size'=>7);
//Default input box size
$def_textarea=array('rows'=>3,'cols'=>50);
//Make list of animations
foreach(array_keys($animations) as $value)
    $animation_list[$value]=$value;

//ammo types

$var_header_js=php_data_to_js($effect_var_names);
$personality_js=php_data_to_js($personality);
?>
<html>
<head>
<script type="text/javascript" src="../javascript/soundmanager2-nodebug-jsmin.js"></script>
<script>
var personality=<?php echo $personality_js; ?>;
var headers=<?php echo $var_header_js; ?>;
var animations=<?php echo $animationjs; ?>;
var sound_dir='../<?php echo SOUND_DIR; ?>';
var image_dir='../<?php echo IMAGES_DIR; ?>';
var swfdir='../<?php echo SWF_DIR; ?>';

function fix_all(
    base_animation,base_defaults,
    equip_animation,equip_defaults,
    flee_animation,flee_defaults,
    hit_animation,hit_defaults,
    die_animation,die_defaults,
    attack_close_animation,attack_close_defaults,
    attack_throw_animation,attack_throw_defaults,
    attack_shoot_animation,attack_shoot_defaults,
    skill_animation,skill_defaults,
    spell_animation,spell_defaults,
    item_animation,item_defaults)
    {
    fix_animation_options('base_',base_animation,base_defaults);
    fix_animation_options('equip_',equip_animation,equip_defaults);
    fix_animation_options('flee_',flee_animation,flee_defaults);
    fix_animation_options('hit_',hit_animation,hit_defaults);
    fix_animation_options('die_',die_animation,die_defaults);
    fix_animation_options('attack_close_',attack_close_animation,attack_close_defaults);
    fix_animation_options('attack_throw_',attack_throw_animation,attack_throw_defaults);
    fix_animation_options('attack_shoot_',attack_shoot_animation,attack_shoot_defaults);
    fix_animation_options('skill_',skill_animation,skill_defaults);
    fix_animation_options('spell_',spell_animation,spell_defaults);
    fix_animation_options('item_',item_animation,item_defaults);

    init_audio(function (){alert('No Audio.')});
    }

window.SM2_DEFER = true;

</script>
<script type="text/javascript" src="anim_prep.js"></script>
</head>
<body onload="return fix_all(
    personality.base_animation,personality.base_data,
    personality.equip_animation,personality.equip_data,
    personality.flee_animation,personality.flee_data,
    personality.hit_animation,personality.hit_data,
    personality.die_animation,personality.die_data,
    personality.attack_close_animation,personality.attack_close_data,
    personality.attack_throw_animation,personality.attack_throw_data,
    personality.attack_shoot_animation,personality.attack_shoot_data,
    personality.skill_animation,personality.skill_data,
    personality.spell_animation,personality.spell_data,
    personality.item_animation,personality.item_data
    );">
<form method="post" action="edit_personality.php" name="formdata">
<table>
  <tr><th>
    <table>
      <tr>
        <?php
        $tindex=$personalityindex;
        do  {
           	$tindex--;
            } while($tindex>0 && !array_key_exists($tindex,$personalities));
        if ($tindex>0)
            echo "<td><a href=\"edit_personality.php?personality=$tindex\">Previous personality</a></td>"
        ?>
        <td><a href="personalities.php">Return to personality list</a></td>
        <?php
        echo "<td><a href=\"delete_personality.php?personality=$personalityindex\">Delete this personality</a></td>"
        ?>
        <td><a href="edit_personality.php?personality=0">New personality</a></td>
        <?php
        $tindex=$personalityindex;
        $keys=array_keys($personalities);
        sort($keys);
        end($keys);
        $last=end($keys);
        do  {
           	$tindex++;
            } while($tindex<=$last && !array_key_exists($tindex,$personalities));
        if ($tindex<=$last)
            echo "<td><a href=\"edit_personality.php?personality=$tindex\">Next personality</a></td>"
        ?>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Item Name</th>
        <td>
          <?php make_input('name',$personality->name); ?>
          <?php make_input('personality',$personalityindex,array('type'=>'hidden')); ?>
        </td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Base animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('base_animation',$personality->base_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'base_\',this.value);')); ?></td>
        <td><table id="base_images"></table></td>
        <td><table id="base_sounds"></table></td>
        <td><table id="base_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Equip animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('equip_animation',$personality->equip_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'equip_\',this.value);')); ?></td>
        <td><table id="equip_images"></table></td>
        <td><table id="equip_sounds"></table></td>
        <td><table id="equip_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Flee animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('flee_animation',$personality->flee_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'flee_\',this.value);')); ?></td>
        <td><table id="flee_images"></table></td>
        <td><table id="flee_sounds"></table></td>
        <td><table id="flee_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Hit animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('hit_animation',$personality->hit_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'hit_\',this.value);')); ?></td>
        <td><table id="hit_images"></table></td>
        <td><table id="hit_sounds"></table></td>
        <td><table id="hit_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Die animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('die_animation',$personality->die_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'die_\',this.value);')); ?></td>
        <td><table id="die_images"></table></td>
        <td><table id="die_sounds"></table></td>
        <td><table id="die_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Close-up attack animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('attack_close_animation',$personality->attack_close_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'attack_close_\',this.value);')); ?></td>
        <td><table id="attack_close_images"></table></td>
        <td><table id="attack_close_sounds"></table></td>
        <td><table id="attack_close_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Throwing attack animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('attack_throw_animation',$personality->attack_throw_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'attack_throw_\',this.value);')); ?></td>
        <td><table id="attack_throw_images"></table></td>
        <td><table id="attack_throw_sounds"></table></td>
        <td><table id="attack_throw_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Shooting attack animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('attack_shoot_animation',$personality->attack_shoot_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'attack_shoot_\',this.value);')); ?></td>
        <td><table id="attack_shoot_images"></table></td>
        <td><table id="attack_shoot_sounds"></table></td>
        <td><table id="attack_shoot_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Skill animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('skill_animation',$personality->skill_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'skill_\',this.value);')); ?></td>
        <td><table id="skill_images"></table></td>
        <td><table id="skill_sounds"></table></td>
        <td><table id="skill_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Spell animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('spell_animation',$personality->spell_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'spell_\',this.value);')); ?></td>
        <td><table id="spell_images"></table></td>
        <td><table id="spell_sounds"></table></td>
        <td><table id="spell_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Item animation data</th></tr>
      <tr>
        <th>Animation</th>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><?php make_select('item_animation',$personality->item_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'item_\',this.value);')); ?></td>
        <td><table id="item_images"></table></td>
        <td><table id="item_sounds"></table></td>
        <td><table id="item_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <td><input type="submit" name="OP" value="Update Database"></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <?php
        $tindex=$personalityindex;
        do  {
           	$tindex--;
            } while($tindex>0 && !array_key_exists($tindex,$personalities));
        if ($tindex>0)
            echo "<td><a href=\"edit_personality.php?personality=$tindex\">Previous personality</a></td>"
        ?>
        <td><a href="personalities.php">Return to personality list</a></td>
        <?php
        echo "<td><a href=\"delete_personality.php?personality=$personalityindex\">Delete this personality</a></td>"
        ?>
        <td><a href="edit_personality.php?personality=0">New personality</a></td>
        <?php
        $tindex=$personalityindex;
        $keys=array_keys($personalities);
        sort($keys);
        end($keys);
        $last=end($keys);
        do  {
           	$tindex++;
            } while($tindex<=$last && !array_key_exists($tindex,$personalities));
        if ($tindex<=$last)
            echo "<td><a href=\"edit_personality.php?personality=$tindex\">Next personality</a></td>"
        ?>
      </tr>
    </table>
  </th></tr>
</table>
</form>
<!--<applet name="Jukebox" id="Jukebox" code="Jukebox.class"  archive="../java/Jukebox.jar" width="700" height="20"></applet>-->
</body>
</html>
