# Docker Compose Quick Overview

### Installation

>  sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose  
>  sudo chmod +x /usr/local/bin/docker-compose  
>  docker-compose --version  

### Docker Compose is like a playbook with instruction on how to deploy container infrastructure 

*docker-compose.yml* - this is the original name of the playbook file and it looks something like this


>version: "3.8"  
>services:  
>  servicename1:  
>    build:  
>    image:  
>    volumes:  
>    environment:  
>    network: *this was added in version 3.4*  
>    volumes:  
>    ports:  
>    command:  
> volumes:  
> networks:  


**Dashes represent Lists.**

*All members of a list are lines beginning at the same indentation level starting with a -*

### A list of tasty fruits
\-Apple  
\-Orange  
\-Strawberry  
\-Mango  

No Dashes Means that they are Key Value Pairs to a Dictionary.

    A dictionary is represented in a simple key: value form

martin:  
  name: Martin D'vloper  
  job: Developer  
  skill: Elite  

### The following two constructs are the same

environment:  
  RACK_ENV: development  
  SHOW: 'true'  
  SESSION_SECRET:  

*is the same as:*

environment:  
 \- RACK_ENV=development  
 \- SHOW=true  
 \- SESSION_SECRET  

## Here are the details of the docker-compose file syntax

**build:** /path/to/Dockerfile  
**image:** image_name:tag  
**labels:**  
  com.example.description: "Something Fishy"  
**labels:**  
  \- "com.example.description=something fishy"  
**network:** bridge  
**network:** none  
**command:** echo "hello world"  
**command:** ["echo","hello world"]  
**container_name:** my_fancy_container  
**depends-on:**  
   \-db  
   \-redis  

>docker-compose up starts services in dependency order. In the following example, db and redis are started before web.  
>docker-compose up SERVICE automatically includes SERVICE’s dependencies. In the example below, docker-compose up web also creates and starts db and redis.  
> docker-compose stop stops services in dependency order. In the following example, web is stopped before db and redis.


**restart_policy:** -> this is a new stanza that replaces restart 
  condition: on-failure | any | none 
  delay: 5s 
  max_attempts: 3  
  window: 120s -> how long to wait before deciding a restart has succeeded 

**restart:** "no"  
**restart:** always  
**restart:** on-failure  
**restart:** unless-stopped  

**devices:**  
  \- "/dev/ttyUSB0:/dev/ttyUSB0"  
**dns:** 8.8.8.8  
**dns:**  
  \- 8.8.8.8 
  \- 9.9.9.9  

**dns_search:** example.com  
**dns_search:**  
  \- dc1.example.com  
  \- dc2.example.com  

**entrypoint:** /code/entrypoint.sh  
**entrypoint:** ["php", "-d", "/bin/stuff.php"]  

**environment:**  
  VAR1: 25  
  VAR2: "some stuff"  
**environment:**  
  \- VAR1=25  
  \- VAR2="some stuff"  

**extra_hosts:**  -> *add entries to /etc/hosts*  
  \- "somehost:1.2.3.4"  
  \- "otherhost:1.3.2.1"  

**networks:**  
  \- some-network  
  \- other-network  

**ipv4_address:** 1.2.3.4  

**logging:**  
 driver: syslog | json-file  
 options:  
 syslog-address: "tcp://1.2.3.4:123"  

**ports:**  
  \- "3000"  
  \- "3000-3005"  
  \- "8000:8000"  
  \- "9090-9091:8080-8081"  
  \- "127.0.0.1:8001:8001"  
  \- "6060:6060/udp"  

**user:** postgresql  
**working_dir:** /code  
**domainname:** foo.com  
**hostname:** foo  
**mac_address:** 02:42:ac:11:65:43  
**privileged:** true  
**stdin_open:** true  
**tty:** true  

**volumes:**  
 \- [src]:target:[mode]  
 \- /opt/data:/var/lib/mysql:rw  

**volumes:** 
 \- type: volume  
   source: mydata  
   target: /data  
   read_only:   


**networks:**  
 net_name:  
	external: true  
	name: actual_name_of_the_network  

 net_name:  
	driver: bridge | macvlan | ipvlan | host | none  
	driver_opts:  
		foo: "bar"  
		baz: 1  
	attachable: true  
	ipam:  
	driver: default  
	config:  
	- subnet: 192.168.1.0/24  
	- gateway: 192.168.1.1  
