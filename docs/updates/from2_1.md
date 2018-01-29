# Acuparse
## AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.

# Upgrading from 2.1.x to 2.2.x
Major changes include Access support and the activating of SSL. You will need to rebuild your Apache config and optionally install a Let's Encrypt certificate. 
> **NGINX USERS**: If you are using NGINX as a proxy, please review [docs/NGINX.md](docs/NGINX.md) and update your configuration manually.

# Update Core
``` cd /opt/acuparse && sudo git pull ```

# Run 2.1 Migration Script
``` cd ~ && wget https://raw.githubusercontent.com/acuparse/installer/master/resources/from2_1.sh && sh from2_1.sh && rm from2_1.sh```
