var iframe=null;

function init_audio(callback)
    {
        soundManager = new SoundManager();
        soundManager.url = swfdir;
        soundManager.flashVersion = 9; // optional: shiny features (default = 8)
        soundManager.useFlashBlock = false; // optionally, enable when you're ready to dive in
        //soundManager.onready(callback);
        soundManager.ontimeout(callback);

        //soundManager.useHTML5Audio = true;
        soundManager.reboot(); // start SM2 init.

    //jukebox=document.getElementById('Jukebox');
    }

function play(key)
    {
    var url=document.formdata[key].value;
    play_sound(url);
    }

function play_sound(url)
    {
    if(url)
        {

        audio = soundManager.createSound({
            id: url,
            url: sound_dir+url,
            autoLoad: true, 
            loops: 1
            });
        if (audio)
            audio.play()
        /*
        var jukebox=document.getElementById('Jukebox');
        if(jukebox)
            try {
                jukebox.play_sound(sound_dir+url,128);
                }
            catch(e){
                alert(e);
                }
        */
        }
    }

function fix_animation_options(prefix,animation,defaults)
    {
    var index,tbody,tr,td,div,input,img;
    var data=animations[animation];

    var images=document.getElementById(prefix+'images');
    //purge all image entries
    while(images.firstChild!=null)
        images.removeChild(images.firstChild);
    //Create a TBODY.
    tbody=document.createElement('tbody');
    images.appendChild(tbody);
    //append all image entries
    for (index in data.images)
        {
        tr=document.createElement('tr');
        tbody.appendChild(tr);
        td=document.createElement('td');
        td.style.backgroundColor="rgb(192,192,192)";
        td.key=prefix+'image_'+index;
        td.imageloc=data.imageloc[index];
        td.index=index;
        td.onclick=function(){select_image(this.key,this.imageloc);};
        tr.appendChild(td);

        img=document.createElement('img');
        img.style.cssFloat="right";
        img.style.maxHeight='64px';
        img.style.maxWidth='64px';
        img.style.backgroundColor='gray';
        img.key=td.key;
        img.id=td.key;
        img.imageloc=td.imageloc;
        img.onclick=function(){select_image(this.key,this.imageloc);};
        td.appendChild(img);

        div=document.createElement('div');
        div.style.visibility='visible';
        //div.onclick=function(){alert('change pic');};
        td.appendChild(div);
        div.appendChild(document.createTextNode(data.images[index]));
        div.appendChild(document.createElement('br'));
        input=document.createElement('input');
        input.name=td.key;
        input.type='hidden';
        if(defaults && defaults.images[index])
            {
            div.appendChild(document.createTextNode(defaults.images[index]));
            input.value=defaults.images[index];
            }
        else
            {
            div.appendChild(document.createTextNode('None'));
            input.value='';
            }
        div.appendChild(input);

        img.src='../'+data.imageloc[index]+input.value;
        if(input.value=='')
            {
            img.style.visibility='hidden';
            }
        }

    var sounds=document.getElementById(prefix+'sounds');
    while(sounds.firstChild!=null)
        sounds.removeChild(sounds.firstChild);
    //Create a TBODY.
    tbody=document.createElement('tbody');
    sounds.appendChild(tbody);
    //append all image entries
    for (index in data.sounds)
        {
        tr=document.createElement('tr');
        td=document.createElement('td');
        td.style.backgroundColor="rgb(192,192,192)";
        td.key=prefix+'sound_'+index;
        td.index=index;
        td.onclick=function(){select_sound(this.key);};
        tr.appendChild(td);
        td.appendChild(document.createTextNode(data.sounds[index]));
        td.appendChild(document.createElement('br'));
        div=document.createElement('div');
        //div.onclick=function(){alert('change pic');};
        td.appendChild(div);
        input=document.createElement('input');
        input.name=td.key;
        input.type='hidden';
        if(defaults && defaults.sounds[index])
            {
            div.appendChild(document.createTextNode(defaults.sounds[index]));
            input.value=defaults.sounds[index];
            }
        else
            {
            div.appendChild(document.createTextNode('None'));
            input.value='';
            }

        div.appendChild(input);
        img=document.createElement('img');
        img.src='play.png';
        img.style.cssFloat="right";
        img.style.height='32px';
        img.style.width='32px';
        img.key=td.key;
        img.id=td.key;
        img.onclick=function(){play(this.key);};
        if(input.value=='')
            {
            img.style.visibility='hidden';
            }
        tr.appendChild(img);
        tbody.appendChild(tr);
        }

    var times=document.getElementById(prefix+'times');
    while(times.firstChild!=null)
        times.removeChild(times.firstChild);
    //Create a TBODY.
    tbody=document.createElement('tbody');
    times.appendChild(tbody);
    //append all image entries
    for (index in data.times)
        {
        tr=document.createElement('tr');
        td=document.createElement('td');
        td.index=index;
        tr.appendChild(td);
        td.appendChild(document.createTextNode(data.times[index]));
        td.appendChild(document.createElement('br'));
        div=document.createElement('div');
        //div.onclick=function(){alert('change pic');};
        td.appendChild(div);
        input=document.createElement('input');
        input.name=prefix+'time_'+index;
        input.type='text';
        if(defaults && index in defaults.times)
            {
            input.value=defaults.times[index];
            }
        div.appendChild(input);
        tbody.appendChild(tr);
        }

    return true;
    }

function select_image(key,path)
    {
    if(iframe)
        return;
    var image=document.getElementById(key);
    iframe=document.createElement('iframe');
    iframe.style.position='fixed';
    iframe.style.top='0px';
    iframe.style.left='0px';
    iframe.style.width='800px';
    iframe.style.height='600px';
    iframe.style.right='auto';
    iframe.style.bottom='auto';
    iframe.style.backgroundColor='white';
    iframe.id='iframe';
    document.body.appendChild(iframe);
    iframe.src='select_image.php?path='+path+"&key="+key;
    }

function cancel_select()
    {
    if(iframe)
        {
        iframe.parentNode.removeChild(iframe);
        iframe=null;
        }
    }

function choose_image(key,path,filename)
    {
    document.formdata[key].value=filename;
    var image=document.getElementById(key);
    if(filename)
        {
        image.src='../'+path+filename;
        image.style.visibility='';
        if(image.imageloc)
            image.nextSibling.lastChild.data=filename;
        }
       else
        {
        image.style.visibility='hidden';
        if(image.imageloc)
            image.nextSibling.lastChild.data=filename;
        }
    cancel_select();
    }

function select_sound(key)
    {
    if(iframe)
        return;
    iframe=document.createElement('iframe');
    iframe.src='select_sound.php?path='+sound_dir+"&key="+key;
    iframe.style.position='fixed';
    iframe.style.top='0px';
    iframe.style.left='0px';
    iframe.style.width='800px';
    iframe.style.height='600px';
    iframe.style.backgroundColor='white';
    iframe.id='iframe';
    document.body.appendChild(iframe);
    }

function choose_sound(key,filename)
    {
    document.formdata[key].value=filename;
    var image=document.getElementById(key);
    if(filename)
        {
        image.style.visibility='';
        image.parentNode.firstChild.lastChild.firstChild.data=filename;
        }
       else
        {
        image.style.visibility='hidden';
        image.parentNode.firstChild.lastChild.firstChild.data='None';
        }
    cancel_select();
    }
    
