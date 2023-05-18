# General notes on Alpine Linux



### Repositories


Alpine comes with some repositories configured but not all of them are enabled implicitly 
The repository system is named apk, and the configuration files are very similar with debian apt  
The configuration files are stored under */etc/apk/repositories*

There are a couple of branches that you will find in the repository configuration

**main**	
	
- base system packages
- packages that do not have dependencies in other repositories
- has a support cycle of 2 years
	
**edge**
	
- the development tree
- has a support cycle of 6 months

### Packages

Alpine, being a bare-bones distribution does not come with many tools installed  
The following is a list with a couple of these packages that are usualy needed

#### *Base*

- **gcc and make** - if you will need to build the tools package you'll also need these two
- **linux-headers** - also needed for local builds
- **open-vm-tools** / **virtualbox-guest-additions** / **qemu-guest-agent** / **xe-guest** - if you are on a virtualmachine 
- **shadow package** - includes: useradd, groupadd, usermod, chsh
- **bash** - if you are not happy with busybox which is the default
- **coreutils** - many of the tools bundled in busybox, in their standalone form: tail, sort, wc, whoami, etc
- **sudo** - the well known sudo package
- **vim**, **nano**, or **joe** - whichever you prefer
- **man-pages** - alpine does not come preinstalled with the man viewer nor the documentation pages
- **curl and wget**

- **create generic admin account**
- **update-ca-certificates**
- **copy ssh pub keys**
- **add admin account to groups: sudo, vboxsf, root, etc**
- **create the /var/log/admin files**

#### *Above base*

- **mc**, **htop**, **tmux**
- **git**
- **gpg**
- **wget** and **curl**
- **dig** , **bind9-utils**, **nslookup**
- **openssh**
- **figlet**

#### *Networking*

- **net-tools**, **nmap**, **tcpdump**, **ngrep**, **nc / netcat**, **hping**
- **iproute2**; by default this functionality comes from busybox
- **openvpn3**

#### *Security*

- **openssl**
- **fail2ban**
- **tripwire**

#### *Software build tools*

- **gcc** and **make** - if you want to compile from sources


#### *SMTP and Email tools*

- **ssmtp**, **msmtp**
- **swaks**


#### *Storage*

- **lvm2**
- **iscsi target and initiator**

>For lvm2 you need to add it service to rc-update - *rc-update add lvm2*
>For iscsi to work you need to start the service dbus - *rc-update add dbus*


### Man pages

- by default there is no man; it should be installed from mandoc package
- by default man pages are not installed for any package; you sould install the corresponding -doc package


### Docker

Docker can be installed straight from the alpine repository

>apk add docker docker-compose  
>rc-update add docker  
>service docker start  


### Alpine services

Alpine installs the initialization scripts for its services under */etc/init.d*, but these  
services are not configured to start automatically.  
We can manage these services with *rc-update*



