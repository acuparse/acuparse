# Acuparse NGINX Proxy Config Guide

When using a domain, install NGINX to make redirects easier. It also keeps your custom domain configuration separate from
the Acuparse config.

This is not generally required, as Apache should listen to everything by default.

- Install NGINX:

```bash
apt install nginx
```

- Edit the config file:

```bash
nano /etc/nginx/sites-available/reverse.conf
```

**Replace `<domain>` with your domain and `<external_ip>` with your external IP address. Update `<certificate>` and `<key>`
with the path to your certificate and key.**

```nginx
server {
    listen 443 ssl;
    server_name <external_ip> <domain> www.<domain>;
    ssl_certificate <certificate>;
    ssl_certificate_key <key>;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_ciphers HIGH:!aNULL:!MD5;
    root /var/www/html;
    location / {
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $remote_addr;
        proxy_set_header Host $host;
        proxy_pass http://127.0.0.1:8080;

        # Cache configuration
        proxy_cache reverse_cache;
        proxy_cache_valid 3s;
        proxy_no_cache $cookie_PHPSESSID;
        proxy_cache_bypass $cookie_PHPSESSID;
        proxy_cache_key "$scheme$host$request_uri";
        add_header X-Cache $upstream_cache_status;
    }
}

server {
    listen 80;

    # Site Directory
    root /opt/acuparse/src/public;

    # Domain
    server_name <external_ip> www.<domain>;

    # Reverse Proxy and Proxy Cache Configuration
    location / {
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $remote_addr;
        proxy_set_header Host $host;
        proxy_pass http://127.0.0.1:8080;

        # Cache configuration
        proxy_cache reverse_cache;
        proxy_cache_valid 3s;
        proxy_no_cache $cookie_PHPSESSID;
        proxy_cache_bypass $cookie_PHPSESSID;
        proxy_cache_key "$scheme$host$request_uri";
        add_header X-Cache $upstream_cache_status;
    }
}
# Redirect to WWW
server {
    server_name  <domain>;
    rewrite ^(.*) $scheme://www.<domain>$1 permanent;
}
```

- Activate NGINX config

```bash
ln /etc/nginx/sites-available/reverse.conf /etc/nginx/sites-enabled/
```

- Edit Apache port config

```bash
nano /etc/apache2/ports.conf
```

- Change `Listen 80` to `Listen 8080`
- Change `Listen 443` to `Listen 4433`

- Update Apache site config

```bash
nano /etc/apache2/sites-available/acuparse.conf
```

- Change `<VirtualHost *:80>` to `<VirtualHost *:8080>`

- Disable Apache SSL site

```bash
a2dissite acuparse-ssl.conf
```

- Tell Apache where to get the real visitor IP

```bash
apt install libapache2-mod-rpaf
```

- Add your external IP to RPAFproxy_ips:

```bash
nano /etc/apache2/mods-available/rpaf.conf`, `RPAFproxy_ips 127.0.0.1 <external_ip> ::1
```

- Enable RPAF:

```bash
a2enmod rpaf
```

- Restart Apache and NGINX:

```bash
service apache2 restart && service nginx restart
```

- Generate a Let's Encrypt Cert:

```bash
apt install python3-certbot-nginx -y && certbot --nginx --email <email> -d <domain> -d www.<domain>
```
