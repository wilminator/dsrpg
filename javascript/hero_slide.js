var HERO_STATS_SMALL=45;
var HERO_STATS_LARGE=98;

var hero_move_stats=false;

var player_slide={oldparty:0,oldgroup:0,newparty:0,newgroup:0,nextgroup:0,nextparty:null,groupoffset:0,slidetimeout:false,slidehook:false};
var opponent_slide={oldparty:0,oldgroup:0,newparty:0,newgroup:0,nextgroup:0,nextparty:null,groupoffset:0,slidetimeout:false,slidehook:false};

function hero_center(party,group)
    {
    var count=get_group_length(party,group);
    var character,hero;
    for(character=0;character<count;++character)
        {
        hero=get_hero(party,group,character);
        object_x(hero.html.pic,hero.x_offset);
        }
    var varstore=(party==player_party?player_slide:opponent_slide);
    varstore.oldgroup=group;
    varstore.newgroup=group;
    varstore.nextgroup=group;
    varstore.oldparty=party;
    varstore.newparty=party;
    varstore.nextparty=party;

    var index;
    for(index=0;index<get_party_length(party);++index)
        object_background_color(get_group(party,index).html.group,
            (index==varstore.oldgroup)?(party==player_party?'rgb(128,0,0)':'rgb(0,0,128)'):'black');
    }

function hero_slide_center(party,group)
    {
    var varstore=(party==player_party?player_slide:opponent_slide);
    var small=(party==player_party && get_party(party).monster);
    if(varstore.nextparty==party && varstore.nextgroup==group)
        return;
    if(small && varstore.nextgroup!=null)
        {
        for(character=0;character<get_group_length(player_party,varstore.nextgroup);character++)
            object_hide(get_hero(player_party,varstore.nextgroup,character).html.actionbox);
        }
    if(varstore.nextparty!=null)
        object_background_color(get_group(party,varstore.nextgroup).html.group,'black');
    varstore.nextgroup=group;
    object_background_color(get_group(party,varstore.nextgroup).html.group,
        (party==player_party?'rgb(128,0,0)':'rgb(0,0,128)'));
    if(varstore.slidetimeout==false)
        {
        start_event();
        slide_direction(varstore);
        slide_hero(party);
        }
    if(small&&menu_state!=MENU_STATE_FIGHT_NONE&&menu_state!=MENU_STATE_FIGHT_WAIT)
        {
        for(character=0;character<get_group_length(player_party,group);character++)
            object_show(get_hero(player_party,group,character).html.actionbox);
        }
    }

function slide_out(group)
    {
    if(fight.parties[player_party].monster)
        {
        for(character=0;character<get_group_length(player_party,group);character++)
            object_show(get_hero(player_party,group,character).html.actionbox);
        }
    else
        {
        hero_move_stats=true;
        if(player_slide.slidetimeout==false)
            slide_out_loop(group);
        else
            player_slide.slidehook='slide_out_loop('+group.toString()+')';
        }
    }

function slide_out_loop(group)
    {
    if(player_slide.groupoffset==0||player_slide.groupoffset==-800||player_slide.groupoffset==0)
        player_slide.groupoffset=800;
    player_slide.groupoffset-=80;
    player_slide.oldgroup=group;
    slide_hero_stats(group,1-(player_slide.groupoffset/800));
    if(player_slide.groupoffset>0)
        player_slide.slidetimeout=setTimeout('slide_out_loop('+group.toString()+')',50);
    else
        player_slide.slidetimeout=false
    }

function slide_in(group)
    {
    if(fight.parties[player_party].monster)
        {
        for(character=0;character<get_group_length(player_party,group);character++)
            object_hide(get_hero(player_party,group,character).html.actionbox);
        }
    else
        {
        hero_move_stats=false;
        if(player_slide.slidetimeout==false)
            slide_in_loop(group);
        else
            player_slide.slidehook='slide_in_loop('+group.toString()+')';
        }
    }

function slide_in_loop(group)
    {
    if(player_slide.groupoffset==0||player_slide.groupoffset==-800||player_slide.groupoffset==0)
        player_slide.groupoffset=800;
    player_slide.groupoffset-=80;
    slide_hero_stats(group,player_slide.groupoffset/800);
    if(player_slide.groupoffset>0)
        player_slide.slidetimeout=setTimeout('slide_in_loop('+group.toString()+')',50);
    else
        player_slide.slidetimeout=false
    }

