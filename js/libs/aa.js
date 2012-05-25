

/**
 * App-Arena JS SDK. For more information visit our documentation
 * @version 0.5
 * @see http://www.app-arena.com/docs/display/developer/Javascript+SDK
 */

var aa=new Object();

aa.is_object=function(value)
{
  value=this.convert_value(value);
  return (typeof(value) == 'object'); 
}

aa.is_string=function(value)
{
  value=this.convert_value(value);

  return (typeof(value) == 'string');
}
/**
 *  change value to a normal value,  no undefined, no false
 *  @param string  to_type transform to which type of variable
 */
aa.convert_value=function(value,totype)
{
  if(typeof(value) == 'undefined') return false;

  if(value == null) return false;


  //handle different type
  if(totype == 'boolean')
  {
    if(this.is_boolean(value) )
    {
      return value;
    }
    else if(this.is_array(value) )
    {
      if(value.length == 0 )
        return false;
    }
    else if(this.is_object(value))
    {
      if(count(value) == 0 )
        return false;
    }
    else if(this.is_int(value))
    {
      if(value == 0 )
        return false;
    }
    else if(this.is_string(value) )
    {
      if(trim(value) == '' )
        return false;
    }

    return true;
  }
  else
  {
    return value;
  }
}


/** extend Object **/
aa.object=function(){};

aa.object.prototype.isset=function(key){
  var value=this[key];
  return typeof value != 'undefined';
}

aa.object.prototype.has=function(key){
  var value=this[key];
  return typeof value != 'undefined';
}

aa.object.prototype.get=function(key){
  if( this.has(key) == false)
  {
    return false;
  }

  return this[key];
}

aa.object.prototype.set=function(key,value){
  this[key]=value;
}

aa.object.prototype.add=function(key,value){
   this.set(key,value);
}

aa.object.prototype.set_data=function(object){
  for(var k in object)
  {
    this.set(k,object[k]);
  }
}
aa.object.prototype.each=function(callback){
  for(var k in this)
  {
    if(this.hasOwnProperty(k) == false)
    {
      continue;
    }

    callback(k,this.get(k));
  }
}


aa.object.prototype.unset=function(key){
  if(this.isset(key))
  {
    return delete(this[key]);
  }
}


aa.translate=new aa.object();
aa.translate.is_enable=function(){
   if(this.count() == 0)
   {
      return false;
   }
   else
   {
      return true;
   }
}

aa.translate.__t=function(args){
	//support  parameter as an array or as normal
	if(aa.is_object(args) ){
	  arguments=args;
  	}

  	var count=arguments.length;
  	if( count == 0 ) {
  		return '';
  	}

  var str='';

  	if( count > 0 ) {
  		var str= arguments[0];
  	}

  	if( aa.translate.isset(str) ) {
  		str=aa.translate.get(str);
  	}

  	//replace variables
  	if( count > 1 ) {
  		for( var i = 1 ; i < count ; ++i ){
  			str=str.replace("%s",arguments[i]);
  		}
  	}
  	return str;
}


/**
 * Set a translation for use in all js files 
 * @param key identifier of translated string
 * @param value String translation
 */
function set_translation(key,value){
	if(aa.is_object(key))  {
		for(var k in key)    {
		  aa.translate.set(k,key[k]);
		}
	} else if ( aa.is_string(key) && aa.is_string(value) ) {
		aa.translate.set(key,value);
	}
}


/**
 * Returns a translated string from the current js translation object
 * @returns Translated string
 */
function __e()
{
  return aa.translate.__t(arguments);
}


/**
 * Loads a phtml template from the templates folder to the #main container of the page with a sliding effect.
 * @param filename Filename including extension. E.g. welcome.phtml
 * @param aa_inst_id Current app-arena instance id
 */
function loadTemplate(filename, aa_inst_id){
	//set the first menu item as the landing content.
	$("#main").slideUp( 0, function(){
		$("#main").load( "templates/" + filename + "?aa_inst_id=" + aa_inst_id, function(){
			$("#main").slideDown();
			hide_loading();
			$("body").scrollTop();
		});
	});
}

