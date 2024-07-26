To have wordpress working with a reverse proxy like Nginx Proxy Manager you need to add the following snippets to  
the wp_config.php

At the begining:

> define('FORCE_SSL_ADMIN', true);
>     define('FORCE_SSL_LOGIN', true);
>     if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
>       $_SERVER['HTTPS']='on';


At the very end of the file:

> define('WP_HOME','https://intranet.bit-soft.ro');
> define('WP_SITEURL','https://intranet.bit-soft.ro');
-----------------------------------------------------------

If WordPress is hosted behind a reverse proxy that provides SSL, but is hosted itself without SSL, these options will initially send any requests into an infinite redirect loop. To avoid this, you may configure WordPress to recognize the HTTP_X_FORWARDED_PROTO header (assuming you have properly configured the reverse proxy to set that header).
Example

define( 'FORCE_SSL_ADMIN', true );
// in some setups HTTP_X_FORWARDED_PROTO might contain 
// a comma-separated list e.g. http,https
// so check for https existence
if( strpos( $_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false )
    $_SERVER['HTTPS'] = 'on';

