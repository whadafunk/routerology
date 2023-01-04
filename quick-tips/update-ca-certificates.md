# How to update the system ca certificates on Debian based distros



Debian based distros are storing the trusted certificates under */etc/ssl/certs*.  
Actually under */etc/ssl/certs* these are symbolic links to the certificates, and there is also a file
called *ca-certificates.crt* which contains all the certificates unde */etc/ssl/certs* concatenated one after another.  

Most of the certificates under */etc/ssl/certs* are linked to files under */usr/share/ca-certificates*, or files under 
*/usr/local/share/ca-certificates*.

Also important to this process is a configuration file, under */etc/ca-certificates.conf*, which lists all the .crt file paths (relative to */usr/share/ca-certificates*) that will be taken into account.  
These files will be linked under */etc/ssl/certs*, and the certificates inside them will be considered trusted authorities.  
The linking and concatenating of ca-certificates.conf, is done by the command *update-ca-certificates*.

The .crt files under */usr/local/share/ca-certificates*, will be taken into account by *update-ca-certificates* regardless if their names
appear or not in the */etc/ca-certificates.conf*

If you want there is also a command to update the paths from /etc/ca-certificates, and that is *dpkg-reconfigure ca-certificates*, which  
will display an interactive menu where you will be able to select the certificates that you want to trust; the result being updating of the   
file */etc/ca-certificates.conf*
