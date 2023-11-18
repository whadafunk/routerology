# Cron implementation in Alpine Linux

Cron searches /var/spool/cron/crontabs for crontab files which are named after user accounts; 
together with the system crontab /etc/crontab, the found crontabs are loaded into the memory. 
Cron also searches for any files in the /etc/cron.d directory, which have a different format (see crontab(5)). 
Cron examines all stored crontabs and checks each job to see if it needs to be run in the current minute. 
When executing commands, any output is mailed to the owner of the crontab (or to the user specified in the MAILTO environment variable in the crontab, if such exists). 
Any job output can also be sent to syslog by using the -s option.

Cron checks these files and directories:

/etc/crontab
    system crontab, usually used to run daily, weekly, monthly jobs. See crontab(5) for more details.
/etc/cron.d/
    directory that contains system cronjobs stored for different users.
/var/spool/cron/crontabs
    directory that contains user crontables created by the crontab(1) command. 


In alpine, /var/spool/cron/crontabs is a symbolic link to /etc/cronabs
