function setCookie(cookieName,cookieValue) {
 var today = new Date();
 var expire = new Date();
 nDays = 1000;
 if (nDays==null || nDays==0) nDays=1;
 expire.setTime(today.getTime() + 3600000*24*nDays);
 document.cookie = cookieName+"="+escape(cookieValue)
                 + ";expires="+expire.toGMTString();
}

function setCookie_old (name, value) {
         var argv = setCookie.arguments;
         var argc = setCookie.arguments.length;
         var today = new Date();
         var expi = new Date();
         expi.setTime(today.getTime() + 10000*60*24*365*1000000);
         var expires = (argc > 2) ? argv[2] : expi;
         var path = (argc > 3) ? argv[3] : null;
         var domain = (argc > 4) ? argv[4] : null;
         var secure = (argc > 5) ? argv[5] : false;
         document.cookie = name + "=" + escape(value) +
         ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) +
         ((path == null) ? "" : ("; path=" + path)) +
         ((domain == null) ? "" : ("; domain=" + domain)) +
         ((secure == true) ? "; secure" : "");
}

function getCookie(Name) {
          var search = Name + "="
          if (document.cookie.length > 0) { // if there are any cookies
                    offset = document.cookie.indexOf(search) 
                    if (offset != -1) { // if cookie exists 
                              offset += search.length 
                              // set index of beginning of value
                              end = document.cookie.indexOf(";", offset) 
                              // set index of end of cookie value
                              if (end == -1) 
                                        end = document.cookie.length
                              return unescape(document.cookie.substring(offset, end))
                    } 
          }
}