<?php
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';

#Prep the game objects.
require_once INCLUDE_DIR.'jobs.php';
require_once INCLUDE_DIR.'personalities.php';
require_once INCLUDE_DIR.'abilities.php';
require_once INCLUDE_DIR.'items.php';
require_once INCLUDE_DIR.'party.php';
require_once INCLUDE_DIR.'team.php';

session_start();

#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_membership_access('dse','/auth/login.php','index.php');
redirect_on_hold($userid,'dse','on_hold.php','../index.php');

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

$status='';

//If actor is not set, then dont do the action!
if(isset($_GET['actor']))
    {
    #Break down the hero's items and equipment.
    $actor=$_GET['actor'];
    $character=&$team->get_character($actor);
    $key=$party->find_character($character->charid);
    $character=&$party->get_character($key);
    list($group,$position)=explode(';',$key);
    $job_name=$GLOBALS['jobs'][$character->jobid]->name;
    $personality_name=$GLOBALS['personalities'][$character->personalityid]->name;

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
    }
else
    $actor='';

//Build an actor list
$actor_list=array();
foreach($team->characters as $index=>$member)
    $actor_list[$index]=$member->name;

//Build item list
$item_list=array();
$full_item_list=array();
$qty_list=array();
$spell_list=array();
$skill_list=array();
$action_list=array();

