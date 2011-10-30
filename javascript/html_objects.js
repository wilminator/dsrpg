function object_create_group(party,group,parent)
    {
    if(party!=player_party)
        return object_create_monster_group(party,group,parent);
    if(get_party(party).teams.length==0)
        return object_create_groupbox(party,group,parent);
    return object_create_hero_group(party,group,parent);
    }

function object_create_hero_group(party,group,parent)
    {
    var group_obj=get_group(party,group);
    var div_group=object_create('div',parent,
        {className:'group',title:group_obj.name,myparty:party,mygroup:group
        ,onmouseover:function(){highlight_hero_group(this.myparty,this.mygroup);}
        ,onmouseout:function(){unhighlight_hero_group(this.myparty,this.mygroup);}
        ,onclick:function(){hero_slide_center(this.myparty,this.mygroup);}
        });

    div_group.appendChild(document.createTextNode((group+1).toString()));

    object_y(div_group,428+50*group);
    object_default_visibility(div_group);
    return {box:div_group,group:div_group,group_text:null};
    }

function object_create_monster_group(party,group,parent)
    {
    var group_obj=get_group(party,group);
    var div_group=object_create('div',parent,
        {className:'egroup',title:group_obj.name,myparty:party,mygroup:group
        ,onmouseover:function(){highlight_hero_group(this.myparty,this.mygroup);}
        ,onmouseout:function(){unhighlight_hero_group(this.myparty,this.mygroup);}
        ,onclick:function(){hero_slide_center(this.myparty,this.mygroup);}
        });

    div_group.appendChild(document.createTextNode((group+1).toString()));

    object_y(div_group,200+50*group);
    object_default_visibility(div_group);
    return {box:div_group,group:div_group,group_text:null};
    }

function object_create_stats(party,group,character,parent)
    {
    if(party!=player_party)
        return object_create_monster_stats(party,group,character,parent);
    if(get_party(party).teams.length==0)
        return object_create_monster_hero_stats(party,group,character,parent);
    return object_create_hero_stats(party,group,character,parent);
    }

