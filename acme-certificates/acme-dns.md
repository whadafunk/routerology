# ACME-DNS 


ACME-DNS is one of the protocols that the certbot/acme client can use to complete DNS validation.
With LetsEncrypt DNS validation you have a couple of choices to update the TXT record:

- Use a specific api of a cloud DNS provider (Route53, CloudFlare, DigitalOcean, etc)
- Use ACME-DNS
- Manual update

Acme-dns provides a simple API exclusively for TXT record updates and should be used with ACME magic "\_acme-challenge" - subdomain CNAME records. 
This way, in the unfortunate exposure of API keys, the effects are limited to the subdomain TXT record in question. 


DNS Validation means having a specific txt record added to your domain so the CA can read it and validate ownership. 

\_acme-challenge.packets.lab   IN TXT 4yF4WuGPpgg3b7JNM17P7nyAdFZEoeBRBLGjPMm5Y0g  

By using a specific provider API you can automate the acme-challenge record creation. 
By using ACME-DNS you are using some sort of redirection.
Basically ACME-DNS is like any other API, that can permit updating DNS record through a REST API. 
There are a couple of components to ACME-DNS

- DNS Server that can accept ACME-DNS request for updating its zones.
- ACME DNS Client (integrated usualy as a certbot plugin), that can send requests to the Server. 
- A CNAME record pointing to a TXT record hosted usually on a public ACME DNS.

## Let's describe the process for a public acme-dns server:

There are a couple of public servers we can use freely:

- https://auth.acme-dns.io
- http://auth.example.com

1. Register with the ACME-DNS server:

 *curl -s -X POST https://auth.acme-dns.io/register | python3 -m json.tool*

you will get a response with registration details in json format

> { "allowfrom": [], 
>  "fulldomain": "9d2c7b32-4af4-482c-9e46-718acf50539e.auth.acme-dns.io", 
>  "password": "cLzdpV031ieuZzAE7jVNnX08uMqV0OsyIbf6Cqfm", 
>  "subdomain": "9d2c7b32-4af4-482c-9e46-718acf50539e", 
>  "username": "9d97e467-dd67-41d4-871f-5590b3d03c05" } 

1.5. Optional: secure the updates to the acme dns server

> curl -X POST http://auth.acme-dns.io/register \
>    -H "Content-Type: application/json" \
>    --data '{"allowfrom": ["193.231.25.0/24"]}'

2. You will need to create a CNAME pointing to the fulldomain. This is called a magic acme subdomain.

\_acme-challenge.packets.lab     IN      CNAME       9d2c7b32-4af4-482c-9e46-718acf50539e.auth.acme-dns.io 

3. Use the above json in the configuration of your acme-dns client

### If you want you can use a registration domain/user/password for a maximum of two domains.

{
  "example.com": {
    "username": "eabcdb41-d89f-4580-826f-3e62e9755ef2",
    "password": "pbAXVjlIOE01xbut7YnAbkhMQIkcwoHO0ek2j4Q0",
    "fulldomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf.auth.example.com",
    "subdomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf",
    "allowfrom": ["10.244.0.0/16"]
  },
  "example.org": {
    "username": "eabcdb41-d89f-4580-826f-3e62e9755ef2",
    "password": "pbAXVjlIOE01xbut7YnAbkhMQIkcwoHO0ek2j4Q0",
    "fulldomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf.auth.example.com",
    "subdomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf",
    "allowfrom": ["10.244.0.0/16"]
  }
}

### Reflections on multiple certificates on the same credentials/domain

I was expecting to have a different magic cname for each certificate name, but that cannot be automated because we can only create records on the ACME-DNS server. 
In order to fully automate such a setup there needs to be a delegation to the NS of an ACME-DNS server.  
Also, I was wondering if there is possible to have your domain hosted on the ACME-DNS server and create directly an _acme-challenge txt record without the magic redirection.

The recommended practice is to have a different magic cname for each certificate name. If you specify the domain example.com in your json, 
that counts for example.com and \*.example.com certificates.

Rolling update of two TXT records to be able to answer to challenges for certificates that have both names: yourdomain.tld and \*.yourdomain.tld, as both of the challenges point to the same subdomain.


### Example of api updating

curl -X POST https://auth.acme-dns.io/update \ 
> -H "X-Api-User: 9d97e467-dd67-41d4-871f-5590b3d03c05" \
> -H "X-Api-Key: cLzdpV031ieuZzAE7jVNnX08uMqV0OsyIbf6Cqfm" \ 
> --data '{"subdomain": "9d2c7b32-4af4-482c-9e46-718acf50539e", "txt": "\_\_\_validation\_token\_recieved\_from\_the\_ca\_\_\_"}' | python -m json.tool

curl -X POST http://auth.example.com/register
{
  "username": "eabcdb41-d89f-4580-826f-3e62e9755ef2",
  "password": "pbAXVjlIOE01xbut7YnAbkhMQIkcwoHO0ek2j4Q0",
  "fulldomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf.auth.example.com",
  "subdomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf",
  "allowfrom": []
}

curl -X POST http://auth.example.com/register \
    -H "Content-Type: application/json" \
    --data '{"allowfrom": ["10.244.0.0/16"]}'

{
  "username": "eabcdb41-d89f-4580-826f-3e62e9755ef2",
  "password": "pbAXVjlIOE01xbut7YnAbkhMQIkcwoHO0ek2j4Q0",
  "fulldomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf.auth.example.com",
  "subdomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf",
  "allowfrom": ["10.244.0.0/16"]
}

{
  "example.com": {
    "username": "eabcdb41-d89f-4580-826f-3e62e9755ef2",
    "password": "pbAXVjlIOE01xbut7YnAbkhMQIkcwoHO0ek2j4Q0",
    "fulldomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf.auth.example.com",
    "subdomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf",
    "allowfrom": ["10.244.0.0/16"]
  },
  "example.org": {
    "username": "eabcdb41-d89f-4580-826f-3e62e9755ef2",
    "password": "pbAXVjlIOE01xbut7YnAbkhMQIkcwoHO0ek2j4Q0",
    "fulldomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf.auth.example.com",
    "subdomain": "d420c923-bbd7-4056-ab64-c3ca54c9b3cf",
    "allowfrom": ["10.244.0.0/16"]
  }
}

\_acme-challenge.example.com CNAME d420c923-bbd7-4056-ab64-c3ca54c9b3cf.auth.example.com
\_acme-challenge.example.org CNAME d420c923-bbd7-4056-ab64-c3ca54c9b3cf.auth.example.com







