# [Acuparse](https://www.acuparse.com)

AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.

> **Notice:** This program is open source 3rd party software. It is neither written nor supported by AcuRite.

## Live Example Station

***See Acuparse in action via [ghwx.ca](https://www.ghwx.ca)***

## How it Works

[Acuparse](https://www.acuparse.com) is a PHP/MySQL program that captures, stores, and displays weather data from an AcuRite
5-in-1/Atlas weather station and tower sensors, via your Access/smartHUB. It uploads weather data to
[Weather Underground](https://https://www.wunderground.com), [Weathercloud](https://weathercloud.net),
[PWS Weather](https://www.pwsweather.com), [Windy](https://www.windy.com), and [CWOP](http://www.wxqa.com).
It also processes and stores images from a local network camera for display and uploads to Weather Underground.

Built for weather geeks and designed to be clean, simple, and mobile friendly. It uses a minimal UI with a focus on data,
not flashy graphics. Designed to compliment MyAcuRite and other 3rd party's sites and tools.

Acuparse requires a working AcuRite Access/smartHUB. You redirect weather data from your Access/smartHUB to your Acuparse
server. It is captured, stored, and then passed along to MyAcuRite untouched.
The response received from MyAcuRite is sent back to your Access/smartHUB. If sending data to MyAcuRite is disabled or
when using a SmartHUB, Acuparse creates the response.

## Features

- **Your weather data belongs to you, stored on your OWN server.**
- Display live camera images, send them to Weather Underground, and link from other sites like Weathercloud.
- Uploads weather data from your 5-in-1/Atlas and Tower sensors to external providers.
- Customize barometer readings.
- Light and Dark Themes.
- Display data in both &#8457; and &#8451;. Selectable default.
- Multiple tower sensors; public or private.
- Multiple admin or regular user accounts.
    - Regular users can only view private sensor data.
- Archived data display.
- Watch data flow using the syslog.
- Stays online even when MyAcuRite is not.
- Does not require internet access. Can be deployed independently of MyAcuRite.
- Export JSON data for use in external applications.
- Customizable email outage notifications.
- Google Invisible reCAPTCHA and Analytics support.

## Installation

> **Note:** If you are not using an Access, you will need to setup a DNS redirect using a DNS server on your local network.
>
> **Access Users:** The Acurite Access sends data to MyAcuRite using an SSL connection. By Default Apache will use the snake oil cert to serve over HTTPS.
> For most users, this should be sufficient. If you use a hostname, you will need to install and configure an SSL certificate.
> The installer will ask and attempt to generate a Lets Encrypt cert for you.

See [docs/INSTALL.md](https://docs.acuparse.com/INSTALL) for detailed installation instructions.

Installing on a fresh instance of a Debian/Rasbian Buster(10) or Ubuntu 18.04/19.04 is the only officially supported and tested install method.

### Quick Install

- Install the base operating system and update.
- Download and run the installer.
    - `wget https://gitlab.com/acuparse/installer/raw/master/install && sudo bash install | tee ~/acuparse.log`

## Additional Outputs

The primary user interface uses AJAX to pull the most recent HTML formatted data every minute.

Acuparse includes a Display mode for better viewing while in full-screen.

- Display Mode: `http(s)://<yourip/domain>/display`
    - Force light theme: `http(s)://<yourip/domain>/display?light`
    - Force dark theme: `http(s)://<yourip/domain>/display?dark`

Additionally, you can request Bootstrap 4 formatted HTML, a JSON array, or plain text formatted for watermarking.

- HTML: `http(s)://<yourip/domain>/?weather`
- Archive HTML: `http(s)://<yourip/domain>/archive?html`
- JSON: `http(s)://<yourip/domain>/?json`
- Tower JSON: `http(s)://<yourip/domain>/?json_tower&sensor=<SENSOR ID>`
- Plain Text: `http(s)://<yourip/domain>/?cam`

## Troubleshooting

The best way to troubleshoot your install is to view the syslog. All output is logged there.
`tail -f /var/log/syslog`

## What's Missing

- **Not all Atlas data is currently logged!**
    - Lightning, Light, and UV coming in Version 3.
- Advanced Data Reporting.
    - Reports can be run against the database. Open issues for custom report requests and tag them with the reports tag.
        - phpMyAdmin is an excellent tool for advanced manual reporting on your station.
- Charts and Graphing.
    - Since this is available at most external weather sites.
- Multiple Access/smartHUB/5-in-1/Atlas sensors.
    - The current framework is built around a single Access/smartHUB and 5-in-1/Atlas sensor.

## Donations

If you like Acuparse, please support the project by buying me a coffee!

[![Buy Me a Coffee](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/maxp)

## Licencing

Acuparse is open-source software. Released with an AGPL-3.0+ license. It also uses several other open source scripts.
Their licences are included where available.

3rd party scripts located in `src/pub/lib`.

See [LICENSE](LICENSE) for more details.

## Support and Discussion

[Join the chat on keybase](https://keybase.io/team/acuparse)

- Support for the core application/bugs is handled via [GitLab Issues](https://gitlab.com/acuparse/acuparse/issues).
    - You may also open a new issue by mailing [support@acuparse.com](mailto:support@acuparse.com).
- Community support is also provided via the [Users Mailing List](https://groups.google.com/a/lists.acuparse.com/forum/#!forum/users).

If you require advanced or commercial support, send mail to [hello@acuparse.com](mailto:hello@acuparse.com).

## Release Notes

See [CHANGELOG.md](CHANGELOG.md) for detailed release notes.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for more details.
