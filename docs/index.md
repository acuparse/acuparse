# [Acuparse Documentation](https://docs.acuparse.com)

## Project Pipeline Status
| Acuparse Main | Installer | Website |
| ---- | ---- | --- |
| [![Acuparse Status](https://gitlab.com/acuparse/acuparse/badges/master/pipeline.svg "Acuparse Status")](https://gitlab.com/acuparse/acuparse/pipelines) | [![Installer Status](https://gitlab.com/acuparse/installer/badges/master/pipeline.svg "Installer Status")](https://gitlab.com/acuparse/installer/pipelines) | [![Website Status](https://gitlab.com/acuparse/website/badges/master/pipeline.svg "Website Status")](https://gitlab.com/acuparse/acuparse/pipelines) | 

Welcome to the [Acuparse](https://www.acuparse.com) Documentation. Use the resources below to assist in your installation and configuration of Acuparse.

[Acuparse](https://www.acuparse.com) is a PHP/MySQL program that captures, stores, and displays weather data from an AcuRite 5-in-1/Atlas weather station and tower sensors, via your Access/smartHUB. It uploads weather data to [Weather Underground](https://https://www.wunderground.com), [Weathercloud](https://weathercloud.net), [PWS Weather](https://www.pwsweather.com), [Windy](https://www.windy.com), and [CWOP](http://www.wxqa.com). It also processes and stores images from a local network camera for display and uploads to Weather Underground.

Built for weather geeks and designed to be clean, simple, and mobile friendly. It uses a minimal UI with a focus on data, not flashy graphics. Designed to compliment MyAcuRite and other 3rd party's sites and tools. 

> **Notice:** This program is open source 3rd party software. It is neither written nor supported by AcuRite®‎. This software requires a working AcuRite®‎ Access/smartHUB. Weather data gets redirected from your Access/smartHUB to your Acuparse server. It is captured, stored, and passed along to MyAcuRite.
> The response from MyAcuRite gets sent back to the Access/smartHUB. Although the syslog may assist with diagnosing issues; if you cannot send data to MyAcuRite, you may experience trouble with Acuparse.
> Users currently having trouble sending updates to Weather Underground should find Acuparse much more stable.

***Git Repositories***

- [GitLab Repo (Primary)](https://gitlab.com/acuparse/acuparse)
- [GitHub Repo (Mirror)](https://github.com/acuparse/acuparse)

## Main Installation Guide

> **Info:** Installation supported on Debian/Rasbian Buster(10) or Ubuntu 18.04/19.04.

- ***[Acuparse Install Guide](INSTALL)***

### Quick Install

Install the base operating system and update. Then download and run the installer.

`wget https://gitlab.com/acuparse/installer/raw/master/install && sudo bash install | tee ~/acuparse.log`

### Updates

- [From 2.1.X](updates/from2_1)
- [From 2.4.X](updates/from2_4)

## Optional Configuration

- [DNS Redirect Configuration](DNS)
- [NGINX Configuration](NGINX)
- [WINDOWS Configuration](DNS)

## External Updater Configuration

- [Weather Underground](external/WU)
- [Weathercloud](external/WC)
- [PWS Weather](external/PWS)
- [CWOP](external/CWOP)
- [WINDY](external/WINDY)

### Generic

- [WeatherPoly](external/generic/WeatherPoly)
