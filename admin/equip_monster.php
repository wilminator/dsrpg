<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','EDIT_MONSTER','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');


define ('INCLUDE_DIR','../include/');
require_once INCLUDE_DIR.'monster_store.php';
require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'effects.php';
require_once INCLUDE_DIR.'array.php';

$monster_store=new MONSTER_STORE;


#Break down the monster's items and equipment.
$monsterindex=$_REQUEST['monster'];
$monster=$monster_store->get_monster($monsterindex);

$status='';

#See if this is an update.
if(isset($_POST['item0']))
    {
    //Build list of new inventory.
    $inventory=array();
    $equipment=array();
    //Make a Character to test the equipping of items.
    #($name,$stats,$inventory,$equipment,$abilities,$personalityid)
    $character=monster($monster->name,$monster->stats,array(),
        array(),$monster->abilities,$monster->personalityid,$monster->gold,
        $monster->ai_action,$monster->ai_goal,$monster->ai_target,$monster->ai_experience);
    $new_equip=false;
    for($index=0;$index<11;$index++)
        if($_POST["item$index"]!=0 && $_POST["qty$index"]!=0)
            {
            $result=$character->add_item($_POST["item$index"],$_POST["qty$index"]);
            if($result===true)
                {
                $status.="Item $index ({$_POST["item$index"]}) could not be added.<br>";
                continue;
                }
            $inv_index=count($inventory);
            $inventory[]=array('item'=>$_POST["item$index"],'qty'=>$_POST["qty$index"]);
            //echo "{$_POST["qty$index"]} {$_POST["item$index"]} in slot $index<br>";
            if($_POST["armed$index"]=='Yes'&&!isset($_POST["equip$index"]))
                {
                $equipment[]=array('slot'=>$inv_index,'side'=>$_POST["side$index"]);
                //echo "Re-equipping item $index<br>";
                }
            if($_POST["armed$index"]=='No'&&isset($_POST["equip$index"]))
                {
                $new_equip=$inv_index;
                $new_index=$index;
                }
            }
        else
            {
            //echo "Nothing in slot $index<br>";
            }

    if($new_equip!==false)
        {
        $equipment[]=array('slot'=>$new_index,'side'=>$_POST["side$new_index"]);
        //echo "Equipping item $new_index as new equipment.<br>";
        }

   	$equip_stack=$equipment;
    //Try equipping stuff.
    $good_stack=array();
    $retry_stack=array();
    while(count($equip_stack))
        {
        $lucky_item=array_shift($equip_stack);
        do {
            $result=$character->equip_item($lucky_item['slot'],$lucky_item['side']);
            if($result===true)
                {
                //This ammo failed to equip.  Add it to retry stack.
                array_push($retry_stack,$lucky_item);
                $status.="Can't equip {$items[$inventory[$lucky_item['slot']]['item']]->name} yet- will retry.<br>";
                }
            elseif($result===false)
                {
                //This item is unequipable..
                $status.="Can't equip {$items[$inventory[$lucky_item['slot']]['item']]->name}: non-equipable item.<br>";
                }
            elseif(is_array($result))
                {
                //Array means it worked.  Add it to success stack.
                array_push($good_stack,$lucky_item);
                $status.="Equipped {$items[$inventory[$lucky_item['slot']]['item']]->name} successfully.<br>";
                }
            else
                {
                //This integer is the item to be unequipped in order for this to work.
                //Remove this item from the success stack and try again.
                foreach(array_keys($good_stack) as $index)
                    if($good_stack[$index]['slot']===$result)
                        array_splice($good_stack,$index,1);
                $character->unequip_item($result);
                $status.="Had to unequip {$items[$inventory[$result]['item']]->name} in order to try to equip {$items[$inventory[$lucky_item['slot']]['item']]->name}.<br>";
                continue;
                }
            break;
            } while (true);
        }
    //Now retry ammo.
    while(count($retry_stack))
        {
        $lucky_item=array_shift($retry_stack);
        do {
            $result=$character->equip_item($lucky_item['slot'],$lucky_item['side']);
            if($result===true)
                {
                //This ammo failed to equip.  Lose it.
                $status.="Can't equip {$items[$inventory[$lucky_item['slot']]['item']]->name} because no equipped weapon can use it.<br>";
                }
            elseif(is_array($result))
                {
                //Array means it worked.  Add it to success stack.
                array_push($good_stack,$lucky_item);
                $status.="Equipped {$items[$inventory[$lucky_item['slot']]['item']]->name} successfully.<br>";
                }
            else
                {
                //This integer is the item to be unequipped in order for this to work.
                //Remove this item from the success stack and try again.
                foreach(array_keys($good_stack) as $index)
                    if($good_stack[$index]['slot']==$result)
                        array_splice($good_stack,$index,1);
                $character->unequip_item($result);
                $status.="Had to unequip {$items[$inventory[$result]['item']]->name} in order to try to equip {$items[$inventory[$lucky_item['slot']]['item']]->name}.<br>";
                continue;
                }
            break;
            } while (true);
        }
    //Good_stack is the new equipment list.
    $monster->equipment=$good_stack;
    //Update the inventory.
    $monster->items=$inventory;
    //Update the monster
    $monster_store->set_monster($monster,$monsterindex);
    }

