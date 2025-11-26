#! /bin/bash
# touch /var/lib/dhcpd/dhcpd.leases
# DHCPD start options
# -f -> foreground, -d -> debug logging, -p -> custom port
# -cf -> config file, -lf -> lease file, -pf -> pid file
# -q -> suppress copyright message on start
/usr/bin/dhcpd -4 -q -lf /var/lib/dhcpd/dhcpd.leases

cd /opt/glass-isc-dhcp
npm start

wait -n

exit $?
