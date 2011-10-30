<?php
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';

#Prep the game objects.
require_once INCLUDE_DIR.'party.php';
require_once INCLUDE_DIR.'team.php';

session_start();

#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_membership_access('dse','/auth/login.php','index.php');
redirect_on_hold($userid,'dse','on_hold.php','../index.php');

//If there is a fight in progress, jump to it.
/*
if(isset($_SESSION['prefight']))
    {
    header('Location: rpg.php');
    exit;
    }
*/
//New way!
require_once INCLUDE_DIR.'fight_store.php';
$fight_store=new FIGHT_STORE();
$result=$fight_store->get_fight($userid);
if($result!=false)
    {
    header('Location: rpg.php');
    exit;
    }

#If all the data we need isn't here, then load in the filler data.
if(!isset($_SESSION['party']))
    {
    header('Location: pick_team.php');
    exit;
    }

#Check for a cancel
if(isset($_POST['GO_BACK']))
    {
    header('Location: setup_fight.php');
    exit;
    }


#Generate a temporary hero party.
$party=$_SESSION['party'];
$team=$_SESSION['team'];

#Break down the hero's items and equipment.
$group=$_GET['group'];
$position=$_GET['position'];

$status='';

#Break down the hero's items and equipment.
$character=&$party->get_character("$group;$position");
//list($name,$job,$level,$personality,$group,$inventory,$equipment)=$_SESSION['heroes'][$hero_index];
$job_name=$jobs[$character->jobid]->name;
$personality_name=$character->personality->name;

//Check for equipping.
for($index=0;$index<count($character->inventory);$index++)
    {
    if(isset($_POST["equip$index"]))
        {
        switch($_POST["equip$index"])
            {
            case 'Unequip':
                $character->unequip_item($index);
                $status.="{$character->name} removed the {$items[$character->inventory[$index]['item']]->name}.";
                break;
            case 'Equip':
            case 'Equip Left':
            case 'Equip Right':
                do {
                    $result=$character->equip_item($index,($_POST["equip$index"]=='Equip Left'?0:1));
                    if(is_numeric($result))
                        {
                        $status.="{$character->name} removed the {$items[$character->inventory[$result]['item']]->name}.";
                        $character->unequip_item($result);
                        }
                    } while (is_numeric($result));
                if (is_array($result))
                    $status.="{$character->name} equipped the {$items[$character->inventory[$index]['item']]->name}.";
                else
                    $status.="{$character->name} could not equip the {$items[$character->inventory[$index]['item']]->name}.";
                break;
            }
        $party->store_party();
        }
    }


//Now process an action, if needed.
if(isset($_POST['DO_ACTION']))
    {
    $GLOBALS['output']='';
    $action=$_POST['action'];
    $ability=$_POST['ability'];
    if(isset($_POST['qty']))
        $qty=$_POST['qty'];
    if(isset($_POST['group']))
        $tgroup=$_POST['group'];
    if(isset($_POST['character']))
        $tposition=$_POST['character'];
    switch($_POST['action'])
        {
        case 2: //Use item
            $myability=$GLOBALS['items'][$character->inventory[$ability]['item']];
            $status=perform_setup_action($party,$action,$ability,'used',$myability->name,$myability->use_targets,$group,$position,$tgroup,$tposition);
            break;
        case 5: //Cast spell
            $myability=$GLOBALS['abilities'][$character->abilities[$ability]];
            $status=perform_setup_action($party,$action,$ability,'cast',$myability->name,$myability->targets,$group,$position,$tgroup,$tposition);
            break;
        case 4: //Use skill
            $myability=$GLOBALS['abilities'][$character->abilities[$ability]];
            $status=perform_setup_action($party,$action,$ability,'performed',$myability->name,$myability->targets,$group,$position,$tgroup,$tposition);
            break;
        case 9: //Give item
            $item=$character->inventory[$ability]['item'];
            $myitem=$GLOBALS['items'][$item];
            $qty=$character->remove_item($ability,$qty);
            $mychar=&$party->get_character("$tgroup;$tposition");
            $given=$qty-$mychar->add_item($item,$qty);
            if($qty!=$given)
                $character->add_item($item,$qty-$given);
            $party->store_party();
            if($group!=$tgroup ||$position!=$tposition)
                $status="{$character->name} gave $given {$myitem->name}(s) to {$mychar->name}. ";
            else
                $status="{$character->name} reorganized $given {$myitem->name}(s). ";
            break;
        case 1001: //Sell item
            $item=$character->inventory[$ability]['item'];
            $sold=$character->remove_item($ability,$qty);
            $worth=round($sold*$GLOBALS['items'][$item]->price*.75);
            $team->gold+=$worth;
            $team->store_team();
            $status="{$character->name} sold $sold {$GLOBALS['items'][$item]->name}(s) for $worth gold.";
            break;
        }
    }

