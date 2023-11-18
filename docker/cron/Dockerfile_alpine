# Start with the basics
FROM alpine:latest
MAINTAINER gri.daniel@gmail.com
LABEL website=www.com

# The cron daemon itself should be run as root, even if you refer crontabs for different users
# ...Yes, I know root is the default
USER root

# A test environment variable; see if we can get this to the cron processes
ENV cron_var="quick brown fox"

# If we want to use cron we first have to install it
RUN 	apk update \
	&& apk add cronie


# COPY entrypoint.sh /root/

# If you want to run crontabs under specified user, then we first have to create it

RUN	adduser --disabled-password -g john -h /home/john -s /bin/sh john && \
	echo "john:pass123" | chpasswd


# We can allways mount an external crontab to the /var/spool/cron/crontabs/
COPY crontabs/root /root/crontab_file
RUN crontab -u root /root/crontab_file

# If you want to run crontabs under the previously created user
#COPY crontabs/john /home/john/crontab_file
#RUN crontab -u john /home/john/crontab_file

# It might also work if you copy the file directly under /var/spool/cron/crontabs/ with the name of the user


CMD ["/usr/sbin/crond","-f"]
