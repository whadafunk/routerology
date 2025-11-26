FROM alpine:3.20
MAINTAINER gri.daniel@gmail.com
RUN apk update
RUN apk add \
	bash \
	bind-tools \
	dhcp-server-vanilla
COPY ./dhcpd.conf /etc/dhcp/dhcpd.conf
RUN touch /var/lib/dhcp/dhcpd.leases
# DHCPD start options
# -f -> foreground, -d -> debug logging, -p -> custom port
# -cf -> config file, -lf -> lease file, -pf -> pid file
# -q -> suppress copyright message on start
CMD ["/usr/sbin/dhcpd","-4","-q","-f"]
