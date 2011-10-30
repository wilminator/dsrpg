// var jukebox;
var audio_clips={
    hover:'Cursor1.mp3',
    select:'Item1.mp3',
    unselect:'Cursor2.mp3',
    cancel:'Cancel1.mp3',
    miss:'Evasion2.mp3',
    hpup:'Recovery7.mp3',
    mpup:'Recovery3.mp3',
    noeffect:'',
    revive:'Cold10.mp3'
    };
var audio_cached=0;
var sound_list={};
var audio_handles={}
var music_handle=null;
var sound_volume = 100;
var music_volume = 100;

function init_audio(callback)
    {
        soundManager = new SoundManager();
        soundManager.url = swfdir;
        soundManager.flashVersion = 9; // optional: shiny features (default = 8)
        soundManager.useFlashBlock = false; // optionally, enable when you're ready to dive in
        soundManager.onready(callback);
        soundManager.ontimeout(callback);

        //soundManager.useHTML5Audio = true;
        soundManager.reboot(); // start SM2 init.

    //jukebox=document.getElementById('Jukebox');
    }
    
function load_static_sounds()
    {
    for (var clip in audio_clips)
        {
        if(audio_clips[clip])
            {
            try {
                load_sound(sound+audio_clips[clip])
                //jukebox.load_sound(sound+audio_clips[clip],9);
                //audio_cached++;
                }
            catch(e){
                alert(e);
                }
            }
        }

    }
function create_clip(fullurl)
    {
    index = 0;
    while (soundManager.getSoundByID && soundManager.getSoundByID(fullurl + index))
        index++;
    if (!soundManager.canPlayURL(fullurl))
        {
        if (fullurl.slice(-4)=='.mp3')
            {
            fullurl = fullurl.slice(0,-4) + '.mp3';
            }
        if (!soundManager.canPlayURL(fullurl))
            {
            window.status = fullurl + ' cannot play.  '
            }
        }
    clip = soundManager.createSound({
        id: fullurl+index,
        url: fullurl,
        autoLoad: true, 
        loops: 1
        });
    return clip;
    }

function load_sound(fullurl)
    {
    if(!(fullurl in audio_handles))
        {
        clip = create_clip(fullurl);
        audio_handles[fullurl] = [clip];
        }
    }

function count_initializing_clips()
    {
    var count = 0;
    for (url in audio_handles)
        {
        handle = audio_handles[url][0];
        if ((handle.networkState && handle.networkState == handle.NETWORK_LOADING) || (handle.readyState && handle.readyState == 1))
            count++;
        }
    return count;
    }

function add_to_sound_list(url)
    {
    if(url=='') return;
    if(!(url in sound_list))
        sound_list[url]=0;
    sound_list[url]++;
    }

function cache_fight_sounds()
    {
    sound_list={};

    var pindex,gindex,cindex;
    var party,group,character;
    var personality,ability,item;
    var index,index2,index3;

    for(pindex in fight.parties)
        {
        party=fight.parties[pindex];
        for(gindex in party.groups)
            {
            group=party.groups[gindex];
            for(cindex in group.characters)
                {
                character=group.characters[cindex];
                personality=personalities[character.personalityid];
                for(index in personality)
                    {
                    if(index.slice(-5)=='_data')
                        {
                        for(index2 in personality[index].sounds)
                            {
                            add_to_sound_list(personality[index].sounds[index2]);
                            }
                        }
                    }
                for(index in character.abilities)
                    {
                    ability=abilities[character.abilities[index]];
                    for(index2 in ability.impact_data.sounds)
                        add_to_sound_list(ability.impact_data.sounds[index2]);
                    }
                for(index in character.inventory)
                    {
                    item=items[character.inventory[index].item];
                    for(index2 in item.fight_impact_data.sounds)
                        add_to_sound_list(item.fight_impact_data.sounds[index2]);
                    for(index2 in item.use_impact_data.sounds)
                        add_to_sound_list(item.use_impact_data.sounds[index2]);
                    }
                }
            }
        }

    //now load the sounds
    for(index in sound_list)
        {
        load_sound(sound+index);
        //jukebox.load_sound(sound+index,sound_list[index]);
        audio_cached++;
        }
    }

function is_audio_cache_loaded()
    {
    var count = count_initializing_clips();
    //var count=jukebox.countInitializingPlayers();
    if(count>audio_cached) audio_cached=count;
    if(count==0)
        {
        audio_cached=0;
        return [1,1];
        }
    return [audio_cached-count,audio_cached];
    }

function set_music_volume(vol)
    {
    //if(jukebox) jukebox.setMusicVolume(vol);
    music_volume = vol;
    }

function set_sound_volume(vol)
    {
    //if(jukebox) jukebox.setSoundVolume(vol);
    sound_volume = vol;
    }

function prep_voices(url,number)
    {
    fullurl = sound+url;
    load_sound(fullurl);
    //jukebox.load_sound(sound+url,number);
    if (audio_handles[fullurl].length < number)
        {
        nextclip = create_clip(fullurl);
        audio_handles[fullurl].unshift(nextclip);
        }
    }

function get_clip(fullurl)
    {
    if (!audio_handles[fullurl]) return null;
    clip = audio_handles[fullurl].shift()
    if (audio_handles[fullurl].length == 0 || !audio_handles[fullurl][0].ended)
        {
        nextclip = create_clip(fullurl);
        audio_handles[fullurl].unshift(nextclip);
        }
    audio_handles[fullurl].push(clip);
    return clip;
    }

function stop_music()
    {
    if (music_handle)
        music_handle.pause();
    /*
    if(jukebox)
        try {
            music_handle.stop();
            //jukebox.stop_music();
            }
        catch(e){
            alert(e);
            }
    */
    }

function play_music(url)
    {
    if (url)
        {
        stop_music();
        load_sound(music+url);
        music_handle = get_clip(music+url);
        music_handle.setVolume(music_volume);
        music_handle.play({loops:999});
        /*
        if(jukebox)
            try {
                jukebox.play_music(music+url);
                }
            catch(e){
                alert(e);
                }
        */
        }
    }

function play_sound(url)
    {
    if(url)
        {
        clip = get_clip(sound+url)
        clip.setVolume(sound_volume);
        clip.play();
        return clip;
        /*
        if(jukebox)
            try {
                jukebox.play_sound(sound+url);
                }
            catch(e){
                alert(e);
                }
        */
        }
    return null;
    }

function pan_sound(clip, pan)
    {
    clip.setPan(pan);
    }