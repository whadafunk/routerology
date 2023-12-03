# Docker Compose Quick Overview

Compose is a tool for defining and running multi-container Docker applications. 
With Compose, you use a YAML file to configure your application's services.  
Then, with a single command, you create and start all the services from your configuration.

### Installation

>  sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose  
>  sudo chmod +x /usr/local/bin/docker-compose  
>  docker-compose --version  

Nowadays, compose has been included as a docker subcommand, so it will be available with any docker installation.

### Docker Compose is like a playbook with instruction on how to deploy container infrastructure 

```
The Compose file is a YAML file defining *version* (DEPRECATED), *services* (REQUIRED), *networks*, *volumes*, *configs* and *secrets*.  
The default path for a Compose file is *compose.yaml* (preferred) or *compose.yml* in working directory.  
Compose implementations SHOULD also support *docker-compose.yaml* and *docker-compose.yml* for backward compatibility.  
If both files exist, Compose implementations MUST prefer canonical compose.yaml one.
```
*docker-compose.yml* - this is the legacy name of the playbook file and it looks something like this:


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

A dictionary is represented in a **simple key: value** form

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

### Top-Level Networks Example

    networks:
      default:
        external: true
        name: bridge
      macnet1:
        driver: macvlan
        driver_opts:
          parent: enp0s8
          macvlan_mode: bridge
          ipvlan_mode: l2 | l3
        attachable: true
        ipam:
          driver: default
          config:
           - subnet: 192.168.1.0/24
             ip_range: 192.168.1.128/25
             gateway: 192.168.1.1
             aux_addresses:
               host1: 192.168.1.10
               host2: 192.168.1.11
      hostnet:
        external: true
        name: host

Here are a couple of driver options from the implicit bridge network:

            "com.docker.network.bridge.enable_icc": "true",
            "com.docker.network.bridge.enable_ip_masquerade": "true",
            "com.docker.network.bridge.host_binding_ipv4": "0.0.0.0",
            "com.docker.network.bridge.name": "docker0",
            "com.docker.network.driver.mtu": "1500"


## Docker compose volumes

Volumes define mount host paths or named volumes that are accessible by service containers. You can use volumes to define multiple types of mounts; volume, bind, tmpfs, or npipe.  
If the mount is a host path and is only used by a single service, it can be declared as part of the service definition. 
To reuse a volume across multiple services, a named volume must be declared in the top-level volumes key.

There is a short and a long syntax for using volumes under services in docker compose:

### The short syntax, 

uses a single string with colon-separated values to specify a volume mount 

> (VOLUME:CONTAINER_PATH), 
> or an access mode (VOLUME:CONTAINER_PATH:ACCESS_MODE).

**ACCESS_MODE:** A comma-separated , list of options:

   *  rw: Read and write access. This is the default if none is specified.
   *  ro: Read-only access.

### The long syntax,
The long form syntax allows the configuration of additional fields that can't be expressed in the short form

* type: The mount type. Either volume, bind, tmpfs, npipe, or cluster
* source: The source of the mount, a path on the host for a bind mount, or the name of a volume defined in the top-level volumes key. Not applicable for a tmpfs mount.
* target: The path in the container where the volume is mounted.
* read_only: Flag to set the volume as read-only.
* volume: Configures additional volume options:
 *   nocopy: Flag to disable copying of data from a container when a volume is created.
* tmpfs: Configures additional tmpfs options:
  *   size: The size for the tmpfs mount in bytes (either numeric or as bytes unit).
  *   mode: The file mode for the tmpfs mount as Unix permission bits as an octal number.

See the follwing examples and try to figure it out

**volumes:**  
 \- [src]:target:[mode]  
 \- /opt/data:/var/lib/mysql:rw  

**volumes:** 
 \- type: volume  
   source: mydata  
   target: /data  
   read_only:   


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


## Top Level Networks Object

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


## Top Level Volumes Object

The top-level volumes declaration lets you configure named volumes that can be reused across multiple services.  
To use a volume across multiple services, you must explicitly grant each service access by using the volumes attribute within the services top-level element.  
The volumes attribute has additional syntax that provides more granular control.

- **name:** the name of the volume
- **external:** true or false
- **driver:** local (is the only driver supported by default on most systems)
- **driver_opts:** these options differ depending on type (nfs,cifs,none, etc)
        type: nfs
         o: "addr=10.40.0.199,nolock,soft,rw"
         device: ":/docker/example"

### Here is an example for a smb/cifs volume
 nas-share: \
    driver_opts: \
      type: cifs \
      o: "username=[username],password=[password]" \
      device: "//my-nas/share"

### Here is an example for a local bind volume

a_volume: \
    driver: local \
    driver_opts: \
      type: none \
      o: bind \
      device: "/opt/volumes/a_volume" \
    name: "a_volume" '

### If you just want to create just a named volume, do not specify anything else than name:

named_volume:
    name: my_named_volume


*The interesting thing to note here is that there are multiple types supported with local driver:
 none, cifs, nfs, tmpfs, btrfs)*

docker volume create --driver local \
    --opt type=tmpfs \
    --opt device=tmpfs \
    --opt o=size=100m,uid=1000 \
    foo

docker volume create --driver local \
    --opt type=btrfs \
    --opt device=/dev/sda2 \
    foo


docker volume create --driver local \
    --opt type=nfs \
    --opt o=addr=192.168.1.1,rw \
    --opt device=:/path/to/dir \
    foo

docker volume create --driver local \
    --opt type=none \
    --opt device=/var/opt/my_website/dist \
    --opt o=bind web_data

docker volume create \
    --driver local \
    --opt type=nfs \
    --opt o="vers=4,addr=<IP of NAS>,rw" \
    --opt device=:<Path on NAS> \
    v_portainer

and

docker volume create \
    --driver local \
    --opt type=cifs \
    --opt o=addr=<IP of NAS>,rw \
    --opt device=//<IP of NAS>/<Share on NAS> \
    --opt o=uid=0,username=<smbuser>,password=<smbpassword>,nounix,file_mode=0770,dir_mode=0770 \
    v_portainer

