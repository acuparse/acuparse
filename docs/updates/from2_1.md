# Upgrading from 2.1.x

Major changes include Access support and the activating of SSL. You will need to rebuild your Apache config and optionally
install a Let's Encrypt certificate.
> **NGINX USERS**: If you are using NGINX as a proxy, please review [docs/NGINX.md](https://acuparse.gitlab.io/acuparse/NGINX)
> and update your configuration manually.

## Update Core

```bash
cd /opt/acuparse && sudo git pull
```

## Run 2.1 Migration Script

```bash
cd ~ && wget https://gitlab.com/acuparse/installer/raw/master/resources/from2_1 && bash from2_1.sh && rm from2_1.sh
```
