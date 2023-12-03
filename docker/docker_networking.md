# Docker Networking Cheatsheet

### Simple bridge docker interface

docker network create --driver bridge --subnet 172.17.0.0/24 --gateway 172.17.0.1
    --ip-range 172.17.0.0/26 --attachable  --opt com.docker.network.bridge.name=docker0
    custom_bridge

*you might think that for the bridge driver we need a parent interface but
that's not the case, because we are just creating a bridge but we don't attach yet
any interface to it.
The interfaces will be attached with each started container. A veth interface corresponding
to the started container to be more precise*

### MacVLAN docker interface

docker network create --driver macvlan --subnet xxxx --gateway xxx1 --opt parent=eth1
--opt macvlan_mode=bridge macnet


### IPVLAN docker interface
docker network create --driver ipvlan --subnet xxx --opt parent=eth1 --opt ipvlan_mode=l3
