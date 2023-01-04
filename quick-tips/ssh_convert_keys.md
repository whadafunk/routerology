# How to convert from SSH2 to PEM and viceversa using ssh-keygen

A while ago I stumbled upon this and it was puzzling because although the 
documentation and the manual page states that you can use -e -m parameters to convert
both public and private keys of a pair in reality -e option will only output the public
key in the requested format.

I found some info on the internet about this

> **It cannot be done by the ssh-keygen program even though most man pages say it can. They discourage it so that you will use multiple public keys.**

It turns out that you can use ssh-keygen but not the -e option is the one we're interested
in, but the -p (for changing the password)
Baiscally we request changing the password together with the -m parameter (for the format of the key), so ssh-keygen will change the password and store the key in the same file but with a different format. 


Even so..., I could'n get the keys to work with putty. I ended up using puttygen anyway.

Ex:

ssh-keygen -p -m -f user_key.file

it will ask for the current password (if there is one) and then for the new password
and finally it will write the file

Let's recap some of the ssh-keygen options

- *-t*	specify the type of the key (rsa or dsa)
- *-b*	bitlength (1024, 2048, 4096)
- *-m*	the key-format (ssh2 or pem). this option can be use at key-creation, at key export
and at key password change. If we create new keypair without specifying -m, the default key-format will be ssh2
- *-C*	the comment that will be embedded in the public key. Usualy the username, but if we don't specify anything it will default to the current logged user
- *-f* the file that we refer to. This can be a keyfile, or a known-hosts file also
- *-F* find specified hostname or ip in the knownhosts file
- *-R* delete specified hostname or ip from the knownhosts file 
- *-H* hash the hostname part in the knownhosts file
