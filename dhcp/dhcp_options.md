
Important to understand there are server specific options (the ones that server sends to the client), 
and there are client specific options (that are sent by the client in the dhcp request).


Server DHCP Options

- 1 NetMask
- 3 Default Gateway
- 6 DNS
- 15 Domain Name
- 31 Lease Time
- 128 TFTP Server IP Address
- 150 TFTP Server Address
- 138 CAPWAP

Client Specific Option

- 60 Vendor Identifier Class
- 61 Client Identifier (usualy the mac address)
- 12 Host Name
- 55 Parameters request list



## Option 43 structure


The way option 43 option is interpreted depends on the vendor, but many times the format of the option is
a TLV Construct - Type Length Value

The most popular example is that of specifying the ip address of the WiFi Controller

Type 01 (Hex)
Length 04 (4 Bytes length in hex)
Value C0A801C8 (192.168.1.200 in hex) - each octet is transformed from decimal to hex

- 
