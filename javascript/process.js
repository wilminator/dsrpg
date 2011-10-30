function convert_action_list_to_playlist(action_list)
    {
    var playback_actions,grouped_lists,index,event_index;
    var pindex,gindex,cindex,id_list,message_list,effect_list,event_list;
    var type,data,item,slot,ammo,action,event,group;
    var tpindex,tgindex,tcindex,range,effect;
    var seen,target_list;
    var impact_name,impact_data;
    var ability,name,character,jsfunc,spell,fight_message;
    var effects,pty,grp,hero;

    playback_actions=[];

    grouped_lists=preprocess_action_list(action_list);

    for(index in grouped_lists)
        {
        group=grouped_lists[index];
        id_list=group['id'];
        message_list=group['message'];
        effect_list=group['effect'];
        event_list=group['event'];

        //Take a peek to find out who our main actor is.
        pindex=id_list[0][1][0];
        gindex=id_list[0][1][1];
        cindex=id_list[0][1][2];
        //Get this actor's personality.
        hero=personalities[get_hero(pindex,gindex,cindex).personalityid];
        //Process the event list.
        for(event_index in event_list)
            {
            event=event_list[event_index];
            type=event[0];
            data=event[1];
            switch(type)
                {
                case 'EquipSlot':
                    item=data[0];
                    slot=data[1];
                    playback_actions.push([0,'equip_item',pindex,gindex,cindex,item,slot]);
                    break;
                case 'UnequipSlot':
                    slot=data[0];
                    playback_actions.push([0,'unequip_item',pindex,gindex,cindex,slot]);
                    break;
                case 'UseItem':
                    item=data[0];
                    playback_actions.push([0,'consume_item',pindex,gindex,cindex,item,true]);
                    break;
                case 'ExpendAmmo':
                    ammo=data[0];
                    playback_actions.push([0,'consume_item',pindex,gindex,cindex,ammo,false]);
                    break;
                case 'AlterStat':
                    tpindex=data[0];
                    tgindex=data[1];
                    tcindex=data[2];
                    stat=data[3];
                    amount=data[4];
                    playback_actions.push([0,'alter_stat',tpindex,tgindex,tcindex,stat,amount]);
                    break;
                }
            }

        //Move main actor on screen and remove the Turn effect.
        action=id_list.shift();
        playback_actions.push([0,'present_group',pindex,gindex]);
        //Next get the target.
        action=id_list.shift();
        if(action)
            {
            tpindex=action[1][0];
            tgindex=action[1][1];
            tcindex=action[1][2];
            range=action[1][3];
            }
        //If there is an effect, then pull the group id for it.
        //Otherwise, just use 'Target'.
        if(effect_list.length>0)
            {
            effect=effect_list[0]['group'].split(';');
            tpindex=effect[0];
            tgindex=effect[1];
            }
        /*
        #!!IMPORTANT!!#
        Either this action affects the same or opposite sides.
        If the action effects the opposite sides,
            ($pindex==$player_party)^($tpindex==$player_party)
        then show the other team, delay, then do the action sequence.
        If not, then do the action sequence THEN call the party.
        */
        if((pindex==player_party)^(tpindex==player_party))
            {
            playback_actions.push([0,'present_group',tpindex,tgindex]);
            playback_actions.push([1]);
            seen=true;
            }
        else
            seen=false;
        //Set the function call and target stack.
        target_list=[];
        impact_name='';
        impact_data=null;
        //Do action sequence (the attack actions)
        for(event_index in message_list)
            {
            event=message_list[event_index];
            type=event[0];
            data=event[1];
            switch(type)
                {
                //Starting actions (messages)
                case 'Attack':
                    item=items[data[0]];
                    //alert(data[1].toString()+data[0]+'-'+data[2]);
                    //Call js that makes the actor attack
                    switch(item.fight_effect_type)
                        {
                        case 'close':
                            playback_actions.push([0,'invoke_animation',hero.attack_close_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.attack_close_data]);
                            break;
                        case 'throw':
                            playback_actions.push([0,'invoke_animation',hero.attack_throw_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.attack_throw_data]);
                            break;
                        case 'shoot':
                            playback_actions.push([0,'invoke_animation',hero.attack_shoot_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.attack_shoot_data]);
                            break;
                        }
                    //Set the js that shows the impact of the attack (sword slashes, etc)
                    impact_name=item.fight_impact_animation;
                    impact_data=item.fight_impact_data;
                    break;
                case 'Item':
                    item=items[data]
                    //Show title
                    playback_actions.push([0,'set_fight_message',item.name]);
                    playback_actions.push([0,'show_fight_message_by_hero',pindex,gindex,cindex]);
                    //Call the js that shows the actor using the item
                    playback_actions.push([0,'invoke_animation',hero.item_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.item_data]);
                    //standard JS that causes the message to dissapear before continuing.
                    playback_actions.push([1]);
                    //Set the js that shows the impact of the item (bomb tossed, explosions, etc)
                    impact_name=item.use_impact_animation;
                    impact_data=item.use_impact_data;
                    break;
                case 'Spell':
                    spell=abilities[data];
                    //Show title
                    playback_actions.push([0,'set_fight_message',spell.name]);
                    playback_actions.push([0,'show_fight_message_by_hero',pindex,gindex,cindex]);
                    //Call JS that makes the person cast a spell.
                    //Call the js that shows the actor casting the spell
                    playback_actions.push([0,'invoke_animation',hero.spell_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.spell_data]);
                    //standard JS that causes the message to dissapear before continuing.
                    playback_actions.push([1]);
                    //Set the js that shows the impact of the spell
                    impact_name=spell.impact_animation;
                    impact_data=spell.impact_data;
                    break;
                case 'Skill':
                    skill=abilities[data];
                    //Show title
                    playback_actions.push([0,'set_fight_message',skill.name]);
                    playback_actions.push([0,'show_fight_message_by_hero',pindex,gindex,cindex]);
                    //Call the js that shows the actor using the skill
                    playback_actions.push([0,'invoke_animation',hero.skill_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.skill_data]);
                    //standard JS that causes the message to dissapear before continuing.
                    playback_actions.push([1]);
                    //Call js that makes the actor attack
                    switch(skill.skill_effect_type)
                        {
                        case 'close':
                            playback_actions.push([0,'invoke_animation',hero.attack_close_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.attack_close_data]);
                            break;
                        case 'throw':
                            playback_actions.push([0,'invoke_animation',hero.attack_throw_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.attack_throw_data]);
                            break;
                        case 'shoot':
                            playback_actions.push([0,'invoke_animation',hero.attack_shoot_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.attack_shoot_data]);
                            break;
                        }
                    //Set the js that shows the impact of the skill
                    impact_name=skill.impact_animation;
                    impact_data=skill.impact_data;
                    break;
                case 'Equip':
                    item=data[0];
                    ammo=data[1];
                    fight_message='Equipping '+items[item].name;
                    if(ammo)
                        fight_message+='and '+items[ammo].name;
                    //Call the js that shows the actor changing equipment
                    playback_actions.push([0,'invoke_animation',hero.equip_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.equip_data]);
                    //Should ba a part of personality -OR- a
                    //standard JS that uses a standard pic.
                    playback_actions.push([0,'set_fight_message',fight_message]);
                    playback_actions.push([0,'show_fight_message_by_hero',pindex,gindex,cindex]);
                    playback_actions.push([1]);
                    break;
                case 'Unequip':
                    item=data[0];
                    ammo=data[1];
                    fight_message='Removing '+item;
                    //Call the js that shows the actor changing equipment
                    playback_actions.push([0,'invoke_animation',hero.equip_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.equip_data]);
                    //Should ba a part of personality -OR- a
                    //standard JS that uses a standard pic.
                    playback_actions.push([0,'set_fight_message',fight_message]);
                    playback_actions.push([0,'show_fight_message_by_hero',pindex,gindex,cindex]);
                    playback_actions.push([1]);
                    break;
                case 'Defend':
                    break;
                case 'Run':
                    playback_actions.push([0,'set_fight_message','Running Scared!']);
                    playback_actions.push([0,'show_fight_message_by_hero',pindex,gindex,cindex]);
                    //Call the js that shows the actor running
                    playback_actions.push([0,'invoke_animation',hero.flee_animation,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},0,[],hero.flee_data]);
                    playback_actions.push([1]);
                    break;
                case 'Give':
                    name=data[0];
                    playback_actions.push([0,'set_fight_message',name]);
                    playback_actions.push([0,'show_fight_message_by_hero',pindex,gindex,cindex]);
                    playback_actions.push([1]);
                    break;
                case 'NoMP':
                    playback_actions.push([0,'set_fight_message','Not enough MP']);
                    playback_actions.push([0,'show_fight_message_by_hero',pindex,gindex,cindex]);
                    playback_actions.push([1]);
                    break;
                }
            }
        for(event_index in effect_list)
            {
            effects=effect_list[event_index];
            effect=effects['group'].split(';');
            pty=effect[0];
            grp=effect[1];
            if(tpindex!=pty ||tgindex!=grp||seen==false)
                {
                playback_actions.push([0,'present_group',pty,grp]);
                playback_actions.push([1]);
                seen=true;
                }
            //process_effects($playback_actions,$effects['effects'],$pre_effect_js,$impact_js,$base_object,$range);
            //playback_actions.push([0,impact_js,pindex,gindex,cindex,tpindex,tgindex,tcindex,range,effects['effects']]);
            //Call the js that shows the impact on the target(s)
            if(impact_data)
                playback_actions.push([0,'invoke_animation',impact_name,{party:pindex,group:gindex,character:cindex},{party:tpindex,group:tgindex,character:tcindex},range,effects['effects'],impact_data]);
            else
                alert('No impact data');
            playback_actions.push([1]);
            }
        }
    //log_error("Storing playback actions.\n".php_data_to_js($playback_actions),100);
    return playback_actions;
    }

