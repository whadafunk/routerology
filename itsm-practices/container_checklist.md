# Container Checklist


## Generic Actions

- Set a custom cli prompt through env PS1
- Update ca-certificates
- Create custom user
- Set timezone
- Copy SSH-Keys, Certificates
- Copy /etc/profile
- Repository update
- Repository Cleanup (packages and indexes)
- DEBIAN_FRONTEND=noninteractive
- Mount persistent /var/log/admin/summary
- Mount persistent /var/log/admin/journal
- Link /etc/motd to /var/log/admin/summary
- Configure container logging
-

## Supplementary packages

### Basic Tools

- shadow
- ping
- apt-transport-https
- wget
- nano / vim

### Enhance Tools

- mc
- git
- ssh tools
- host
- whois
- dig
- gnupg
- tmux / screen

### Networking Tools

- iproute2
- hping
- net-tools
- tcpdump
- ngrep
- netcat
- nmap

### Security

- openssl
- fail2ban
- nikto

### Authentication

- nss ldap
- pam ldap

### Email

- ssmtp / msmtp
- swaks







