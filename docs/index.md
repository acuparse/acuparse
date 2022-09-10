# [Acuparse Documentation](https://docs.acuparse.com)

## Project Services Status

|                                                                           Core                                                                            | Installer                                                                                                                                                     | RTL Relay                                                                                                                                                   | Website                                                                                                                                                                                                                                                | DNS Sync                                                                                                                                        | Release Service                                                                                                                                                                                                                                           |
|:---------------------------------------------------------------------------------------------------------------------------------------------------------:|---------------------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [![Acuparse Status](https://gitlab.com/acuparse/acuparse/badges/stable/pipeline.svg "Acuparse Pipeline")](https://gitlab.com/acuparse/acuparse/pipelines) | [![Installer Status](https://gitlab.com/acuparse/installer/badges/master/pipeline.svg "Installer Pipeline")](https://gitlab.com/acuparse/installer/pipelines) | [![RTL-Relay Status](https://gitlab.com/acuparse/rtl-relay/badges/main/pipeline.svg "RTL Relay Pipeline")](https://gitlab.com/acuparse/rtl-relay/pipelines) | [![Website Status](https://gitlab.com/acuparse/website/badges/master/pipeline.svg "Website Pipeline")](https://gitlab.com/acuparse/website/pipelines) ![Website Status](https://img.shields.io/uptimerobot/status/m787016706-5967cac56e544c773094fc78) | [![DNS Status](https://gitlab.com/acuparse/dns_sync/badges/master/pipeline.svg "DNS Pipeline")](https://gitlab.com/acuparse/dns_sync/pipelines) | [![DNS Status](https://gitlab.com/acuparse/rms/badges/master/pipeline.svg "RMS Pipeline")](https://gitlab.com/acuparse/rms/pipelines) ![Release Management Service Status](https://img.shields.io/uptimerobot/status/m787016696-3d8a19d46b2014c8daa8d9d6) |

Welcome to the [Acuparse](https://www.acuparse.com) Documentation. Use the resources below to assist in your
installation and configuration of Acuparse.

Built for weather geeks and designed to be clean, simple, and mobile friendly. It uses a minimal UI with a focus on
data, not flashy graphics. Designed to compliment MyAcuRite and other 3rd party's sites and tools.

!!! attention
    This program is open source 3rd party software. It is neither written nor supported by AcuRite.

## How it Works

[Acuparse](https://www.acuparse.com) is a PHP/MySQL program that captures, stores, and displays weather data from an
AcuRite Iris (5-in-1) or Atlas (7-in-1) weather station and tower sensors, via your Access/smartHUB. It uploads weather
data to
[Weather Underground](https://https://www.wunderground.com), [CWOP](http://www.wxqa.com)
, [Weathercloud](https://weathercloud.net),
[PWS Weather](https://www.pwsweather.com), [Windy](https://www.windy.com), [Windguru](https://www.windguru.cz),
[OpenWeather](https://openweathermap.org/), and [MQTT](https://mqtt.org/) Brokers. It also processes and stores images from a local network camera for
display and linking to external websites.

Built for weather geeks and designed to be clean, simple, and mobile friendly. It uses a minimal UI with a focus on
data, not flashy graphics. Designed to compliment MyAcuRite and other 3rd party's sites and tools.

Acuparse requires a working AcuRite Access/smartHUB. You redirect weather data from your Access/smartHUB to your
Acuparse server. It is captured, stored, and then passed along to MyAcuRite untouched. The response received from
MyAcuRite is sent back to your Access/smartHUB. If sending data to MyAcuRite is disabled or when using a smartHUB,
Acuparse creates the response.

## Main Installation Guides

!!! info
    Installation supported on Debian/Rasbian Bullseye (11)/Buster (10) or Ubuntu Jammy (22.04)/Focal (20.04).

- ***[Main Install Guide](INSTALL)***
- ***[Docker Install Guide](DOCKER)***
- ***[RaspberryPi Direct Connect](other/RPI_DIRECT_CONNECT)***

### Quick Install

- Install the base operating system and update.
- Download and run the installer.

    ```bash
    curl -o https://gitlab.com/acuparse/installer/-/raw/master/install && sudo bash install | tee ~/acuparse.log`
    ```

#### Docker Compose

```bash
curl -o https://gitlab.com/acuparse/installer/-/raw/master/install_docker && sudo bash install_docker | tee ~/acuparse.log
```

- See the [DOCKER Install Guide](DOCKER) for more details.

## API

- [API Guide](API)

## Updating

- [Update Guide](UPDATING)

### Version Specific Guides

- [Version 3](updates/v3)
- [Version 2.3](updates/v2_3)

## Optional Configuration

- [DNS Redirect Configuration](other/DNS)
- [NGINX Configuration](other/NGINX)
- [Backups](other/BACKUPS)
- [Telemetry](other/TELEMETRY)
- [RTL Dongle / Realtime Guide](REALTIME.md)

## External Updater Configuration

- [Weather Underground](external/WU)
- [Weathercloud](external/WC)
- [PWS Weather](external/PWS)
- [CWOP](external/CWOP)
- [WINDY](external/WINDY)
- [Windguru](external/WINDGURU)
- [Open Weather Map](external/OPENWEATHER)
- [MQTT](external/MQTT)

### Generic

- [WeatherPoly](external/generic/WEATHERPOLY)

## Troubleshooting

- [Troubleshooting Guide](TROUBLESHOOTING)

## Community Support

- [Slack](https://communityinviter.com/apps/acuparse/docs)
- [Users Mailing List](https://groups.google.com/a/lists.acuparse.com/forum/#!forum/users)

- Support for the core application/bugs is handled via [GitLab Issues](https://gitlab.com/acuparse/acuparse/issues).
    - You may also open a new issue by mailing [support@acuparse.com](mailto:support@acuparse.com). If you require
      advanced or commercial support, send mail to [hello@acuparse.com](mailto:hello@acuparse.com).

## Git Repositories

### Primary

- [GitLab](https://gitlab.com/acuparse/acuparse)

### Public Mirrors

- [GitHub](https://github.com/acuparse/acuparse)
- [BitBucket](https://bitbucket.org/acuparse/acuparse)
- [Azure](https://dev.azure.com/acuparse/acuparse)