function preprocess_action_list(action_list)
    {
    var retval=[],index,group;

    /*
    if(typeof(action_list) !='Array')
        {
        data_request('client_error','action_list is not an array',{action_list:action_list});
        return retval;
        }
    */

    while(action_list.length>0)
        {
        //Make a list of just one fighter's actions.
        var action_group=[];
        do {
            action_group.push(action_list.shift());
            } while(action_list.length>0 && action_list[0][0]!="Turn");
        //Run thru the list again.  Now separate into lists of
        //Things that just need to be done and things that affect
        //others.
        var event_list=[];  //Lists things like ammo use, item loss, slot mods
        var effect_list=[]; //Lists things like damage, equipping
        var message_list=[];//Lists what is about to be done.
        var id_list=[];     //Lists who does the action and who is the primary target.
        for(index in action_group)
            {
            action=action_group[index];
            switch(action[0])
                {
                case 'Attack':
                case 'Item':
                case 'Spell':
                case 'Skill':
                case 'Equip':
                case 'Unequip':
                case 'Defend':
                case 'Run':
                case 'NoMP':
                    message_list.push(action);
                    break;
                case 'Turn':
                case 'Target':
                    id_list.push(action);
                    break;
                case 'Miss':
                case 'Damage':
                case 'Restore':
                case 'Died':
                case 'NoEffect':
                    effect_list.push(action);
                    break;
                case 'EquipSlot':
                case 'UnequipSlot':
                case 'UseItem':
                case 'ExpendAmmo':
                case 'UseMP':
                case 'AlterStat':
                    event_list.push(action);
                    break;
                default:
                    data_request('client_error','Unknown action in action list.',{action:action[0],action_group:action_group,action_list:action_list});
                    break;
                }
            }    
        //Sub-parse the effect list into groups
        var groups={};
        for(index in effect_list)
            {
            var effect=effect_list[index];
            var pty=effect[1][0];
            var grp=effect[1][1];
            var target=pty.toString()+';'+grp.toString();
            if(!(target in groups))
                groups[target]=[];
            groups[target].push(effect);
            }
        effect_list=[];
        for(group in groups)
            effect_list.push({group:group,effects:groups[group]});
        //Only bother if there are other events or effects.
        if(event_list.length>0 || effect_list.length>0)
            retval.push({id:id_list,message:message_list,effect:effect_list,event:event_list});
        }
    return retval;
    }
