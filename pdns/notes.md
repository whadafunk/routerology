# Power DNS Configuration Notes




### WebServer

PowerDNS features an webserver that exposes json/rest API. This API allow for server configuration and monitoring.
To launch the internal webserver add the webserver keyword to the config.
This will start a webserver on default port 8081, without passwor protection.
Here are a couple of more options related to the PDNS WebServer:

- webserver yes|no
- webserver-address 127.0.0.1
- webserver-password
- webserver-port
- webserver-allow-from
- webserver-max-body-size

When loading webserver page from a browser you will be prompted with user / password invite. You can input as user anything, but the password needs to match the one in the configuration webserver-password.

The PowerDNS daemons accept a static API Key, configured with the api-key option, which has to be sent in the X-API-Key header.

Both webserver password and api key can be specified in clear or hashed.
The hashed version can be obtained with **pdnsutil hash-password**


### PDNS Authoritative Config Options


- allow-axfr-ips: ip-ranges, separated by commas
- allow-dns-update-from
- allow-notify-from
- also-notify
- api yes|no
- api-key thesecretapikey
- cache-ttl seconds to store int the packet cache
- query-cache-ttl 20
- config-dir /path/to/pdns.conf
- daemon yes|no (daemonize the process)
- disable-axfr yes|no
- disable-syslog yes|no
- distributor-threads: the number of distributor (backends) threads
- dnsupdate: no Enable/Disable RFC2136
- dnsupdate-require-tsig
- include-dir: /path/to/additional/config
- launch: bind,mysql,remote 
- local-address: local address to bind to
- local-port: 53
- log-dns-details: no
- log-dns-queries: no
- log-timestamp: yes
- logging-facility: 
- loglevel: 4
- primary: no
- max-cache-entries: 1000000
- only-notify: 0.0.0.0/0, !192.168.1.0/24
- receiver-threads
- secondary
- server-id: the hostname of the server
- setgid
- setuid
- tcp-control-port: 5300
- tcp-control-range: 
- tcp-control-secret

- gmysql-port: 3306
- gmysql-socket: /tmp/mysql (mutually exclusive with gmysql-host)
- gmysql-dbname: pdns
- gmysql-user: pdnuser
- gmysql-group: client
- gmysql-password: paspass
- gmysql-s


