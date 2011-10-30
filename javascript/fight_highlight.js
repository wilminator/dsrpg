var highlight_last_hero_party=null;
var highlight_last_hero_group=null;
var highlight_last_hero_individual=null;

function highlight_hero_range(party,group,individual)
    {
    if(menu_state!=MENU_STATE_FIGHT_PLAYER && menu_state!=MENU_STATE_FIGHT_TARGET)
        return false;
    var index;
    var retval=false;
    var count=get_group_length(party,group);
    var start=(target_range>-1)?individual-target_range:0;
    if (start<0) start=0;
    var end=(target_range>-1)?individual+target_range+1:count;
    if (end>=count)
        end=count;
    for(index=start;index<end;++index)
        if(get_hero(party,group,index).current.HP>0 || target_dead==1)
            retval=retval|highlight_hero(party,group,index,individual);
    if(highlight_last_hero_party!=party
        || highlight_last_hero_group!=group
        || highlight_last_hero_individual!=individual)
        {
        highlight_last_hero_party=party;
        highlight_last_hero_group=group;
        highlight_last_hero_individual=individual;
        play_sound(audio_clips['hover']);
        }
    return retval;
    }

function highlight_hero(party,group,individual,center)
    {
    if(menu_state!=MENU_STATE_FIGHT_PLAYER && menu_state!=MENU_STATE_FIGHT_TARGET)
        return false;
    var object=get_hero(party,group,individual).html,color,rgb;
    show_hp(party,group,individual);
    var range=target_range+1;
    if(target_range>=0)
        color=Math.floor(128*(range-Math.abs(individual-center))/range)+127;
    else
        color=255;
    if(curr_hero && curr_hero.join()==[group,individual].join() && party==player_party)
        {
        rgb='rgb('+color+','+color+','+Math.floor(color/2)+')';
        object_border(object.stats,'solid 2px '+rgb);
        object_border(object.pic,'solid 2px '+rgb);
        if(object.actionbox)
            object_border(object.actionbox,'solid 2px '+rgb);
        }
    else
        {
        rgb='rgb('+color+','+color+','+color+')';
        if(object.actionbox)
            object_border(object.actionbox,'solid 2px '+rgb);
        if (object.stats)
            object_border(object.stats,'solid 2px '+rgb);
        object_border(object.pic,'solid 2px '+rgb);
        }
    set_stats_zindex(party,group,individual,true);
    return true;
    }


function unhighlight_hero_range(party,group,individual)
    {
    if(menu_state!=MENU_STATE_FIGHT_PLAYER && menu_state!=MENU_STATE_FIGHT_TARGET)
        return false;
    var index;
    var center=individual;
    var count=get_group_length(party,group);
    var start=(target_range>-1)?center-target_range:0;
    if (start<0) start=0;
    var end=(target_range>-1)?center+target_range+1:count;
    if (end>=count)
        end=count;
    for(index=start;index<end;++index)
        if(get_hero(party,group,index).current.HP>0|| target_dead==1)
            unhighlight_hero(party,group,index);
    }

function unhighlight_hero(party,group,individual)
    {
    if(menu_state!=MENU_STATE_FIGHT_PLAYER && menu_state!=MENU_STATE_FIGHT_TARGET)
        return false;
    hide_hp(party,group,individual);
    var object=get_hero(party,group,individual).html;
    if(curr_hero && curr_hero.join()==[group,individual].join() && party==player_party)
        {
        object_border(object.stats,'solid 2px yellow');
        object_border(object.pic,'solid 2px yellow');
        if(object.actionbox)
            object_border(object.actionbox,'solid 2px yellow');
        }
    else
        {
        if(object.stats)
            object_border(object.stats,'solid 2px rgb(96,96,160)');
        if(object.actionbox)
            object_border(object.actionbox,'solid 2px rgb(96,96,160)');
        object_border(object.pic,'none');
        }
    set_stats_zindex(party,group,individual,false);
    }

function highlight_hero_group(party,group)
    {
    if(menu_state!=MENU_STATE_FIGHT_PLAYER && menu_state!=MENU_STATE_FIGHT_TARGET)
        return false;
    var object=get_group(party,group).html.group;
    object_border(object,'solid 8px white');
    if(highlight_last_hero_party!=party
        || highlight_last_hero_group!=group
        || highlight_last_hero_individual!=null)
        {
        highlight_last_hero_party=party;
        highlight_last_hero_group=group;
        highlight_last_hero_individual=null;
        play_sound(audio_clips['hover']);
        }
    }

function unhighlight_hero_group(party,group)
    {
    if(menu_state!=MENU_STATE_FIGHT_PLAYER && menu_state!=MENU_STATE_FIGHT_TARGET)
        return false;
    var object=get_group(party,group).html.group;
    object_border(object,(party==player_party?'solid 8px rgb(0,0,128)':'solid 8px rgb(128,0,0)'));
    }

function set_stats_zindex(party,group,character,mouseover)
    {
    if(party==player_party)
        {
        var person=get_hero(party,group,character);
        set_player_window_zindex(party,group,character,mouseover,person.html.stats);
        if(person.html.actionbox)
            {
            set_player_window_zindex(party,group,character,mouseover,person.html.actionbox);
            }
        }
    }

function set_player_window_zindex(party,group,character,mouseover,object)
    {
    if(curr_hero && curr_hero.join()==[group,character].join())
        {
        if(mouseover)
            object_z(object,207);
        else
            object_z(object,(group==player_slide.newgroup?207:201));
        }
    else
        {
        if(mouseover)
            object_z(object,206);
        else
            object_z(object,(group==player_slide.newgroup?205:200));
        }
    }
