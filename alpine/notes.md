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

- **shadow package**, includes: useradd, groupadd, usermod, chsh
- **bash**
- **coreutils**
- **sudo**
- **iproute2**; by default this functionality comes from busybox



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



