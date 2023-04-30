# Docker thoughts and best practices



## A couple of thoughts and lessons I learned from using docker

- You might want to have peristency either for the config or the data of the app inside the container
- For data persistency you would usualy use a volume. Something like *docker run -v ./www_data:/var/www/html*
- The same volume technique can be used for persistency of a config file *docker run -v ./httpd.conf:/etc/httpd/httpd.conf*
	- The volume technique used above has the advantage that you can modify the persistent data (config or appdata)
	from the main userspace, and you might not even have to restart the container
- Another technique for persistency that is used mostly with config files is to write a docker file that builds a custom
container and copies the config file inside
>FROM httpd:2.4  
>COPY ./my-httpd.conf /usr/local/apache2/conf/httpd.conf

To obtain a valid config file from httpd, you have also a couple of options

- $ docker run --rm httpd:2.4 cat /usr/local/apache2/conf/httpd.conf > my-httpd.conf 
- $ docker run httpd:2.4, then use docker cp container:/usr/local/apache2/conf/httpd.conf ./my-httpd.conf

There are some container images, like mysql and mariadb, that need some environment variables at runtime.  
In case of mysql these are at minimum the root user and password for the server:

- MARIADB_ROOT_PASSWORD
- MARIADB_ALLOW_EMPTY_ROOT_PASSWORD
- MARIADB_ROOT_HOST
> The following three commands are used to create a database and a user
and assign GRANT ALL to the created user on the created database
- MARIADB_DATABASE
- MARIADB_USER
- MARIADB_PASSWORD

*$ docker run --detach --name some-mariadb --env MARIADB_USER=example-user --env MARIADB_PASSWORD=my_cool_secret --env MARIADB_ROOT_PASSWORD=my-secret-pw  mariadb:latest*

## Runtime container params

Some of the commands of the docker file are used at the container runtime, and they do not produce changes in the storage layer at build time.
These commands are: 

- Environment variables
- User ID of the running process
- Exposed Ports
- Volume


## User and Group IDs

- The credentials of the running process can be specified with option user or with the USER parameter in Dockerfile
- By default if no user is specified docker runs as root
- Docker only cares about user ID, and not about user name associated with that ID (the name in the container can differ 
	from the one in the host)
- If you are not runing containers as root there might be access issues inside the container; you are a limited user
- There is a feature that maps the root in the container to a normal user in the host
- There is another way which you can run a process inside the container as non-root. And that is by the way of su
- Regarding the last option it can be controlled in an init script with env variables

## Docker init system

Starting with Docker 1.13, an initialization system based on tini has been implemented for Docker.

```
Tini is a tiny and simplest init available for containers. It works by spawning a single child and waiting for it to exit while reaping the zombie processes as well as performing signal forwarding.
```

Tini can be used in more than one way:

- Using the --init option from *docker container run*
- Using prebuilt tini images, and modifying ENTRYPOINT to *ENTRYPOINT ["/usr/local/bin/tini", "--", "/docker-entrypoint.sh"]*
- Using precompiled tini packages 
- Manually adding Tini *ADD https://github.com/krallin/tini/releases/download/${TINI_VERSION}/tini /tini*

The easiest way to use tini is by leveraging the --init option in docker  
This way, whatever you specified in ENTRYPOINT or CMD is passed as argument to /sbin/tini

>Is recomended to use --init whenever you have a container with more than one process
>A common use-case for me is when I need cron jobs to run in the same container


## Docker and Cron

Running cron tasks inside a container is not very straight forward. There are multiple options to do that:

	- Running the cron jobs on the host -> docker container exec *container_name* command
	- Running the cron jobs on a dedicated container (that is only running cron) -> docker container exec *container-name* command
	- Running the cron jobs as an additional service in the same container, an using tini or other container init system.


## Docker logging

Docker fetches the stdout and stderr streams and passes them to the logging drivers which in turn writes them to the desired location:
	- log file
	- log collector
	- log management service
By default docker uses the JSON-file driver, which will write all the logs in json format on the docker host,  
under */var/lib/docker/containers/ID/ID-json.log*
We can display these logs by using the command *docker container logs*.  
Here are a couple of usefull parameters of this command:  
	- --folow, -f -> Display logs continuously
	- --tail, -n  -> Specify the number of lines from the end to display
	- --details   -> Display additional details
	- --since
	- --until
	- --timestamps, -t 

To run the container with a different log driver use *--log-driver* parameter.  
There is also the *--log-opt* parameters which is used to supply additional parameters depending on the driver used.  

Example:
```
--log-driver=syslog \
  	--log-opt syslog-address=udp://172.16.100.1:514 \
  	--log-opt tag=${DOCKER_NAME} \
  	--log-opt syslog-format=rfc5424 \
	--log-opt mode=non-blocking
	--log-opt max-buffer-size=8M
	--log-opts max-size=8M
	--log-opts max-file=5
```

In the example above the last two options are relevant to the *json-file* logging driver.
Also, there is the non-blocking mode specification, which frees the application from waiting to the logging system.  
Basically the logs are first writen in a RAM buffer, and then writen to disk.
The tradeoff wih non-blocking mode is the risk of losing some logs if the RAM buffer fails, but you can gain in container speed.

