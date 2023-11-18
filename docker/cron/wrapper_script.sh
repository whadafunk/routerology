#!/bin/bash


# Start the second process
service cron start

# Start the first process
/usr/local/bin/apache2-foreground

# Wait for any process to exit
wait -n

# Exit with status of process that exited first
exit $?

