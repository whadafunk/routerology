# Linux LDAP Authentication With NSS and PAM


### When it comes to LDAP authentication in linux we can leverage more than one mechanism. 
### The two mechanisms we are going to talk about here are NSS (Name Service Switch) and PAM (Pluggable Authentication Modules)

### NSS (Name Service Switch)

>	Each call to a function which retrieves data from a system
>       database like the password or group database is handled by the
>       Name Service Switch implementation in the GNU C library.  The
>       various services provided are implemented by independent modules,
>       each of which naturally varies widely from the other.
