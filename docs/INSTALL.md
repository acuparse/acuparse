# Acuparse
## AcuRite®‎ smartHUB and IP Camera Data Processing, Display, and Upload.

# Installation Guide:
**Requires LAMP stack. Some PHP, Apache, and GNU/Linux experience recommended.**

Can be run locally or on a remote/cloud server. 

Installing on a fresh instance of a Debian based OS is the only officially supported and tested install method. Any other method is not officially supported or tested.

* Setup a local DNS override for `hubapi.myacurite.com` pointing to the external IP address of your Acuparse server.
> **Info:** Exactly how to do this depends on the network where the smartHUB is installed. Use a firewall that allows you to customize your DNS. Such as pfSense® or OPNsense®.

## Install Acuparse:
> **Info:** Installer currently supports Debian Stretch(9) and Ubuntu 16.04 LTS.

* Install the base operating system and update.

* Download and run the Acuparse installer.

``` wget https://raw.githubusercontent.com/acuparse/installer/install.sh && sudo sh install.sh```

### Installing Acuparse Manually:
* Switch to the root account to install:
    * `sudo su`
* Change to the root directory:
    * `cd ~`

* Install the required packages:

    * Ubuntu 16 / Debian 9: `apt-get install git ntp imagemagick exim4 apache2 mysql-server php7.0 libapache2-mod-php7.0 php7.0-mysql php7.0-gd php7.0-curl php7.0-json php7.0-cli`

* Secure your MySQL install: `mysql_secure_installation`

#### Email Server Config:
* Run `dpkg-reconfigure exim4-config` and choose the correct values for your system. Most users will need to select `internet site; mail is sent and received directly using SMTP` and accept the rest of the defaults.

#### Install Acuparse:
* Get the Acuparse source:

    ``` git init /opt/acuparse && cd /opt/acuparse && git remote add -t master -f origin https://github.com/acuparse/acuparse.git && git checkout master ```
 
* Set the owner on the web root: `chown -R www-data:www-data src`

* Disable the default Apache config: `a2dissite 000-default.conf`

* Enable the Acuparse virtual host config: `ln config/acuparse.conf /etc/apache2/sites-enabled/`

* Make sure mod-rewrite is enabled: `a2enmod rewrite`

> **Info:** Due to the DNS redirect Apache needs to listen and serve connections for the IP address and your domain, if configured. If using a domain, use an NGINX proxy in-front. This will make domain redirects easier to manage.
See the NGINX install details below.

#### Setup Database:
* Create a new MySQL database for Acuparse.

    ``` mysql -uroot -p<your_password> -e "CREATE DATABASE acuparse; GRANT ALL PRIVILEGES ON `acuparse`.* TO 'acuparse@localhost' IDENTIFIED BY '<your_DB_password>'; GRANT SUPER, EVENT ON *.* TO 'acuparse'@'localhost'" ```

#### Finish Up:
* Restart Apache: `service apache2 restart`

* Edit your cron to run the external updater script every minute:

    `crontab -e`, `* * * * * php /opt/acuparse/cron/cron.php > /dev/null 2>&1`
    
* Visit `http://<yourip/domain>` to populate the database, create an account, and finish configuration.

## Database Trimming:
Readings are stored in multiple temporary database tables. This temporary data should be cleaned up regularly to avoid ballooning the database.
When the external updater runs, it archives the most recent readings to the archive table for later use.

Database trimming is handled using the MySQL event handler.

* Recommend enabling trimming, unless you really need the additional data.
    * When enabled tower data is also trimmed. If you want to keep tower data, use that option instead.

## Barometer Readings:
You can modify the barometer readings used by Acuparse. Set your smartHUB to use station pressure using MyAcuRite and adjust your offset.
Readings are only modified in Acuparse and are sent to 3rd party sites. It does not modify the data MyAcuRite receives.
When you make changes on MyAcuRite you will eventually get a response back to the smartHUB with the updates. 

Check the syslog and watch for your changes. Once your smartHUB is reporting updated readings, modify the Acuparse config with your required offset.

> **Info:** It's recommended to use MyAcuRite to handle the pressure offset, where possible. Since it will configure the offset on the smartHUB. Try adjusted pressure and modify your elevation.

## Uploading Data:
Detailed instructions for each available in docs/external.

* The cron job setup earlier will process your weather data and send updates to external sites automatically, as required.
    * Data is only sent to external sites when there is new data to send and enough time has passed for CWOP updates.
> **Notice:** Disable updating of Weather Underground from your smartHUB/MyAcuRite. Watch your syslog for the response from MyAcuRite.