if(isset($character))
    {
    //Build item list
    foreach($character->inventory as $index=>$item)
        {
        $myitem=&$GLOBALS['items'][$item['item']];
        if($myitem->effect!=0)
            $item_list[]=array($index,$myitem->name,$myitem->useable_in_field());
        $full_item_list[]=array($index,$myitem->name,true);
        $qty_list[]=$item['qty'];
        }

    //Build spell list
    if(isset($character))
        foreach($character->abilities as $index=>$ability)
            {
            $myability=&$GLOBALS['abilities'][$ability];
            if($myability->type==1) //is skill
                $skill_list[]=array($index,"{$myability->name} ($myability->mp_used)",$myability->useable_in_field() & ($character->current['MP']>=$myability->mp_used));
            else //is spell
                $spell_list[]=array($index,"{$myability->name} ($myability->mp_used)",$myability->useable_in_field() & ($character->current['MP']>=$myability->mp_used));
            }

    //Build action list
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
<?php
if(isset($group))
    echo "var character=party.groups[$group].characters[$position];\n";
?>

var action=null;
var ability=null;
var range=null;
var effect=null;
var group=null;

function select_action(object)
    {
    action=parseInt(object.value);
    document.form_data.desc.value='';
    switch(action)
        {
        case COMMAND_USE_ITEM: //Use Item
            build_list(document.form_data.ability,item_list);
            break;
        case COMMAND_CAST_SPELL: //Cast Spell
            build_list(document.form_data.ability,spell_list);
            break;
        case COMMAND_USE_SKILL: //Use Skill
            build_list(document.form_data.ability,skill_list);
            break;
        case COMMAND_GIVE_ITEM: //Give Item
        case COMMAND_SELL_ITEM: //Sell Item
            build_list(document.form_data.ability,full_item_list);
            break;
        }
    document.form_data.ability.style.visibility='visible';
    document.getElementById('qty_line').style.visibility='hidden';
    document.form_data.group.style.visibility='hidden';
    document.form_data.character.style.visibility='hidden';
    document.form_data.DO_ACTION.style.visibility='hidden';
    }

function build_list(object,data_list)
    {
    while (object.firstChild)
        object.removeChild(object.firstChild);

    var select=null;
    for(index=0;index<data_list.length;index++)
        {
        select=document.createElement('option');
        select.style.color=(data_list[index][2]?'black':'red');
        select.value=data_list[index][0];
        select.appendChild(document.createTextNode(data_list[index][1]));
        object.appendChild(select);
        }
    }

function select_ability(object)
    {
    ability=parseInt(object.value);
    switch(action)
        {
        case COMMAND_USE_ITEM: //Use Item
            document.form_data.desc.value=items[character.inventory[ability].item].description;
            range=items[character.inventory[ability].item].use_targets;
            effect=items[character.inventory[ability].item].effect;
            break;
        case COMMAND_CAST_SPELL: //Cast Spell
        case COMMAND_USE_SKILL: //Use Skill
            document.form_data.desc.value=abilities[character.abilities[ability]].description;
            range=abilities[character.abilities[ability]].targets;
            effect=abilities[character.abilities[ability]].effect;
            break;
        case COMMAND_GIVE_ITEM: //Give Item
            //Show the qty line
            document.form_data.qty.value=character.inventory[ability].qty;
            document.getElementById('qty_line').style.visibility='visible';
            document.form_data.desc.value=items[character.inventory[ability].item].description;
            range=0;
            break;
        case COMMAND_SELL_ITEM: //Sell Item
            //Show the qty line
            document.form_data.qty.value=character.inventory[ability].qty;
            document.getElementById('qty_line').style.visibility='visible';
            document.form_data.desc.value=items[character.inventory[ability].item].description;
            range=-2;
            break;
        }
    if(action==COMMAND_GIVE_ITEM
        || (range>-2 && is_field_effect(effect)==true && (action==COMMAND_USE_ITEM || character.current.MP>=abilities[character.abilities[ability]].mp_used)))
        {
        var data_list=[];
        for(index=0;index<party.groups.length;index++)
            data_list.push([index,party.groups[index].name,(action==COMMAND_GIVE_ITEM | must_be_alive(effect)==false | group_is_alive(index))]);
        build_list(document.form_data.group,data_list);
        document.form_data.group.style.visibility='visible';
        document.form_data.DO_ACTION.style.visibility='hidden';
        }
    else if (action==COMMAND_SELL_ITEM
        || (is_field_effect(effect)==true && (action==COMMAND_USE_ITEM || character.current.MP>=abilities[character.abilities[ability]].mp_used)))
        {
        document.form_data.group.style.visibility='hidden';
        document.form_data.DO_ACTION.style.visibility='visible';
        }
    else
        {
        document.form_data.group.style.visibility='hidden';
        document.form_data.DO_ACTION.style.visibility='hidden';
        }
    document.form_data.character.style.visibility='hidden';
    }

function must_be_alive(effect)
    {
    switch(effect)
        {
        case 1: //Heal
        case 2: //Hurt
        case 4: //Slay
        case 5: //Increase Stats
        case 6: //Decrease Stats
        case 7: //Steal Stats
        case 8: //Cause Good Status
        case 9: //Remove Good Status
        case 10: //Cause Bad Status
        case 11: //Remove Bad Status
        case 12: //Restore MP
            return true;
        }
    return false;
    }

function is_field_effect(effect)
    {
    switch(effect)
        {
        case 1: //Heal
        case 3: //Revive
        case 11: //Remove Bad Status
        case 12: //Restore MP
            return true;
        }
    return false;
    }

function group_is_alive(group)
    {
    sum=0;
    for(index=0;index<party.groups[group].characters.length;index++)
        sum+=party.groups[group].characters[index].current.HP;
    return sum>0;
    }

function select_group(object)
    {
    if(action!=COMMAND_GIVE_ITEM && must_be_alive(effect) && group_is_alive(parseInt(object.value))==false)
        return false;
    group=parseInt(object.value);
    if(action==COMMAND_GIVE_ITEM || (range>-1 && is_field_effect(effect)==true))
        {
        var data_list=[];
        for(index=0;index<party.groups[group].characters.length;index++)
            data_list.push([index,party.groups[group].characters[index].name,(action==COMMAND_GIVE_ITEM | must_be_alive(effect)==false | party.groups[group].characters[index].current.HP>0)]);
        build_list(document.form_data.character,data_list);
        document.form_data.character.style.visibility='visible';
        document.form_data.DO_ACTION.style.visibility='hidden';
        }
    else if (is_field_effect(effect)==true)
        {
        document.form_data.character.style.visibility='hidden';
        document.form_data.DO_ACTION.style.visibility='visible';
        }
    else
        {
        document.form_data.character.style.visibility='hidden';
        document.form_data.DO_ACTION.style.visibility='hidden';
        }
    }

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
<form method="get">
  <table>
    <tr>
      <td><?php make_select("actor",$actor,$actor_list,array('size'=>4));?></td>
    </tr>
    <tr>
      <td><input type="submit" name="PICK_ACTOR" value="Select a hero"></td>
    </tr>
  </table>
</form>
<form method="post" name="form_data">
    <table>
<?php if (isset($character))
    { ?>
      <caption><b><?php echo $character->name; ?> - Level <?php echo $character->level; ?> <?php echo $job_name; ?></b></caption>
<?php
    } ?>
      <tr>
<?php if (isset($character))
    { ?>
        <td>
          <table width="100%">
            <tr>
              <th align="left">Action</th>
            </tr>
            <tr>
              <td><?php make_select("action",'',$action_list,array('size'=>5,'onkeypress'=>'select_action(this);','onclick'=>'select_action(this);'));?></td>
              <td><?php make_select("ability",'',$fake_list,array('size'=>5,'style'=>'visibility:hidden','onkeypress'=>'select_ability(this);','onclick'=>'select_ability(this);'));?></td>
              <td><?php make_select("group",'',$fake_list,array('size'=>3,'style'=>'visibility:hidden','onkeypress'=>'select_group(this);','onclick'=>'select_group(this);'));?></td>
              <td><?php make_select("character",'',$fake_list,array('size'=>4,'style'=>'visibility:hidden','onkeypress'=>'select_character(this);','onclick'=>'select_character(this);'));?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr id="qty_line" style="visibility:hidden">
        <td>
          <table width="100%">
            <tr>
              <th>Quantity</th>
              <td><input name="qty" value="1"></td>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table width="100%">
            <tr>
              <th align="left">Description:</th>
              <td><input type="submit" name="DO_ACTION" value="Do action" style="visibility:hidden"></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td><textarea name="desc" cols="35" rows="4"></textarea></td>
      </tr>
    </table>
  </th></tr>
  <tr>
<?php
    } ?>
    <td>
      <table>
        <tr><th></th><th>HP</th><th>MP</th></tr>        
        <?php
        foreach(array_keys($team->characters) as $index)
            {
            $mychar=&$team->characters[$index];
            echo "<tr><th>{$mychar->name}</th><td align=\"center\">{$mychar->current['HP']} / {$mychar->base['HP']}</td><td align=\"center\">{$mychar->current['MP']} / {$mychar->base['MP']}</td></tr>";
            }
        ?>
      </table>
    </td>
  </tr>
</table>
<input type="submit" name="GO_BACK" value="Return To Last Menu">
</form>
</body>
</html>
