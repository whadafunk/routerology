# Linux LDAP Authentication With NSS and PAM


### When it comes to LDAP authentication in linux we can leverage more than one mechanism. 
### The two mechanisms we are going to talk about here are NSS (Name Service Switch) and PAM (Pluggable Authentication Modules)

### NSS (Name Service Switch)

>	Each call to a function which retrieves data from a system
>       database like the password or group database is handled by the  
>       Name Service Switch implementation in the GNU C library.  The  
>       various services provided are implemented by independent modules,  
>       each of which naturally varies widely from the other.  

What the previous paragraph tries to say, is that for services that can retrieve info from database, there is a hook that allows us to have  
multiple database types that can offer that information.  
Like for example for hostname resolution we can look into DNS  or in a file like /etc/hosts.
The precedence of these database is configured under the file /etc/nsswitch.conf

/etc/nsswitch.conf  

> Example configuration of GNU Name Service Switch functionality.
> If you have the `glibc-doc-reference' and `info' packages installed, try:
> info libc "Name Service Switch" for information about this file.
>
> passwd:         files systemd ldap  
> group:          files systemd ldap  
> shadow:         files ldap  
> gshadow:        files  
>
> hosts:          files dns  
> networks:       files  
>
> protocols:      db files  
> services:       db files  
> ethers:         db files  
> rpc:            db files  


As you can see in the example above nss works also with passwd, group and shadow information, and that's what we need in order to authenticate 
users through NSS with ldap


For this to works (NSS LDAP), we need a library called libnss-ldapd. There are actually two libraries that we can use:

- libnss-ldapd (the newer simpler library based on nslcd service)  
- libnss-ldap (a older and more complex library for the same thing)  

For simple authentication we can use just that, but if we want to do more complex things, like changing the user password, or spawning actions
at user login (like creating a home directory), then we also need PAM with one of the libraries:

- libpam-ldapd (newer library that works with the same configuration as nsldc)  
- libpam-ldap (older, more complex library)  


Let's look first at libnss-ldapd  

This works with a service called nslcd which is making the actual ldap queries and optionally with a caching daemon called nscd
The configuration file for nslcd daemon is /etc/nslcd, and in the current folder you can find a commented configuration template

The important things in the configuration file are:

- Coordinates of the LDAP server  
- Credentials to bind to LDAP server  
- Base DN and filters  
- mapping rules for mapping linux fields from passwd, group, shadow to ldap fields in active directory  

If you missed the initial configuration wizard of libnss-ldapd, where you can add ldap authentication to passwd, group and shadow databases  
just run *dpkg-reconfigure libnss-ldapd*, and you'll have it, but you can also just edit */etc/nsswitch.conf*

Another useful thing.., if you want to debug your nslcd, just stop the service with *systemctl stop nsldc*  
and start the daemon manually with nslcd -d. You will see a lot of usefull information

And another useful thing, is the getent command which can retrieve nss information:

- getent passwd  
- getent group  
- getent shadow  


For the second part (the ldap part), install libpam-ldapd, and use a wizard to automatically reconfigure files under /etc/pam.d

- pam-auth-update  
- dpkg-reconfigure libpam-runtime  

The important thing in that wizzard is the *create home directory on login* option. Other than that there are pretty much standard
things that are writen to config files under /etc/pam.d:

- common-account  
- common-password
- common-auth
- common-session

The common config snippets are included in all the pam services, so this way you will have ldap authentication not only for system login  
but also for any other service on the system (imap, smtp, http, etc...)


One extra step would be to restrict LDAP login on the system just to a specific group of users.  
For that you can use the pam module pam_access.so, in the common-auth config file:

auth    required                        pam_access.so
auth    required                        pam_permit.so

This pam_access.so module, works with a configuration service under /etc/security/access.conf

*-:ALL EXCEPT root (Accounting) (network-admin):ALL EXCEPT LOCAL*





