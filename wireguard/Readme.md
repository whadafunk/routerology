# WireGuard Tutorial


Wireguard is a network encryption protocol and set of tools that we can use to setup secure encrypted tunnels between two network hosts. 
Of course, like most of the tunneling technology it not only solves the issue of security over an insecure medium like the internet, but also solves the problem of routing

It is a code included in the official linux kernel code, and there are a couple of tools that you will find in almost every official linux repository (Debian, Ubuntu, RedHat, etc).
The main tools are wg and wg-quick. 
wg is used to generate keys, to apply config to wireguard interfaces and to show status
wg-quick is a script that saves us from manually manage the wireguard interface. it will automatically create and manage the interface, and apply the config by using wg. 


How it is configured without wg-quick


1. Create a wireguard interface

ip link add name wg0 type wireguard
ip address add dev wg0 192.168.100.1/24
ip link set dev wg0 up


2. Create the private and public keys


wg genkey | tee /etc/wireguard/key.prv | wg /etc/wireguard/pubkey > key.pub  

two files will going to be created: key.prv and key.pub


3. Get a list with all the peer's public keys

peer1.key, peer2.key,... and so on

4. Create the configuration file /etc/wireguard/wg0.conf

There are two types of stanzas in this config file: [Interface] and [Peers]

The [Interface] stanza specifies the private key and optionally the listen address. 
The [Peers] stanza specifies the public key of the peer, the allowed ip addresses of the peer and in case of a remote client, the Endpoint address:port.

5. Apply the configuration file to the wireguard interface using wg. 

wg setconf wg0 /etc/wireguard/wg0.conf


Mentions about the config file

There are a couple of params in the config file that are used only by the wg-quick script and they will be flagged as errors by wg setconf.
Those are address which is used to configure the address of the wireguard interface, and PostUp/PostDown which indicate scripts that need to be run with interface up/down events.
There is an option to wg-quick to display the config file without the wg-quick specific parameters:

wg-quick strip wg0



Other mentions

The AllowedIP peer parameter is used as an ACL for incoming traffic, and as a routing indication for outgoing traffic, because it needs to know what public key is associated with specific ip or ip class. If it has the public key, it will also have the endpoint of that public key, even if that is not in the configuration.


Built-in Roaming


There is a roaming mechanism mentioned in the official documentation which I have not tested. It says that nodes can change their endpoint address and the change will be picked up by the peer at the other end which will update dynamicaly its configuration.
Like for example in a communication between A and B, where A knows that B address is 1.1.1.1. When B changes its endpoint address to 1.1.1.2 and sends something to A, that node will notice and update its config.
In the same manner, the server does not have and enpoint address in its config, but it gets that information from the peers when they are sending encrypted traffic.








