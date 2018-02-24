# Acuparse
## AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.

# Installation Guide:
**Requires LAMP stack. Some PHP, Apache, and GNU/Linux experience recommended.**

Can be run locally or on a remote/cloud server. 

Installing on a fresh instance of a Debian based OS is the only officially supported and tested install method.

## DNS Redirect
If you are connecting your Access/SmartHUB directly to Acuparse, you can install Bind9 and redirect the DNS locally. Otherwise, you will need a DNS server installed on your network.
See [docs/DNS.md](docs/DNS.md)

### smartHUB
* Setup a local DNS override for `hubapi.myacurite.com` pointing to the external IP address of your Acuparse server.

### Access
* Setup a local DNS override for `atlasapi.myacurite.com` pointing to the external IP address of your Acuparse server.

## Install Acuparse:
> **Info:** Installer currently supports Debian Stretch(9), Ubuntu 16.04 LTS and Raspbian.

* Install the base operating system and update.

* Download and run the Acuparse installer.

``` wget https://raw.githubusercontent.com/acuparse/installer/master/install.sh && sudo sh install.sh```

### Installing Acuparse Manually:
* Switch to the root account to install:
    * `sudo su`
* Change to the root directory:
    * `cd ~`

* Install the required packages:

    * Debian 9 / Ubuntu 16 / Raspbian: `apt-get install git ntp imagemagick exim4 apache2 mysql-server php7.0 libapache2-mod-php7.0 php7.0-mysql php7.0-gd php7.0-curl php7.0-json php7.0-cli`

* Secure your MySQL install: `mysql_secure_installation`

#### Email Server Config:
* Run `dpkg-reconfigure exim4-config` and choose the correct values for your system. Most users will need to select `internet site; mail is sent and received directly using SMTP` and accept the rest of the defaults.

#### Install Acuparse:
* Get the Acuparse source:

    ``` git init /opt/acuparse && cd /opt/acuparse && git remote add -t master -f origin https://github.com/acuparse/acuparse.git && git checkout master ```
 
* Set the owner on the web root: `chown -R www-data:www-data src`

* Disable the default Apache config: `a2dissite 000-default.conf`

* Remove unneeded config files `rm /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/default-ssl.conf`

* Enable the Acuparse virtual host config: `cp /opt/acuparse/config/acuparse.conf /etc/apache2/sites-available/`

* Enable the Acuparse SSL virtual host config: `cp /opt/acuparse/config/acuparse-ssl.conf /etc/apache2/sites-available/`

* Make sure mod-rewrite is enabled: `a2enmod rewrite`

* Make sure SSL is enabled: `a2enmod ssl`

* Restart Apache: `service apache2 restart`

#### SSL Certificate Installation:
By Default Apache will use the snake oil cert to serve over HTTPS. For most users, this should be sufficient. If you use a hostname, install a certificate!

``` wget https://raw.githubusercontent.com/acuparse/installer/master/resources/le.sh && sh le.sh```

#### Setup Database:
* Create a new MySQL database for Acuparse.

    ``` mysql -uroot -p<your_password> -e "CREATE DATABASE acuparse; GRANT ALL PRIVILEGES ON `acuparse`.* TO 'acuparse@localhost' IDENTIFIED BY '<your_DB_password>'; GRANT SUPER, EVENT ON *.* TO 'acuparse'@'localhost'" ```

#### Finish Up:
* Edit your cron to run the external updater script every minute:

    `crontab -e`, `* * * * * php /opt/acuparse/cron/cron.php > /dev/null 2>&1`
    
* Visit `http://<yourip/domain>` to populate the database, create an account, and finish configuration.

## Database Trimming:
Readings get stored in multiple temporary database tables. This temporary data should be cleaned up regularly to avoid ballooning the database.
When the external updater runs, it archives the most recent readings to the archive table for later use.

Database trimming is handled using the MySQL event handler.

* Recommend enabling trimming, unless you need the additional data.
    * When enabled tower data is also trimmed. Should you wish to keep tower data, use that option instead.

If you find that the event scheduler is not behaving, check and make sure MySQL is up to date. Some upgrades from Debian 8 will not upgrade the database properly.

``mysql_upgrade -uroot -p<PASSWORD>``

## Barometer Readings:
You can modify the barometer readings used by Acuparse. Set your Access/smartHUB to use station pressure using MyAcuRite and adjust your offset.
Readings are only modified in Acuparse and sent to 3rd party sites. It does not alter the data MyAcuRite receives.
When you make changes on MyAcuRite you will eventually get a response back to the Access/smartHUB with the updates. 

