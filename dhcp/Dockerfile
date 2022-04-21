FROM alpine:latest
MAINTAINER gri.daniel@gmail.com
RUN apk update
RUN apk add dhcp-server-vanilla
COPY ./dhcpd.conf /etc/dhcp/dhcpd.conf
# touch /var/lib/dhcpd/dhcpd.leases
# DHCPD start options
# -f -> foreground, -d -> debug logging, -p -> custom port
# -cf -> config file, -lf -> lease file, -pf -> pid file
# -q -> suppress copyright message on start
CMD ["/usr/sbin/dhcpd","-4","-q"]
