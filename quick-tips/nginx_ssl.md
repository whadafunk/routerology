Nginx reverse proxy to Heroku fails SSL handshake




2025/02/25 18:34:31 [error] 36122#36122: *786613 SSL_do_handshake() failed (SSL: error:0A000438:SSL routines::tlsv1 alert internal error:SSL alert number 80) while SSL handshaking to upstream, client: 10.154.100.1, server: names-pub.asseco-see.mgmt, request: "GET / HTTP/2.0", upstream: "https://172.16.170.5:8443/", host: "names-pub.asseco-see.ro"


It turns out that the problem was related to SNI after all. I found this ticket on nginx.org:

https://trac.nginx.org/nginx/ticket/229

Which led me to the proxy_ssl_server_name directive:

http://nginx.org/r/proxy_ssl_server_name

By setting to "on" in your config, you'll be able to proxy to upstream hosts using SNI.



Please try by adding proxy_ssl_server_name on


As a note for others a related condition that Heroku imposes is that the HOST field must match the custom domain name.

So in addition to proxy_ssl_server_name you may also want to set a line like:

proxy_set_header Host mycustomdomain.com;





In case someone experience the same issue and above directives are not enough, you will need to use "proxy_ssl_name name" - https://nginx.org/en/docs/http/ngx_http_proxy_module.html#proxy_ssl_name:

proxy_ssl_server_name on; #mandatory

proxy_ssl_protocols TLSv1.3; #sometimes is needed

proxy_ssl_name myfqdn.com;
