# Acuparse Installation Guide

This guide is designed to walk through the steps required to install Acuparse on a freshly installed Debian based server.

!!! note
    Installation only supported on Debian/Rasbian Buster(10) and Ubuntu 18.04/20.04.

## Automated Acuparse Installation

- Install your base operating system and update.
- Then run the installer.

```bash
curl -O https://gitlab.com/acuparse/installer/raw/master/install && sudo bash install | tee ~/acuparse.log
```

### Docker Compose

```bash
curl -O https://gitlab.com/acuparse/installer/raw/master/install_docker && sudo bash install_docker | tee ~/acuparse.log
```

- See the [Docker Install Guide](https://docs.acuparse.com/DOCKER) for more details

### Raspberry Pi

!!! warning Only follow the below guide if you are **directly** connecting an Access/SmartHub to your Pi. If you are not
directly connecting to a Pi, follow the automated, Docker, or manual install process described in this doc.

- If you're connecting an Access/SmartHub **directly** to your PI, see the detailed direct installation guide for Raspbian
in this [Wiki Doc](https://gitlab.com/acuparse/acuparse/wikis/Installation-on-Raspberry-Pi).

## Manual Acuparse Installation

- Switch to the root account to install:
  
    ```bash
    sudo su
    ```
  
- Change to the root directory:
  
    ```bash
    cd ~
    ```
  
- Check and update your system timezone:
  
    ```bash
    dpkg-reconfigure tzdata && systemctl restart rsyslog.service
    ```
  
- Install the required packages:

    - Debian/Rasbian Buster(10):

      ```bash
      apt install git ntp imagemagick exim4 apache2 default-mysql-server php7.3 libapache2-mod-php7.3 php7.3-mysql php7.3-gd php7.3-curl php7.3-json php7.3-cli php7.3-common -y
      ```

    - Ubuntu 20.04 LTS:

      ```bash
      apt install git ntp imagemagick exim4 apache2 default-mysql-server php7.4 libapache2-mod-php7.4 php7.4-mysql php7.4-gd php7.4-curl php7.4-json php7.4-cli php7.4-common -y
      ```

    - Ubuntu 18.04 LTS:

      ```bash
      apt install git ntp imagemagick exim4 apache2 default-mysql-server php7.2 libapache2-mod-php7.2 php7.2-mysql php7.2-gd php7.2-curl php7.2-json php7.2-cli php7.2-common -y
      ```

- Secure your MySQL install:
  
  ```bash
  mysql_secure_installation
  ```

### Email Server Config

Run `dpkg-reconfigure exim4-config` and choose the correct values for your system. Most users will need to select
`internet site; mail is sent and received directly using SMTP` and accept the rest of the defaults.

### Install Acuparse

- Get the Acuparse source:
  
  ```bash
  git init /opt/acuparse && cd /opt/acuparse && git remote add -t master -f origin https://gitlab.com/acuparse/acuparse.git && git checkout master
  ```
  
- Set the owner on the web root:
  
  ```bash
  chown -R www-data:www-data src
  ```
  
- Disable the default Apache config:
  
  ```bash
  a2dissite 000-default.conf
  ```
  
- Remove unneeded config files:
  
  ```bash
  rm /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/default-ssl.conf
  ```
  
- Enable the Acuparse virtual host config:
  
  ```bash
  cp /opt/acuparse/config/acuparse.conf /etc/apache2/sites-available/
  ```
  
- Enable the Acuparse SSL virtual host config:
  
  ```bash
  cp /opt/acuparse/config/acuparse-ssl.conf /etc/apache2/sites-available/
  ```
  
- Ensure mod-rewrite is enabled:
  
  ```bash
  a2enmod rewrite
  ```
  
- Ensure SSL is enabled:
  
  ```bash
  a2enmod ssl
  ```
  
- Restart Apache:
  
  ```bash
  service apache2 restart
  ```

### SSL Certificate Installation

By Default Apache will use the snake oil cert to serve over HTTPS. For most users, this should be sufficient. If you use a hostname, install a certificate!

```bash
wget https://gitlab.com/acuparse/installer/raw/master/resources/le && sudo bash le
```

### Setup Database

- Create a new MySQL database for Acuparse:
  
```bash
mysql -u root -p {MYSQL_ROOT_PASSWORD} -e "DELETE FROM mysql.user WHERE User=''; DELETE FROM mysql.user WHERE
User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1'); DROP DATABASE IF EXISTS test; DELETE FROM mysql.db WHERE
Db='test' OR Db='test\\_%'; FLUSH PRIVILEGES;"
```

```bash
mysql -u root -p {MYSQL_ROOT_PASSWORD} -e "CREATE DATABASE IF NOT EXISTS acuparse; GRANT ALL PRIVILEGES ON acuparse.*
TO 'acuparse' IDENTIFIED BY '$ACUPARSE_DATABASE_PASSWORD'; GRANT SUPER, EVENT ON *.* TO 'acuparse'; FLUSH PRIVILEGES;"
```

### Finish Up

- Edit your cron to run the external updater script every minute:

```bash
crontab -e`, `* * * * * php /opt/acuparse/cron/cron.php > /dev/null 2>&1
```

- Visit `http://{IP_ADDRESS/HOSTNAME}` to populate the database, create an account, and finish configuration.

#### Optional

- Install phpMyAdmin for database administration

```bash
apt install phpmyadmin
```

## Check Installation

### Syslog

View your syslog to see the data flowing through your system and to look for any trouble. Enable debug logging for a more detailed view.

```bash
tail -f /var/log/syslog
```

## DNS Redirect

> **Note:** Access users can use the included script to modify the Access upload server instead of, or as well as, redirecting DNS.
>
> See [/admin/access](/admin/access) once logged into your site.

If you are connecting your Access/smartHUB directly to Acuparse, you can install Bind9 and redirect the DNS locally.
Otherwise, you will need a DNS server installed on your network. See the ***[DNS Redirect Guide](https://docs.acuparse.com/DNS)***
for more details.

### Manually Update Access

Change to Acuparse:

```bash
curl -d 'ser=<ACUPARSE IP/FQDN>' http://<ACCESS IP>/config.cgi
```

Reset to default:

```bash
curl -d 'ser=atlasapi.myacurite.com' http://<ACCESS IP>/config.cgi
```

### smartHUB

- Setup a local DNS override for `hubapi.myacurite.com` pointing to the external IP address of your Acuparse server.

### Access

- Setup a local DNS override for `atlasapi.myacurite.com` pointing to the external IP address of your Acuparse server.

## Updating

Detailed upgrade instructions for significant releases will be published in the docs/updates folder if required.

- Pull the changes from Git.

```bash
cd /opt/acuparse && sudo git pull
```

- Connect to your site to complete the update.

---

## Database Trimming

Readings get stored in multiple temporary database tables. This temporary data should be cleaned up regularly to avoid ballooning
the database.

When the external updater runs, it archives the most recent readings to the archive table for later use.

**Database trimming is accomplished via the MySQL event scheduler.**

- Recommend enabling trimming, unless you need the additional data.
    - When enabled tower data is also trimmed. Should you wish to keep tower data, use that option instead.

If you find that the event scheduler is not behaving, ensure MySQL is up to date. Some upgrades from Debian 8 will not upgrade
the database properly.

```bash
mysql_upgrade -u root -p {YOUR_SQL_ROOT_PASSWORD}
```

## Barometer Readings

You can modify the barometer readings used by Acuparse. Set your Access/smartHUB to use station pressure using MyAcuRite
and adjust your offset.

Readings are only modified in Acuparse and sent to 3rd party sites. It **does not** modify the reading MyAcuRite receives
from your Access/smartHUB.

Check the syslog and watch for your changes. Once your Access/smartHUB is reporting updated readings, modify the Acuparse
config with your required offset.

### Barometer Source

If you are using an Access and a smartHUB at the same time, it can cause trouble with barometer readings. Since they can
be slightly different.

- Default:
    - Saves all Barometer readings.
- Hub:
    - Saves only the smartHUB Barometer readings.
- Access:
    - Saves only the Access Barometer readings.

## Uploading Data

Detailed instructions for each available in docs/external.

- The cron job setup earlier will process your weather data and send updates to external sites automatically, as required.
    - Data only sent to external sites when there is new data to send and enough time has passed for CWOP updates.

> **Notice:** Disable updating of Weather Underground from your Access/smartHUB/MyAcuRite. Watch your syslog for the response
> from MyAcuRite.

### Master Sensor

By default Acuparse will use the 5-in-1/Atlas sensor to upload data to external sites. To upload data from a tower, change
the Master Temp/Humidity Sensor.

Changing the sensor sends those readings externally instead of the 5-in-1/Atlas data. You can also choose to use the tower
readings for the data archive or use the readings from the 5-in-1/Atlas.

## MyAcuRite Responses

### Access

When MyAcuRite receives your readings, it responds with a JSON response in the following format:

```json
{"sensor1":"","PASSWORD1":"","timezone":"","elevation":"","ID1":""}
```

Variable | Description
--- | ---
timezone | Local timezone offset of the Access.
ID1 | Weather Underground Station ID.
PASSWORD1 | Weather Underground Station Password.
sensor1 | Sensor used to send data to Weather Underground.
elevation | Elevation of the Access in feet.

- A typical response:

    ```bash
    {"timezone":"00:00""}
    ```

### smartHUB

> **Notice:** To prevent firmware updates, the smartHUB response is now being generated by Acuparse. The response generated
> by MyAcuRite is no longer sent back to the smartHUB.
>
> **smartHUB settings modified using the MyAcuRite site will not be reflected by the smartHUB.**

When MyAcuRite receives your readings, it responds with a JSON response in the following format:

```json
{"localtime":"00:00:00","checkversion":"","ID1":"","PASSWORD1":"","sensor1":"","elevation":""}
```

Variable | Description
--- | ---
localtime | Local time the reading was received. Keeps time on the Access/smartHUB and is used mainly for rainfall readings.
checkversion | The current firmware version available. Currently 224.
ID1 | Weather Underground Station ID.
PASSWORD1 | Weather Underground Station Password.
sensor1 | Sensor used to send data to Weather Underground.
elevation | Elevation of the smartHUB in feet.

Acuparse will now always respond with:

```json
{"localtime":"00:00:00","checkversion":"224"}
```

Setting localtime to the local time of your Acuparse install.

## Email and Mailgun Settings

Acuparse will attempt to use a locally installed email server. You can enable Mailgun instead in your Admin Settings.

### Mailgun

You will need your Mailgun API key and domain to configure.

See [How Do I Add or Delete a Domain?](https://help.mailgun.com/hc/en-us/articles/203637190-How-Do-I-Add-or-Delete-a-Domain-)

## Email Outage Notifications

Outage notifications are sent to all registered admins. You can configure some simple values for outage checking,
the system will email you when there is no data received.

The updater first checks to see if there is new data to send. If there isn't, it will start the email process.
If there is no new data due to updates not being received in the configured period, Acuparse will send an email at your
chosen interval.

## Tower Sensors

Acuparse allows for the addition of as many Tower sensors as the Access/smartHUB will pass along.
You can choose which sensors are shown publicly or only to logged in users. Towers are configured and arranged using the
admin settings.

- Acuparse also supports Indoor/Outdoor Temp and Humidity monitors, as well as lightning towers.

## Lightning Sensors

You can have a main Lightning sensor on your Atlas, as well as one Tower sensor.
Configure the Lightning sensor settings in your admin site settings inorder to display those readings.

## Additional Outputs

The primary user interface uses AJAX to pull the most recent HTML formatted data from the API automatically.

Acuparse includes a special display mode for better viewing while in full-screen.

- Display Mode: `http(s)://<yourip/domain>/display`
    - Force light theme: `http(s)://<yourip/domain>/display?light`
    - Force dark theme: `http(s)://<yourip/domain>/display?dark`

Additionally, you can request Bootstrap 4 formatted HTML, JSON array(s), or plain text formatted for watermarking.

- See the [API Guide](https://docs.acuparse.com/API) for details.

## Web Cam Installation (optional)

Three scripts are included in the `cam/templates` directory. They are used to get and process images from an IP camera.
You will need to be able to get a snapshot from the camera, or an RTSP stream.

Images get stored in `src/pub/img/cam`. They should be backed up regularly to avoid loss.

Script | Description
--- | ---
local | Runs on a host local to the camera (such as an NVR) and sends the image to the Acuparse server.
remote | Processes an image on the Acuparse server.
combined | Processes an image when the camera and Acuparse are both installed locally.

### Cam Archive Sort Order

The timestamp sort order can be changed in your admin features settings. You can sort today and archive by either `descending`
or `ascending`. Default is `ascending`.

### Local/Remote Setup

- On the system local to the camera:
    - Copy the cam directory to the acuparse directory and go there:

      ```bash
      cp cam/ /opt/acuparse/ && cd /opt/acuparse
      ```

    - Copy `local` from `cam/templates` to the cam folder and modify the values:

      ```bash
      cp cam/templates/local cam/
      ```

    - Setup a cron job to process the image:

      ```bash
      crontab -e`, `0,15,30,45 * * * * /bin/bash /opt/acuparse/cam/local > /dev/null 2>&1
      ```

    - Setup SSH keys so you can log in to your remote host from the local host without a password:

      ```bash
      ssh-copy-id -i ~/.ssh/{YOUR_KEY} {USERNAME}@{HOSTNAME}
      ```

- On the Acuparse server:
    - Copy `remote` from `cam/templates` to the cam folder and modify the values:

      ```bash
      cp cam/templates/remote cam/
      ```

### Combined Setup

- Copy `combined` from `cam/templates` to the cam folder and modify the values:
  
```bash
cp cam/templates/combined cam/
```

- Setup a cron job to process the image:

```bash
crontab -e`, `0,15,30,45 * * * * /bin/bash /opt/acuparse/cam/combined > /dev/null 2>&1
```

!!! info
    Ensure ImageMagick is installed and available. Otherwise, images will not get processed.

## Invisible reCAPTCHA

Recaptcha loads on the authentication and contact forms, as well as, when requesting a password reset.

- Sign up for a reCAPTCHA account at [google.com/recaptcha](https://www.google.com/recaptcha).
- Select Invisible reCAPTCHA when registering your new site.
- Enter your site key and secret in your site settings.

## Debug Server

You can send MyAcuRite readings to an external debug server. To enable, manually edit `src/usr/config.php`.

- Find `debug->server->show` and change it to true.

The debug tab will now appear in your system settings.

## Backup/Restore

A script is included in `cron` to run daily backups. It will run automatically on Docker installs, but local installs
will need to enable this manually.

- See the [Backup Guide](https://docs.acuparse.com/BACKUPS) for details.