#If this is a continue command, then goto the init_fight page.
if(isset($_POST['GO_BACK']))
    {
    header("Location: edit_monster.php?monster=$monsterindex");
    exit;
    }

#Generate HTML variables
$character=$monster->make_monster();
$pxp=$character->calculate_pxp();
$inventory=$monster->items;
$equipment=$monster->equipment;

#Item list
foreach(array_keys($items) as $index)
    $item_list[$index]=$items[$index]->name;
ksort($item_list);
#Equipped list
foreach($equipment as $index=>$info)
    $equipped[$info['slot']]=$info['side'];
#Side list
$side_list=array('0'=>'Left','1'=>'Right');

//Default input box size
$def_size=array('size'=>3);
?>
<form method="post">
<table>
  <tr><th>
    <table cellpadding="8">
      <caption><b>Monster Equipment Editor:</b></caption>
      <tr>
        <th>Name</th>
        <th>Gold</th>
        <th>PXP</th>
        <th>Exchange Status</th>
      </tr>
      <tr>
        <td><?php echo $monster->name; ?></td>
        <td><?php echo $monster->gold; ?></td>
        <td><?php echo $pxp; ?></td>
        <td><?php echo $status; ?></td>
      </tr>
    </table>
  </th></tr>
  <tr><td>
    <table cellpadding="8">
<?php
      echo "<tr>\n";
      foreach($GLOBALS['character_stats'] as $key)
          echo "<th>$key</th>\n";
      echo "</tr>\n<tr>\n";
      foreach($GLOBALS['character_stats'] as $key)
          {
          echo "<td align=\"center\">";
          if(in_array($key,array('HP','MP')))
              echo $character->current[$key].'/'.$character->get_base($key);
          else
              echo $character->base[$key];
          echo "</td>\n";
          }
      echo "</tr>\n<tr>\n";
      foreach($GLOBALS['character_stats'] as $key)
          {
          $value = $character->base[$key];
          echo "<td>";
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
                  && $character->equipment['lhand']!=$character->equipment['rhand'])
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
  </td></tr>
  <tr><th>
    <table>
      <tr>
        <th>Item</th>
        <th>Qty</th>
        <th>Equipped?</th>
        <th>Side</th>
        <th></th>
        <th>Info</th>
      </tr>
  <?php
    for($index=0;$index<CHARACTER_MAX_ITEMS;$index++)
        {
        if(isset($inventory[$index]))
            $iteminfo=$inventory[$index];
        else
            $iteminfo=array('item'=>0,'qty'=>0);
        $itemid=$iteminfo['item'];
        if($itemid>0)
            $desc='<b>Equipment:</b> '.$items[$itemid]->describe_equip().
                '<br><b>When used:</b> '.$items[$itemid]->describe_use();
        else
            $desc='';
        $qty=$iteminfo['qty'];
        if(isset($equipped[$index]))
            {
            $equipped_where=$equipped[$index];
            $is_equipped='Yes';
            $edit_side=array('size'=>'1','readonly'=>'readonly');
            $label='Unequip';
            }
        else
            {
            $equipped_where=0;
            $is_equipped='No';
            $edit_side=array('size'=>'1');
            $label='Equip';
            }
        ?>
      <tr>
        <td><?php make_select("item$index",$itemid,$item_list); ?></td>
        <td><?php make_input("qty$index",$qty,$def_size); ?></td>
        <td><?php make_input("armed$index",$is_equipped,array('readonly'=>'readonly','size'=>3)); ?></td>
        <td><?php make_select("side$index",$equipped_where,$side_list); ?></td>
        <td><?php make_input("equip$index",$label,array('type'=>'submit')); ?></td>
        <td><?php echo $desc; ?></td>
      </tr>
        <?php
        }
  ?>
    </table>
  </th></tr>
  <tr><th>
    <table>
      <tr>
        <td><input type="submit" name="OP" value="Refresh Stats"></td>
        <td><input type="submit" name="GO_BACK" value="Return To Last Menu"></td>
      </tr>
    </table>
  </th></tr>
</table>
</form>