function object_create_hero_stats(party,group,character,parent)
    {
    var hero=get_hero(party,group,character);
    var top=10+group*47;
    var left=80*(5-get_group_length(party,group))+character*160;

    var job_name=(hero.jobid>0?jobs[hero.jobid].name:'Monster');

    var div_stat=object_create('div',parent,
        {className:'namebox',title:hero.name,myparty:party,mygroup:group,mychar:character
        ,onmouseover:function(){highlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onmouseout:function(){unhighlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onclick:function(){select_hero(this.myparty,this.mygroup,this.mychar);}
        });

    var div_heroname=object_create('div',null,{className:'heroname'});
    div_heroname.appendChild(document.createTextNode(hero.name));
    div_stat.appendChild(div_heroname);
    var div_herojob=object_create('div',null,{className:'herojob'});
    div_herojob.appendChild(document.createTextNode(job_name));
    div_stat.appendChild(div_herojob);

    var div_hplabel=object_create('div',null,{className:'hplabel'});
    div_hplabel.appendChild(document.createTextNode('HP'));
    div_stat.appendChild(div_hplabel);
    var div_hpbar=object_create('div',null,{className:'hpbar'});
    div_stat.appendChild(div_hpbar);
    div_stat.appendChild(object_create('div',null,{className:'hpbarback'}));
    var div_hp=object_create('div',null,{className:'hp'});
    div_hp.appendChild(document.createTextNode(''));
    div_stat.appendChild(div_hp);

    var div_mplabel=object_create('div',null,{className:'mplabel'});
    div_mplabel.appendChild(document.createTextNode('MP'));
    div_stat.appendChild(div_mplabel);
    var div_mpbar=object_create('div',null,{className:'mpbar'});
    div_stat.appendChild(div_mpbar);
    div_stat.appendChild(object_create('div',null,{className:'mpbarback'}));
    var div_mp=object_create('div',null,{className:'mp'});
    div_mp.appendChild(document.createTextNode(''));
    div_stat.appendChild(div_mp);

    var img_actpic=object_create_image('none.png',null,{className:'action',alt:'action'},menu_images);
    div_stat.appendChild(img_actpic);
    var div_action=object_create('div',null,{className:'action'});
    var text_action=document.createTextNode('');
    div_action.appendChild(text_action);
    div_stat.appendChild(div_action);
    var div_target=object_create('div',null,{className:'target'});
    var text_target=document.createTextNode('');
    div_target.appendChild(text_target);
    div_stat.appendChild(div_target);

    //Locate div_stat
    object_y(div_stat,top);
    object_x(div_stat,left);
    object_default_visibility(div_stat);

    var div_pic=object_create('div',parent,{className:'hero',myparty:party,mygroup:group,mychar:character
        ,onmouseover:function(){highlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onmouseout:function(){unhighlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onclick:function(){select_hero(this.myparty,this.mygroup,this.mychar);}
        });

    var img_pic=object_create_image(personalities[hero.personalityid].base_data.images[0],null,{alt:hero.name},fighter_images);
    div_pic.appendChild(img_pic);

    object_default_visibility(div_pic);

    return {stats:div_stat,hp:div_hpbar,hpval:div_hp,mp:div_mpbar,mpval:div_mp,dmgbar:null,
        actpic:img_actpic,action:text_action,target:text_target,actionbox:null,
        heropic:div_pic,heroimg:img_pic};
    }

function object_create_monster_hero_stats(party,group,character,parent)
    {
    var hero=get_hero(party,group,character);
    var top=0;
    var left=96+character*70;

    var job_name=(hero.jobid>0?jobs[hero.jobid].name:'Monster');

    var div_stat=object_create('div',null,
        {className:'pmnamebox',title:hero.name,myparty:party,mygroup:group,mychar:character
        ,onmouseover:function(){highlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onmouseout:function(){unhighlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onclick:function(){select_hero(this.myparty,this.mygroup,this.mychar);}
        });

    var div_hpbar=object_create('div',null,{className:'pmhpbar'});
    div_stat.appendChild(div_hpbar);
    div_stat.appendChild(object_create('div',null,{className:'pmhpbarback'}));
    var div_hp=object_create('div',null,{className:'pmhp'});
    div_hp.appendChild(document.createTextNode(''));
    div_stat.appendChild(div_hp);

    var div_mpbar=object_create('div',null,{className:'pmmpbar'});
    div_stat.appendChild(div_mpbar);
    div_stat.appendChild(object_create('div',null,{className:'pmmpbarback'}));
    var div_mp=object_create('div',null,{className:'pmmp'});
    div_mp.appendChild(document.createTextNode(''));
    div_stat.appendChild(div_mp);

    //Locate div_stat
    object_y(div_stat,top);
    object_x(div_stat,left);

    var div_actionbox=object_create('div',parent,{className:'pmactionbox',myparty:party,mygroup:group,mychar:character
        ,onmouseover:function(){highlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onmouseout:function(){unhighlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onclick:function(){select_hero(this.myparty,this.mygroup,this.mychar);}
        });
    var img_actpic=object_create_image('none.png',null,{className:'pmaction',alt:'action'},menu_images);
    div_actionbox.appendChild(img_actpic);
    var div_action=object_create('div',null,{className:'pmaction'});
    var text_action=document.createTextNode('');
    div_action.appendChild(text_action);
    div_actionbox.appendChild(div_action);
    var div_target=object_create('div',null,{className:'pmtarget'});
    var text_target=document.createTextNode('');
    div_target.appendChild(text_target);
    div_actionbox.appendChild(div_target);

    object_hide(div_actionbox);
    object_y(div_actionbox,412);

    var div_pic=object_create('div',parent,{className:'hero',myparty:party,mygroup:group,mychar:character
        ,onmouseover:function(){highlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onmouseout:function(){unhighlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onclick:function(){select_hero(this.myparty,this.mygroup,this.mychar);}
        });

    var img_pic=object_create_image(personalities[hero.personalityid].base_data.images[0],null,{alt:hero.name},fighter_images);
    div_pic.appendChild(img_pic);

    object_default_visibility(div_pic);

    return {stats:div_stat,hp:div_hpbar,hpval:div_hp,mp:div_mpbar,mpval:div_mp,dmgbar:null,
        actpic:img_actpic,action:text_action,target:text_target,actionbox:div_actionbox,
        heropic:div_pic,heroimg:img_pic};
    }

function object_create_monster_stats(party,group,character,parent)
    {
    var hero=get_hero(party,group,character);

    var div_pic=object_create('div',parent,
        {className:'monster',title:hero.name,myparty:party,mygroup:group,mychar:character
        ,onmouseover:function(){highlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onmouseout:function(){unhighlight_hero_range(this.myparty,this.mygroup,this.mychar);}
        ,onclick:function(){select_hero(this.myparty,this.mygroup,this.mychar);}
        });

    var img_pic=object_create_image(personalities[hero.personalityid].base_data.images[0],null,{alt:hero.name},fighter_images);
    div_pic.appendChild(img_pic);

    var div_dmgbar=object_create('div',null,{className:'damagebar'});
    var div_hpbar=object_create('div',null,{className:'mhpbar'});
    div_dmgbar.appendChild(div_hpbar);
    div_dmgbar.appendChild(object_create('div',null,{className:'mhpbarback'}));
    var div_hp=object_create('div',null,{className:'mhp'});
    div_hp.appendChild(document.createTextNode(''));
    div_dmgbar.appendChild(div_hp);
    div_pic.appendChild(div_dmgbar);
    object_opacity(div_dmgbar,100);

    //Locate div_pic
    object_default_visibility(div_pic);

    return {stats:null,hp:div_hpbar,hpval:div_hp,mp:null,mpval:null,dmgbar:div_dmgbar,
        actpic:null,action:null,target:null,actionbox:null,
        heropic:div_pic,heroimg:img_pic};
    }

function object_create_groupbox(party,group,parent)
    {
    var group_obj=get_group(party,group);
    var alive=group_living_count(party,group);

    var div_box=object_create('div',parent,{className:'pmgroupbox'});
    var div_group=object_create('div',null,
        {className:'pmgroupname',title:group_obj.name+' Alive:'+alive.toString(),myparty:party,mygroup:group
        ,onmouseover:function(){highlight_hero_group(this.myparty,this.mygroup);}
        ,onmouseout:function(){unhighlight_hero_group(this.myparty,this.mygroup);}
        ,onclick:function(){hero_slide_center(this.myparty,this.mygroup);}
        });
    var text_group=document.createTextNode(group_obj.name+':'+alive.toString());
    div_group.appendChild(text_group);
    div_box.appendChild(div_group);

    //This one has more- it must attach the characters' stat boxes here.
    var character,object;
    for(character=0;character<get_group_length(party,group);character++)
        {
        object=get_hero(party,group,character).html.stats;
        div_box.appendChild(object);
        }

    object_y(div_box,47*group);
    object_default_visibility(div_box);

    return {box:div_box,group:div_group,group_text:text_group};
    }

