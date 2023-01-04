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

