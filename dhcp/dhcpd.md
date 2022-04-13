# ISC DCHPD Server Thoughts


DHCPD uses mainly two files:

- The configuration file /etc/dhcp/dhcpd.conf
- The lease database file /var/lib/dhcpd/dhcpd.leases

The statements of the configuration file, can be categorised in:

- declarations
- parameters

The declarations are the ones that describe the network topology
The parameters are telling the server how to do specific things,
and they often have values fr time, quantity, etc

The basic building block is the *subnet* declaration.
The subnet identifies a connected subnet for the dhcp server, so we need to have at least one subnet
for each network interface of the server, even if we don't allocate ip addresses from each of these networks.
If we want to allocate ips dynamically from a specific subnet then under that subnet declaration we need
to have a *range* declaration with the available ips

If we server more than one subnet from under a single network interface, then those subnet declarations need
to be enclosed under shared-network

DHCP server will allocate ip addresses and a series of other parameters which are expressed as dhcp options.
Most of the time we use the same set of parameters for a specific subnet, and a specific subnet
will be matched with a request either by using the gateway information, if the request is relayed, or by
mathing the subnet with the interface where the request came in.

If we want we can match the request by using other info like the hardware address, or even dhcp options that 
are transmitted by the client.
A common case is using the hardware address. In this case we use a host declaration the request will be matched
on the specified hardware ethernet address. Going further we can enclose specific options under the host declaration.
The host declaration can contain also the fixed address parameters for assigning an ip address to the client,
but is not mandatory, in which case a subnet will be used for ip allocation.

Host declarations are matched to actual DHCP or BOOTP clients by matching the dhcp-client-identifier option specified in the host declaration to the one supplied by the client, or, if the host declaration or the client does not provide a dhcp-client-identifier option, by matching the hardware parameter in the host declaration to the network hardware address supplied by the client.

If we have some parameters that are the same for multiple hosts we can group host declarations under Group and
configure these parameters only once for all the group.







