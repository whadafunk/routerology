# A couple of thoughts and lessons I learned from using docker

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