Check the syslog and watch for your changes. Once your Access/smartHUB is reporting updated readings, modify the Acuparse config with your required offset.

> **Info:** It's recommended to use MyAcuRite to handle the pressure offset, where possible. Since it will configure the offset on the Access/smartHUB. Try adjusted pressure and modify your elevation.

## Uploading Data:
Detailed instructions for each available in docs/external.

* The cron job setup earlier will process your weather data and send updates to external sites automatically, as required.
    * Data is only sent to external sites when there is new data to send and enough time has passed for CWOP updates.
> **Notice:** Disable updating of Weather Underground from your Access/smartHUB/MyAcuRite. Watch your syslog for the response from MyAcuRite.

## MyAcuRite Response:
### SmartHUB
When MyAcuRite receives your readings, it sends back a JSON response to your smartHUB in the following format.
`{"localtime":"00:00:00","checkversion":"","ID1":"","PASSWORD1":"","sensor1":"","elevation":""}`.

* localtime = Local time the reading was received. Keeps time on the Access/smartHUB and is used mainly for rainfall readings.
* checkversion = The current firmware version available. Currently 224.
* ID1 = Weather Underground Station ID.
* PASSWORD1 = Weather Underground Station Password.
* sensor1 = Sensor used to send data to Weather Underground.
* elevation = Elevation of the smartHUB in feet.

A typical response looks like this:
`{"localtime":"00:00:00","checkversion":"224"}`

### Access
When the Access reports readings to MyAcurite, it responds with an empty message. `{}`.

## Email Outage Notifications:
Outage notifications are sent to all registered admins. You can configure some simple values for outage checking, the system will email you when there is no data received.

The updater first checks to see if there is new data to send. If there isn't, it will start the email process.
If there is no new data due to updates not being received in the configured period, Acuparse will send an email at your chosen interval.

## Tower Sensors:
Acuparse allows for the addition of as many Tower sensors as the Access/smartHUB will pass along. You can choose which sensors are shown publicly or only to logged in users. Towers are configured and arranged using the admin settings.
          
## Check Installation:

## Syslog:
View your syslog to see the data flowing through your system and to look for any trouble. Enable debug logging for a more detailed view.

``` tail -f /var/log/syslog ```

## Data Display:
The primary user interface uses AJAX to pull the most recent HTML formatted data every minute.

Aside from the primary interface, you can also pull the bootstrap formatted HTML data or a JSON array, for use in outside applications.

* JSON: `http://<yourip/domain>/?json`
* HTML: `http://<yourip/domain>/?weather`

## Web Cam Installation (optional):
Three scripts are included in the `cam/templates` directory. They are used to get and process images from an IP camera.

Images get stored in `src/pub/img/cam`. They should be backed up regularly to avoid loss.

* local.sh - runs on a host local to the camera such as the NVR and sends the image to the Acuparse server.
* remote.sh - processes the image on the Acuparse server.
* combined.sh - used to process the image if the camera and Acuparse are both installed locally.

### Local/Remote Setup:
* On the system local to the camera:
    * Copy the cam directory to `/opt/acuparse/`
    * Copy `local.sh` from `cam/templates` to the cam folder and modify the values. `cp cam/templates/local.sh cam/`
    * Setup a cron job to process the image: `crontab -e`, `0,15,30,45 * * * * /bin/sh /opt/acuparse/cam/local.sh > /dev/null 2>&1`
    * Setup SSH keys so you can log in to your remote host from the local host without a password.

* On the Acuparse server:
    * Copy `remote.sh` from `cam/templates` to the cam folder and modify the values. `cp cam/templates/remote.sh cam/`

### Combined Setup:
* Copy `combined.sh` from `cam/templates` to the cam folder and modify the values. `cp cam/templates/combined.sh cam/`
* Setup a cron job to process the image: `crontab -e`, `0,15,30,45 * * * * /bin/sh /opt/acuparse/cam/combined.sh > /dev/null 2>&1`

> **Info:** Make sure ImageMagick is installed and available. Otherwise, images will not get processed.

## Invisible reCAPTCHA:
Recaptcha loads on the login and contact forms, as well as, when requesting a password reset.
* Sign up for a reCAPTCHA account at [google.com/recaptcha](https://www.google.com/recaptcha).
* Select Invisible reCAPTCHA when registering your new site.
* Enter your site key and secret in your site settings.
