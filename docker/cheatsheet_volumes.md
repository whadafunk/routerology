# Docker Volumes CheatSheet


Docker volumes can be created at runtime (with *docker container run --mount* or *docker container run -v*), 
or they can be created in advance by using the command *docker volume create*.
This first paragraph is important to understand as well as understanding that bind volumes(or bind mounts) which are very common
to be created at runtime can be created in advance with docker volume create as well.

In most cases when you create a volume at runtime you need to specify a source and destination/target,
and the driver which is implied to be *local*, which is the default if not spefified otherwise.
Creating volume types other than bind mounts at run time is not very common, because is easier to understand 
and organize the command when creating volumes through the dedicated command *docker volume create*

In advanced uses, you will also specify the driver and driver options.
For the driver options, most common used are **type** and **device**, where device reads something as source of the data.  
Depending on the type, there is another option which is used to specify suboptions specific to that type.
I think that the mecanism underneath volumes local driver is *kernel mount* with different supported filesystems, 
and as you will see, the suboptions to docker volume create are similar with mount options specific to that particular filesystem.

- for nfs a suboption would be the ip address of the service, and rw/ro flags
- for a cifs type, we have user/password and uid suboptions

The volume types that you can create with local driver are:

- NFS -> type=nfs
- CIFS / SMB -> type=cifs
- Bind Mount -> type=none
- Block filesystem (ext4, iso9660, btrfs, zfs, etc) -> type=volume, o="type=ext4"
- Standard docker volume -> type=volume
- Tmpfs -> type=tmpfs

### Examples


docker volume create --driver local \
    --opt type=tmpfs \
    --opt device=tmpfs \
    --opt o=size=100m,uid=1000 \
    foo
----------------------------------------
docker volume create \
    --driver local \
    --opt type=btrfs \
    --opt device=/dev/sda2 \
    foo

----------------------------------------
docker volume create --driver local \
    --opt type=nfs \
    --opt o=addr=192.168.1.1,rw \
    --opt device=:/path/to/dir \
    foo

docker volume create \
    --driver local \
    --opt type=nfs \
    --opt o="vers=4,addr=<IP of NAS>,rw" \
    --opt device=:<Path on NAS> \
    v_portainer
----------------------------------------
docker volume create --driver local \
    --opt type=none \
    --opt device=/var/opt/my_website/dist \
    --opt o=bind web_data

docker volume create \
    --driver local \
    --opt type=cifs \
    --opt device=//<IP of NAS>/<Share on NAS> \
    --opt o=addr=1.2.3.4,uid=0,username=<smbuser>,password=<smbpassword>,nounix,file_mode=0770,dir_mode=0770 \
    v_portainer

*The addr suboption is required if you specify a hostname instead of an IP. This lets Docker perform the hostname lookup.*

docker run \
  --mount='type=volume,dst=/external-drive,volume-driver=local,volume-opt=device=/dev/loop5,volume-opt=type=ext4'


