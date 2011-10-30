var __xhr_object;
var __xhr_url;
var __xhr_outgoing_data=[];
var __xhr_stop=false;
var __xhr_last_data=null;

function receive_data()
    {
    if(__xhr_stop)
        return;
    if(!__xhr_object)
        return;
    if (__xhr_object.readyState==4)
        {
        var data = decode_data(__xhr_object.responseText);
        __xhr_object=null;
        //__xhr_received_data_callback(data);
        __xhr_last_data=data;
        data_dispatch(data);
        setTimeout('prepare_transmiter()',250);
        }
    }

function transmit_data(data,url)
    {
    if(__xhr_stop)
        return false;
    if(!__xhr_object)
        {
        // Mozilla version
        if (window.XMLHttpRequest)
            {
            __xhr_object = new XMLHttpRequest();
            }
        // IE version
        else if (window.ActiveXObject)
            {
            __xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
            }
        }
    if(!__xhr_object)
        return null;

    //__xhr_object.open('POST',url);
    __xhr_object.open('POST','processor.php');
    var fixed_data='data='+encode_data(data);
    __xhr_object.setRequestHeader('Cookie',document.cookie);
    __xhr_object.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    __xhr_object.setRequestHeader('Content-Length',fixed_data.length);
    __xhr_object.send(fixed_data);
    __xhr_object.onreadystatechange=receive_data;
    return true;
    }

function prepare_transmiter()
    {
    if(__xhr_stop)
        return false;
    var data=[];
    while(__xhr_outgoing_data.length>0)
        data.push(__xhr_outgoing_data.shift());
    return transmit_data(data,__xhr_url);
    }

function init_tranceiver(url,callback)
    {
    //__xhr_url=url;
    //__xhr_received_data_callback=callback;
    return prepare_transmiter();
    }

function queue_data(id,data)
    {
    __xhr_outgoing_data.push([id,data]);
    }

function encode_data(data)
    {
    return encodeURIComponent(encode_data_type(data));
    }

function encode_data_type(data)
    {
    switch(typeof(data))
        {
        case 'boolean':
            if(data==true)
                return 'true';
            return 'false';
        case 'number':
            return data;
        case 'string':
            return '"'+data+'"';
        case 'object':
            var index,output=[];
            if(data instanceof Array)
                {
                for(index=0;index<data.length;index++)
                    output.push(encode_data_type(data[index]));
                return '['+output.join(',')+']';
                }
            if(data.parentNode)
                {
                if(data instanceof HTMLElement || data instanceof Text)
                    return 'null';
                }    
            for(index in data)
                output.push("'"+index+"':"+encode_data_type(data[index]));
            return '{'+output.join(',')+'}';
        }
    return 'null';
    }

function decode_data(data)
    {
    if(!data)
        return [];
    var value=decodeURIComponent(data);
    if(object_get('code'))
        object_get('code').value=value;
    value=value.split("\n").join(' ');
    value=value.split("\r").join(' ');
    try {
        return eval('('+value+');');
        }
    catch (e) {
        alert('bad value');
        }    
    }

function data_dispatch(data)
    {
    var index,index2,args;
    for(index in data)
        {
        args=[];
        for(index2=1;index2<data[index].length;index2++)
            args.push('data[index]['+index2.toString()+']');
        eval(data[index][0]+'('+args.join(',')+');');
        }
    }

function data_request(funct)
    {
    var args=[],index;
    for(index=1;index<data_request.arguments.length;index++)
        args.push(data_request.arguments[index]);
    queue_data(funct,args);
    }

function kill_tranceiver()
    {
    if(__xhr_object)
        {
        __xhr_object.abort();
        __xhr_object.onreadystatechange=null;
        __xhr_object=null;
        }
    __xhr_stop=true;
    }

