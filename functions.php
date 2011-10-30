<?php
function display_team(&$team,$images)
    {
    echo "<table><tr>\n";
    $pxp=$team->get_pxp();
    echo "<td><table>
<tr><th>Team</th><td>{$team->name}</td></tr>
<tr><th>Gold</th><td>{$team->gold}</td></tr>
<tr><th>PXP</th><td>$pxp</td></tr>
<tr><td></td></tr>
</table></td>\n";
    foreach(array_keys($team->characters) as $index)
        {
        echo "<td align=\"center\"><a href=\"setup_fight.php?teamid={$team->teamid}\">\n";
        display_hero($team->characters[$index],$images);
        echo "</a></td>\n";
        }
    echo "</tr></table>\n";
    }

function display_hero(&$character,$images)
    {
    echo <<<EOD
<table>
  <tr><td align="center"><img style="height:90px;width:90px;border:0px none" src="{$images}{$GLOBALS['personalities'][$character->personalityid]->base_data['images'][0]}" /></td></tr>
  <tr><td align="center"><b>{$character->name}</b></td></tr>
  <tr><td align="center"><b>{$GLOBALS['jobs'][$character->jobid]->name}</b></td></tr>
  <tr><td align="center"><b>Level {$character->level}</b></td></tr>
</table>

EOD;
    }

function display_hero_tile(&$party,&$character,$group,$position,$fighter_images,$icon_images)
    {
    $right='';
    $left='';
    $up='';
    $down='';

    if($position>0)
        $left="<a href=\"setup_fight.php?group=$group&position=$position&direction=l\"><img style=\"height:23px;width:23px;border:0px none\" src=\"{$icon_images}biglast.png\" /></a>";

    if($position<$party->groups[$group]->count()-1)
        $right="<a href=\"setup_fight.php?group=$group&position=$position&direction=r\"><img style=\"height:23px;width:23px;border:0px none\" src=\"{$icon_images}bignext.png\" /></a>";

    if($group<2)
        {
        $nextgroup=$group+1;
        while($nextgroup<=2
            && isset($party->groups[$nextgroup])
            && $party->groups[$nextgroup]->count()>=GROUP_MAX_COUNT)
            $nextgroup++;
        if($nextgroup<=2)
            $down="<a href=\"setup_fight.php?group=$group&position=$position&direction=d\"><img style=\"height:23px;width:23px;border:0px none\" src=\"{$icon_images}bigdown.png\" /></a>";
        }

    if($group>0)
        {
        $nextgroup=$group-1;
        while($nextgroup>=0
            && isset($party->groups[$nextgroup])
            && $party->groups[$nextgroup]->count()>=GROUP_MAX_COUNT)
            $nextgroup--;
        if($nextgroup>=0)
            $up="<a href=\"setup_fight.php?group=$group&position=$position&direction=u\"><img style=\"height:23px;width:23px;border:0px none\" src=\"{$icon_images}bigup.png\" /></a>";
        }

    echo <<<EOD
  <table cellpadding="0" cellspacing="0" width="100%">
    <tr><td></td><td colspan="2" align="center" valign="center">{$up}</td><td></td></tr>
    <tr><td align="center" valign="center">{$left}</td>
    <td>
      <a href="setup_fight.php?group=$group&position=$position&equip=YES" style="text-decoration:none">
        <table cellpadding="0" cellspacing="0">
          <tr>
            <td>
              <table cellpadding="0" cellspacing="0">
              <tr><td>
                <a href="setup_fight.php?group=$group&position=$position&equip=YES" style="text-decoration:none">
                  <img style="height:60px;width:60px;border:0px none" src="{$fighter_images}{$GLOBALS['personalities'][$character->personalityid]->base_data['images'][0]}" /></td></tr>
                </a>
              </table>
            </td>
            <td>
              <table>
                <tr><td colspan="2" align="center"><b>{$character->name}</b></td></tr>
                <tr><th>HP</th><td>{$character->current['HP']} / {$character->base['HP']}</td></tr>
                <tr><th>MP</th><td>{$character->current['MP']} / {$character->base['MP']}</td></tr>
              </table>
            </td>
          </tr>
        </table>
      </a>
    </td>
    <td align="center" valign="center">{$right}</td></tr>
    <tr><td></td><td colspan="2" align="center" valign="center">{$down}</td><td></td></tr>
  </table>

EOD;
    }
?>
