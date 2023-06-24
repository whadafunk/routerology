# Docker Compose Quick Overview

### Installation

>  sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose  
>  sudo chmod +x /usr/local/bin/docker-compose  
>  docker-compose --version  

### Docker Compose is like a playbook with instruction on how to deploy container infrastructure 

```
The Compose file is a YAML file defining *version* (DEPRECATED), *services* (REQUIRED), *networks*, *volumes*, *configs* and *secrets*. The default path for a Compose file is *compose.yaml* (preferred) or *compose.yml* in working directory. Compose implementations SHOULD also support *docker-compose.yaml* and *docker-compose.yml* for backward compatibility. If both files exist, Compose implementations MUST prefer canonical compose.yaml one.
```
*docker-compose.yml* - this is the original name of the playbook file and it looks something like this


>version: "3.8"  
>services:  
>  servicename1:  
>    build:  
>    image:  
>    volumes:  
>    environment:  
>    networks: *this was added in version 3.4*  
>    volumes:  
>    ports:  
>    command:  
> volumes:  
> networks:  
> configs:
> secrets:


**Dashes represent Lists in YAML**

*All members of a list are lines beginning at the same indentation level starting with a -*

### A list of tasty fruits
\-Apple  
\-Orange  
\-Strawberry  
\-Mango  

No Dashes Means that they are Key Value Pairs to a Dictionary.

    A dictionary is represented in a simple key: value form

martin:  
  name: Martin Developer  
  job: Developer  
  skill: Elite  

There are cases in compose file when you can use both list or a dictionary collection for a specific object.  
Environment is a good such example, but lists seem to be more suited for single values, and not for key:value pairs. 
Like for example with networks, or volumes, devices, dns, dns-search, extra-hosts, ports, expose

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

*The list version in this case is not adding clarity or simplify things*

## Docker compose networking

>By default Compose sets up a single network for your app. Each container for a service joins the default network 
>and is both reachable by other containers on that network, and discoverable by them at a hostname identical to the container name.
>Your app’s network is given a name based on the *“project name”*, which is based on the name of the directory it lives in. 
>You can override the project name with either the *--project-name* flag or the *COMPOSE_PROJECT_NAME* environment variable.

Is best to use the *name:* element for the networks defined in compose.yaml, because if you don't the network name will be  
prefixed by the project's name.
In case of external networks, I think you can get away without using the *name:* elements, and docker will try to find a name 
that matches the network itself, but if you want to be sure you can use the *name:*. Also *name:* allows you to use 
an environment variable as value. Ex: *name: "${NETWORK_ID}"*

## Here are the details of the docker-compose file syntax

**build:** /path/to/Dockerfile  
**image:** image_name:tag  
**labels:**  
  com.example.description: "Something Fishy"  
**labels:**  
  \- "com.example.description=something fishy"  
**networks:** bridge  
**networks:** none  
**command:** echo "hello world"  
**command:** ["echo","hello world"]  
**container_name:** my_fancy_container  
**depends-on:**  
   \-db  
   \-redis  

>docker-compose up starts services in dependency order. In the following example, db and redis are started before web.  
>docker-compose up SERVICE automatically includes SERVICE’s dependencies. In the example below, docker-compose up web also creates and starts db and redis.  
>docker-compose stop stops services in dependency order. In the following example, web is stopped before db and redis.


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

ipv4_address: 1.2.3.4 -> this option should be placed inside a network section 

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
	gateway: 192.168.1.1 -> this is only supported in version 2 

*You can define a default network like this*
networks:  
  default:  
    external:  
      name: my-pre-existing-network
