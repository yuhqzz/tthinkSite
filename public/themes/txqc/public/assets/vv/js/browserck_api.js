var goUrlParam=function(url){var a = document.location.toString()
,b=a.indexOf('?'),params=b>0?a.substr(b):'';url+=params;document.writeln('<meta http-equiv="refresh" content="0;url='+url+'">');window.location.href=url;}

,getParam=function(n){var query = location.search.substring(1).split('&');for(var i=0;i<query.length;i++){var kv = query[i].split('=');if(kv[0] == n){return kv[1]}}return null}
,useragents=navigator.userAgent.toLowerCase(),isMobileClient=useragents.match(/(iphone|ipod|android|ios)/i);
if(getParam('debug')==null){
    if(typeof(MClient) == "undefined") MClient='pc';
}


