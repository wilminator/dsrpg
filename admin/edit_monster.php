<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_MONSTER','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'functions.php';
require_once INCLUDE_DIR.'monster_store.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';

if(get_magic_quotes_gpc())
    $_POST=array_map('stripslashes',$_POST);

if(!function_exists('php_data_to_js'))
    {
    require_once INCLUDE_DIR.'js_rip.php';
    }

$monster_store=new MONSTER_STORE;

if(isset($_POST['OP']))
    {
    //Get the monster id number. 0 is a new monster.
    $monsterindex=$_REQUEST['monster'];

    //Get the ability count
    $ability_count=$_POST['ability_count'];
    $m_abilities=array();

    for($count=0;$count<=$ability_count;$count++)
        if(!isset($_POST["del_ability_$count"])
            && $_POST["ability_$count"]>0)
            $m_abilities[]=$_POST["ability_$count"];

    //Make a new monster
    #($name,$stats,$abilities,$items,$equipment,$gold,$personalityid)
    if($monsterindex>0)
        {
        $monster=&$monster_store->get_monster($monsterindex);
        $inventory=$monster->items;
        $equipment=$monster->equipment;
        }
    else
        {
        $inventory=array();
        $equipment=array();
        }

    $this_monster=new MONSTER(
        $_POST['name'],
        array($_POST['HP'],$_POST['MP'],$_POST['Speed'],$_POST['Accuracy'],$_POST['Strength'],
            $_POST['Dodge'],$_POST['Block'],$_POST['Power'],$_POST['Resistance'],$_POST['Focus']),
        $m_abilities,$inventory,$equipment,$_POST['gold'],$_POST['personalityid'],
        $_POST['ai_action'],$_POST['ai_goal'],$_POST['ai_target'],$_POST['ai_experience']);

    //update that monster in the databse
    $monsterindex=$monster_store->set_monster($this_monster,$monsterindex);

    //If this is Update Inventory, then switch pages.
    if($_POST['OP']=="Update Inventory")
        {
        header("Location: equip_monster.php?monster=$monsterindex");
        exit;
        }
    }
else
    $monsterindex=$_REQUEST['monster'];

$monsters=&$monster_store->get_all_monsters();
if($monsterindex>0)
    $monster=&$monsters[$monsterindex];
else
    $monster=new MONSTER(
        'New Monster',
        array(12,6,8,8,8,7,8,8,8,8),
        array(),array(),array(),0,1,
        0,0,0,50);

//Setup HTML display variables.
$ability_list=array(0=>'');
$result=mysql_do_query("select abilityid,name,description,mp_used from abilities order by name");
while($data=mysql_fetch_assoc($result))
    {
    $ability_list[$data['abilityid']]="$data[name] ($data[mp_used])";
    $ability_desc[$data['abilityid']]=$data['description'];
    }

$personality_list=array();
//$result=mysql_do_query("select abilityid,name,description from abilities");
//while($data=mysql_fetch_assoc($result))
require_once INCLUDE_DIR.'personalities.php';
foreach($GLOBALS['personalities'] as $index=>$personality)
    {
    $personality_list[$index]=$personality->name;
    $personality_pic[$index]='../'.FIGHTER_IMAGES_DIR.$personality->base_data['images'][0];
    }
asort($personality_list);


//Default input box size
$def_size=array('size'=>7);
//Default input box size
$def_textarea=array('rows'=>3,'cols'=>50);
?>
<script>
abilities=<?php echo php_data_to_js($ability_desc); ?>;

function show_ability_description(object)
    {
    var ability=object.value;
    document.form_data.ability_desc.value=abilities[ability];
    }

personalities=<?php echo php_data_to_js($personality_pic); ?>;

function show_personality(object)
    {
    var ability=object.value;
    document.form_data.pic.src=personalities[ability];
    }
