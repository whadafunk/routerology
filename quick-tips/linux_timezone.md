# Managing timezone in Linux Systems and Containers


Most Linux distributions use the tzdata package to provide timezone information.  
The timezone configuration / reconfiguration is done with *dpkg-reconfigure tzdata*  
There are two files that tzdata is using to set the timezone:

- /etc/timzone
- /etc/localtime

The */etc/timezone* file contains the name of the Timezone, like Europe/Bucharest  
The */etc/localtime* is a symbolic link to the timezone database for the selected location.  


When you are runining dpkg-reconfigure tzdata without any other parameters it will promtpt you  
to choose the desired timezone, with default selected zone as the one specified under /etc/timezone.  
In case of a container the interactive menu is not what we want, so we need to suppress that:  

```
ENV DEBIAN_FRONTEND=noninteractive
RUN echo "Europe/London" > /etc/timezone
RUN dpkg-reconfigure -f noninteractive tzdata
```

In the above Dockerfile snippet, we can see that first we write the /etc/timezone with the correct timezone,  
and after that, we run dpkg-reconfigure in noninteractive mode.

There is another simpler way to set timezone for containers, and that is by  
setting the evironment variable to the desired timezone value.  
Example:  

```
ENV TZ=Europe/London
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y tzdata
``
 
