# [Acuparse](http://www.acuparse.com)
## AcuRite®‎ smartHUB and IP Camera Data Processing, Display, and Upload.
### See it in action @ [ghwx.ca](http://www.ghwx.ca)

Acuparse is a PHP/MySQL program that captures, stores, and displays weather data from an AcuRite®‎ 5n1 weather station and tower sensors, via your smartHUB. It uploads weather data to Weather Underground, PWS Weather, and CWOP. It also processes and stores images from a local network camera for display.

Built for weather geeks and designed to be clean, simple, and mobile friendly. It uses a minimal UI with a focus on data, not flashy graphics. Designed to compliment MyAcuRite and other 3rd party's sites and tools. 

> **Notice:** This program is open source 3rd party software. It is neither written nor supported by AcuRite®‎. This software requires a working AcuRite®‎ smartHUB. Weather data is redirected from your smartHUB to your Acuparse server. It's captured, stored, and passed along to MyAcuRite.
The response from MyAcuRite is then sent back to the smartHUB. Although the syslog may assist with diagnosing issues; if you can't send data to MyAcuRite, you may experience trouble with Acuparse.
Users currently having trouble sending updates to Weather Underground should find Acuparse much more stable.

*AcuRite®‎ has announced the new Atlas sensor line. Once one is available for testing, the script will be updated to support both the 5n1 and the Atlas sensors.*

# Features:
* **Your weather data belongs to you, stored on your OWN server.**
* Showcase your live weather camera.
* Uploads weather data to external providers.
* Customize barometer readings.
* Display data in both &#8457; and &#8451;. Selectable default.
* Multiple tower sensors, public or private.
* Multiple admin/regular user logins.
    * Regular users can only view private sensor data. Future access to reporting is also planned.
* Archived data display.
* Watch data flow using the syslog.
* Stays online even when MyAcuRite is not.
* Does not require internet access. Can be deployed independent of MyAcuRite.
* Export JSON data for use in external applications.
* Customizable email outage notifications.
* Google Invisible reCAPTCHA and Analytics support.

## What's Missing:
* Advanced Data Reporting.
    * Reports can be run against the database. Open issues for custom report requests and tag them with the reports tag. phpMyAdmin is a great tool for advanced manual reporting on your station.
* Charts and Graphing.
    * Since this is available at most external weather sites, there is no current roadmap for inclusion. Graphing might become available as a part of future reporting, if there is enough demand.
* Multiple smartHUB's/5n1 sensors and ability to choose which sensor uploads externally.
    * The current framework is centered around one smartHUB with a single 5n1 sensor.
    Version 2 will continue using the single smartHub model. With development of Version 3 centering around support for multiple smartHUBS's, 5n1's, and Atlas sensors. 
    The ability to customise the sensor data sent to external sites will also be added. Since support for multiple sensors is going to require a deep restructuring, it's unknown if there will be an upgrade path to Version 3, from 2.
    Please keep that in mind when planning your long term data archiving. The core focus with any data migration between version 2 and 3 will be the archive data. Community feedback will mostly drive the development of version 3 and any migration paths.

# Installation:
**Requires LAMP stack. Some PHP, Apache, and GNU/Linux experience recommended.**

Installing on a fresh instance of a Debian based OS is the only officially supported and tested install method. Any other method is not officially supported or tested.

Acuparse can also, in theory, be run locally on a Raspberry Pi or similar configuration. It has not been extensively tested.

* See [docs/INSTALL.md](docs/INSTALL.md) for detailed installation instructions.

## Quick Install:
> **Info:** Installer currently supports Debian Stretch(9) and Ubuntu 16.04 LTS.

Install the base operating system and update. Then download and run the installer.

``` wget https://raw.githubusercontent.com/acuparse/installer/master/install.sh && sudo sh install.sh```

# Updating:
Detailed upgrade instructions for major releases will be published to the docs/updates folder, if required.

The basic update process:

* Pull the changes from Git.

    ``` cd /opt/acuparse && sudo git pull ```
* Connect to your site to complete the update.

# Licencing:
Acuparse is licenced under the AGPL-3.0+. It also uses several other open source scripts and their licences are included where available.

3rd party scripts are located in `src/pub/lib`.

See [LICENSE](LICENSE) for more details.

## Commercial Licence:
A commercial licence without any AGPL restrictions is also available. Visit [acuparse.com/commercial](https://www.acuparse.com/commercial) for details.

# Support:
Support for the core application is handled via [issues](https://github.com/acuparse/acuparse/issues).

Implementation and technical support is provided separately via the [maintainers](https://www.acuparse.com/support).

If you require advanced assistance, consider a commercial licence.

# Release Notes:

See [CHANGELOG.md](CHANGELOG.md) for detailed release notes.

# Contributing:

See [CONTRIBUTING.md](CONTRIBUTING.md) for more details.

# Discussion:
Join the [mailing lists](https://lists.acuparse.com).
