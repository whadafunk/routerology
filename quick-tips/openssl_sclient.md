# OPENSSL S_CLIENT

## This is a very handy tool that we can use to test SSL/TLS connections
### I use it mostly for TLS SMTP sessions, but is also handy for HTTP and any other protocol that uses clear text commands


> openssl s_client -connect example.com:443

*Use the openssl s_client -connect flag to display diagnostic information about the SSL connection to the server.
The information will include the servers certificate chain, printed as subject and issuer.
The end entity server certificate will be the only certificate printed in PEM format.*

> openssl s_client -connect example.com:443 -showcerts

*The showcerts flag appended onto the openssl s_client connect command prints out and will show the entire certificate chain in PEM format,
whereas leaving off showcerts only prints out and shows the end entity certificate in PEM format.
Other than that one difference, the output is the same.* 

> openssl s_client -connect example.com:443 -tls1_3
> openssl s_client -connect example.com:443 -no_tls1_3

> openssl s_client -connect ldap-host:389 -starttls ldap

*Adding the -starttls flag to your openssl s_client -connect command will send the protocol specific message for switching to SSL/TLS communication. 
Supported protocols include smtp, pop3, imap, ftp, xmpp, xmpp-server, irc, postgres, mysql, lmtp*

> openssl s_client -connect example.com:443 -servername example.com

*SNI is a TLS extension that supports one host or IP address to serve multiple hostnames so that host and IP no longer have to be one to one.
Use the -servername switch to enable SNI in s_client.
If the certificates are not the same when using the -servername flag vs without it, you will know that SNI is required.*

> openssl s_client -verify_hostname www.example.com -connect example.com:443
> openssl s_client -verify_return_error -connect example.com:443


> openssl s_client \
  -connect example.com:443 \
  -cert <cert_and_key.pem>

### openssl s_client option	Option Description
- **cert**	The certificate to be used for TLS client authentication.
- **certform**	The format of the certificate. PEM is the default, but DER may be specified.
- **key**	The private key matching the provided certificate.
- **keyform**	The format of the private key. PEM is the default, but DER may be specified.
- **cert_chain**	The complete trust chain.
- **pass**	The password source of the private key, if encrypted with a password.


## HTTP Example

```http
GET /client HTTP/1.0
Host: test.sockettools.com
Accept: text/*
Connection: close
```

openssl s_client -tls1_2 -connect test.sockettools.com:443 < http-request.txt