## MyAcuRite Response:
When MyAcuRite receives your readings, it sends back a JSON response to your smartHUB in the following format.
`{"localtime":"00:00:00","checkversion":"","ID1":"","PASSWORD1":"","sensor1":"","elevation":""}`.

* localtime = Local time the reading was received. Keeps time on the smartHUB and is used mainly for rainfall readings.
* checkversion = The current firmware version available. Currently 224.
* ID1 = Weather Underground Station ID.
* PASSWORD1 = Weather Underground Station Password.
* sensor1 = Sensor used to send data to Weather Underground.
* elevation = Elevation of the smartHUB in feet.

A typical response looks like this:
`{"localtime":"00:00:00","checkversion":"224"}`

## Email Outage Notifications:
Outage notifications will be sent to all registered admins. You can configure some simple values for outage checking and the system will email you when there is no data received.

The updater first checks to see if there is new data to send. If there isn't, it will start the email process.
If there is no new data due to updates not being received in the configured time period, Acuparse will send an email at your chosen interval.

## Tower Sensors:
Acuparse allows for the addition of numerous tower sensors. As many as the smartHUB will pass along. You can choose which sensors are shown publicly or only to logged in users. Towers are configured and arranged using the admin settings.
          
## Check Installation:

## Syslog:
View your syslog to see the data flowing through your system and to look for any trouble. Enable debug logging for a more detailed view.

``` tail -f /var/log/syslog ```

## Data Display:
The main user interface uses AJAX to pull the most recent HTML formatted data every minute.

Aside from the main interface you can also pull the bootstrap formatted HTML data or a JSON array, for use in outside applications.

* JSON: `http://<yourip/domain>/?json`
* HTML: `http://<yourip/domain>/?weather`

## Web Cam Installation (optional):
Three scripts are included in the `cam/templates` directory. They are used to get and process images from an IP camera.

Images are stored by the scripts in `src/pub/img/cam`. They should be backed up regularly to avoid loss.

* local.sh - runs on a host local to the camera such as the NVR and sends the image to the Acuparse server.
* remote.sh - processes the image on the Acuparse server.
* combined.sh - used to process the image if the camera and Acuparse are both installed locally.

### Local/Remote Setup:
* On the system local to the camera:
    * Copy the cam directory to `/opt/acuparse/`
    * Copy `local.sh` from `cam/templates` to the cam folder and modify the values. `cp cam/templates/local.sh cam/`
    * Setup a cron job to process the image: `crontab -e`, `0,15,30,45 * * * * /bin/sh /opt/acuparse/cam/local.sh > /dev/null 2>&1`
    * Setup SSH keys so you can login to your remote host from the local host without a password.

* On the Acuparse server:
    * Copy `remote.sh` from `cam/templates` to the cam folder and modify the values. `cp cam/templates/remote.sh cam/`

### Combined Setup:
* Copy `combined.sh` from `cam/templates` to the cam folder and modify the values. `cp cam/templates/combined.sh cam/`
* Setup a cron job to process the image: `crontab -e`, `0,15,30,45 * * * * /bin/sh /opt/acuparse/cam/combined.sh > /dev/null 2>&1`

> **Info:** Make sure Imagemagick is installed so images can be processed.

## Invisible reCAPTCHA:
Recaptcha is used on the login form and when requesting a password reset.
* Sign up for a reCAPTCHA account at [google.com/recaptcha](https://www.google.com/recaptcha).
* Select Invisible reCAPTCHA when registering your new site.
* Enter your site key and secret in your site settings.

## NGINX Proxy Config (optional):

When using a domain, install NGINX to make redirects easier. It also keeps your custom domain configuration separate from the Acuparse config.

* Install NGINX: `apt-get install nginx`
* Edit the config file: `nano /etc/nginx/sites-available/reverse.conf`

** Replace `<domain>` with your domain and `<external_ip>` with your external IP address. **

```
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

server {
        listen 80;
        server_name <domain>;
        return 301 $scheme://www.<domain>$request_uri;
}
```

* Activate NGINX config: `ln /etc/nginx/sites-available/reverse.conf /etc/nginx/sites-enabled/`

* Edit Apache config: `nano /etc/apache2/ports.conf`
    * Change `Listen 80` to `Listen 8080`

* Tell Apache where to get the real visitor IP: `apt-get install libapache2-mod-rpaf`

    * Add your external IP to RPAFproxy_ips:
        `nano /etc/apache2/mods-available/rpaf.conf`, `RPAFproxy_ips 127.0.0.1 <external_ip> ::1`

     * Enable RPAF: `a2enmod rpaf`

* Restart Apache and NGINX: `service apache2 restart && service nginx restart`
