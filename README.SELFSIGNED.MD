link
[https://www.humankode.com/ssl/create-a-selfsigned-certificate-for-nginx-in-5-minutes/](https://www.humankode.com/ssl/create-a-selfsigned-certificate-for-nginx-in-5-minutes/)

1. create .crt .key (precondition)
```
export SSL_SOURCE_PATH=/nginx/ssl
export DOMAIN=terrazza.io
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout ${SSL_SOURCE_PATH}/${DOMAIN}.key -out ${SSL_SOURCE_PATH}/${DOMAIN}.crt 
```
2. move files to /etc/ssl/certs.
```
export SSL_TARGET_PATH=/etc/ssl
mv ${SSL_SOURCE_PATH}/${DOMAIN}.crt ${SSL_TARGET_PATH}/certs/${DOMAIN}.crt
mv ${SSL_SOURCE_PATH}/${DOMAIN}.key ${SSL_TARGET_PATH}/private/${DOMAIN}.crt
``` 
3. modify /nginx  
``` 
listen 443 ssl http2;
listen [::]:443  ssl http2;

ssl_certificate /etc/ssl/certs/localhost.crt;
ssl_certificate_key /etc/ssl/private/localhost.key;

ssl_protocols TLSv1.2 TLSv1.1 TLSv1;
```
3. create trusted CA record (inside nginx!)
``` 
certutil -d sql:$HOME/.pki/nssdb -A -t "P,," -n "localhost" -i localhost.crt
```