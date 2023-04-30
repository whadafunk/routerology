# SMTP SASL Test

### Here is a quick list of commands to test SASL LOGIN authentication


**Convert our user and password to base64 strings***
echo -en "userxy" | base64
dXNlcnh5
echo -en "password" | base64
cGFzc3dvcmQ=

**Here is the SMTP Session where we use the previous user and password in base64**

```
AUTH LOGIN
334 VXClcm5hbWU6
dXNlcnh5
334 UGFzc4dvcmQ8
cGFzc3dvcmQ=
235 2.7.0 Authentication successful
```
