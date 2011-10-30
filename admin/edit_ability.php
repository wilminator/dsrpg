<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_ABILITY','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

//Quick look- if $_SESSION['animationjs'] does not exist then jump to get_animations.php
if(!array_key_exists('animationjs',$_SESSION) || !$_SESSION['animationjs'])
    {
    header('Location: get_animations.php?return=edit_ability.php&ability='.$_REQUEST['ability']);
    exit;
    }
$animations=$_SESSION['animations'];
$animationjs=$_SESSION['animationjs'];

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'functions.php';
require_once INCLUDE_DIR.'ability_store.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';
require_once INCLUDE_DIR.'js_rip.php';

if(get_magic_quotes_gpc())
    $_POST=array_map('stripslashes',$_POST);

$ability_store=new ABILITY_STORE;

if(isset($_POST['OP'])&&$_POST['OP']=="Update Database")
    {
    //Get the ability id number. 0 is a new ability.
    $abilityindex=$_REQUEST['ability'];

    //Fix description
    $description=stripslashes($_POST['description']);

    //Prep the special variables.
    $impact_animation_data=$animations[$_POST['impact_animation']];
    $impact_image_count=count($impact_animation_data['images']);
    $impact_sound_count=count($impact_animation_data['sounds']);
    $impact_time_count=count($impact_animation_data['times']);
    for($index=0;$index<$impact_image_count;$index++)
        $impact_images[]=$_POST["impact_image_$index"];
    for($index=0;$index<$impact_sound_count;$index++)
        $impact_sounds[]=$_POST["impact_sound_$index"];
    for($index=0;$index<$impact_time_count;$index++)
        $impact_times[]=$_POST["impact_time_$index"];
    $impact_data=array('images'=>$impact_images,'sounds'=>$impact_sounds,'times'=>$impact_times);

    //Make a new ability
    $this_ability=new ABILITY(
        $_POST['name'],$_POST['type'],$_POST['effect'],$_POST['base'],$_POST['added'],$_POST['attribute'],$_POST['mp_used'],$_POST['targets'],
        $description,$_POST['menu_pic'],$_POST['skill_effect_type'],$_POST['impact_animation'],$impact_data);

    //update that ability in the databse
    $abilityindex=$ability_store->set_ability($this_ability,$abilityindex);
    }
else
    $abilityindex=$_REQUEST['ability'];

$abilities=&$ability_store->get_all_abilities();
if($abilityindex>0)
    $ability=&$abilities[$abilityindex];
else
    $ability=new ABILITY('New Ability',0,0,0,0,0,0,-2,'','none.png','none','ranged_arc_impact',array('images'=>array('','',''),'sounds'=>array('','',''),'times'=>array(1,100,0.0,30,1)));

//Setup HTML display variables.
//Default input box size
$def_size=array('size'=>7);
//Default input box size
$def_textarea=array('rows'=>3,'cols'=>50);
//Make list of effect animation types
$effect_types=array('none','close','throw','shoot');
//Make list of animations
foreach(array_keys($animations) as $value)
    $animation_list[$value]=$value;
foreach($effect_types as $value)
    $effect_types_list[$value]=$value;

$image='../'.ABILITY_IMAGES_DIR.$ability->menu_pic;

$var_header_js=php_data_to_js($effect_var_names);
$ability_js=php_data_to_js($ability);
?>
<html>
<head>
<script>
window.SM2_DEFER = true;

var ability=<?php echo $ability_js; ?>;
var headers=<?php echo $var_header_js; ?>;
var animations=<?php echo $animationjs; ?>;
var sound_dir='../<?php echo SOUND_DIR; ?>';
var image_dir='../<?php echo IMAGES_DIR; ?>';
var swfdir='../<?php echo SWF_DIR; ?>';


function fix_all(value,animation,defaults)
    {
    fix_variable_headers(value);


    init_audio(function (){alert('No Audio.')});

    return fix_animation_options('impact_',animation,defaults);
    }

function fix_variable_headers(value)
    {
    var base=document.getElementById('base');
    var added=document.getElementById('added');
    base.firstChild.data=headers[value][0];
    added.firstChild.data=headers[value][1];
    return true;
    }
