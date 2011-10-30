function set_objects(name,cntarray)
    {
    var index,index2;
    var array=Array();
    for(index=0;index<cntarray.groups.length;++index)
        {
        array[index]=Array();
        for(index2=0;index2<cntarray.groups[index].characters.length;++index2)
            array[index][index2]=object_get(name+index.toString()+index2.toString());
        }
    return array;
    }

function get_picture_x(div)
    {
    var object=div.firstChild;
    while(object && !(object.width>0))
        object=object.nextSibling;
    if(!object)
       return 0;
    return parseInt(object.width);
    }

function get_picture_y(div)
    {
    var object=div.firstChild;
    while(object && !object.width) object=object.nextSibling;
    if(!object)
       return 0;
    return parseInt(object.height);
    }

function ratio_slide(x1,x2,p)
    {
    return x1+(x2-x1)*p;
    }

function get_hero(party,group,character,check)
    {
    var group_obj=get_group(party,group);
    if(!group_obj.characters[character] && !check)
        //Log error on server
        data_request('client_error','Bad character',{party:party,group:group,character:character,fight:fight});
    return group_obj.characters[character];
    }

function get_group(party,group,check)
    {
    var party_obj=get_party(party);
    if(!party_obj.groups[group] && !check)
        //Log error on server
        data_request('client_error','Bad group',{party:party,group:group,fight:fight});
    return party_obj.groups[group];
    }

function get_group_length(party,group)
    {
    return get_object_length(get_group(party,group).characters);
    }

function get_party(party,check)
    {
    if(party==-1)
        {
        return mapparty;
        }
    if(!fight.parties[party] && !check)
        //Log error on server
        data_request('client_error','Bad party',{party:party,fight:fight});
    return fight.parties[party];
    }

function get_party_length(party)
    {
    return get_object_length(get_party(party).groups);
    }

function get_fight_length()
    {
    if(fight==null) return -1;
    return get_object_length(fight.parties);
    }

function get_object_length(object)
    {
    var index=0;
    for(var o in object)
        index++;
    return index;
    }

function hide_cause_is_dead(party,group,character)
    {
    var hero=get_hero(party,group,character);
    if(hero.html.img)
        {
        object_hide(hero.html.img);
        //object_opacity(hero.html.img,10);
        //object_z(hero.html.pic,0);
        //hero.html.pic.style.zIndex='0';
        }
    }

function show_cause_is_alive(party,group,character)
    {
    var hero=get_hero(party,group,character);
    if(hero.html.img)
        {
        object_default_visibility(hero.html.img);
        //object_opacity(hero.html.img,100);
        //object_z(hero.html.pic,find_z(hero.html.pic));
        }
    }

function only_living_targets($effect)
    {
    switch($effect)
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

function is_effect_bad(effect)
    {
    switch(effect)
        {
        /*
        case 1:  //Heal
        case 3:  //Revive
        case 5:  //Increase Stats
        case 8:  //Cause Good Status
        case 11: //Remove Bad Status
        case 12: //Restore MP
            return false;
        */
        case 2: //Hurt
        case 4:  //Slay
        case 6:  //Decrease Stats
        case 7:  //Steal Stats
        case 9:  //Remove Good Status
        case 10: //Cause Bad Status
            return true;
        }
    return false;
    }

function in_array(needle,haystack)
    {
    var index;
    for(index in haystack)
        if(haystack[index]==needle)
            return true;
    return false;
    }
