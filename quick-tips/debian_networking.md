# Debian networking configuration files

Configuration files for networking in Debian can be found under 
**/etc/network/** where you will find various files and folders like:

* interfaces
* interfaces.d/
* if-up.d/
* if-down.d/
* if-pre-up.d/

If you don't want to bother with all these files and folders you can just use 
the interfaces files where you can declare all your interfaces and have them 
configured in there

*Pay attention to the NetworkManager service. If that is running, it might 
configure your interfaces automatically*


## Interfaces file example

> source /etc/network/interfaces.d/*  
> auto lo  
> iface lo inet loopback  
>  
> auto eth0  
>> iface eth0 inet static  
> address 10.0.2.15  
> netmask 255.255.255.0  
> gateway 10.0.2.1  
> dns-domain sandbox.local  
> dns-nameservers 193.231.236.25  
> 
> auto eth1  
>> allow-hotplug eth1  
> iface eth1 inet static  
> address 192.168.56.15  
> netmask 255.255.255.0  

