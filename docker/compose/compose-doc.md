# Docker Compose Overview

Compose is a tool for defining and running multi-container Docker applications. 
It is basically a process that controls the starting and stopping of containers and their resources, based of a configuration file.
With Compose, you use a YAML configuration file which is called compose.yaml  
Then, with a single command, you create and start all the services from your configuration.
The docker Compose is like a playbook with instruction on how to deploy container infrastructure 

### Docker Compose Installation

```
sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose  
sudo chmod +x /usr/local/bin/docker-compose  
docker-compose --version  
```

Nowadays, compose has been included as a docker subcommand, so it will be available with any docker installation.



The Compose file is a YAML file defining *version* (DEPRECATED), *services* (REQUIRED), *networks*, *volumes*, *configs* and *secrets*.  
The default path for a Compose file is *compose.yaml* (preferred) or *compose.yml* in working directory.  
Compose implementations SHOULD also support *docker-compose.yaml* and *docker-compose.yml* for backward compatibility.  
If both files exist, Compose implementations MUST prefer canonical compose.yaml one.

Enlisting all the commands and parameters from compose.yaml, is not in the scope of this document, and is recommended that you
Open the docker compose reference in a separate window when writing such compose config files.

[“Docker Compose Reference”](https://docs.docker.com/reference/compose-file/)


*docker-compose.yml* - this is the legacy name of the playbook file and it looks something like this:

> version: "3.8"  
> **services:**  
>  servicename1:  
>    build:  
>    image:  
>    volumes:  
>    environment:  
>    networks: *this was added in version 3.4*  
>    volumes:  
>    ports:  
>    command:  
> **volumes:**  
> **networks:**  
> **configs:**
> **secrets:**

### Docker compose commands and runtime parameters

**docker-compose up** starts services in dependency order.  
In the following example, db and redis are started before web.  
**docker-compose up SERVICE** automatically includes SERVICE’s dependencies.  
In the example below, docker-compose up web also creates and starts db and redis.  
**docker-compose stop** stops services in dependency order.   
In the following example, web is stopped before db and redis.

docker compose config
docker compose build [SERVICE]
docker compose images
docker compose exec
docker compose logs
docker compose ls
docker compose ps
docker compose restart
docker compose up
 --detach -d
 --scale
 --file -f
 --project-name -p



### Docker compose project name

Every docker compose.yaml file is associated with a project name which by default gets the name of the folder in which the compose.yaml resides. 
If you want you can control the name of the compose project using these methods:

- Command Line runtime param —project-name
- Environment Variable COMPOSE_PROJECT_NAME
- Top Level stanza in the compose.yaml. name:

The name of the project is prefixed to all network names created by compose, if you don’t specify a network name

### Yaml Specifics

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




### Top Level Networks Object

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
        	gateway: 192.168.1.1 -> *this is only supported in version 2* 

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



## Docker compose networking

By default Compose sets up a single network for your app.  
Each container for a service joins the default network 
and is both reachable by other containers on that network, 
and discoverable by them at a hostname identical to the container name.  
Your app’s network is given a name based on the *“project name”*, 
which is based on the name of the directory it lives in.   


Is best to use the *name:* element for the networks defined in compose.yaml, because if you don't the network name will be prefixed by the project's name.   
In case of external networks, I think you can get away without using the *name:* elements, and docker will try to find a name that matches the network itself, but if you want to be sure you can use the *name:*.   
Also *name:* allows you to use an environment variable as value. Ex: *name: "${NETWORK_ID}"*

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

Volumes define mount host paths or named volumes that are accessible by service containers.  
You can use volumes to define multiple types of mounts; volume, bind, tmpfs, or npipe.  
If the mount is a host path and is only used by a single service, it can be declared as part of the service definition. 
To reuse a volume across multiple services, a named volume must be declared in the top-level volumes key.

There is a short and a long syntax for using volumes under services in docker compose:

### The short syntax 

uses a single string with colon-separated values to specify a volume mount 

        (VOLUME:CONTAINER_PATH), 
        or an access mode (VOLUME:CONTAINER_PATH:ACCESS_MODE).

**ACCESS_MODE:** A comma-separated , list of options:

   *  rw: Read and write access. This is the default if none is specified.
   *  ro: Read-only access.

### The long syntax

The long form syntax allows the configuration of additional fields that can't be expressed in the short form

* type: The mount type. Either volume, bind, tmpfs, npipe, or cluster. the volume type under services differ from what you use under top level volumes
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
         - [src]:target:[mode]  
         - /opt/data:/var/lib/mysql:rw  

        **volumes:** 
         - type: volume | bind 
           source: mydata  
           target: /data  
           read_only:   

