# ISC DCHPD Server Thoughts


DHCPD uses mainly two files:

- The configuration file */etc/dhcp/dhcpd.conf*  
- The lease database file */var/lib/dhcpd/dhcpd.leases*  

The configuration file uses two types of statements that can be:  

- declarations
- parameters

The declarations are the ones that describe the network topology.  
The parameters are telling the server how to do specific things,  
and they often have values for time, quantity, etc  

The configuration aspects of DHCP server revolves arround these topics:

-Define the network topology using subnet declarations
-Define the addresses available for dynamic allocation by using range declarations under subnet or pool
-Define other dhcp options like default-router, domain-name, domain-name-servers, mtu, netmask, broadcast
-Group subnet,pool, and host declarations under Group or Shared-Network declarations
-Identify DHCP request by using dhcp options like vendor class or client class, or other options
-Differentiate how requests are serviced by using classes, subclasses and pools

The basic building block is the *subnet* declaration.  
The subnet identifies a connected subnet for the dhcp server, so we need to have at least one subnet
for each network interface of the server, even if we don't allocate ip addresses from each of these networks.

If we want to allocate ips dynamically from a specific subnet then under that subnet declaration we need
to have a *range* declaration with the available ips  
You can have multiple *range* declaration under the same subnet or pool

If we server more than one subnet from under a single network interface, then those subnet declarations need
to be enclosed under *shared-network*

DHCP server will allocate ip addresses and a series of other parameters which are expressed as dhcp options.  

Most of the time we use the same set of parameters for a specific subnet, and a specific subnet
will be matched with a request either by using the gateway information, if the request is relayed, or by
mathing the subnet with the interface where the request came in.

If we want we can match the request by using other info like the hardware address, or even dhcp options that 
are transmitted by the client.

A common case is using the hardware address in a *host* declaration. In this case we use a host declaration the request will be matched
on the specified hardware ethernet address. 

Going further we can enclose specific options under the host declaration.  
The host declaration can contain also the fixed address parameters for assigning an ip address to the client,
but is not mandatory, in which case a subnet will be used for ip allocation.  
**If we use a fixed address to allocate an ip to host, that ip should not also be listed as being available for dynamic assignment.**  

Host declarations are matched to actual DHCP or BOOTP clients by matching the dhcp-client-identifier option specified in the host declaration
 to the one supplied by the client, or, if the host declaration or the client does not provide a dhcp-client-identifier option,
 by matching the hardware parameter in the host declaration to the network hardware address supplied by the client.

If we have some parameters that are the same for multiple hosts we can group host declarations under Group and
configure these parameters only once for all the group.


The last important declaration is the pool, which can be used to differentiate multiple types of hosts under the same subnet. You configure specific options for the pool and use allow and deny statements to match clients on specific pool.  
For example you can use allow knonwn-clients, which means that the pool matches only the clients that have matching host declarations in your config.

*I'm not entirely sure about the correct way to place pool in the config. It seems that you can enclose pool inside the subnet declaration,  
but you can also put it outside subnet, in which case you need to include a range statement enclosed directly inside pool.*

When a client is to be booted, its boot parameters are determined by consulting that client's host declaration (if any),  
 and then consulting any class declarations matching the client, followed by the pool, subnet and shared-network declarations
 for the IP address assigned to the client.
Scopes are never considered twice, and if parameters are declared in more than one scope,
 the parameter declared in the most specific scope is the one that is used. 


### A quick example of dhcpd.conf structure

\# Global Options
option domain-name "sandbox.lab";  
option domain-name-servers ns1.sandbox.lab, ns2.sandbox.lab;  

default-lease-time 600;  
max-lease-time 7200;  

