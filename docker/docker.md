# Dockerfile Explanations 


### Dockerfile is an instructionset that is used to build a specific container
### Main commands of a Dockerfile are:

- FROM
- MAINTAINER
- RUN
- COPY
- ENV
- WORKDIR
- LABEL
- CMD
- ENTRYPOINT
- VOLUME
- EXPOSE



> Pay attention that many of the docker commands have shell and exec forms (RUN, ENTRYPOINT, CMD)
> Commands that you run in exec mode do not pass through a shell and there are a couple effects
- Environment variables do not get interpreted
- If you want to run command with a different shell you can do that with RUN ["/bin/bash", "-c", "echo hello"]
- If CMD is used to pass arguments to the ENTRYPOINT command, then both commands should be specified in json format
- When you are using the shell format any runtime arguments will be ignored
- You can use the line continuation "\" only with shell mode (because it is a shell feature)
- The multiple command chaining with && can also be used only in shell mode

## Here are some details of the Dockerfile commands

- **FROM**  -> *FROM alpine:latest*
- **MAINTAINER** -> *MAINTAINER gri.daniel@gmail.com*
- **RUN** 
	- *RUN echo "hello world"* -> this is the shell form
	- *RUN ["echo","hello world"]* - this is the exec form which is not passed through a shell
	- Most of the times RUN is used to install apps with apt or dnf -> *dnf -y install vim*
- **COPY src dst** -> *COPY /home/user/docker/file /opt file* -> it will copy the file to the container /opt directory
- **WORKDIR** -> sets the workdir for all the following RUN commands
- **ENV** -> *ENV MYVAR=something*
	- You can use quotes if the value has spaces -> *ENV MYVAR="Some string with spaces"*
	- There is an alternative syntax that is not using the equal sign.
	- The first form has the advantage of being able to declare multiple environment variables on a single line
	- ENV declared variables can be referred in dockerfile with $var
- **EXPOSE** -> *EXPOSE 80/tcp*
	- If you want to expose multiple ports you should include multiple expose instructions
- **VOLUME** -> *VOLUME /myvolume* -> Creates a volume and adds it to the container at runtime
- **USER** -> *USER <user>[:<group>] or USER <UID>[:<GID>]*


*### The main difference between CMD and ENTRYPOINT is that CMD can be overriden  at runtime*
- **CMD**
	- *CMD [ "sh", "-c", "echo $HOME" ]*
	- *CMD echo $HOME*
- *ENTRYPOINT*

	- if both CMD and ENTRYPOINT are present in a Dockerfile, the CMD will be interpreted as parameter to the commands
	- specified with entrypoint.
	- again, the CMD can be overriden as parameters also

- .dockerignore -> path and file patterns to be excluded from build
	- */temp*
	- temp?
	- \# comments are possible with hashtag

