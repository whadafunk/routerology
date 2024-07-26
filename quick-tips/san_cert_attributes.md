# Alternative Subject Names In Microsoft CA Web Request


In the Attributes box, type the desired SAN attributes. 

-DNS
-IP
-email
-URI
-RID

### SAN attributes take the following form:

**san:dns=dns.name[&dns=dns.name]**

Multiple DNS names are separated by an ampersand (&).   
For example, if the name of the domain controller is **corpdc1.fabrikam.com**  
and the alias is **ldap.fabrikam.com**, both names must be included in the SAN attributes.  
The resulting attribute string is displayed as follows:  

**san:dns=corpdc1.fabrikam.com&dns=ldap.fabrikam.com**


### SAN attributes in FortiGate CSR


 	

The CSR can be generated from System -> Certificates -> Generate.

Fill in the required details and mention the SAN in the below format, for example:

 

DNS:domain1.com

IP:1.2.3.4

 

If multiple entries need to be added, they should be separated by a comma, with no space in between. For example:

 

DNS:domain1.com,DNS:domain2.com,IP:1.2.3.4
