function update_stats(party,group,character)
    {
    var hero=get_hero(party,group,character);
    var small=(party==player_party && get_party(party).monster);
    update_bar(hero.html.hp,hero.html.hpval,hero.current.HP,hero.base.HP,small);
    //init_update_hp(party,group,character); //Ensure the update gets done.
    if(hero.html.mp)
        update_bar(hero.html.mp,hero.html.mpval,hero.current.MP,hero.base.MP,small);
    update_group_box(party,group);
    }

function update_bar(bar,barval,displayed,maximum,small)
    {
    var width=(small)?62:100;
    object_width(bar,(maximum==0)?0:(width*displayed/((maximum==-100)?100:maximum)));
    var output=displayed.toString();
    if(!small && maximum>0)
        output+='/'+maximum.toString();
    barval.firstChild.data=output;
    }

function init_update_hp(party,group,character)
    {
    start_event();
    show_hp(party,group,character);
    update_hp(party,group,character);
    }

function update_hp(party,group,character)
    {
    var hero=get_hero(party,group,character);
    var small=(party==player_party && get_party(party).monster);
    var diff=hero.HPDT-hero.HPD;

    hero.HPD+=(diff>0)?Math.ceil(diff/10):((diff<0)?Math.floor(diff/10):0);

    update_bar(hero.html.hp,hero.html.hpval,hero.HPD,hero.base.HP,small);

    if (hero.HPD!=hero.HPDT)
        setTimeout('update_hp('+party.toString()+','+group.toString()+','+character.toString()+');',50);
    else
       {
        hero.hpdisplay=setTimeout(
            'hide_hp('+party.toString()+','+group.toString()+','+character.toString()+');',1000);
        finish_event();
        }
    }

function init_update_mp(party,group,character)
    {
    start_event();
    update_mp(party,group,character);
    }

function update_mp(party,group,character)
    {
    var hero=get_hero(party,group,character);
    var small=(party==player_party && get_party(party).monster);
    var diff=hero.MPDT-hero.MPD;

    hero.HPD+=(diff>0)?Math.ceil(diff/10):((diff<0)?Math.floor(diff/10):0);

    update_bar(hero.html.mp,hero.html.mpval,hero.MPD,hero.base.MP,small);

    if (hero.MPD!=hero.MPDT)
        setTimeout('update_mp('+party.toString()+','+group.toString()+','+character.toString()+');',50);
    else
        finish_event();
    }

function show_hp(party,group,character)
    {
    var hero=get_hero(party,group,character);
    if (hero.showhp==0) //Then we are not allowed to see the enemy's HP
        {
        return;
        }

    if (hero.hpdisplay)
        {
        clearTimeout(hero.hpdisplay);
        hero.hpdisplay=null;
        }
    //Show the Value only if showhp is 2
    if(hero.showhp>=2)
        object_default_visibility(hero.html.hpval);
    else
        object_hide(hero.html.hpval);
    if(hero.html.dmgbar)
        object_show(hero.html.dmgbar);
    }

function hide_hp(party,group,character)
    {
    var hero=get_hero(party,group,character);
    if(hero.html.dmgbar) //If shohp is 3 then always show hp.
        {
        if(hero.showhp==3)
            object_show(hero.html.dmgbar);
        else
            object_hide(hero.html.dmgbar);
        }
    hero.hpdisplay=null;
    }

function update_group_box(party,group)
    {
    var group_obj=get_group(party,group);
    var alive=group_living_count(party,group);
    if(group_obj.html.group_text)
        {
        group_obj.html.group.title=group_obj.name+' Alive:'+alive.toString();
        group_obj.html.group_text.data=group_obj.name+':'+alive.toString();
        }
    if(alive==0)
        object_opacity(group_obj.html.box,25);
    else
        object_opacity(group_obj.html.box,100);
    }
