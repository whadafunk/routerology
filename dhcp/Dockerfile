FROM alpine:latest
MAINTAINER gri.daniel@gmail.com
RUN apk update
RUN apk add dhcp-server-vanilla
COPY ./dhcpd.conf /etc/dhcp/dhcpd.conf
# touch /var/lib/dhcpd/dhcpd.leases
CMD ["/usr/sbin/dhcpd","-4","-q"]
