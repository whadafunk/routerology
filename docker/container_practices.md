# A couple of container best practices

## Container Process Privileges
 
There are container images that are running under unprivileged user (apache, nginx, php-apache, etc). 
You need to be aware of what that user is, and set persistent files permissions accordingly.  
For example, for apache based image the UID/GID in Debian, is 33 which corresponds with www-data user, 
so if that apache will need to access any files those files will need to be readable by UID 33.  
The permissions system will impact you mostly when mounting persistent volumes in the container, because there might be 
a discrepancy between the running container UID and the owner of the persistent files.

## Use a startup script to start you containers

Most of the times you will construct a more complex line to start your container, which can do things like:

- mounting peristent storage
- configuring networking
- enable interactive and terminal
- set container name
- publish ports
- specify user for the running processes
- declare environment variables
- specify hostname
- add / drop container privileges
- remove container when stopped
- restart policy

```
--detached, --rm, --interactive, --tty, --name, --hostname
--domainname, --dns, --network, --ip, --user, --privilege
--restart, --env, --logs, --volume, --mount
```
Is best to enclose all those options in a startup script.

- Use variables in your script, like $DOCKER_NAME and $DOCKER_DIR
- Comment your script
- Store the script in a special folder structure dedicated to that container


## Mount as persistent the following types of data

- **config**
- certificates
- **data**
- **logs**
- profiles

## Use shell mode for RUN and exec mode for ENTRYPOINT and CMD
