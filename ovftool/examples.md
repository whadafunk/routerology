# OVFTOOL EXAMPLES


### Deploy an OVF package on an ESXi host.

> ovftool package.ovf vi://my.esx-machine.example.com/

### If your host has multiple data stores, use the -ds option:

> ovftool package.ovf -ds=storage1 vi://my.esx-machine.example.com/ 

### Deploying an OVF Package to vCenter Server

### The following command deploys testVM.ovf from a local Windows disk to a data store named storage1 in host (12.98.76.54) from vCenter (12.34.56.789). the VM will be named myVM on the host.

> ovftool -ds=storage1 -n=myVM C:\testVM.ovf vi://user1:passwd@12.34.56.789/?ip=12.98.76.54.

### OVF Tool can power on a virtual machine or vApp after deployment.

> ovftool --powerOn package.ovf vi://MyvCenterServer/?dns=fast-esx-host1.example.com. 

### You can deploy an OVF package from OVF Tool into vCloud Director.

> ovftool --net:"VM Network=intnet" LAMP.ovf "vcloud://jd:PASSWORD@example.com:443/? org=myOrg&vapp=test1&vdc=orgVdc&catalog=catalog" 
> ovftool http://my_ovflib/vm/my_vapp.ovf \
           vcloud://username:pass@my_cloud?org=MyOrg&vdc=MyVDC&catalog=MyCatalog&vapp=myVapp

(Imports an OVF from http into a vCloud instance and names the vApp myVapp) 

### The following command imports a virtual machine from vSphere into a vCloud instance and names the resulting vApp ‘myVapp’.

> ovftool vi://username:pass@my_host/my_datacenter/vm/my_vm_folder/my_vm_name \
           vcloud://username:pass@my_cloud?org=MyOrg&vdc=MyVDC&catalog=MyCatalog&vapp=myVapp
           
### The following command exports a Virtual Machine from a vCloud instance into an OVF package.

> ovftool vcloud://username:pass@my_cloud?org=MyOrg&vdc=MyVDC&catalog=MyCatalog&vapp=myVapp \
           /ovfs/myVapp.ovf
           
### The following example locates the virtual machine or vApp based on its DNS name through the vCenter Server and powers it off:

> ovftool --powerOffSource vi://MyvCenterServer/?dns=test-vm test-vm.ova 

### To display a summary of information about the OVF package [in probe mode], use the following syntax:

> ovftool https://my_ovflib/vm/my_vapp.ovf 


### Conversions between one format to other

> ovftool  /ovfs/my_vapp.ovf /ovfs/my_vapp.ova
> ovftool  /ovfs/my_vapp.ova /ovfs/my_vapp.ovf
> ovftool /vms/my_vm.vmx /ovfs/my_vapp.ovf
> ovftool vmxs/Nostalgia.vmx ovfs/Nostalgia.ova
> ovftool https://my_ovf_server/ovfs/my_vapp.ova /vm/my_vm.vmx
> ovftool http://www.mycompany.com/ovflib/BigDemo.ovf x:/myvms/BigDemo.vmx

*If you convert an OVF package to a VMX format without specifying the target directory, OVF Tool creates a directory using the OVF package name and writes the .vmx and .vmdk files in it.*

> ovftool "Windows 7.ovf" .

*The VMX file is written at Windows 7/Windows 7.vmx.*

> ovftool https://my_ovf_server/ovfs/my_vapp.ova /vm/my_vm.vmx

> ovftool /ovfs/my_vapp.ovf vi://username:pass@my_esx_host

> ovftool /ovfs/my_vm.vmx vi://username:pass@my_esx_host
> ovftool Nostaliga.vmx vi://user:pwd@host/Datacenter/host/host1.foo.com


### This example uses a datastore location query to convert a VM (located on a vCenter Server) to OVF format.

> ovftool vi://username:pass@my_vc_server/my_datacenter?ds=[Storage1] foo/foo.vmx c:\ovfs\

or

> ovftool vi://username:pass@my_host/my_datacenter/vm/my_vm_folder/my_vm_name /ovfs/my_vapp.ovf

### To install or add files to a vCenter Server from an OVF package, use the following syntax (uses a managed ESXi host’s IP address):

> ovftool /ovfs/my_vapp.ovf vi://username:pass@my_vc_server/?ip=10.20.30.40


### This example uses a datastore location query to convert a VM (located on a vCenter Server) to OVF format.

> ovftool vi://username:pass@my_vc_server/my_datacenter?ds=[Storage1] foo/foo.vmx c:\ovfs\

or

> ovftool vi://username:pass@my_host/my_datacenter/vm/my_vm_folder/my_vm_name /ovfs/my_vapp.ovf


### This example uses a vSphere inventory path to install or add files to a vCenter Server from an OVF package.

> ovftool /ovfs/my_vapp.ovf vi://username:pass@my_vc_server/my_datacenter/host/my_host
