# SASL Authentication Notes

> SASL is a library that adds authentication mechanisms to processes like SMTP, IMAP, LMTP, POP3
> For a specific authentication scheme the library needs to read some configuration parameters, and it can do that  
> either from a dedicated config file, or the service itself can send configuration parameters to the library

The smtp and smtpd services from postfix, for example are using dedicated sasl configuration files.
The cyrus imap and pop3 services on the other hand, can call the library with the configuration parameters, from their own config files.

### Configuring SASL for Postfix
Apart from configuration inside main.cf, you also need to take care of the configuration file for the sasl library. 
The way this works is that smtp/smtpd is sending a request to the sasl library with the name of the application/service needing authentication,  
and the library is using that name to construct a path/filename where to find the name of the configuration file  
for that particular application/service.

#### There are a couple of places where sasl looks for the service configuration file:
- /etc/sasl2
- /usr/local/lib/sasl2
- /etc/postfix/sasl 

If you don't kwnow where the sasl library is looking for a configuration file, there are a couple of ways to find that out

- Use strace -p *master_process_id* -f to attach to the postfix master process and look for open -ENOENT messages from the SASL
- Postfix provides a tool special for that, which is called *saslfinger* and it will show you a lot of useful information about the SASL configuration in Postfix




Is this the config file where we specify mechanisms and methods for the SASL authentication:

pwcheck_method: salsauthd, auxprop, pwcheck
auxprop_plugin: sasldb, sql, ldapdb
mech_list: PLAIN LOGIN DIGEST-MD5 CRAM-MD5 GSSAPI ANONYMOUS OTP
log_level: 1 - 7
sql_engine: mysql, pgsql, sqlite
slq_hostnames: host:[port]
sql_user
sql_passwd
sql_database
sql_select: SELECT UserPassword from email.credentials where AccountName = '%u' and Domain = '%r'
sql_usessl: yes | no
SASL has two small utilities for testing (sample-server and sample-client)
Sample server sends sample as application_name by default, but it can be configured


If you want to use local users with sasldb, then you have a couple of utilities to create and manage users

saslpasswd2 (-c create, -d disable, -f sasldb, -a appname, -u domain)
sasldblistusers2 -f sasldb



### CONFIG FILES PATHS ####


Usualy the config files for sasl library are searched under /etc/sasl2, or /usr/local/lib/sasl2
The name of the config file is sent by the process that is calling the library.
In case of postfix for example there are a couple of settings related to path finding:


- smtpd_sasl_path
Implementation-specific information that the Postfix SMTP client passes through to the SASL plug-in implementation that is selected with smtp_sasl_type. 
Typically this specifies the name of a configuration file or rendezvous point. 

- smtpd_sasl_application_name
The application name that the Postfix SMTP server uses for SASL server initialization. This controls the name of the SASL configuration file. 
The default value is smtpd, corresponding to a SASL configuration file named smtpd.conf.

Search path for Cyrus SASL application configuration files, currently used only to locate the $smtpd_sasl_path.conf file. 
Specify zero or more directories separated by a colon character, or an empty value to use Cyrus SASL's built-in search path. 
- cyrus_sasl_config_path


