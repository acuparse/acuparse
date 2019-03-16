# [Acuparse](https://www.acuparse.com)
## AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.
### See it in action @ [ghwx.ca](https://www.ghwx.ca)

Acuparse is a PHP/MySQL program that captures, stores, and displays weather data from an AcuRite®‎ 5-in-1/Atlas weather station and tower sensors, via your Access/smartHUB. It uploads weather data to Weather Underground, Weathercloud, PWS Weather, and CWOP. It also processes and stores images from a local network camera for display and uploads to Weather Underground.

Built for weather geeks and designed to be clean, simple, and mobile friendly. It uses a minimal UI with a focus on data, not flashy graphics. Designed to compliment MyAcuRite and other 3rd party's sites and tools. 

> **Notice:** This program is open source 3rd party software. It is neither written nor supported by AcuRite®‎. This software requires a working AcuRite®‎ Access/smartHUB. Weather data gets redirected from your Access/smartHUB to your Acuparse server. It is captured, stored, and passed along to MyAcuRite.
The response from MyAcuRite gets sent back to the Access/smartHUB. Although the syslog may assist with diagnosing issues; if you cannot send data to MyAcuRite, you may experience trouble with Acuparse.
Users currently having trouble sending updates to Weather Underground should find Acuparse much more stable.

# Features:
* **Your weather data belongs to you, stored on your OWN server.**
* Display live camera images, send them to Weather Underground, and link from other sites.
* Uploads weather data from your 5-in-1/Atlas and Tower sensors to external providers.
* Customize barometer readings.
* Light and Dark Themes.
* Display data in both &#8457; and &#8451;. Selectable default.
* Multiple tower sensors; public or private.
* Multiple admin or regular user accounts.
    * Regular users can only view private sensor data. 
* Archived data display.
* Watch data flow using the syslog.
* Stays online even when MyAcuRite is not.
* Does not require internet access. Can be deployed independently of MyAcuRite.
* Export JSON data for use in external applications.
* Customizable email outage notifications.
* Google Invisible reCAPTCHA and Analytics support.

## What's Missing:
* **Not all Atlas data is currently logged!**
    * Lightning, Light, and UV coming in Version 3.
* Advanced Data Reporting.
    * Reports can be run against the database. Open issues for custom report requests and tag them with the reports tag. phpMyAdmin is an excellent tool for advanced manual reporting on your station.
* Charts and Graphing.
    * Since this is available at most external weather sites.
* Multiple Access/smartHUB/5-in-1/Atlas sensors
    * The current framework is built around a single Access/smartHUB and 5-in-1/Atlas sensor.

# Additional Outputs:
The primary user interface uses AJAX to pull the most recent HTML formatted data every minute.

Acuparse includes a Display mode for better viewing while in full-screen.
* Display Mode: `http(s)://<yourip/domain>/display`
    * Force light theme: `http(s)://<yourip/domain>/display?light`
    * Force dark theme: `http(s)://<yourip/domain>/display?dark`

Additionally, you can request Bootstrap 4 formatted HTML, a JSON array, or plain text formatted for watermarking.

* HTML: `http(s)://<yourip/domain>/?weather`
* Archive HTML: `http(s)://<yourip/domain>/archive?html`
* JSON: `http(s)://<yourip/domain>/?json`
* Tower JSON: `http(s)://<yourip/domain>/?json_tower&sensor=<SENSOR ID>`
* Plain Text: `http(s)://<yourip/domain>/?cam`
   
# Installation:
**Requires LAMP stack. Some PHP, Apache, and GNU/Linux experience recommended.**
> **Note:** If you are not using an Access, you will need to setup a DNS redirect using a DNS server on your local network. 

See [docs/INSTALL.md](https://acuparse.github.io/acuparse/INSTALL) for detailed installation instructions.

Installing on a fresh instance of a Debian based OS is the only officially supported and tested install method.

Acuparse can, **in theory**, be run locally on a Raspberry Pi(Raspbian). Some installer modifications might be required, if not using a supported OS.

**Access Users**

The Acurite Access sends data to MyAcuRite using an SSL connection. By Default Apache will use the snake oil cert to serve over HTTPS. For most users, this should be sufficient. If you use a hostname, you will need to install and configure an SSL certificate. The installer will ask and attempt to generate a Lets Encrypt cert for you.

## Quick Install:
> **Info:** Installer supports Debian Stretch(9), Ubuntu 18.04 LTS, and Raspbian Stretch(9).

Install the base operating system and update. Then download and run the installer.

`wget https://raw.githubusercontent.com/acuparse/installer/master/install.sh && sudo sh install.sh | tee ~/acuparse.log`

### Troubleshooting:
If you experience unexpected results during or after your install, remove the config file and try again.

`sudo rm /opt/acuparse/src/usr/config.php`

# Updating:
Detailed upgrade instructions for significant releases will be published in the docs/updates folder if required.

* Pull the changes from Git.

    `cd /opt/acuparse && sudo git pull`
* Connect to your site to complete the update.

# Donations:
If you like Acuparse, please support the project by buying me a coffee!

<a href="https://www.buymeacoffee.com/maxp" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: auto !important;width: auto !important;" ></a>

# Licencing:
Acuparse is open-source software. Released with an AGPL-3.0+ license. It also uses several other open source scripts. Their licences included where available.

3rd party scripts located in `src/pub/lib`.

See [LICENSE](LICENSE) for more details.

# Support and Discussion:
Support for the core application/bugs is handled via [GitHub Issues](https://github.com/acuparse/acuparse/issues).

For everything else, join the [Users Mailing List](https://lists.acuparse.com/listinfo/users).

If you require advanced assistance, consider [commercial licencing and support](https://www.acuparse.com/commercial). 

# Release Notes:

See [CHANGELOG.md](CHANGELOG.md) for detailed release notes.

# Contributing:

See [CONTRIBUTING.md](CONTRIBUTING.md) for more details.
