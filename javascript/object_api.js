function object_get(name)
    {
    if (document.getElementById)
        return document.getElementById(name);
    alert('This web browser does not support the getElementById Javascript function, a current web standard. This demo will not function properly without it. Please upgrade to a newer browser, such as Mozilla 1.3, IE 6, or Netscape 7. Thank you.');
    return false;
    }

function object_color(object,color)
    {
    object.style.color=color;
    }

function object_background_color(object,color)
    {
    object.style.backgroundColor=color;
    }

function object_border(object,border)
    {
    object.style.border=border;
    }

function object_border_color(object,color)
    {
    object.style.borderColor=color;
    }

function object_opacity(object,opacity)
    {
    var perc=opacity/100.0;
    object.style.MozOpacity=perc;
    object.style.filter='alpha(opacity='+opacity.toString()+')';
    object.style.opacity=perc;
    }

function object_z(object,z)
    {
    object.style.zIndex=z;
    }

function object_y(object,y)
    {
    object.style.top=object_format_px(y);
    }

function object_y_base(object,y)
    {
    object.style.top=object_format_px(y-object_get_height(object));
    }

function object_x(object,x)
    {
    if (object)
        object.style.left=object_format_px(x);
    else
        alert('No object');
    }

function object_format_px(value)
    {
    var val=parseInt(value).toString()+'px';
    if (val=="NaNpx")
        val="0px";//alert (value.toString() + ' ' + val);
    return val;
    }

function object_get_x(object)
    {
    if (object.style.left)
        return parseInt(object.style.left);
    if (object.left)
        return parseInt(object.left);
    return 0;
    }

function object_get_y(object)
    {
    if (object.style.top)
        return parseInt(object.style.top);
    if (object.top)
        return parseInt(object.top);
    return 0;
    }

function object_width(object,width)
    {
    if(width.toString().slice(-1)=='%')
        object.style.width=width;
    else
        object.style.width=object_format_px(width);
    }

function object_get_width(object)
    {
    var retval= null;
    if(object.style.width)
        retval=parseInt(object.style.width);
    else if (object.width)
        retval=parseInt(object.width);
    else
        retval=object.scrollWidth;
    return retval;
    }

function object_height(object,height)
    {
    if(height.toString().substring(-1)=='%')
        object.style.height=height;
    else
        object.style.height=object_format_px(height);
    }

function object_get_height(object)
    {
    var retval=null;
    if(object.style.height)
        retval= parseInt(object.style.height);
    else if (object.height)
        retval=parseInt(object.height);
    else
        retval=object.scrollHeight;
    return retval;
    }
    
function object_get_center(object,other_object)
    {
    var retval= {x:object_get_x(object)+object_get_width(object)/2,y:object_get_y(object)+object_get_height(object)/2};
    if(other_object)
        {
        retval.x-=object_get_width(other_object)/2;
        retval.y-=object_get_height(other_object)/2;
        }
    return retval;
    }

function object_hide(object)
    {
    object.style.visibility='hidden';
    }

function object_show(object)
    {
    object.style.visibility='visible';
    }

function object_default_visibility(object)
    {
    object.style.visibility='';
    }

function object_set_image(object,src,dir)
    {
    if(!dir)
        dir='';
    push_image(dir+src);
    object.src=dir+src;
    }

function object_get_cached_image(src,dir)
    {
    if(!dir)
        dir='';
    return get_image(dir+src);
    }
    
function object_add_child(parent,object)
    {
    return parent.appendChild(object);
    }

function object_create(tag,parent,attributes)
    {
    var object=document.createElement(tag);
    var attribute;
    var parent_html=parent?parent:document.body;
    if(attributes)
        for(attribute in attributes)
            //Changed to direct alteration to allow cross-browser compatability
            //object.setAttribute(attribute,attributes[attribute]);
            object[attribute]=attributes[attribute];
    if(parent)
        {
        object.style.position='absolute';
        object_hide(object);
        parent_html.appendChild(object);
        }
    return object;
    }

function object_create_image(src,parent,attributes,dir)
    {
    var object=object_create('img',parent,attributes);
    object_set_image(object,src,dir);
    return object;
    }

function object_create_text(text,parent)
    {
    var object=document.createTextNode(text);
    var parent_html=parent?parent:document.body;
    if(parent)
        object_add_child(parent,object);
    return object;
    }

function object_delete(object)
    {
    object.parentNode.removeChild(object);
    }

