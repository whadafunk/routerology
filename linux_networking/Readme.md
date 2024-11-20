# Linux Networking Bits And Pieces

**A collection of configuration snippets and short docs about configuring networking stack in linux**



### loopback vs dummy vs alias


In the old days, the method to have another ip on your linux machine would be to create an interface alias, which works
like a secondary ip on the interface even though it looks like is creating a new interface


It can be done with ifconfig like in the following example but is a very outdated method

```
ifconfig lo:0 127.0.0.2 netmask 255.0.0.0 up
ifconfig lo:1 127.0.0.3 netmask 255.0.0.0 up
ifconfig lo:2 127.0.0.4 netmask 255.0.0.0 up
```

Another method, supported by the linux kernel for some time now is to configure secondary ip address with the *ip* command 
from the *iproute2* package.
Nowadays you can configure as many additional ip addresses you want

ip address add z.x.c.v/24 dev eth0

You can still use iproute2 like in the command above to create aliases. Basically you assign specific labels to the added
ip addresses

ip address add z.x.c.v/23 dev eth0 label eth0:1

If you will use ip address show to display your changes you will not see any new interface like eth0:1, but as soon
as you will use ifconfig -a, you will be able to see the labeled addresses as distinct interfaces.

The final way to add an ip address to your linux box, is to create a dummy interface, which needs a kernel module called dummy

*ip link add name dummy0 type dummy*


### Reflexions on the vlan filtering support


For those coming from cisco, the vlan configuration aproach in linux is a bit confusing, because there is no concept 
of trunk and access ports with linux. 
The way to look at ports in this context is like this:

- ports that are accepting a vlan tag or no
- ports that are putting untagged frames in a special vlan (some kind of native vlan)
- ports that are removing the vlan tag for specific vlan at egress


When you are using vlan filtering with virtual interfaces like dummy or veth you need to be aware that frames entering those interfaces will be untagged. 
Those frames are originated on the box itself and put on a specific interface enslaved to a bridge. If you want those frames to be tagged, then the ingress port should have a vlan with pvid configured. 
* One question though arises. A bridge port without a pvid will still be able to accept untagged frames?





