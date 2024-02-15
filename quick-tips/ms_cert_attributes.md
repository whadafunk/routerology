# Alternative Subject Names In Microsoft CA Web Request


In the Attributes box, type the desired SAN attributes. 

### SAN attributes take the following form:

**san:dns=dns.name[&dns=dns.name]**

Multiple DNS names are separated by an ampersand (&).   
For example, if the name of the domain controller is **corpdc1.fabrikam.com**  
and the alias is **ldap.fabrikam.com**, both names must be included in the SAN attributes.  
The resulting attribute string is displayed as follows:  

**san:dns=corpdc1.fabrikam.com&dns=ldap.fabrikam.com**

