# Installing PHP Module extensions on official Docker PHP image

## There are many versions/tags of the official docker php image but all of them are based either on Debian or Alpine
## Going further some of the images include Apache/Nginx, and some of them don't, but we are mostly interested in the ones with Apache or Nginx

## The image comes preinstalled with a couple of basic php modules but in many cases you will need to install more extensions.

## Of course there are a couple of ways in which you can add those php module extensions:

- Compile them using docker-php-ext-install, docker-php-ext-configure, docker-php-ext-enable
- Install them using PECL; this needs docker-php-ext-enable
- Using the mlocati script; [php-extension-installer](https://github.com/mlocati/docker-php-extension-installer)


## From the above methods, maybe the mlocati script is the easiest one:

FROM php:7.2-cli

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gd xdebug


------------------------------------------


FROM php:7.2-cli

RUN curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gd xdebug



-------------------------------------------


FROM php:7.2-cli

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd xdebug

## Compilation errors

configure: error: utf8_mime2text() has new signature, but U8T_CANONICAL is missing. This should not happen. Check config.log for additional information.
solution: this is because php won't use IMAP without SSL.

docker-php-ext-configure --with-imap-ssl

## Default php modules included

[PHP Modules]
Core, ctype, curl, date, dom, fileinfo, filter, ftp, hash,  
iconv, json, libxml, mbstring, mysqlnd, openssl, pcre,  
PDO, pdo-sqlite,  Phar, posix, random, readline,  
Reflection, session, SimpleXML, sodium, SPL, sqlite3,  
standard, tokenizer, xml, xmlreader, xmlwriter, zlib

## php.ini

The default config can be customized by copying configuration files into the $PHP_INI_DIR/conf.d/

```
FROM php:7.4-fpm-alpine

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
```


