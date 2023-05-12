FROM php-apache-bit
MAINTAINER daniel.grigore@bit-soft.ro
RUN export DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y \
        cron \
        fping
COPY cron.d/php /etc/cron.d
COPY cron.d/glpi /root
RUN crontab -u www-data /root/glpi

# The following three lines are an alternative install of the crontab file for www-data
# Apparently the one above works better
#COPY cron.d/glpi /var/spool/cron/crontabs/www-data
#RUN chown -R www-data:www-data /var/spool/cron/crontabs/www-data
#RUN cron /var/spool/cron/crontabs/www-data
COPY ./wrapper_script.sh /root/wrapper_script.sh
ENTRYPOINT ["/root/wrapper_script.sh"]