function slide_hero_stats(group,ratio)
    {
    for(index=0;index<get_group_length(player_party,group);++index)
        {
        var object=get_hero(player_party,group,index).html.stats;
        object_y(object,ratio_slide(10+47*player_slide.oldgroup,350,ratio));
        object_height(object,ratio_slide(HERO_STATS_SMALL,HERO_STATS_LARGE,ratio));
        if (curr_hero && curr_hero.join==[group,index].join())
            zindex++;
        set_stats_zindex(player_party,group,index,false);
        }
    }

function slide_hero(party)
    {
    var count,x_offset,index,hero,shift_offset;
    var varstore=(party==player_party?player_slide:opponent_slide);

    if(varstore.groupoffset!=0)
        {
        varstore.slidetimeout=setTimeout('slide_hero('+party.toString()+');',50);
        if(varstore.groupoffset>0)
            {
            varstore.groupoffset-=80;
            ratio=varstore.groupoffset/800;
            shift_offset=-800;
            }
        else
            {
            varstore.groupoffset+=80;
            ratio=varstore.groupoffset/-800;
            shift_offset=800;
            }

        for(index=0;index<get_group_length(varstore.oldparty,varstore.oldgroup);++index)
            {
            hero=get_hero(varstore.oldparty,varstore.oldgroup,index);
            object_x(hero.html.pic,(hero.x_offset+varstore.groupoffset+shift_offset));
            }
        if(hero_move_stats && party==player_party)
            slide_hero_stats(varstore.oldgroup,ratio,200);

        for(index=0;index<get_group_length(varstore.newparty,varstore.newgroup);++index)
            {
            hero=get_hero(varstore.newparty,varstore.newgroup,index);
            object_x(hero.html.pic,(hero.x_offset+varstore.groupoffset));
            }
        if(hero_move_stats && party==player_party)
            slide_hero_stats(varstore.newgroup,1-ratio,205);
        return;
        }
    else
        {
        varstore.oldgroup=varstore.newgroup;
        varstore.oldparty=varstore.newparty;
        varstore.slidetimeout=false;
        if(varstore.slidehook)
            {
            setTimeout(varstore.slidehook,50);
            varstore.slidehook=false;
            }
        if (varstore.nextgroup!=varstore.oldgroup||varstore.nextparty!=varstore.oldparty)
            {
            play_sound(audio_clips['select']);
            slide_direction(varstore);
            slidetimeout=setTimeout('slide_hero('+party.toString()+');',50);
            }
        else
            finish_event();
        }
    }

function slide_direction(varstore)
    {
    var v1;
    var v2;
    varstore.newgroup=varstore.nextgroup;
    varstore.newparty=varstore.nextparty;
    if (varstore.newparty==varstore.oldparty)
        {
        v1=((varstore.newgroup-varstore.oldgroup)+get_party_length(varstore.newparty))%get_party_length(varstore.newparty);
        v2=get_party_length(varstore.newparty)/2;
        }
    else
        {
        v1=((varstore.newparty-varstore.oldparty)+fight.parties.length)%fight.parties.length;
        v2=fight.parties.length/2;
        }
    varstore.groupoffset=(v1>v2)?-800:(v1<v2)?800:(Math.random()>=.5)?-800:800;
    }

function expand_hero_stats(index)
    {
    if(!index) index=0;
    ++index;
    if(index<100) setTimeout('expand_hero_stats('+index+')',20);
    size_hero_stats(index);
    }

function shrink_hero_stats(index)
    {
    if(!index) index=100;
    --index;
    if(index>0) setTimeout('shrink_hero_stats('+index+')',20);
    size_hero_stats(index);
    }

function size_hero_stats(index)
    {
    var ratio=index/100.0;
    var index,index2;
    for(index=0;index<get_party_length(-1);++index)
        for(index2=0;index2<get_group_length(-1,index);++index2)
            {
            var object=get_hero(-1,index,index2).html.stats;
            object_y(object,ratio_slide(10+47*index,10+100*index,ratio));
            object_height(object,ratio_slide(HERO_STATS_SMALL,HERO_STATS_LARGE,ratio));
            }
    }