</script>
<form name="form_data" method="post" action="edit_monster.php">
<table>
  <tr><th>
    <table>
      <tr>
        <?php
        $tindex=$monsterindex;
        do  {
           	$tindex--;
            } while($tindex>0 && !array_key_exists($tindex,$monsters));
        if ($tindex>0)
            echo "<td><a href=\"edit_monster.php?monster=$tindex\">Previous monster</a></td>"
        ?>
        <td><a href="monsters.php">Return to monster list</a></td>
        <?php
        echo "<td><a href=\"delete_monster.php?monster=$monsterindex\">Delete this monster</a></td>"
        ?>
        <td><a href="edit_monster.php?monster=0">New monster</a></td>
        <?php
        $tindex=$monsterindex;
        $keys=array_keys($monsters);
        sort($keys);
        $last=end($keys);
        do  {
           	$tindex++;
            } while($tindex<=$last && !array_key_exists($tindex,$monsters));
        if ($tindex<=$last)
            echo "<td><a href=\"edit_monster.php?monster=$tindex\">Next monster</a></td>"
        ?>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Monster Name</th>
        <td>
          <?php make_input('name',$monster->name); ?>
          <?php make_input('monster',$monsterindex,array('type'=>'hidden')); ?>
        </td>
        <th>XP Worth</th>
        <td>
          <?php make_input('PXP',$monster->pxp,array('size'=>7,'disabled'=>'disabled')); ?>
        </td>
        <th>Gold</th>
        <td>
          <?php make_input('gold',$monster->gold,$def_size); ?>
        </td>
        <th>Personality</th>
        <td>
          <?php make_select('personalityid',$monster->personalityid,$personality_list,array('onkeyup'=>"show_personality(this);",'onclick'=>"show_personality(this);")); ?>
        </td>
      </tr>
    </table>
  </th>
  <td rowspan="6" valign="top">
    <img name="pic" src="<?php echo $personality_pic[$monster->personalityid]; ?>">
  <td>
  </tr>
  <tr><th>
    <table>
  <tr><th>
    <table>
      <?php
      echo "<tr>\n";
      foreach($monster->stats as $key=>$value)
          echo "<th>$key</th>\n";
      echo "</tr>\n<tr>\n";
      foreach($monster->stats as $key=>$value)
          {
          echo "<th>";
          make_input($key,$monster->stats[$key],$def_size);
          echo "</th>\n";
          }
      echo "</tr>\n";
      ?>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>AI Action</th>
        <td>
          <?php make_select('ai_action',$monster->ai_action,$GLOBALS['ai_action'],array()); ?>
        </td>
        <th>AI Goal</th>
        <td>
          <?php make_select('ai_goal',$monster->ai_goal,$GLOBALS['ai_goal'],array()); ?>
        </td>
        <th>AI Target</th>
        <td>
          <?php make_select('ai_target',$monster->ai_target,$GLOBALS['ai_target'],array()); ?>
        </td>
        <th>AI Experience</th>
        <td>
          <?php make_select('ai_experience',$monster->ai_experience,$GLOBALS['ai_experience'],array()); ?>
        </td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <th>Ability</th>
        <th>Delete?</th>
        <th>Desc</th>
      </tr>
      <?php
        for($count=0;$count<count($monster->abilities);$count++)
            {
            echo "      <tr>\n        <td>";
            echo $ability_list[$monster->abilities[$count]];
            make_input("ability_$count",$monster->abilities[$count],array('type'=>'hidden'));
            echo "</td>\n        <td>";
            make_checkbox("del_ability_$count",false);
            echo "</td>\n        <td>";
            echo $ability_desc[$monster->abilities[$count]];
            echo "</td>\n      </tr>\n";
            }
        ?>
      <tr>
        <td><?php make_select("ability_$count",'',$ability_list,array('onkeypress'=>"show_ability_description(this);",'onclick'=>"show_ability_description(this);")); ?></td>
        <td><?php make_input("ability_count",count($monster->abilities),array('type'=>'hidden')); ?></td>
        <td><?php make_textarea("ability_desc",'',$def_textarea); ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <td><input type="submit" name="OP" value="Update Database"></td>
        <td><input type="submit" name="OP" value="Update Inventory"></td>
      </tr>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <?php
        $tindex=$monsterindex;
        do  {
           	$tindex--;
            } while($tindex>0 && !array_key_exists($tindex,$monsters));
        if ($tindex>0)
            echo "<td><a href=\"edit_monster.php?monster=$tindex\">Previous monster</a></td>"
        ?>
        <td><a href="monsters.php">Return to monster list</a></td>
        <?php
        echo "<td><a href=\"delete_monster.php?monster=$monsterindex\">Delete this monster</a></td>"
        ?>
        <td><a href="edit_monster.php?monster=0">New monster</a></td>
        <?php
        $tindex=$monsterindex;
        $keys=array_keys($monsters);
        sort($keys);
        $last=end($keys);
        do  {
           	$tindex++;
            } while($tindex<=$last && !array_key_exists($tindex,$monsters));
        if ($tindex<=$last)
            echo "<td><a href=\"edit_monster.php?monster=$tindex\">Next monster</a></td>"
        ?>
      </tr>
    </table>
  </th></tr>
</table>
</form>
