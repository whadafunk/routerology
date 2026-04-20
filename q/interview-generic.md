# Interview Questions

## Section 1: Fundamentals & Core Concepts

### Background & Experience
1. Describe in a couple of words the following technologies and give some examples of products/vendors: computing, networking, storage, network security, middleware, ops, application security.
2. Name a couple of nice open-source products that you used or experimented with. Name something cool about a vendor product.
3. Tell something about your technical certifications: some challenge, a personal experience, what you think you gained.

### Operating Systems & Basics
4. What is an Operating System?
5. A couple of different Linux distros (core, alpine, lfs, centos, oracle, debian, clearos, kali, arch) - what are the differences?
6. What is a terminal? What is a shell? What is a virtual terminal? What is a pseudo terminal?
7. How do you schedule tasks in Linux? leaning into cron
8. What is the logging system in Linux? How about newer distros post 2022?
9. STDOUT, STDERR, STDIN - explain each and their purpose.
10. What does `2>&1` do?

### Networking Fundamentals
11. Packet vs frame - what’s the difference?
12. Baseband vs broadband - what distinguishes them?
13. Why do we need IP?
14. What determines the source IP address used by a host when sending packets if no explicit source IP is specified?
15. How a network switch works - explain the basics.
16. How a network router works - explain the basics.

### Core Protocols
17. Describe how the ping command works.
18. Describe how the traceroute command works.
19. What is TTL (Time To Live)?
20. Tell me a couple of features of the ICMP protocol.
21. How is ARP working? Why do we need it?
22. Describe 2 use cases when proxy ARP is used, even if not explicit.
23. Can we communicate with a MAC address in a different broadcast domain?

### Addressing & Routing
24. What is NAT, and what are some of its use cases: Public SNAT, Service Publish, Overlapping Addresses?
25. What RPF means to you? What is asymmetric routing?
26. Routing and longest prefix matching - explain the concept.
27. Packet forwarding - what does it mean?

### DHCP & DNS
28. DHCP - how does it work and what is its purpose?
29. DNS discussion: zone types, zone vs domain - what’s the difference?
30. Describe the following DNS records: A, CNAME, MX, SPF, TXT, PTR.
31. What is a glue record? What is a stealth NS?
32. How do you use nslookup?
33. DNS troubleshooting scenario: FQDN resolves to different IP addresses from different networks - how would you troubleshoot?

---

## Section 2: Troubleshooting & Practical Scenarios

### Network Configuration Exercises
1. You have 2 switches with access ports in VLAN 10 and VLAN 20. Will computers connected to each of these switches communicate with one another?

2. You have 2 PCs from different network classes: 192.168.1.10/24 and 192.168.2.10/24. How would you route between those PCs? (Consider: reduce netmask, install a 3rd PC with secondary IP addresess, configure secondary ip address on one of the hosts etc.)

3. You have a network segment with a couple of computers and 2 routers, both connected to the internet with default routes. Where do you operate changes to transform one of the routers into the default gateway for the computers on the segment?

4. What are the reasons that a node might not show in the traceroute result?

5. You have a Linux PC with multiple interfaces. What do you need to configure on it, apart from routes, in order for that PC to function as a router? Is the same possible on windows, or other operating systems?

6. The users are saying "Network is not working anymore." Provide some troubleshooting steps.
7. Why do we use gre in ipsec and not plain ipsec when dealing with routing protocols like OSPF and EIGRP? What about BGP?

---

## Section 3: Advanced Networking & Security

### Firewalls & Packet Filtering
34. Stateless vs stateful firewalls - explain the difference.
35. Stateless example: Cisco ACL. How do we work around stateless ACLs in Cisco? (Reflexive ACL, CBC, IP Inspect, Zone-Based Firewall)

### VPN & Tunneling
36. VPN types: L2 vs L3 - provide examples (L2TP, PPTP, L2F, VXLAN vs IPSec, OpenVPN).
37. VPN Features: encryption, authenticity, private IP encapsulation.
38. Tunnel use cases - describe where tunneling is used.
39. Why do we need to use GRE encapsulated in IPSec when routing protocols like OSPF and EIGRP are running over the tunnel?