</script>
<script type="text/javascript" src="../javascript/soundmanager2-nodebug-jsmin.js"></script>
<script type="text/javascript" src="anim_prep.js"></script>
</head>
<body onload="return fix_all(ability.effect,ability.impact_animation,ability.impact_data);">
<form method="post" name="formdata" action="edit_ability.php">
<table>
  <tr><th>
    <table>
      <tr>
        <?php
        $tindex=$abilityindex;
        do  {
           	$tindex--;
            } while($tindex>0 && !array_key_exists($tindex,$abilities));
        if ($tindex>0)
            echo "<td><a href=\"edit_ability.php?ability=$tindex\">Previous ability</a></td>"
        ?>
        <td><a href="abilities.php">Return to ability list</a></td>
        <?php
        echo "<td><a href=\"delete_ability.php?ability=$abilityindex\">Delete this ability</a></td>"
        ?>
        <td><a href="edit_ability.php?ability=0">New ability</a></td>
        <?php
        $tindex=$abilityindex;
        $keys=array_keys($abilities);
        sort($keys);
        end($keys);
        $last=end($keys);
        do  {
           	$tindex++;
            } while($tindex<=$last && !array_key_exists($tindex,$abilities));
        if ($tindex<=$last)
            echo "<td><a href=\"edit_ability.php?ability=$tindex\">Next ability</a></td>"
        ?>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Ability Name</th>
        <td>
          <?php make_input('name',$ability->name); ?>
          <?php make_input('ability',$abilityindex,array('type'=>'hidden')); ?>
        </td>
        <th>Ability Type</th>
        <td>
          <?php make_select('type',$ability->type,$ability_types); ?>
        </td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <caption><b>Ability effect:</b></caption>
      <tr>
        <th>Effect</th>
        <th>Use Targets</th>
        <th id="base">Base Value</th>
        <th id="added">Added Value</th>
        <th>Attribute</th>
        <th>MP Used</th>
      </tr>
      <tr>
        <td><?php make_select('effect',$ability->effect,$effects,array('onchange'=>'return fix_variable_headers(this.value);')); ?></td>
        <td><?php make_select('targets',$ability->targets,$ranges); ?></td>
        <td><?php make_input('base',$ability->base,$def_size); ?></td>
        <td><?php make_input('added',$ability->added,$def_size); ?></td>
        <td><?php make_select('attribute',$ability->attribute,$attributes); ?></td>
        <td><?php make_input('mp_used',$ability->mp_used,$def_size); ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <caption><b>Description:</b></caption>
      <tr>
        <td><?php make_textarea('description',$ability->description,$def_textarea); ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Menu Picture</th>
        <th>Effect Type (Skill Only)</th>
        <th>Impact Animation</th>
      </tr>
      <tr>
        <td><?php make_input('menu_pic',$ability->menu_pic); ?><img align="middle" height="34" width="96" src="<?php echo $image; ?>" id="pic" /></td>
        <td><?php make_select('skill_effect_type',$ability->skill_effect_type,$effect_types_list); ?></td>
        <td><?php make_select('impact_animation',$ability->impact_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'impact_\',this.value);')); ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><table id="impact_images"></table></td>
        <td><table id="impact_sounds"></table></td>
        <td><table id="impact_times"></table></td>
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
        $tindex=$abilityindex;
        do  {
           	$tindex--;
            } while($tindex>0 && !array_key_exists($tindex,$abilities));
        if ($tindex>0)
            echo "<td><a href=\"edit_ability.php?ability=$tindex\">Previous ability</a></td>"
        ?>
        <td><a href="abilities.php">Return to ability list</a></td>
        <?php
        echo "<td><a href=\"delete_ability.php?ability=$abilityindex\">Delete this ability</a></td>"
        ?>
        <td><a href="edit_ability.php?ability=0">New ability</a></td>
        <?php
        $tindex=$abilityindex;
        $keys=array_keys($abilities);
        sort($keys);
        end($keys);
        $last=end($keys);
        do  {
           	$tindex++;
            } while($tindex<=$last && !array_key_exists($tindex,$abilities));
        if ($tindex<=$last)
            echo "<td><a href=\"edit_ability.php?ability=$tindex\">Next ability</a></td>"
        ?>
      </tr>
    </table>
  </th></tr>
</table>
</form>
<!--<applet name="Jukebox" id="Jukebox" code="Jukebox.class"  archive="../java/Jukebox.jar" width="700" height="20"></applet>-->
</body>
</html>
