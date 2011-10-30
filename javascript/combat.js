var postfight=null;
var fight=null;
var combat_playback=null;
//var fight_screen=null;

//Must implement, pull out client data from fight
var fight_client_data=null;

function show_fight_message_by_hero(party,group,character)
    {
    var hero=get_hero(party,group,character);
    var y=object_get_y(hero.html.pic)+(get_picture_y(hero.html.pic)-25)/2;
    var x=hero.x_offset+(hero.x_offset<400?get_picture_x(hero.html.pic):-200);
    show_fight_message(x,y);
    }

function present_group(party,group)
    {
    hero_slide_center(party,group);
    return 0;
    }

function number_bounce(party,group,character,value,color)
    {
    object=get_hero(party,group,character).html.pic;
    x=object_get_x(object)+get_picture_x(object)/2;
    y=object_get_y(object)+get_picture_y(object)/2;
    if(party==player_party)
        y-=24;
    //make_damage_fly(x,y,value,color);
    make_damage_fly(object,value,color);
    return 0;
    }

function blood_splat(party,group,character,value,multiplier)
    {
    object=get_hero(party,group,character).html.pic;
    x=object_get_x(object)+get_picture_x(object)/2;
    y=object_get_y(object)+get_picture_y(object)/2;
    //make_blood_fly(x,y,Math.ceil(Math.log(value)/Math.log(10))*multiplier);
    make_blood_fly(object,Math.ceil(Math.log(value)/Math.log(10))*multiplier);
    return 0;
    }

function alter_stat(party,group,character,stat,value)
    {
    var object=get_hero(party,group,character).current;
    var variable="object."+stat;
    eval(variable+"+="+value.toString()+";");
    eval("if("+variable+"<0) "+variable+"=0;");
    if (stat!='HP') update_stats(party,group,character);
    return 0;
    }

function alter_hp_bar(party,group,character,value)
    {
    var object=get_hero(party,group,character);
    object.HPDT+=value;
    if(object.HPDT<0) object.HPDT=0;
    init_update_hp(party,group,character);
    return object.HPDT;
    }

function alter_mp_bar(party,group,character,value)
    {
    var object=get_hero(party,group,character);
    object.MPDT+=value;
    if(object.MPDT<0) object.MPDT=0;
    init_update_mp(party,group,character);
    return 0;
    }

//function consume_item(party,group,character,item,cleanup)
function consume_item(party,group,character,item)
    {
    var hero=get_hero(party,group,character);
    var thing=hero.inventory[item]
    thing.qty--;
    //if(thing.qty==0 && cleanup)
    if(thing.qty==0)
        {
        hero.inventory.splice(item,1);
        hero.command=6;
        }
    display_action(group,character);
    return 0;
    }

function equip_item(party,group,character,item,location)
    {
    var hero=get_hero(party,group,character);
    hero.equipment[location]=item;
    return 0;
    }

function unequip_item(party,group,character,location)
    {
    var hero=get_hero(party,group,character);
    hero.equipment[location]=null;
    return 0;
    }

function init_fight()
    {
    //request our fight data so we can get setup.
    set_loader_message('Loading fight data...');
    set_loader_percentage(0);
    object_show(object_get('loader'));
    object_hide(fight_screen);
    set_menu_state(MENU_STATE_FIGHT_WAIT);

    //Create a timerbox
    object_create_timerbox(fight_screen);

    //Init postfight.
    postfight=null;

    //Start the fight by getting the data.
    request_fight_data();
    return true;
    }

function request_fight_data()
    {
    data_request('request_fight_data');
    }

function receive_fight_data(sequence,pre_fight,actions,post_fight,timeout)
    {
    if(postfight==null)
        {
        //Music!
        play_music(post_fight.music);
        //Background!
        fight_screen.style.backgroundImage='url('+background_images+post_fight.background+')';
        }

    set_loader_percentage(50);
    sequence_number=sequence;
    synchronize_fight_data(pre_fight);
    //data_request('client_error','Bad party',{fight_data:fight});
    postfight=post_fight;
    //!!TEST!! No conversion done! Convert now!
    combat_playback=convert_action_list_to_playlist(actions);
    set_loader_percentage(100);
    set_menu_state(MENU_STATE_FIGHT_NONE);
    update_timerbox(timeout);
    check_fight_timeout();
    prep_fight();
    }

function prep_fight()
    {
    //Proceed with standard init code
    preload_hero_images();
    cache_fight_sounds();
    push_image_array(static_images);
    cache_images();
    verify_cache();
    }

function verify_cache()
    {
    //ensure that the image cache is loaded.
    var left=is_image_cache_loaded();
    set_loader_message('Loading image cache '+left[0]+'/'+left[1]+'...');
    set_loader_percentage(left[0]*100/left[1]);
    if(left[0]<left[1])
        {
        setTimeout('verify_cache()',100);
        object_show(object_get('loader'));
        object_hide(fight_screen);
        return;
        }

    //Now verify the fight html
    verify_fight_html();
    }

function verify_fight_html()
    {
    //ensure that the image cache is loaded.
    var left=is_fight_html_loaded();
    set_loader_message('Loading visible images...');
    set_loader_percentage(left[0]*100/left[1]);
    if(left[0]<left[1])
        {
        setTimeout('verify_fight_html()',100);
        object_show(object_get('loader'));
        object_hide(fight_screen);
        return;
        }

    //Now begin the fight sequence
    verify_audio_precache();
    }

function verify_audio_precache()
    {
    //ensure that the image cache is loaded.
    var left=is_audio_cache_loaded();
    set_loader_message('Completing audio load...');
    set_loader_percentage(left[0]*100/left[1]);
    if(left[0]<left[1])
        {
        setTimeout('verify_audio_precache()',100);
        object_show(object_get('loader'));
        object_hide(fight_screen);
        return;
        }

    //Now begin the fight sequence
    begin_fight();
    }

function begin_fight()
    {
    //Cleanup/reset html
    fight_layout();
    //setup hero slide
    player_slide.oldparty=player_party;
    hero_center(player_slide.oldparty,player_slide.oldgroup);

    //setup monster slide
    //later we will pick an enemy; for now pick someone not us.
    opponent_slide.oldparty=(player_party==0?1:0);
    hero_center(opponent_slide.oldparty,opponent_slide.oldgroup);

    //Now show things.
    object_show(fight_screen);
    object_hide(object_get('loader'));
    //Action!
    set_playback_queue(combat_playback,combat_callback);
    }

function combat_callback()
    {
    if(timeout_time_left()<=60)
        request_timeout_extension();
    //At this point, adopt the postfight as our real data.
    synchronize_fight_data(postfight);
    //Cleanup/reset html
    fight_layout();
    //Now check for completion
    if (test_own_party_dead(player_party))
        {
        //alert("Oh, you died!");
        stop_music();
        data_request('finish_fight');
        return;
        }
    if (test_other_parties_dead(player_party))
        {
        stop_music();
        //alert("They are toast!");
        data_request('finish_fight');
        return;
        }
    slide_out(player_slide.oldgroup);
    set_menu_state(MENU_STATE_FIGHT_PLAYER);
    }

function fight_terminate(url)
    {
    //Later we will have a transition to the map.
    //For now, stop the proxy and hard-switch to a new page.
    stop_music();
    kill_tranceiver();
    window.location=url;
    }
