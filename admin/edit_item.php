<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_ITEM','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

//Quick look- if $_SESSION['animationjs'] does not exist then jump to get_animations.php
if(!array_key_exists('animationjs',$_SESSION) || !$_SESSION['animationjs'])
    {
    header('Location: get_animations.php?return=edit_item.php&item='.$_REQUEST['item']);
    exit;
    }
$animations=$_SESSION['animations'];
$animationjs=$_SESSION['animationjs'];

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'functions.php';
require_once INCLUDE_DIR.'item_store.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';

if(get_magic_quotes_gpc())
    $_POST=array_map('stripslashes',$_POST);

if(!function_exists('php_data_to_js'))
    {
    require_once INCLUDE_DIR.'js_rip.php';
    }

$item_store=new ITEM_STORE;

if(isset($_POST['OP'])&&$_POST['OP']=="Update Database")
    {
    //Get the item id number. 0 is a new item.
    $itemindex=$_REQUEST['item'];

    //Calculate ammo_type
    switch($_POST['ammo_required'])
        {
        case 0: //No
            $ammo_type='';
            break;
        case 1: //Yes, from list.
            $ammo_type=$_POST['ammo_type'];
            break;
        case 2: //Yes, new ammo type.
            $ammo_type=$_POST['new_ammo_type'];
        }

    //Calculate equip_type
    if($_POST['equipment_location']=='')
        $equip_type=null;
    else
        $equip_type=explode('-',$_POST['equipment_location']);

    //Calculate One_use value
    $one_use=($_POST['one_use']=='true');

    //Fix description
    $description=stripslashes($_POST['description']);

    //Prep the special variables.
    //Fight first
    $fight_impact_animation_data=$animations[$_POST['fight_impact_animation']];
    $fight_impact_image_count=count($fight_impact_animation_data['images']);
    $fight_impact_sound_count=count($fight_impact_animation_data['sounds']);
    $fight_impact_time_count=count($fight_impact_animation_data['times']);
    for($index=0;$index<$fight_impact_image_count;$index++)
        $fight_impact_images[]=$_POST["fight_impact_image_$index"];
    for($index=0;$index<$fight_impact_sound_count;$index++)
        $fight_impact_sounds[]=$_POST["fight_impact_sound_$index"];
    for($index=0;$index<$fight_impact_time_count;$index++)
        $fight_impact_times[]=$_POST["fight_impact_time_$index"];
    $fight_impact_data=array('images'=>$fight_impact_images,'sounds'=>$fight_impact_sounds,'times'=>$fight_impact_times);
    //use next
    $use_impact_animation_data=$animations[$_POST['use_impact_animation']];
    $use_impact_image_count=count($use_impact_animation_data['images']);
    $use_impact_sound_count=count($use_impact_animation_data['sounds']);
    $use_impact_time_count=count($use_impact_animation_data['times']);
    for($index=0;$index<$use_impact_image_count;$index++)
        $use_impact_images[]=$_POST["use_impact_image_$index"];
    for($index=0;$index<$use_impact_sound_count;$index++)
        $use_impact_sounds[]=$_POST["use_impact_sound_$index"];
    for($index=0;$index<$use_impact_time_count;$index++)
        $use_impact_times[]=$_POST["use_impact_time_$index"];
    $use_impact_data=array('images'=>$use_impact_images,'sounds'=>$use_impact_sounds,'times'=>$use_impact_times);
    
    //Make a new ITEM
    $this_item=new ITEM(
        $_POST['name'],$_POST['price'],$_POST['effect'],$_POST['use_targets'],$_POST['base'],$_POST['added'],$_POST['attribute'],$one_use,$equip_type,
        array($_POST['HP'],$_POST['MP'],$_POST['Speed'],$_POST['Accuracy'],$_POST['Strength'],
            $_POST['Dodge'],$_POST['Block'],$_POST['Power'],$_POST['Resistance'],$_POST['Focus']),
        array($_POST['pHP'],$_POST['pMP'],$_POST['pSpeed'],$_POST['pAccuracy'],$_POST['pStrength'],
            $_POST['pDodge'],$_POST['pBlock'],$_POST['pPower'],$_POST['pResistance'],$_POST['pFocus']),
        $_POST['targets'],$_POST['attack_count'],$_POST['attack_attribute'],$ammo_type,$description,
        $_POST['menu_pic'],$_POST['fight_effect_type'],
        $_POST['fight_impact_animation'],$fight_impact_data,$_POST['use_impact_animation'],$use_impact_data);

    //update that item in the databse
    $itemindex=$item_store->set_item($this_item,$itemindex);
    }
