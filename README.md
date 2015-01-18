#Any Data Service
====================

##HTTP(S)

--------------------------------

###URI


1. http(s)://[data-type].api.ads.devel/http://google.com?ht=search|ads?callback=callable
2. http(s)://[data-type].api.ads.devel/~google-search|ads?callback=callable

>
- roles.google-search.key			=	"google-search"
- roles.google-search.request.uri	=	"/^http://google.com(.*)$/"
- roles.google-search.request.scheme	=	"http2"



3. http(s)://[data-type].api.ads.devel/unix:/opt/local/var/run/php-ng/php-ng-sock-1|ads?callback=callable


----------------------------------------------------

###Custom Hosts

In file /conf/[APPKEY]/conf.h.ini

etc.hosts.org.07studio.ads.api.@	=	"116.255.186.90"
etc.hosts.org.07studio.ads.api.msgpack	=	"116.255.186.90"
etc.hosts.org.07studio.ads.api.*	=	"116.255.186.90"

** 注: 若有下一级域名,则当前级别域名必须使用@ **