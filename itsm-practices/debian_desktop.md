# DESKTOP INSTALLATION JOURNAL FOR DEBIAN 10 (BUSTER)

## Desktop Environment: Gnome

### Modify swappiness

This is a kernel parameter, that instructs the kernel about the percentage of swap it should use
The parameter is vm.swappiness and the reading is how much free ram we should have left before starting to use swap.
I usually put a low number here like 10, so the swap will be used when I have less than 10% of my ram memory available.

The way to do this permanent is by modifying /etc/sysctl.conf


### Gnome Extensions:

	- Applications menu
	- Dash to dock
	- Places status indicator
	- Remove drive menu
	- Screenshot tool
	- User themes
	- Workspace indicator
	- Draw on your screen
	- Caffeine
	- Show desktop button


### Evolution Exchange support
In order for Evolution to support Exchange as source you need to install
the *evolution-ews* package. 

In older versions of linux there was another package
that is not supported anymore which is called *evolution-mapi*.

### Configure Display manager

If you're not happy with the default *gdm* and you have installed one of the
alternative display managers like *lightdm* or *mdm*, you can configure which
one the system will use like this:  *dpkg-reconfigure gdm*

### Keepas2 Installation

After the installation of the keepass package, in order for auto-type to work you need
to install the *xdotool* package and then configure a key combo with the following command
*/usr/bin/keepass2 --auto-type*

### apt transport

If you need to use atp with https or tor, you have to install specific packages

* apt-transport-http
* apt-transport-tor

### brackets installation (libcurl problems)

 just fixed it but had to rebuild the deb package.

    Download the .deb installer file
    Inside the folder your downloaded the file run dpkg-deb -R ./Brackets.Release.1.12.64-bit.deb Brackets
    Edit file Brackets/DEBIAN/control and replace libcurl3 with libcurl4
    Rebuild .deb installer running dpkg-deb -b Brackets Brackets-fixed.deb
    Install Brackets using the fixed installer running sudo dpkg -i Brackets-fixed.deb

### OpenVPN3

This is a much better option than openvpn, because it has a modern architecture that is using D-Bus, so it will not need root permissions, and it works better in general.

You can find the package and installation details here:

[OpenVPN3](https://openvpn.net/cloud-docs/openvpn-3-client-for-linux/)

### Other packages

* mc -> midnight commander
* htop -> enhanced top
* cutecom 
* minicom 
* deluge -> torrent client
* bind -> I do lots of DNS engineering with my virtual labs, and bind gives me the flexibility I need
* tftp client and server 
* git
* gitkraken
* figlet / toilet -> ascii art generator
* jp2a -> ascii art generator from jpeg
* forticlient-sslvpn -> an opensource fortinet vpn client (works)
* openfortivpn & openfortigui -> an alternative forti vpn client
* screen / tmux
* vscode
* pidgin
* virtualbox
* grip - *a webserver writen in python, that can render markdown pages*
* net-tool (ifconfig, traceroute, etc)
* bind9-dnsutils (nslookup...)

### Installing Adobe Flash

Download the package:
*$ wget https://fpdownload.adobe.com/get/flashplayer/pdc/32.0.0.255/flash_player_npapi_linux.x86_64.tar.gz*

Unpack it:
*$ tar zxpvf flash_player_npapi_linux.x86_64.tar.gz*

copy the libflashplayer.so to Mozilla plugins directory using the cp command. For this, execute the below command in Terminal:
*$ sudo cp libflashplayer.so /usr/lib/Mozilla/plugins/*

Now copy the flash player configurations files to /usr directory using below command,
Then execute the following command to copy the configuration files to /usr directory :
*$ sudo cp –r usr/* /usr*

*An alternative would be to install the pepperflash package which is in the official repository but for me, although the *
*installation went fine, the plugin didn't work*

## Installing Java JDK

You can install the openjdk version from the official repository or you can download the official java jdk from oracle website

### VI

The default vi has some problems related to key-mapping. For example when you press the directional keys, instead of moving the cursor
it will type some character. 
You can solve this issue by putting some configuration options in the vi config file or by installing the full vim package


### Other software

* curl / wget
* tcpdump
* nmap
* ngrep
* netcat
* burp
* wireshark

### Security tools

* sqlmap
* aircrack
* nikto
* yersinia
* thc hydra
* reaver
* kismet
* metasploit
* tor
* maltego
* ettercap
* hashcat
* owasp-zap
* websploit
 