else
    $itemindex=$_REQUEST['item'];

$items=&$item_store->get_all_items();
$item=&$items[$itemindex];

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


if(is_null($item->equip_type))
    $equipment_location='';
else
    $equipment_location=implode('-',$item->equip_type);

//ammo types
$ammo_pretypes=array_unique(array_strip($items,'->ammo_type'));
sort($ammo_pretypes);
foreach($ammo_pretypes as $value)
    $ammo_types[$value]=$value;
$ammo_required=($item->ammo_type==''?0:1);
$ammo_required_list=array(
    0=>'No ammo required',
    1=>'Require ammo from list',
    2=>'Require new ammo type'
    );
$image='../'.ITEM_IMAGES_DIR.$item->menu_pic;

$var_header_js=php_data_to_js($effect_var_names);
$item_js=php_data_to_js($item);
?>
<html>
<head>
<script>
window.SM2_DEFER = true;

var item=<?php echo $item_js; ?>;
var headers=<?php echo $var_header_js; ?>;
var animations=<?php echo $animationjs; ?>;
var sound_dir='../<?php echo SOUND_DIR; ?>';
var image_dir='../<?php echo IMAGES_DIR; ?>';
var swfdir='<?php echo SWF_DIR; ?>';

function fix_all(value,fanimation,fdefaults,uanimation,udefaults)
    {
    fix_variable_headers(value);
    fix_animation_options('fight_impact_',fanimation,fdefaults);

    init_audio(function (){alert('No Audio.')});

    return fix_animation_options('use_impact_',uanimation,udefaults);
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
<body onload="return fix_all(item.effect,item.fight_impact_animation,item.fight_impact_data,item.use_impact_animation,item.use_impact_data);">
<form method="post" action="edit_item.php" name="formdata">
<table>
  <tr><th>
    <table>
      <tr>
        <?php
        $tindex=$itemindex;
        do  {
           	$tindex--;
            } while($tindex>0 && !array_key_exists($tindex,$items));
        if ($tindex>0)
            echo "<td><a href=\"edit_item.php?item=$tindex\">Previous item</a></td>"
        ?>
        <td><a href="items.php">Return to item list</a></td>
        <?php
        echo "<td><a href=\"delete_item.php?item=$itemindex\">Delete this item</a></td>"
        ?>
        <td><a href="edit_item.php?item=0">New item</a></td>
        <?php
        $tindex=$itemindex;
        $keys=array_keys($items);
        sort($keys);
        end($keys);
        $last=end($keys);
        do  {
           	$tindex++;
            } while($tindex<=$last && !array_key_exists($tindex,$items));
        if ($tindex<=$last)
            echo "<td><a href=\"edit_item.php?item=$tindex\">Next item</a></td>"
        ?>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Item Name</th>
        <td>
          <?php make_input('name',$item->name); ?>
          <?php make_input('item',$itemindex,array('type'=>'hidden')); ?>
        </td>
        <th>Price</th>
        <td>
          <?php make_input('price',$item->price); ?>
        </td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <caption><b>Item used for effect:</b></caption>
      <tr>
        <th>Effect</th>
        <th>Use Targets</th>
        <th id="base">Base Value</th>
        <th id="added">Added Value</th>
        <th>Attribute</th>
        <th>Number of Uses</th>
      </tr>
      <tr>
        <td><?php make_select('effect',$item->effect,$effects,array('onchange'=>'return fix_variable_headers(this.value);')); ?></td>
        <td><?php make_select('use_targets',$item->use_targets,$ranges); ?></td>
        <td><?php make_input('base',$item->base,$def_size); ?></td>
        <td><?php make_input('added',$item->added,$def_size); ?></td>
        <td><?php make_select('attribute',$item->attribute,$attributes); ?></td>
        <td><?php make_select('one_use',$item->one_use,$use_modes); ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <caption><b>Item used as equipment:</b></caption>
      <tr>
        <th>Range</th>
        <th>Attack Count</th>
        <th>Attack Attribute</th>
        <th>Equipment Location</th>
      </tr>
      <tr>
        <td><?php make_select('targets',$item->targets,$ranges); ?></td>
        <td><?php make_input('attack_count',$item->attack_count,$def_size); ?></td>
        <td><?php make_select('attack_attribute',$item->attack_attribute,$attributes); ?></td>
        <td><?php make_select('equipment_location',$equipment_location,$equip_loc); ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Requires Ammo?</th>
        <th>Required Ammo</th>
        <th>New Ammo Type</th>
      </tr>
      <tr>
        <td><?php make_select('ammo_required',$ammo_required,$ammo_required_list); ?></td>
        <td><?php make_select('ammo_type',$item->ammo_type,$ammo_types); ?></td>
        <td><?php make_input('new_ammo_type',''); ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <?php
      echo "<tr>\n<th></th>\n";
      foreach($item->statinc as $key=>$value)
          echo "<th>$key</th>\n";
      echo "</tr>\n<tr>\n<th>Percent</th>";
      foreach($item->statpercinc as $key=>$value)
          {
          echo "<th>";
          make_input("p$key",$item->statpercinc[$key],$def_size);
          echo "</th>\n";
          }
      echo "</tr>\n<tr>\n<th>Bonus</th>";
      foreach($item->statinc as $key=>$value)
          {
          echo "<th>";
          make_input($key,$item->statinc[$key],$def_size);
          echo "</th>\n";
          }
      echo "</tr>\n";
      ?>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <caption><b>Description:</b></caption>
      <tr>
        <td><?php make_textarea('description',$item->description,$def_textarea); ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Menu Picture</th>
        <th>Fight Effect Type</th>
        <th>Fight Impact Animation</th>
        <th>Use Impact Animation</th>
      </tr>
      <tr>
        <td><?php make_input('menu_pic',$item->menu_pic,array('type'=>'hidden')); ?><img align="middle" width="96" height="34" src="<?php echo $image; ?>" id="pic" /></td>
        <td><?php make_select('fight_effect_type',$item->fight_effect_type,$effect_types_list); ?></td>
        <td><?php make_select('fight_impact_animation',$item->fight_impact_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'fight_impact_\',this.value);')); ?></td>
        <td><?php make_select('use_impact_animation',$item->use_impact_animation,$animation_list,array('onchange'=>'return fix_animation_options(\'use_impact_\',this.value);')); ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Fight animation data</th></tr>
      <tr>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><table id="fight_impact_images"></table></td>
        <td><table id="fight_impact_sounds"></table></td>
        <td><table id="fight_impact_times"></table></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr><th colspan="3">Use animation data</th></tr>
      <tr>
        <th>Images</th>
        <th>Sounds</th>
        <th>Times and data</th>
      </tr>
      <tr>
        <td><table id="use_impact_images"></table></td>
        <td><table id="use_impact_sounds"></table></td>
        <td><table id="use_impact_times"></table></td>
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
        $tindex=$itemindex;
        do  {
           	$tindex--;
            } while($tindex>0 && !array_key_exists($tindex,$items));
        if ($tindex>0)
            echo "<td><a href=\"edit_item.php?item=$tindex\">Previous item</a></td>"
        ?>
        <td><a href="items.php">Return to item list</a></td>
        <?php
        echo "<td><a href=\"delete_item.php?item=$itemindex\">Delete this item</a></td>"
        ?>
        <td><a href="edit_item.php?item=0">New item</a></td>
        <?php
        $tindex=$itemindex;
        $keys=array_keys($items);
        sort($keys);
        end($keys);
        $last=end($keys);
        do  {
           	$tindex++;
            } while($tindex<=$last && !array_key_exists($tindex,$items));
        if ($tindex<=$last)
            echo "<td><a href=\"edit_item.php?item=$tindex\">Next item</a></td>"
        ?>
      </tr>
    </table>
  </th></tr>
</table>
</form>
<!--<applet name="Jukebox" id="Jukebox" code="Jukebox.class"  archive="../java/Jukebox.jar" width="700" height="20" style="visibility:hidden"></applet>-->
</body>
</html>
