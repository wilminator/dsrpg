var image_list={};

function push_image_array(list)
    {
    var index;
    for(index=0;index<list.length;index++)
        push_image(list[index]);
    }

function push_image(image_name)
    {
    var image;
    if(!image_list[image_name])
        {
        image=new Image();
        image.src=image_name;
        image_list[image_name]=image;
        }
    }

function get_image(name)
    {
    if(!(name in image_list))
        push_image(name);
    return image_list[name];
    }

function cache_images()
    {
    var index, image;
    for(index in image_list)
        if(typeof(image_list[index])=="string" && image_list[index]!=images)
            {
            image=new Image();
            image.src=image_list[index];
            image_list[index]=image;
            }
    }

function preload_hero_images()
    {
    var party,group,character,image,hero;
    for(party=0;party<get_fight_length();party++)
        for(group=0;group<get_party_length(party);group++)
            for(character=0;character<get_group_length(party,group);character++)
                {
                hero=get_hero(party,group,character);
                for(image in personalities[hero.personalityid])
                    if(image.slice(-5)=='_data')
                        for(var image2 in personalities[hero.personalityid][image].image)
                            if(personalities[hero.personalityid][image].image[image2]!='')
                                push_image(fighter_images+personalities[hero.personalityid][image].image[image2]);
                //Later we will also perload all ability animations and item animations
                //Based on what the hero has.
                for(image in hero.abilities)
                    {
                    var ability=abilities[hero.abilities[image]];
                    push_image(ability_images+ability.menu_pic);
                    for(image2 in ability.impact_data.images)
                        if(ability.impact_data.images[image2]!='')
                            push_image(effect_images+ability.impact_data.images[image2]);
                    }
                for(image in hero.inventory)
                    {
                    var item=items[hero.inventory[image].item];
                    push_image(item_images+item.menu_pic);
                    for(image2 in item.fight_impact_data.images)
                        if(item.fight_impact_data.images[image2]!='')
                            push_image(effect_images+item.fight_impact_data.images[image2]);
                    for(image2 in item.use_impact_data.images)
                        if(item.use_impact_data.images[image2]!='')
                            push_image(effect_images+item.use_impact_data.images[image2]);
                    }
                }
    }

function is_image_cache_loaded()
    {
    var index,key,count=0,length=0;
    for(index in image_list)
        {
        length++;
        if(image_list[index].complete)
            count++;
        }
    return [count,length];
    }
