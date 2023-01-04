# NSLCD - Local LDAP Name Service Daemon


## Configuration file: */etc/nslcd.conf*

## Runtime flags: 

- -c, --check
- -d, --debug

The daemon is started through systemd, and it has some parameters that
can be tweaked under /etc/default/nslcd

The best way to debug it is to stop the systemd service and start it
on a console with -d parameter



