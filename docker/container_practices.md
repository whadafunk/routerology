# A couple of container best practices

## There are images that are running under unprivileged user (apache, nginx, php-apache, etc). 
You need to be aware of what that user is, and set persistent files rights accordingly.  
For example, for apache based image the UID/GID is 33 which corresponds with www-data.  

## Use a startup script to start you containers

- Use variables in your script, like $DOCKER_NAME and $DOCKER_DIR
- Use options like: 

```
--detached, --rm, --interactive, --tty, --name, --hostname
--domainname, --dns, --network, --ip, --user, --privilege
--restart, --env, --logs, --volume, --mount
```

## Mount as persistent the following types of data

- config
- certificates
- data
- profiles
- logs

## Use shell mode for RUN and exec mode for ENTRYPOINT and CMD
