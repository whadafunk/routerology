# Reloading partition table without rebooting


### Usualy you get an error message like this one below


> The partition table has been altered.
> Calling ioctl() to re-read partition table.
> Re-reading the partition table failed.: Device or resource busy
> 
> The kernel still uses the old table. The new table will be used at the next reboot or after you run partprobe(8) or kpartx(8).  


There are a couple of tools you can use to reload the partition table


- partprobe /dev/sdb
- blockdev --rereadpt -v /dev/sdb
- hdparm -z /dev/sdb


After running one of these commands you should check the output of *dmesg*