function perform_setup_action(&$party,$action,$actionindex,$verb,$name,$range,$group,$position,$tgroup,$tposition)
    {
    $character=$party->get_character("$group;$position");
    $status="{$character->name} $verb $name";
    if($range<-1)
        $status.=" on the entire party";
    elseif($range==-1)
        $status.=" on {$party->group[$tgroup]->name}";
    elseif($range==0)
        {
        if($group!=$tgroup || $position!=$tposition)
            $status.=" on {$party->groups[$tgroup]->characters[$tposition]->name}";
        }
    else
        {
        for($index=-$range;$index<=$range;$index++)
            if($index+$tposition>=0 && $index+$tposition<$party->groups[$tgroup]->count())
                $names[].=$party->groups[$tgroup]->characters[$tposition+$index]->name;
        aray_push('and '.array_pop($names));
        $status.=" on ".implode((count($names)>2?', ':' '),$names);
        }
    $status.=". ";

    #Do action
    $action_list=$party->do_field_action($action,$actionindex,$group,$position,$tgroup,$tposition);
    //var_dump($action_list);
    $results=convert_action_list_to_playlist($action_list,-1,$party);
    //var_dump($results);

    #Process results
    if($action==4||$action==5)
        array_shift($results);
    $tgroup=0;
    foreach($results as $result)
        {
      	if($result[0]==0)
            switch($result[1])
                {
                case "present_group":
                    $tgroup=$result[3];
                    break;
                case "alter_stat":
                    if($result[6]>0)
                        $status.= $party->groups[$tgroup]->characters[$result[4]]->name."'s $result[5] increased by $result[6]. ";
                    elseif($result[6]<0)
                        $status.= $party->groups[$tgroup]->characters[$result[4]]->name."'s $result[5] decreased by ".(-$result[6]).". ";
                    else
                        $status.= $party->groups[$tgroup]->characters[$result[4]]->name."'s $result[5] did not change. ";
                    break;
                }
        }

    return $status;
    }

//Build item list
$item_list=array();
$full_item_list=array();
$qty_list=array();
foreach($character->inventory as $index=>$item)
    {
    $myitem=&$GLOBALS['items'][$item['item']];
    if($myitem->effect!=0)
        $item_list[]=array($index,$myitem->name,$myitem->useable_in_field());
    $full_item_list[]=array($index,$myitem->name,true);
    $qty_list[]=$item['qty'];
    }

//Build spell list
$spell_list=array();
$skill_list=array();
foreach($character->abilities as $index=>$ability)
    {
    $myability=&$GLOBALS['abilities'][$ability];
    if($myability->type==1) //is skill
        $skill_list[]=array($index,"{$myability->name} ($myability->mp_used)",$myability->useable_in_field() & ($character->current['MP']>=$myability->mp_used));
    else //is spell
        $spell_list[]=array($index,"{$myability->name} ($myability->mp_used)",$myability->useable_in_field() & ($character->current['MP']>=$myability->mp_used));
    }

//Build action list
$action_list=array();
if (count($item_list)>0)
    $action_list[2]='Use Item';
if (count($spell_list)>0)
    $action_list[5]='Cast Spell';
if (count($skill_list)>0)
    $action_list[4]='Use Skill';
if (count($character->inventory)>0)
    {
    $action_list[9]='Give Item';
    $action_list[1001]='Sell Item';
    }

//Build fake list
$fake_list=array();

//For sake of argument, refresh the team data
$team->refresh_team();

?>
<html>
<head>
<script type="text/javascript" src="javascript/constants.js"></script>
<script type="text/javascript">
var item_list=<?php echo php_data_to_js($item_list); ?>;
var full_item_list=<?php echo php_data_to_js($full_item_list); ?>;
var spell_list=<?php echo php_data_to_js($spell_list); ?>;
var skill_list=<?php echo php_data_to_js($skill_list); ?>;
var qty_list=<?php echo php_data_to_js($qty_list); ?>;

var abilities=<?php echo $GLOBALS["abilities_js"]; ?>;
var items=<?php echo $GLOBALS["items_js"]; ?>;
var party=<?php echo php_data_to_js($party); ?>;
var character=party.groups[<?php echo $group; ?>].characters[<?php echo $position; ?>];

var action=null;
var ability=null;
var range=null;
var effect=null;
var group=null;


function select_character(object)
    {
    if(action!=COMMAND_GIVE_ITEM && must_be_alive(effect) && party.groups[group].characters[parseInt(object.value)].current.HP==0)
        return false;
    document.form_data.DO_ACTION.style.visibility='visible';
    }
