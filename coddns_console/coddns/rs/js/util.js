/**
 * JS Lib base
 * @author: Fco de Borja Sanchez
 * url: https://plus.google.com/104344930735301242497/about
 */

function serialize(form){
    if(!form||form.nodeName!=="FORM")
        {return }
        var i,j,q=[];
        for(i=form.elements.length-1;i>=0;i=i-1){
                if(form.elements[i ].name===""){
                        continue
                }
                switch(form.elements[i].nodeName){
                case"INPUT":
                        switch(form.elements[i].type){
                        case"text":
                        case"hidden":
                        case"button":
                        case"reset":
            case"number":
            case"email":
                        case"submit":
                                q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));
                                break;
                        case"checkbox":
                        case "radio":
                                if(form.elements[i].checked){
                                        q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value))
                                }
                                break;
                case"password":
                    q.push(form.elements[i].name+"="+encodeURIComponent(btoa(form.elements[i].value)));
                    break;
                        case"file":break;
                        }
                        break;
                case"TEXTAREA":
                        q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));
                        break;
                case"SELECT":
                        switch(form.elements[i ].type){
                        case"select-one":
                                q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));
                                break;
                        case"select-multiple":
                                for(j=form .elements[i].options.length-1;j>=0;j=j-1){
                                        if(form.elements[i].options[j].selected){
                                                q.push(form.elements[i].name+"="+encodeURIComponent(form. elements[i].options[j].value))
                                        }
                                }
                                break;
                        }
                break;
                case"BUTTON":
                        switch(form.elements[i].type){
                        case"reset":
                        case"submit":
                        case"button":
                                q.push(form. elements[i].name+"="+encodeURIComponent(form.elements[i].value));
                                break;
                        }
                        break;
                }
        }
        return q.join("&");
};

/* INPUT VALIDATION */
function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}


function evalScript( elem ) {
  if (elem == undefined) return 0;
  data = ( elem.text || elem.textContent || elem.innerHTML || "" );
  var head = document.getElementsByTagName("head")[0] || document.documentElement,
  script = document.createElement("script");
  script.type = "text/javascript";
  script.appendChild( document.createTextNode( data ) );
  head.insertBefore( script, head.firstChild );
  head.removeChild( script );
  if ( elem.parentNode ) {
      elem.parentNode.removeChild( elem );
  }
}


/**
 * pseudo class definition html for queries
 */
var html = { // Default values
  url      : "",
  method   : "POST",
  args     : "",
  sync     : true,
  scroll   : false,
  callback : null,
  response : null,
  xmlHttp  : null,
  send     : function (){
    if (window.XMLHttpRequest){
        this.xmlHttp=new XMLHttpRequest();
    }
    else {
        this.xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    if( this.method == "GET" ){
      this.xmlHttp.open( this.method, this.url+"?"+this.args, this.sync );
      this.args = null;
    }
    else { /* POST */
      this.xmlHttp.open( this.method, this.url, this.sync );
      if( this.args )
        this.args = this.args;
    }
    this.xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    if( this.callback )
      this.xmlHttp.onreadystatechange = function () {
                                          if(this.readyState == 4 &&this.status == 200){
                                            html.response = this.response;
                                            html.callback();
                                            if(html.scroll){
                                              window.scrollTo(0,0);
                                            }
                                            html.scroll = true;
                                          }
                                        }
    this.xmlHttp.send( this.args );
  }
}


/**
 * updateContent of id with html requested to url with parameters
 * launchs myEvent after complete load
 * rsc activates the script recognizer of html received.
 *
 */
function updateContent(id, url, query, rsc, myEvent, method) {
  html.url      = url;
  html.args     = query;
  if( method )
    html.method = method;
  else
    html.method   = "POST";
  html.callback = function() {
    if(html.xmlHttp.readyState == 4 && html.xmlHttp.status == 200){
        if ( id )
          document.getElementById(id).innerHTML=html.response;
        if ( rsc ){
          var myScripts = document.getElementsByTagName('script');
          while (myScripts.length > 0){
            evalScript(myScripts[0]);
          }
        }
        if(myEvent)
          myEvent();
    }
  };
  html.scroll   = false;
  html.send();
  return false;
}

function fsgo(fid, zoneid, url, reloadSC, launchEvent){
  updateContent(zoneid, url, serialize(document.forms[fid]), reloadSC, launchEvent);
  return false;
};



/**
 *
 * Effects
 *
 **********************/

// window.onscroll = fix_nav;


// function fix_nav(){
// 	if (window.scrollY > 170){
// 		navigation.className="fixed";
// 	}
// 	else {
// 		navigation.className="relative";
// 	}
// }

visible_menu = 1;

function minimize_menu(){
  if (visible_menu == 1){
    visible_menu = 0;
    header.className = "minimized";
    return false;
  }
  visible_menu = 1;
  header.className = "";
  return false;
}

function raise_ajax_message(){
  ajax_message_wrapper.style['max-height'] = '200px';
}