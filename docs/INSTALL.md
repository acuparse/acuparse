# Installation Guide:
**Requires LAMP stack. Some PHP, Apache, and GNU/Linux experience recommended.**

Can be run locally or on a remote/cloud server. 

Installing on a fresh instance of a Debian based OS is the only officially supported and tested install method.

## DNS Redirect
> **Note:** Access users can use the included script to modify the Access upload server instead of, or as well as, redirecting DNS. <br> See [/admin/access](/admin/access) once logged into your site.

If you are connecting your Access/SmartHUB directly to Acuparse, you can install Bind9 and redirect the DNS locally. Otherwise, you will need a DNS server installed on your network.
See [docs/DNS.md](https://acuparse.github.io/acuparse/DNS)

### smartHUB
* Setup a local DNS override for `hubapi.myacurite.com` pointing to the external IP address of your Acuparse server.

### Access
* Setup a local DNS override for `atlasapi.myacurite.com` pointing to the external IP address of your Acuparse server.

## Automated Acuparse Installation:
> **Info:** Installer supports Debian Stretch(9), Ubuntu 18.04 LTS, and Raspbian Stretch(9).

* Install the base operating system and update:
    * `apt-get update && apt-get dist-upgrade`

* Download and run the Acuparse installer:

`wget https://raw.githubusercontent.com/acuparse/installer/master/install.sh && sudo sh install.sh | tee ~/acuparse.log`

### Raspberry Pi
* For a detailed installation guide on Raspbian, view the [Wiki Doc](https://github.com/acuparse/acuparse/wiki/Installation-on-Raspberry-Pi)

## Manual Acuparse Installation:
* Switch to the root account to install:
    * `sudo su`
* Change to the root directory:
    * `cd ~`

* Check and update your system timezone:
    * `dpkg-reconfigure tzdata && systemctl restart rsyslog.service`

* Install the required packages:
    * Debian Stretch(9) and Raspbian Stretch(9):
    
    `apt install ca-certificates apt-transport-https && wget -q https://packages.sury.org/php/apt.gpg -O- | sudo apt-key add - && echo "deb https://packages.sury.org/php/ stretch main" | sudo tee /etc/apt/sources.list.d/php.list && apt-update`

    * Debian Stretch(9), Ubuntu 18.04 LTS, and Raspbian Stretch(9):
        * `apt-get install git ntp imagemagick exim4 apache2 mysql-server php7.2 libapache2-mod-php7.2 php7.2-mysql php7.2-gd php7.2-curl php7.2-json php7.2-cli php7.2-common`

* Secure your MySQL install:
    * `mysql_secure_installation`

#### Email Server Config:
Run `dpkg-reconfigure exim4-config` and choose the correct values for your system. Most users will need to select `internet site; mail is sent and received directly using SMTP` and accept the rest of the defaults.

#### Install Acuparse:
* Get the Acuparse source:
    * `git init /opt/acuparse && cd /opt/acuparse && git remote add -t master -f origin https://github.com/acuparse/acuparse.git && git checkout master`
 
* Set the owner on the web root:
    * `chown -R www-data:www-data src`

* Disable the default Apache config:
    * `a2dissite 000-default.conf`

* Remove unneeded config files:
    * `rm /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/default-ssl.conf`

* Enable the Acuparse virtual host config:
    * `cp /opt/acuparse/config/acuparse.conf /etc/apache2/sites-available/`

* Enable the Acuparse SSL virtual host config:
    * `cp /opt/acuparse/config/acuparse-ssl.conf /etc/apache2/sites-available/`

* Ensure mod-rewrite is enabled:
    * `a2enmod rewrite`

* Ensure SSL is enabled:
    * `a2enmod ssl`

* Restart Apache:
    * `service apache2 restart`

#### SSL Certificate Installation:
By Default Apache will use the snake oil cert to serve over HTTPS. For most users, this should be sufficient. If you use a hostname, install a certificate!

* `wget https://raw.githubusercontent.com/acuparse/installer/master/resources/le.sh && sh le.sh`

#### Setup Database:
* Create a new MySQL database for Acuparse:

    * `mysql -u root -p {YOUR_SQL_ROOT_PASSWORD} -e "CREATE DATABASE acuparse; GRANT ALL PRIVILEGES ON `acuparse`.* TO 'acuparse@localhost' IDENTIFIED BY '{YOUR_ACUPARSE_DB_PASSWORD}'; GRANT SUPER, EVENT ON *.* TO 'acuparse'@'localhost'"`

#### Finish Up:
* Edit your cron to run the external updater script every minute:

    * `crontab -e`, `* * * * * php /opt/acuparse/cron/cron.php > /dev/null 2>&1`
    
* Visit `http://{IP_ADDRESS/HOSTNAME}` to populate the database, create an account, and finish configuration.

#### Optional:
* Install phpMyAdmin for database administration
    * `apt-get install phpmyadmin`
    
# Check Installation:

## Syslog:
View your syslog to see the data flowing through your system and to look for any trouble. Enable debug logging for a more detailed view.

* `tail -f /var/log/syslog`

## Data Display:
The primary user interface uses AJAX to pull the most recent HTML formatted data every minute.

Aside from the primary interface, you can also pull the bootstrap formatted HTML data or a JSON array, for use in outside applications.

* JSON: `http://<yourip/domain>/?json`
* HTML: `http://<yourip/domain>/?weather`

# Database Trimming:
Readings get stored in multiple temporary database tables. This temporary data should be cleaned up regularly to avoid ballooning the database.
When the external updater runs, it archives the most recent readings to the archive table for later use.

**Database trimming is accomplished via the MySQL event scheduler.**

* Recommend enabling trimming, unless you need the additional data.
    * When enabled tower data is also trimmed. Should you wish to keep tower data, use that option instead.

If you find that the event scheduler is not behaving, ensure MySQL is up to date. Some upgrades from Debian 8 will not upgrade the database properly.

* `mysql_upgrade -u root -p {YOUR_SQL_ROOT_PASSWORD}`

# Barometer Readings:
You can modify the barometer readings used by Acuparse. Set your Access/smartHUB to use station pressure using MyAcuRite and adjust your offset.
Readings are only modified in Acuparse and sent to 3rd party sites. It **does not** modify the reading MyAcuRite receives from your Access/smartHUB.

Check the syslog and watch for your changes. Once your Access/smartHUB is reporting updated readings, modify the Acuparse config with your required offset.

## Barometer Source:
If you are using an Access and a SmartHUB at the same time, it can cause trouble with barometer readings. Since they can be slightly different.
* Default:
    * Saves all Barometer readings.
* Hub:
    * Saves only the SmartHUB Barometer readings.
* Access:
    * Saves only the Access Barometer readings.

# Uploading Data:
Detailed instructions for each available in docs/external.

* The cron job setup earlier will process your weather data and send updates to external sites automatically, as required.
    * Data is only sent to external sites when there is new data to send and enough time has passed for CWOP updates.
> **Notice:** Disable updating of Weather Underground from your Access/smartHUB/MyAcuRite. Watch your syslog for the response from MyAcuRite.

## Master Sensor:
By default Acuparse will use the 5N1/Atlas sensor to upload data to external sites. To upload data from a tower, change the Master Temp/Humidity Sensor.
Changing the sensor sends those readings externally instead of the 5N1/Atlas data. You can also choose to use the tower readings for the data archive or use the readings from the 5N1/Atlas.

# MyAcuRite Responses:

## Access:
When MyAcuRite receives your readings, it responds with a JSON response in the following format:
* `{"sensor1":"","PASSWORD1":"","timezone":"","elevation":"","ID1":""}`

Variable | Description
--- | ---
timezone | Local timezone offset of the Access.
ID1 | Weather Underground Station ID.
PASSWORD1 | Weather Underground Station Password.
sensor1 | Sensor used to send data to Weather Underground.
elevation | Elevation of the Access in feet.

* A typical response: `{"timezone":"00:00""}`

## SmartHUB:
> **Notice:** To prevent firmware updates, the SmartHUB response is now being generated by Acuparse. The response generated by MyAcuRite is no longer sent back to the smartHUB.
>
>**smartHUB settings modified using the MyAcuRite site will not be reflected by the smartHUB.**
 
When MyAcuRite receives your readings, it responds with a JSON response in the following format:
* `{"localtime":"00:00:00","checkversion":"","ID1":"","PASSWORD1":"","sensor1":"","elevation":""}`

Variable | Description
--- | ---
localtime | Local time the reading was received. Keeps time on the Access/smartHUB and is used mainly for rainfall readings.
checkversion | The current firmware version available. Currently 224.
ID1 | Weather Underground Station ID.
PASSWORD1 | Weather Underground Station Password.
sensor1 | Sensor used to send data to Weather Underground.
elevation | Elevation of the smartHUB in feet.

Acuparse will now always respond with: `{"localtime":"00:00:00","checkversion":"224"}`. 
 Setting localtime to the local time of your Acuparse install. 

# Email Outage Notifications:
Outage notifications are sent to all registered admins. You can configure some simple values for outage checking, the system will email you when there is no data received.

The updater first checks to see if there is new data to send. If there isn't, it will start the email process.
If there is no new data due to updates not being received in the configured period, Acuparse will send an email at your chosen interval.

# Tower Sensors:
Acuparse allows for the addition of as many Tower sensors as the Access/smartHUB will pass along. You can choose which sensors are shown publicly or only to logged in users. Towers are configured and arranged using the admin settings.
* Acuparse also supports Indoor/Outdoor Temp and Humidity monitors but will not save or display any additional data.          

# Web Cam Installation (optional):
Three scripts are included in the `cam/templates` directory. They are used to get and process images from an IP camera.

Images get stored in `src/pub/img/cam`. They should be backed up regularly to avoid loss.

Script | Description
--- | ---
local.sh | Runs on a host local to the camera (such as an NVR) and sends the image to the Acuparse server.
remote.sh | Processes an image on the Acuparse server.
combined.sh | Processes an image when the camera and Acuparse are both installed locally.

## Local/Remote Setup:
* On the system local to the camera:
    * Copy the cam directory to the acuparse directory and go there:
        * `cp cam/ /opt/acuparse/ && cd /opt/acuparse`
    * Copy `local.sh` from `cam/templates` to the cam folder and modify the values:
        * `cp cam/templates/local.sh cam/`
    * Setup a cron job to process the image:
        * `crontab -e`, `0,15,30,45 * * * * /bin/sh /opt/acuparse/cam/local.sh > /dev/null 2>&1`
    * Setup SSH keys so you can log in to your remote host from the local host without a password:
        * `ssh-copy-id -i ~/.ssh/{YOUR_KEY} {USERNAME}@{HOSTNAME}`

* On the Acuparse server:
    * Copy `remote.sh` from `cam/templates` to the cam folder and modify the values:
        * `cp cam/templates/remote.sh cam/`

## Combined Setup:
* Copy `combined.sh` from `cam/templates` to the cam folder and modify the values:
    * `cp cam/templates/combined.sh cam/`
* Setup a cron job to process the image:
    * `crontab -e`, `0,15,30,45 * * * * /bin/sh /opt/acuparse/cam/combined.sh > /dev/null 2>&1`

> **Info:** Ensure ImageMagick is installed and available. Otherwise, images will not get processed.

# Invisible reCAPTCHA:
Recaptcha loads on the authentication and contact forms, as well as, when requesting a password reset.
* Sign up for a reCAPTCHA account at [google.com/recaptcha](https://www.google.com/recaptcha).
* Select Invisible reCAPTCHA when registering your new site.
* Enter your site key and secret in your site settings.