</script>
</head>
<body>
<h1 style="color:orange"><?php echo $status; ?></h1>
<form method="post" name="form_data">
<table>
  <tr><th valign="top">
    <table width="100%">
      <caption><b><?php echo $character->name; ?> - Level <?php echo $character->level; ?> <?php echo $job_name; ?></b></caption>
      <tr>
        <th>XP Earned</th>
        <td><?php echo $character->exp; ?>/<?php echo $character->need; ?></td>
        <th>PXP Rating</th>
        <td><?php echo $character->pxp; ?></td>
      </tr>
    </table>
    <table width="100%">
      <?php
      echo "<tr>\n";
      foreach($GLOBALS['character_stats'] as $key)
          echo "<th>$key</th>\n";
      echo "</tr>\n<tr>\n";
      foreach($GLOBALS['character_stats'] as $key)
          {
          $value = $character->base[$key];
          echo "<td align=\"center\">";
          if(in_array($key,array('HP','MP')))
              echo $character->current[$key].'/'.$character->get_base($key);
          else
              echo $value;
          echo "</td>\n";
          }
      echo "</tr>\n<tr>\n";
      foreach($GLOBALS['character_stats'] as $key)
          {
          $value = $character->base[$key];
          echo "<td align=\"center\">";
          if(count(array_keys_strict($character->equipment,null))!=count($character->equipment))
              {
              $character->command=1;
              $base=$character->get_current($key);
              $stat1=$base-$value;
              if($stat1>=0) $stat1='+'.$stat1;
              if(!in_array($key,array('HP','MP')))
                  echo "$base ($stat1)";
              else
                  {
                  $stat1=$character->get_base($key)-$value;
                  if($stat1>=0) $stat1='+'.$stat1;
                  echo "($stat1)";
                  }
              if(in_array($key,array('Accuracy','Strength','Speed'))
                  && $character->equipment['lhand']!==$character->equipment['rhand'])
                  {
                  $character->command=0;
                  $base=$character->get_current($key);
                  $stat2=$base-$value;
                  if($stat2>=0) $stat2='+'.$stat2;
                  echo "<br>$base ($stat2)";
                  }
              }

          echo "</td>\n";
          }
      echo "</tr>\n";
      ?>
    </table>
    <table>
      <tr>
        <th>Item</th>
        <th>Qty</th>
        <th>Where Equipped</th>
        <th>Action</th>
      </tr>
  <?php
    foreach(array_keys($character->inventory) as $index)
        {
        $iteminfo=$character->inventory[$index];
        $itemid=$iteminfo['item'];
        $qty=$iteminfo['qty'];
        ?>
      <tr>
        <td><?php echo ($qty>0?$GLOBALS['items'][$itemid]->name:''); ?></td>
        <td align="center"><?php echo ($qty>1?$qty:''); ?></td>
        <td align="center"><?php
        #This here will list where an item is equipped.
        $keys=array_keys_strict($character->equipment,$index);
        echo implode(', ',$keys);
        ?></td>
        <td align="center"><?php
        #This here will list button(s) or link(s) if the item is equippable.
        if(count($keys)>0)
            echo "<input type=\"submit\"name=\"equip$index\" value=\"Unequip\">";
        elseif (count($items[$itemid]->equip_type)>0)
            {
            if(count(array_intersect($items[$itemid]->equip_type,array('hand','arm')))>0)
                echo "<input type=\"submit\" name=\"equip$index\" value=\"Equip Left\"><input type=\"submit\" name=\"equip$index\" value=\"Equip Right\">";
            elseif(count(array_intersect($items[$itemid]->equip_type,array('ammo')))>0)
                {
                if(!is_null($character->equipment['lhand'])
                    && $items[$itemid]->ammo_type==$items[$character->inventory[$character->equipment['lhand']]['item']]->ammo_type
                    && $character->equipment['lhand']!=$character->equipment['rhand'])
                    echo "<input type=\"submit\" name=\"equip$index\" value=\"Equip Left\">";
                if(!is_null($character->equipment['rhand'])
                    && $items[$itemid]->ammo_type==$items[$character->inventory[$character->equipment['rhand']]['item']]->ammo_type)
                    echo "<input type=\"submit\" name=\"equip$index\" value=\"Equip Right\">";
                }
            else
                echo "<input type=\"submit\" name=\"equip$index\" value=\"Equip\">";
            }
        ?></td>
      </tr>
        <?php
        }
  ?>
    </table>
  </th>
  </tr>
  <tr>
    <td>
      <input type="submit" name="GO_BACK" value="Return To Last Menu">
    </td>
  </tr>
</table>
</form>
</body>
</html>
