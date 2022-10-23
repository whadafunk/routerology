# DNS BlackList Mechanism for SMTP

> The DNS blacklist is basicaly a DNS domain where each record can identify a specific client IP or Hostname

- RBL (real time blacklist) -> this type of blacklist stores client ips
- RHSBL (Right Hand Side BlackList) -> this type of blacklist stores hostnames and domain names
- DNSWL (DNS White List) -> This is a DNS Zone that contains exceptions. Normally you host your own zone for whitelisting
- RHSWL (Right Hand Side White List) -> This is also a zone for permissions, but contains domain names instead of client ips


### Content of the blacklist zones

> A blacklist zone adds the client ips in reverse notation as a prefix to the zone name.
> For example, if we host the black list zone **myblacklist.local**, the records will look like this:
> -  82.15.234.193.myblacklist.local	INET	A	127.0.0.2
> -  41.203.1.10.myblacklist.local	INET	A	127.0.0.3

> What you need to observe here, is that the A record can return different IP addresses. With some of the  
> SMTP servers, you can check also the returned IP, and make a decision based on that, but most of the time   
> there is no need to also check for the returned IP.
> These IPs are used to group different categories of blacklisted clients like for example:
> - Clients that were automatically detected and added to the database
> - Clients that were reported by third pary
> - Clients that are uncompliant configured

> A RBL and A DNSWL looks the same, but they are used different

### Content of the RHS Zones

> A right hand side zone has its name from the element that is looked up, which originally is the value  
> From the right side of "@". Ex: daniel@*email.com*
> These zones contain records of a different format, meaning they do not add an ip as prefix to the zone name.
> I am not actually sure how they look but I presume the look something like:

> - email.com.myblacklist.local		INET		A		127.0.0.2

> The same as with the RBL, the Right Hand Side zones are looking the same regardless if they are used for  
> blacklisting or whitelisting.

### Examples of RBL zones, and how you can configure them


reject_rbl_client dnsbl.sorbs.net,
reject_rbl_client zen.spamhaus.org
reject_rbl_client zen.spamhaus.org=127.0.0.[2..11]
reject_rhsbl_client dbl.spamhaus.org
reject_rhsbl_reverse_client dbl.spamhaus.org
reject_rbl_client b.barracudacentral.org=127.0.0.2
reject_rbl_client bl.spamcop.net
reject_rbl_client rbl.abuse.ro=127.0.0.[2..3],
reject_rbl_client rbl.abuse.ro,
reject_rbl_client pbl.abuse.ro,
reject_rhsbl_sender dbl.abuse.ro
