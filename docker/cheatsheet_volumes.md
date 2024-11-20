# Docker Volumes CheatSheet


Docker volumes can be created at runtime (with *docker container run --mount* or *docker container run -v*), 
or they can be created apriory by using the command *docker volume create*

Do not confuse volumes with bind mounts, which can only be created at runtime.

In most cases when you create a volume you need to specify a source and destination, and the driver is implied 
to be *local*, which is the default if not specified otherwise.
In advanced uses, you will also specify the driver and driver options.
For the driver options, most common used are type and device, where device reads something as source of the data
Depending on the type, there is another option which is used to specify suboptions specific to that type.
Suboptions examples:
- for nfs a suboption would be the ip address of the service, and rw/ro flags
- for a cifs type, we have user/password and uid suboptions





docker volume create --driver local \
    --opt type=tmpfs \
    --opt device=tmpfs \
    --opt o=size=100m,uid=1000 \
    foo

docker volume create --driver local \
    --opt type=btrfs \
    --opt device=/dev/sda2 \
    foo


docker volume create --driver local \
    --opt type=nfs \
    --opt o=addr=192.168.1.1,rw \
    --opt device=:/path/to/dir \
    foo

docker volume create --driver local \
    --opt type=none \
    --opt device=/var/opt/my_website/dist \
    --opt o=bind web_data

docker volume create \
    --driver local \
    --opt type=nfs \
    --opt o="vers=4,addr=<IP of NAS>,rw" \
    --opt device=:<Path on NAS> \
    v_portainer

and

docker volume create \
    --driver local \
    --opt type=cifs \
    --opt device=//<IP of NAS>/<Share on NAS> \
    --opt o=uid=0,username=<smbuser>,password=<smbpassword>,nounix,file_mode=0770,dir_mode=0770 \
    v_portainer


docker run \
  --mount='type=volume,dst=/external-drive,volume-driver=local,volume-opt=device=/dev/loop5,volume-opt=type=ext4'


docker volume create \
	--driver local \
	--opt type=volume \
	--opt device=/dev/loop5 \
	--opt o="type=ntfs" \
	my_vol
