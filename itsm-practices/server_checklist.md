#  Checklists and ideeas for creating a generic server template
## The current list is for a debian based distro


### Create a summary of the machine in a text file under */var/log/admin/host_summary*

> **Configure sshd to display the content of host summary as motd at login**

- last change date (update the data/hour of the last important change)
- hostname fqdn (hostname and domainname)
- ip configuration (ip, netmask, gateway, configured dns servers)
- connected networks
- active users and their login methods (user/password, ssh-key, ldap)
- asset number
- technical owner
- business owner
- list of hosted services and their URIs
- disk partitions
- physical resources (cpu/memory/disk)
- physical location, or if a container/vm the virtual location (hostname - ip)

### Create a text-file journal of you operations under */var/log/admin/op_journal*

- Date of the operation
- Operation description
- Name of the persons that carried out the operation
- Important notes


### Generic system actions

- If needed configure the required apt repositories (php, docker, openvpn)
- apt-get update && apt-get upgrade
- Review the list of installed packages, and remove unneeded packages
	> apt-mark showmanual  
	> produce a list of installed packages and save it unde */var/logs/admin*  
	> apt list --installed > */var/log/admin/pkg-list_**date**  
- remove unwanted packages
- review the list of services with *systemctl list-units --type=server*
- review open ports with *netstsat -an*
- check for firewall services (iptables/nftables/firewalld/ufw, etc)
	- systemctl list-units | grep firewall
	- systemctl list-units | grep fw
	- systemctl list-units | grep tables
- disable firewall
	- systemctl disable ufw
	- systemctl disable nftables
- disable apparmor
- check default systemd target with *systemctl get-default*
- set default target to multiuser with *systemctl set-default multiuser.target*
- update the list of trusted certificates
	> copy the certificates unde */usr/local/share/ca-certificates* or /usr/share/ca-certificates
	> review content of /etc/ca-certificates.conf
	> run dpkg-reconfigure ca-certificates
	> run update-ca-certificates
- copy the wildcard certificate under /etc/ssl/

### System services

- install openssh if not installed already
- review sshd configuration
- configure ssh banner message
- configure /etc/motd (you can create a symbolic link to /var/log/admin/summary)
- install and configure fail2ban
- install and configure tripwire
- install backup & configure backup agent
- install & configure asset management agent like OCS
- install and configure VNC if desktop environment needed on server


## System management tools

- apt-transport-http
- apt-transport-tor
- adduser
- sudo
- gnupg
- curl
- wget
- vim
- ca-certificates
- lsb-release
- mc
- htop
- tftp client
- git
- screen / tmux
- net-tools
- iproute2
- bind9-dnsutils
- tcpdump
- nmap
- ngrep
- netcat

## Mail and notifications

- SSMTP or MSMTP


## Authentication packages

- nss ldap
- pam ldap


## Management

- Cockpit
- Webmin
- Ajenti


