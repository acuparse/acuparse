# Acuparse Installation Guide

This guide is designed to walk through the steps required to install Acuparse on a freshly installed Debian based
server.

You can install Acuparse locally on bare-metal or a VM, or you can use a Docker Container based installation.

!!! note
    Installation only supported on Debian/Raspbian Bullseye (11)/Buster (10) or Ubuntu Jammy (22.04)/Focal (20.04).

## RaspberryPi

!!! warning
    **DO NOT** use the automated install if you are **directly** connecting an Access/SmartHub to your Pi. If you **ARE
    NOT** directly connecting to a Pi, follow the Automated, Docker, or Manual install process.

- If you're connecting an Access/SmartHub **directly** to your PI, see the community provided
  [RaspberryPi Direct Connect](https://docs.acuparse.com/other/RPI_DIRECT_CONNECT) installation guide.
    - This guide is not yet officially supported by the Acuparse project but was moved to the main docs for ease of use.
      It may be out of date and has not yet been re-validated. If you have success/problems with this guide, please
      report them on the users mailing list or, in Slack.
    - See also: [Troubleshooting](https://docs.acuparse.com/TROUBLESHOOTING/#raspberrypis)

## Automated Acuparse Installation

### Bare-metal or Virtual

- Install your base operating system and update.
- Then run the installer.

```bash
curl -O https://gitlab.com/acuparse/installer/-/raw/master/install && sudo bash install | tee ~/acuparse.log
```

### Docker Container Based

```bash
curl -O https://gitlab.com/acuparse/installer/-/raw/master/install_docker && sudo bash install_docker | tee ~/acuparse.log
```

- See the [Docker Install Guide](https://docs.acuparse.com/DOCKER) for more details

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

    - Debian/Raspbian Bullseye(11):

      ```bash
      apt install git ntp imagemagick exim4 apache2 default-mysql-server php7.4 libapache2-mod-php7.4 php7.4-mysql php7.4-gd php7.4-curl php7.4-json php7.4-cli php7.4-common -y
      ```

    - Debian/Raspbian Buster(10):

      ```bash
      apt install git ntp imagemagick exim4 apache2 default-mysql-server php7.3 libapache2-mod-php7.3 php7.3-mysql php7.3-gd php7.3-curl php7.3-json php7.3-cli php7.3-common -y
      ```

    - Ubuntu 22.04 LTS:

      ```bash
      apt install git ntp imagemagick exim4 apache2 default-mysql-server php8.1 libapache2-mod-php8.1 php8.1-mysql php8.1-gd php8.1-curl php8.1-cli php8.1-common -y
      ```

    - Ubuntu 20.04 LTS:

      ```bash
      apt install git ntp imagemagick exim4 apache2 default-mysql-server php7.4 libapache2-mod-php7.4 php7.4-mysql php7.4-gd php7.4-curl php7.4-json php7.4-cli php7.4-common -y
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

By Default Apache will use the snake oil cert to serve over HTTPS. For most users, this should be sufficient. If you use
a hostname, install a certificate!

```bash
wget https://gitlab.com/acuparse/installer/-/raw/master/resources/le && sudo bash le
```

### Setup Database

- Create a new MySQL database for Acuparse:

```bash
mysql -u root -p {MYSQL_ROOT_PASSWORD} -e "DELETE FROM mysql.user WHERE User=''; DELETE FROM mysql.user WHERE
User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1'); DROP DATABASE IF EXISTS test; DELETE FROM mysql.db WHERE
Db='test' OR Db='test\\_%'; FLUSH PRIVILEGES;"
```

```bash
mysql -u root -p {MYSQL_ROOT_PASSWORD} -e "CREATE DATABASE IF NOT EXISTS acuparse;
CREATE USER 'acuparse' IDENTIFIED BY 'STRONGPASSWORD';
GRANT ALL ON acuparse.* TO 'acuparse'; GRANT EVENT ON acuparse.* TO 'acuparse';
GRANT RELOAD ON *.* TO 'acuparse'; GRANT SUPER ON *.* TO 'acuparse'; FLUSH PRIVILEGES;"
```

### Finish Up

- Edit your cron to run the external updater script every minute:

```bash
crontab -e`, `* * * * * php /opt/acuparse/cron/cron.php > /dev/null 2>&1
```

- Visit `http://{IP_ADDRESS/HOSTNAME}` to populate the database, create an account, and finish configuration.

## Initial Configuration

- After setting the database configuration, you will need to add your Access/SmartHUB MAC address and sensor ID's.
    - Visit `/admin/settings` and click on the `Sensor` tab.
    - Enter your MAC address with no spaces, dashes, or colons.
    - Enter your 8 digit Iris (5-in-1) or Atlas (7-in-1) ID including any leading 0's.

## Check Installation

### Initial Readings

It can take some time for your initial readings to populate the dashboard. You'll receive a `No Data Received!` message
on your dashboard, until the initial readings are processed and stored.

See [Initial Readings Troubleshooting](https://docs.acuparse.com/TROUBLESHOOTING#initial-readings)

### Syslog

View your syslog to see the data flowing through your system and to look for any trouble. Enable debug logging for a
more detailed view.

```bash
tail -f /var/log/syslog
```

- See the [Syslog Troubleshooting Guide](https://docs.acuparse.com/TROUBLESHOOTING#syslog) for more details

---

## DNS Redirect

!!! note
    Access users can use the included script to modify the Access upload server instead of, or as well as, redirecting DNS.
    See `/admin/access` once logged into your site.

If you are connecting your Access/smartHUB directly to Acuparse, you can install Bind9 and redirect the DNS locally.
Otherwise, you will need a DNS server installed on your network. See the
***[DNS Redirect Guide](https://docs.acuparse.com/other/DNS)*** for more details.

### Manually Update Access Server

Change to Acuparse:

```bash
curl -d 'ser=<ACUPARSE FQDN>' http://<ACCESS IP>/config.cgi
```

Reset to default:

```bash
curl -d 'ser=atlasapi.myacurite.com' http://<ACCESS IP>/config.cgi
```

### smartHUB

- Set up a local DNS override for `hubapi.myacurite.com` pointing to the external IP address of your Acuparse server.

### Access

- Set up a local DNS override for `atlasapi.myacurite.com` pointing to the external IP address of your Acuparse server.

### Troubleshooting

If your having trouble getting your Access readings sent to Acuparse, you might be running into trouble with
the [hardcoded DNS servers](https://docs.acuparse.com/TROUBLESHOOTING/#hardcoded-dns-servers).

---

## Database Trimming

Readings get stored in multiple temporary database tables. This temporary data should be cleaned up regularly to avoid
ballooning the database.

When the external updater runs, it archives the most recent readings to the archive table for later use.

**Database trimming is accomplished via the MySQL event scheduler.**

- Recommend enabling trimming, unless you need the additional data.
    - When enabled tower data is also trimmed. Should you wish to keep tower data, use that option instead.

If you find that the event scheduler is not behaving, ensure MySQL is up-to-date. Some upgrades from Debian 8 will not
upgrade the database properly.

```bash
mysql_upgrade -u root -p {YOUR_SQL_ROOT_PASSWORD}
```

## Barometer Readings

You can modify the barometer readings used by Acuparse. Set your Access/smartHUB to use station pressure using MyAcuRite
and adjust your offset.

Readings are only modified in Acuparse and sent to 3rd party sites. It **does not** modify the reading MyAcuRite
receives from your Access/smartHUB.

Check the syslog and watch for your changes. Once your Access/smartHUB is reporting updated readings, modify the
Acuparse config with your required offset.

## Uploading Data

Detailed instructions for each available in docs/external.

- The cron job setup earlier will process your weather data and send updates to external sites automatically, as
  required.
    - Data only sent to external sites when there is new data to send and enough time has passed for CWOP updates.

!!! info
    It's recommended to disable the updating of Weather Underground from your Access/smartHUB/MyAcuRite when using Acuparse.

### Master Sensor

By default, Acuparse will use the Iris/Atlas sensor to upload data to external sites. To upload data from a tower, change
the Master Temp/Humidity Sensor.

Changing the sensor sends those readings externally instead of the Iris/Atlas data. You can also choose to use the tower
readings for the data archive or use the readings from the Iris/Atlas.

### Realtime Updates

Acuparse supports the use of an RTL dongle along with a special relay service to facilitate realtime updates to weather data.
See the [RTL docs](https://docs.acuparse.com/REALTIME/) for more details on how to set this up.

### MyAcuRite Responses

#### Access

!!! note
    The Access sends data using TLS1.1 encryption. A certificate is required to receive data from the Access.

When MyAcuRite receives your readings, it responds with a JSON response in the following format:

```json
{
  "sensor1": "",
  "PASSWORD1": "",
  "timezone": "",
  "elevation": "",
  "ID1": ""
}
```

| Variable  | Description                                      |
|-----------|--------------------------------------------------|
| timezone  | Local timezone offset of the Access.             |
| ID1       | Weather Underground Station ID.                  |
| PASSWORD1 | Weather Underground Station Password.            |
| sensor1   | Sensor used to send data to Weather Underground. |
| elevation | Elevation of the Access in feet.                 |

- A typical response:

    ```bash
    {"timezone":"00:00""}
    ```

#### smartHUB

!!! Warning
    The smartHUB is now obsolete and no longer supported by Acurite. Acurite Access is the recommended replacement.

Acurite is no longer accepting updates from the smartHUB. The smartHUB will continue to work with Acuparse, but will not
send updates to Acurite. To continue to support the smartHUB Acuparse will now always respond with the below JSON response.

```json
{
  "localtime": "00:00:00",
  "checkversion": "224"
}
```

Setting localtime to the local time of your Acuparse install.

##### Legacy smartHUB MyAcurite Response (Archive Purposes Only)

When MyAcuRite receives your readings, it responds with a JSON response in the following format:

```json
{
  "localtime": "00:00:00",
  "checkversion": "",
  "ID1": "",
  "PASSWORD1": "",
  "sensor1": "",
  "elevation": ""
}
```

| Variable     | Description                                                                                                      |
|--------------|------------------------------------------------------------------------------------------------------------------|
| localtime    | Local time the reading was received. Keeps time on the Access/smartHUB and is used mainly for rainfall readings. |
| checkversion | The current firmware version available. Currently 224.                                                           |
| ID1          | Weather Underground Station ID.                                                                                  |
| PASSWORD1    | Weather Underground Station Password.                                                                            |
| sensor1      | Sensor used to send data to Weather Underground.                                                                 |
| elevation    | Elevation of the smartHUB in feet.                                                                               |

## Email and Mailgun Settings

Acuparse will attempt to use a locally installed email server. You can enable Mailgun instead in your Admin Settings.

### Mailgun

You will need your Mailgun API key and domain to configure.

See [How Do I Add or Delete a Domain?](https://help.mailgun.com/hc/en-us/articles/203637190-How-Do-I-Add-or-Delete-a-Domain-)

## Email Outage Notifications

Outage notifications are sent to all registered admins. You can configure some simple values for outage checking, the
system will email you when there is no data received.

The updater first checks to see if there is new data to send. If there isn't, it will start the email process. If there
is no new data due to updates not being received in the configured period, Acuparse will send an email at your chosen
interval.

## Tower Sensors

Acuparse allows for the addition of as many Tower sensors as the Access/smartHUB will pass along. You can choose which
sensors are shown publicly or only to logged-in users. Towers are configured and arranged using the admin settings.

- Acuparse also supports Indoor/Outdoor Temp and Humidity monitors, as well as lightning towers.

## Lightning Sensors

You can have a main Lightning sensor on your Atlas, as well as one Tower sensor. Configure the Lightning sensor settings
in your admin site settings inorder to display those readings.

## Filter Access Readings

The Access can send unwanted erroneous readings. Filtering is enabled by default. You can disable the filtering of these
readings in the Admin -> System Settings -> Sensor tab.

Acuparse will check the incoming Access readings and drop any that meet ALL the conditions below. Dropped readings will
not flow through to MyAcuRite and will not be saved to the database.

The dropped readings will be reported in your logs and Acuparse will still send a response to your Access.

### Filtered Readings

Readings that have a `tempF` of `-40` OR `0`, `relH` of `0` OR `1`, and `0` wind speeds. As well as, `0` light and UV
readings from the Atlas.

## Additional Outputs

The primary user interface uses AJAX to pull the most recent HTML formatted data from the API automatically.

Acuparse includes a special display mode for better viewing while in full-screen.

- Display Mode: `http(s)://<yourip/domain>/display`
    - Force light theme: `http(s)://<yourip/domain>/display?light`
    - Force dark theme: `http(s)://<yourip/domain>/display?dark`

Additionally, you can request Bootstrap 5 formatted HTML, JSON array(s), or plain text formatted for watermarking.

- See the [API Guide](https://docs.acuparse.com/API) for details.

## Webcam Installation (optional)

The script `webcam` is included in the `cam` directory. It's used to capture and process images from an IP camera.
You will need to be able to get an image snapshot from your camera via http, or an RTSP stream.

Images are stored in `src/pub/img/cam`. They should be backed up regularly to avoid loss.

The `webcam` script combines what was previously three template scripts into an all-in-one script. Allowing for it to be version controlled and no
longer needs to be updated between releases. You will still need to manually update local systems not running Acuparse.

> **Updating from `local`, `remote` or, `combined` scripts?**
>
> Copy `config.env` from `cam/templates` to `cam`, then copy your settings from the existing scripts into `config.env`.
>
> Update your crontab entry to use the new `webcam` script, ensure your using `bash` and not `sh`.
>
> `0,15,30,45 * * * * /bin/bash /opt/acuparse/cam/webcam > /dev/null 2>&1`

The `webcam` script operates in three modes:

| Script Mode | Description                                                                                     |
|-------------|-------------------------------------------------------------------------------------------------|
| `local`     | Runs on a host local to the camera (such as an NVR) and sends the image to the Acuparse server. |
| `remote`    | Processes an image on the Acuparse server.                                                      |
| `combined`  | Processes an image when the camera and Acuparse are both available locally.                     |

The `combined` mode is the default and should be used when you can access your camera from your Acuparse system.
For installs where your camera is not on the same local network as your Acuparse system, use the `local` and `remote` modes.

### Local/Remote Setup

#### Local System

!!! note
    Local scripts will need to be updated if the `webcam` script changes.

- On the system local to your camera, download the templates

    - [ZIP File](https://gitlab.com/acuparse/acuparse/-/archive/stable/acuparse-stable.zip?path=cam/templates)
    - [TAR.GZ File](https://gitlab.com/acuparse/acuparse/-/archive/stable/acuparse-stable.tar.gz?path=cam/templates)
    - [Text](https://gitlab.com/acuparse/acuparse/-/tree/stable/cam/templates)

- Extract the templates and move them into a new Acuparse directory.

  ```bash
  mkdir -p /opt/acuparse && \
  cp cam/ /opt/acuparse/ && \
  cd /opt/acuparse
  ```

- Copy `config.env` from `cam/templates` to the `cam` folder and modify the variables in `config.env`, setting the mode to `local`.

  ```bash
  cp cam/templates cam/
  ```

- Set up a cron job to process the image:

  ```bash
  crontab -e`, `0,15,30,45 * * * * /bin/bash /opt/acuparse/cam/webcam > /dev/null 2>&1
  ```

- Setup SSH keys, so you can log in to your remote host from the local host without a password

  ```bash
  ssh-copy-id -i ~/.ssh/{YOUR_KEY} {USERNAME}@{HOSTNAME}
  ```

#### Remote Acuparse System

- Copy `config.env` from `cam/templates` to the `cam` folder and modify the variables in `config.env`, setting the mode to `remote`.

  ```bash
  cp /opt/acuparse/cam/templates/config.env /opt/acuparse/cam
  ```

### Combined System Setup

- Copy `config.env` from `cam/templates` to the cam folder and modify the variables in `config.env`, setting the mode to `combined`.

  ```bash
  cp /opt/acuparse/cam/templates/config.env cam/
  ```

- Set up a cron job to process the image:

  ```bash
  crontab -e`, `0,15,30,45 * * * * /bin/bash /opt/acuparse/cam/webcam > /dev/null 2>&1
  ```

!!! info
    Ensure ImageMagick is installed and available. Otherwise, images will not get processed.

## Backup/Restore

A script is included in `cron` to run daily backups. It will run automatically on Docker installs, but local installs
will need to enable this manually.

- See the [Backup Guide](https://docs.acuparse.com/other/BACKUPS) for details.

## Southern Hemisphere Support

Users in the Southern Hemisphere generally install their stations pointing North and not South for the solar panels to operate properly. This leads
to an incorrect (opposite) wind direction reading. Acuparse includes a feature to switch your wind direction readings to support installations in the
Southern Hemisphere.

The raw wind degrees readings received by Acuparse will be reversed and used for all external uploaders and system readings.
**Raw data forwarded to MyAcurite will not be modified**.

- Available under Southern Hemisphere Support on the System Settings, Sensor tab.

## Google Settings

### Analytics

You can add your Google Analytics tracking code to appear on your website.

Enter your [Tracking ID](https://support.google.com/analytics/answer/7372977?hl=en) in your site settings.

### Invisible reCAPTCHA

Recaptcha loads on the authentication and contact forms, as well as, when requesting a password reset.

- Sign up for a reCAPTCHA account at [google.com/recaptcha](https://www.google.com/recaptcha).
- Select Invisible reCAPTCHA when registering your new site.
- Enter your site key and secret in your site settings.

## Matomo

Acuparse also includes the ability to report tracking data to Matomo.

Enter your [Site ID](https://matomo.org/faq/general/faq_19212) and Matomo install domain in your system settings.

## Debug Server

You can send MyAcuRite readings to an external debug server. To enable, manually edit `src/usr/config.php`.

- Find `debug->server->show` and change it to true.

The debug tab will now appear in your system settings.
