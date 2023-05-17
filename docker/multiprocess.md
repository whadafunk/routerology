# Running multiple processes in docker.


One use case for multiple processes inside a container that I met often is a php container with cron jobs.


```
#!/bin/bash

# Start the first process
./my_first_process &
  
# Start the second process
./my_second_process &

# Wait for any process to exit
wait -n
  
# Exit with status of process that exited first
exit $?

```