shared-network 192-168 {  
	subnet 192.168.0.0 netmask 255.255.255.0 {  
		range 192.168.0.100  192.168.0.150;  
		option routers 192.168.0.1, 192.168.0.2;  
		pool pool1 {  
			range 192.168.0.200 192.168.0.210;
			allow known-clients; 
		  }  
		pool pool2 {  
			range 192.168.0.220 192.168.0.230;
			allow members of "foo";

		 }  
	subnet 192.168.1.0 netmask 255.255.255.0 {  
		range 192.168.1.100  192.168.1.150;  
		option broadcast-address 192.168.1.255;  
		option routers 192.168.1.1;  
		}  
	}

host pc1 {
	hardware ethernet 00:00:00:11:12:ab;
	filename "boot.conf";
	server-name "srv.sandbox.lab";
	next-server "srv2.sandbox.lab";  
	}

group {  
	option routers 192.168.1.1; 
	host server1 {  
		hardware-address aa:ab:10:11:12:bc;  
		fixed-address 192.168.1.101  
	}
}  


The group declaration is used normally to group together multiple host declarations, but you can put under it subnets, pools, and shared networks also.



### The DHCP Evaluation system 

The ISC DHCP Server, benefits from a powerfull evaluation system.  
You can use it to match on client requests directly or to match client request to specific classes or subclasses.  
To expand on the last sentence, you can have a conditional declaration with if, and else like this:


if option dhcp-user-class = "something" {  
	option domain-name "custom.lab"; 
	option domain-name-servers 127.0.0.1; 
	}  


## Expressions that can be used in the evaluation system:

- Equality - expression1 == expression2  
- Regex, and case-insensitive Regex - expression1 ~= expression2; expression1 ~~ expression2  
- Boolean comparison operators AND, and OR
- Negation operator Not
- Exists - exists option-name
- Known - returns true if the client whose request is being processed is known
- substring (data-expr, offset, length) - this is used to extract a string from a dhcp option
- suffix (data-expr, length) - this also extracts a string from a dhp option but only from the end
- lcase (data-expr)  - transform to lowercase
- ucase (data-expr)  - transform to uppercase
- pick-first-value(data-expr1 [...exprn])  

## DHCP Dynamic updates

This is a mechanism through which the dhcp server updates a dns zone with the A and PTR records for a client
First of all we need to configure the update mechanisms. We can choose between ad-hoc and interim, but only interim is supported.

- ddns-update-style interim;
- allow client-updates / ignore client-updates / deny client-updates
- ddns-domain-name *name*
- ddns-hostname *name*

*the ddns-domain-name and ddns-hostname are used mostly for ad-hoc update*

If DNS only allows secure updates, then we also need to configure the key

key DHCP\_UPDATER {  
	algorithm hmac-md5;  
	secret 03r92j09j203fj20fwe;  
};  

zone example.org {  
	primary 127.0.0.1;  
	key DHCP_UPDATER;  
};  

zone 17.127.10.in-addr.arpa {  
	primary 127.0.0.1;  
	key DHCP_UPDATER;  
}  



### DHCP Classing

You can use classes to match on different request, as an alternative to have options directly enclosed inside a conditional declaration

Classes can work in two modes:

- You declare a class and a condition to match for that class (most of the times you will use an if condition),  
 and then you use the class name in allow statements under pool
- You declare a class but specify only a parameter to be matched on by an aditional declaration, called subclass. In this case,
you can either configure custom options directly under the subclass, or use the subclass name in an allow statement under a pool.  


### Ex1:

class "clients-a" {  
 match if substring ( option dhcp-client-identifier, 1, 3) = "RAS";  
}   

pool  clients-a {  
	allow members of "clients-a";  
}  

### Ex2:  
 
class "clients-b" {  
 match substring ( option dhcp-client-identifier, 1, 3);  
}  
subclass "clients-blue" "value-blue";  
subclass "clients-red" 	"value-red";  
subclass "clients-brown" "value-brown" {  
	option 1;  
	option 2;  
}   

pool clients-blue {  
	alow members of "clients-blue"; 
	}  
pool clients-red" {  
	allow members of "clients-red";  
	}   

The data following the class name in the subclass declaration is a constant value to use in matching the match expression for the class. When class matching is done, the server will evaluate the match expression and then look the result up in the hash table. If it finds a match, the client is considered a member of both the class and the subclass. 

## Configuration elements reference:


- shared-network *name* {}
- subnet *subnet-number* netmask *netmask* {}
- range *low-address [high-address]*;
- host *hostname* {}
- group {}
- allow unknown-clients; deny-unknown-clients; ignore unknown-clients;
- allow bootp; deny bootp; ignore bootp
- allow client-updates; deny client-updates;

### pool declaration allow statements  

- known-clients;
- unknown-clients;
- members of "class";
- all clients;

## Parameters reference:

- authoritative
- not authoritative
- ddns-hostname *name*
- ddns-domainname *name*
- ddns-update-style *style* - ad-hoc, interim, or none
- default-lease-time *time*
- filename *"filename"*
- fixed-address address *[,address...]*;
- hardware *hardware-type hardware-address*
- host-identified option *option-name option-data*
- lease-file-name name;
- log-facility *facility*; by default facility is daemon, but can be any of the syslog facilities
- max-lease-time *time*
- min-lease-time *time*
- next-server *server-name*
- one-lease-per-client *flag*
- pid-file-name *name*
- ping-check *flag*

## DHCP Options reference:


- option bootfile-name *text*
- option broadcast-address *ip-address*
- option dhcp-client-identifier *string*
- option dhcp-lease-time *uint32*; used in a client request
- option domain-name *text*
- option domain-name-servers *ip-address [,ip-address...]*
- option domain-search *domain-list*
- option host-name *string*
- option interface-mtu *uint16*
- option ip-forwarding *flag*
- option ntp-servers *ip-address[,ip-address...]*
- option routers *ip-address[,ip-address...]*
- option subnet-mask *ip-address*
- option user-class *string*
- option vendor-class-identifier *string*

New options are declared as follows:

*option new-name code new-code = definition ;*   

The values of new-name and new-code should be the name you have chosen for the new option and the code you have chosen.  
The definition should be the definition of the structure of the option. 

### Examples of custom DHCP options:

- option use-zephyr code 180 = boolean;
- option use-zephyr on;

- option sql-server-address code 193 = ip-address;
- option sql-server-address sql.example.com;

- option sql-default-connection-name code 194 = text;
- option sql-default-connection-name "PRODZA";


