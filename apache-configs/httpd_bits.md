## HTTPD Config Snippets


### Virtual Hosting

><VirtualHost *:443>*  
>*ServerName www.myserver.com*  
>*ServerAlias www.aliasname.com*  
>*DocumentRoot /var/www/html/teampass*  
>*CustomLog ${APACHE_LOG_DIR}/access.log combined*  
>*ErrorLog ${APACHE_LOG_DIR}/error.log*  
>*LogLevel module_name:trace3i*  
>*</VirtualHost>*

### Redirecting to https
The easiest method to redirect to https would be to use the Redirect directive from mod_alias like in the following example:

>*Redirect permanent / https://sandbox.com*

### If you want you can do https redirection from .htaccess files

>*RewriteEngine On*  
>*RewriteCond %{HTTPS} off*  
>*RewriteCond %{HTTP:X-Forwarded-Proto} !https*  
>*RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [NE,L,R]*

### With wordpress you will find there is a .htaccess with the following config

***That would be usefull in order to have clean URI links that are referencing names  
which cannot be found physically on disk; It's how Wordpress works***

>*<IfModule mod_rewrite.c>*  
>*RewriteEngine On*  
>*RewriteBase /*  
>*RewriteRule ^index\.php$ - [L]*  
>*RewriteCond %{REQUEST_FILENAME} !-f*  
>*RewriteCond %{REQUEST_FILENAME} !-d*  
>*RewriteRule . /index.php [L]*  
>*</IfModule>*  


### SSL Configuration

First you need to load the ssl module

>**LoadModule ssl_module modules/mod_ssl.so**

The most basic ssl configuration requires the following three directives

>*SSLEngine On*  
>*SSLCertificateFile /etc/ssl/cert/server_cert.crt*  
>*SSLCertificateKeyFile /etc/ssl/private/server.key*  

If you want a more sophisticated SSL configuration you can configure things like

* Add the certificate chain
* OCS Stapling
* Cache Databases
* SSL Cipher Suites
* SSL Versions
* Configure SSL Client Features (like loading one or more CA)

>*SSLCertificateChainFile /etc/ssl/cert/server_chain.crt*  
>*SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1 -TLSv1.2 -TLSv1.3*  
>*SSLCipherSuites HIGH:!aNull:!MD5:+MEDIUM:+LOW:+EXP:+eNULL*  
>*SSLSessionCacheTimeout 300*  
===========================================================  

*The settings below are usually configured generically in the ssl module config*

>*SSLRequireSSL On*  
>*SSLSessionCache "shmcb:logs/ssl_cache"*  
>*SSLUseStapling On*  
>*SSLStaplingCache "shmcb:logs/ssl_stapling"*  
>*SSLStaplingResponderTimeout 10*  
>*SSLStaplingReturnResponderErrors On*  
>*SSLStaplingFakeTryLater On*   

Of course, all of the ssl settings described above can be placed inside a VirtualHost directive



