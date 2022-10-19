# Using certbot with letsencrypt certificates


## ACME
> This is a protocol that works over http/s, that is use to talk to a CA (signing request, validation, etc)
> It uses json format

## ACME-DNS
> This is a protocol that validates a certificate signing request based on the existence of some dns record

## Certbot
> Certbot is a client software that is using acme protocol to request a certificate


Certbot uses one of the two methods bellow to validate ownership of the name requested in certificate

- HTTP (meaning that it will verify a special page on your http server)
- DNS (meaning that it will verify a special record on the DNS server)

## Certbot commands and options

- **certificates**  display local certificates
- **register --agree-tos -m admin@email.com** register current client with letsencrypt
- **revoke --cert-name** revoke the specified certificate
- **delete --cert-name** delete the specified certificate
- **certonly** tells certbot to not install the certificate. just write it under /etc/letsencrypt
- **renew** renew local certificates
- **-d domain.com** specify this one or more times for the names in the certificate (ex: -d \*.domain.com -d www.altdomain.com)
- **--domains domain.com** alternative option to specify multiple domains
- **--apache --standalone --nginx** the type of configuration to install the certificate
- **--manual** interactive certificate request
- **--dry-run** simulate only the request
- **--preferred-challenges dns** do not use other challenges than dns
- **--dns-route53** select aws route53 for acme-dns validation

## Examples:

 certbot certonly -d *.domain.com -d *.altcomain.biz --dry-run --preferred-challenges dns --dns-route53

For dns-route53 you also need to install a specific certbot plugin and the awscli.  
Then you need to run *aws configure* to input your API key/challenge