### Encryption & Certificates
40. Symmetric vs Asymmetric encryption - what’s the difference?
41. How HTTPS/TLS works - explain the basic flow.
42. What is a digital certificate?
43. Privacy, Authenticity, Integrity, Non-Repudiation - explain each concept.
44. Token Crypto vs USB Stick - when would you use each?

### Advanced Protocols & Services
45. Email protocols: IMAP, POP3, SMTP, MAPI, LMTP - what are the differences?
46. Why do you think SMTPS is not used on a larger scale?
47. SPF, DKIM, DMARC - explain these email security mechanisms.
48. SMTPS, SMTP Authentication, Cyrus, Dovecot, LOGIN - explain these components.
49. What are some of the email security mechanisms?
50. FTP / SFTP / FTPS - explain each and their firewall implications.
51. AAA / RADIUS - what are they and when are they used?

### SSH & Remote Access
52. What are some of the features of SSH? (Remote port forward, local port forward, key auth, ssh-agent and agent forward, X11 forwarding)
53. Talk about X11/Xorg. How does it compare to Windows graphical system? Who is the successor of Xorg?
54. SSH Forwarding: You can connect through remote VPN to a host. How do you initiate connections from the host back to your local machine?
55. What is the use case for TeamViewer?
56. What is the console session on Windows? Why would you want to log onto that, and how do you do it?

### Storage & Linux Systems
57. LVM (Logical Volume Manager) - explain features: volume flexibility, mirror, stripe, RAID5, RAID6, snapshot, caching.
58. ZFS - what is it and what are its key features?
59. BusyBox - what is it and what is it used for?
60. What is LILO? What other bootmanagers do you know? (GRUB, Syslinux, BURG, Clover)
61. What is a repository manager? Exemplify.
62. Disk space scenario: /var/pool is running out of space. What steps do you take to solve the issue? Consider investigating whether the mount point is a simple partition or a volume managed through LVM or ZFS. What capabilities do LVM and ZFS provide? What is the Windows equivalent? (Consider dynamic vs basic disks.)

### Docker & Container Operations
63. Docker multistage build - explain why and when you’d use it.
64. Docker multiarch build - what is it and when is it needed?

### Linux Advanced Topics
65. Command: `gzip -dc my_file.tar.gz | tar xvf - -C` - explain what this does.
66. Command: `tar cvf - .\ | ssh "tar xvf - -C /usr/src"` - explain what this does and its purpose.

---

## Section 4: Security, Threat Analysis & Defense

### Threat Modeling & Attack Analysis
67. What is threat modeling? What is an attack tree? Do you know any methods? How would you go about such a task?
68. What is the attack surface? What are attack vectors?
69. DOS, DDOS - explain these attacks. How can they be made more efficient?
70. How does a SYN cookie work?

---

## Section 5: Personal & Professional Development

### Career & Growth
71. In what direction do you want to develop in the coming period?
72. Describe your lab environment - what does it include?
73. What have been the challenges in your lab environment?
    - Name solving
    - Networking
    - Internet Access
    - Storage
    - Documentation
    - Templating, rollback, backup

### Work Habits & Organization
74. How do you work in an organized manner and not lose focus on what you’re doing?
75. What habits or routines have you cultivated that you believe are key to your personal success?
76. How do you navigate conflicting priorities in your life without losing your sense of purpose?

### Achievements & Learning
77. Tell an important achievement - describe one from your professional life and one from your personal life.
78. What are your distinctive qualities?
79. Can you describe a moment when a failure or mistake ended up teaching you something valuable?

### Reflections & Motivation
80. If you could give your younger self one piece of advice, what would it be and why?
81. How do you measure personal growth beyond traditional achievements like promotions or awards?
82. What’s a personal challenge you faced that changed how you view your work or life? How did you overcome it?
83. What motivates you to keep going on days when everything feels overwhelming or discouraging?
