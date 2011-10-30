function default_impact_processor(tpindex,tgindex,tcindex,range,events)
    {
    if(range<0)
        return default_group_impact_processor(tpindex,tgindex,tcindex,range,events);
    else
        return default_ranged_impact_processor(tpindex,tgindex,tcindex,range,events);
    }

function default_group_impact_processor(tpindex,tgindex,tcindex,range,events)
    {
    var index;
    for(index in events)
        default_impact_process_event(events[index]);
    return 0;
    }

function filter_events(events,tpindex,tgindex,tcindex)
    {
    var retval=[];
    for(var index in events)
        {
        var type=events[index][0];
        var data=events[index][1];
        var pty=data[0];
        var grp=data[1];
        var chr=data[2];
        if(tpindex==pty&&tgindex==grp&&tcindex==chr)
            retval.push(events[index]);
        }
    return retval;
    }

function default_ranged_impact_processor(tpindex,tgindex,tcindex,range,events)
    {
    var index,index2,tgtevents;
    for(index=0;index<=range;index++)
        {
        tgtevents=filter_events(events,tpindex,tgindex,tcindex-index);
        for(index2 in tgtevents)
            default_impact_process_event(tgtevents[index2]);
        if(index!=0)
            {
            tgtevents=filter_events(events,tpindex,tgindex,tcindex+index);
            for(index2 in tgtevents)
                default_impact_process_event(tgtevents[index2]);
            }
        }
    return 0;
    }

function default_impact_process_event(target)
    {
    var type=target[0];
    var data=target[1];
    var pty=data[0];
    var grp=data[1];
    var chr=data[2];
    switch(type)
        {
        case "Damage":
            var dmg=data[3];
            var crit=data[4];
            number_bounce(pty,grp,chr,dmg,(crit==true?'red':'white'));
            blood_splat(pty,grp,chr,dmg,1);
            var hp=alter_hp_bar(pty,grp,chr,-dmg);
            //Do not show the hit if the character dies.
            if(hp==0)
                break;
            //Hit function
            var chracter=get_hero(pty,grp,chr);
            var personality=personalities[chracter.personalityid];
            invoke_animation(personality.hit_animation,{party:pty,group:grp,character:chr},{party:pty,group:grp,character:chr},0,[],personality.hit_data);
            //play_sound(audio_clips['impact']);
            break;
        case 'Restore':
            var stat=data[3];
            var amt=data[4];
            number_bounce(pty,grp,chr,amt,'cyan');
            if(stat=='HP')
                {
                if(get_hero(pty,grp,chr).HPDT==0)
                    {
                    show_cause_is_alive(pty,grp,chr);
                    play_sound(audio_clips['revive']);
                    }
                else
                    {
                    play_sound(audio_clips['hpup']);
                    }
                alter_hp_bar(pty,grp,chr,amt);
                }
            else
                {
                alter_mp_bar(pty,grp,chr,amt);
                play_sound(audio_clips['mpup']);
                }
            break;
        case 'Died':
            var dmg=data[3];
            //Death function
            var chracter=get_hero(pty,grp,chr);
            var personality=personalities[chracter.personalityid];
            invoke_animation(personality.die_animation,{party:pty,group:grp,character:chr},{party:pty,group:grp,character:chr},0,[],personality.die_data);
            //Should I call a death_js function made for the attack/item/ability?
            blood_splat(pty,grp,chr,dmg,3);
            //play_sound(audio_clips['die']);
            break;
        case 'Miss':
            number_bounce(pty,grp,chr,'Miss','white');
            play_sound(audio_clips['miss']);
            break;
        case 'NoEffect':
            number_bounce(pty,grp,chr,'No Effect','white');
            play_sound(audio_clips['noeffect']);
            break;
        default:
            alert("Unknown action type in action list.");
            break;
        }
    }

function find_z(object)
    {
    var y=object_get_y(object)+object_get_height(object);
    if(y<PIC_ENEMY_TOP)
        return 0;
    if(y==PIC_ENEMY_TOP)
        return 1;
    if(y<PIC_ENEMY_MID)
        return 2;
    if(y==PIC_ENEMY_MID)
        return 3;
    if(y<PIC_ENEMY_BOTTOM)
        return 4;
    if(y==PIC_ENEMY_BOTTOM)
        return 5;
    if(y<PIC_HERO_TOP)
        return 50;
    if(y==PIC_HERO_TOP)
        return 101;
    if(y<PIC_HERO_BOTTOM)
        return 102;
    if(y==PIC_HERO_BOTTOM)
        return 103;
    return 106;
    }
