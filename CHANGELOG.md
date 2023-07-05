# Change Log

All notable changes to this project will be documented in this file.

Format based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [3.9.3](https://www.acuparse.com/releases/v3-9-3/) - 2023-07-05

### Changed

- Bootstrap to 5.3.0.
- FontAwesome to 6.4.0.

### Fixed

- WeatherCloud UV Index to integer (Issue 176).

## [[3.9.2](https://www.acuparse.com/releases/v3-9-2/) - 2022-11-26

### Fixed

- Contact form always enabled when saving system settings.
- Previous cron updates to handle `0` readings with tower data broke external uploads.
- Tower Lightning data not appearing in the API/MQTT (Issue 172).

### Changed

- Bootstrap to 5.2.3.
- FontAwesome to 6.2.1.
- RTL Relay Server to 1.1.0 (Issue 168 & 169).
    - Syslog port changed to `10514` to avoid conflicts, update your compose files if you are using the RTL relay server.
    - `command: -F syslog:relay:514` to `command: -F syslog:relay:10514`
    - Run `docker-compose pull && docker-compose up -d` to update.

### Added

- Option to use the system timezone for RTL readings (Issue 170).

## [[3.9.1](https://www.acuparse.com/releases/v3-9-1/) - 2022-11-20

### Fixed

- UV Index via RTL was not being processed correctly.

### Changed

- Set retained flag on MQTT messages (Issue 171).
- Update CRON to check for `0` readings before uploading and archiving.
    - When using tower sensors and trimming, readings will zero out when the month rolls over.

## [[3.9.0](https://www.acuparse.com/releases/v3-9-0/) - 2022-09-18

### Added

- Beta RTL Dongle Support via the Acuparse Syslog Relay.
- Tower data is now sent during MQTT updates.

### Changed

- FontAwesome to 6.2.0.
- Bootstrap to 5.2.1.
- instant.page to 5.1.1.
- Ongoing changes to lightning update logic.
- Database queries better prepared for occasional duplicated readings.
- Other minor bug fixes and code cleanup.
- Logging levels/debug logging updated.

### Fixed

- Cron Backup script not creating archive as expected (Issue 166).
- Humidity & Pressure Trend not calculated correctly.
- More lightning logic tweaks (Issue 165).

## [[3.8.1](https://www.acuparse.com/releases/v3-8-1/) - 2022-01-01

### Fixed

- Minor changes to lightning logic.

### Added

- Minor changes to better facilitate future testing.

## [[3.8.0](https://www.acuparse.com/releases/v3-8-0/) - 2021-10-27

### Added

- Ability to publish to an external MQTT Broker.

### Changed

- Offline Towers fail gracefully and will show as offline.
    - API will return `NULL` readings for the offline sensor and `"lastUpdated":"OFFLINE"`.

### Fixed

- Minor changes to lightning logic.
- Small bug fixes and minor code refactoring.

## [[3.7.1]](https://www.acuparse.com/releases/v3-7-1/) - 2021-10-17

### Fixed

- Wind Direction reversal not working as expected.

## [[3.7.0]](https://www.acuparse.com/releases/v3-7-0/) - 2021-10-09

### Changed

- Bootstrap to v5.1.3.

### Added

- Southern Hemisphere settings to reverse Wind Direction.
- Matomo Analytics integration.
- Legacy Buster Docker container for backward compatability.
    - Some older Kernels do not support Apache on Bullseye.
    - Tags are `acuparse/acuparse:buster` and `acuparse/acuparse:<VERSION>-buster`.

### Removed

- Weather Underground webcam uploader from cam scripts.
    - WU deprecating camera uploads again, and it never really worked.

### Fixed

- Unable to complete update in UI if update checking disabled.

## [[3.6.1]](https://www.acuparse.com/releases/v3-6-1/) - 2021-10-03

### Changed

- Bootstrap to v5.1.1.
- Jquery to v3.6.0.

### Fixed

- CWOP uploading `0` wind gust.
- Navigation too big on some mobile.
- Update Slack inviter link.

## [[3.6.0]](https://www.acuparse.com/releases/v3-6-0/) - 2021-08-22

### Changed

- Move container image to and add installer support for Debian Bullseye.
    - A [Buster to Bullseye update script](https://docs.acuparse.com/UPDATING/#update-debian-buster-to-bullseye) is
      available.
    - MariaDB container updated to Bullseye and MariaDB v10.5.
- Bootstrap to v5.1.0.
- FontAwesome to v5.15.4.

### Fixed

- `strikecount` always 0 / Undefined array warning.

## [[3.5.0]](https://www.acuparse.com/releases/v3-5-0/) - 2021-07-18

### Changed

- Bootstrap to v5.0.2.
    - Updates to UI formatting and structure.
- Webcam script now uses curl instead of wget.
    - Curl now saves the image as `$CAMERA_FILENAME`. Set `$CAMERA_HOST` to the full image path.

### Fixed

- Timestamps in `api/system/status` not using config date settings.
- Beaufort and Wind Direction inaccuracies when readings are `0`.
- Update Apache default SSL config to specifically allow TLSv1.1.
    - Access uploads readings using TLSv1.1.

### Added

- Raw lightning sensor `strikecount` to JSON API.

## [[3.4.0]](https://www.acuparse.com/releases/v3-4-0/) - 2021-05-18

### Changed

- FontAwesome to v5.15.3.
- Weathercloud API updates.
- General Code Formatting and Cleanup.
- PWSweather now uses API key instead of a password, added `windgustmph`.
- External Upload URLS migrated to HTTPS.
- Camera scripts combined into one main script and variables file.
    - Changes do not affect existing scripts or configurations.
    - See the [install guide](https://docs.acuparse.com/INSTALL/#webcam-installation-optional) for update details.

### Fixed

- Outage notifications sent too often or failing to send.
- Moon rise/set times not using system timezone as expected.

## [[3.3.1]](https://www.acuparse.com/releases/v3-3-1/) - 2021-02-28

### Added

- Windy Station ID.
    - Required when using multiple stations with Windy.

### Fixed

- Weathercloud/WindGuru/OpenWeather uploading data using F not C as expected.

## [[3.3.0]](https://www.acuparse.com/releases/v3-3-0/) - 2021-02-02

### Fixed

- Modified DB Schema to support `feelsF` being null.
    - Resolves Archive update errors.
- Tower sensors now update Access last update time.
- Minor errors with trimming and first user on new installations.
- Stability Updates.

## Added

- Option in settings to filter erroneous Atlas readings.

## Changed

- Boostrap to v4.6.0.

## Removed

- Last Update Time in footer for logged in as an administrator.
    - Didn't update and was misleading.

## [[3.2.2]](https://www.acuparse.com/releases/v3-2-2/) - 2021-01-19

### Fixed

- Properly set `feelsF` and `feelsC` to `null`.
- Docker Container Backup job, not able to set `KEEP_BACKUPS_FOR` in environment.
    - Added additional details to docs.

## [[3.2.1]](https://www.acuparse.com/releases/v3-2-1/) - 2021-01-18

### Added

- Last Update to Sensor Status.
- Iris Wind Gust when using an Access.

### Fixed

- Bring back missing Iris Wind Average.
- Typos in some JSON endpoints.
- JSON Archive attempting to load Atlas data.
- Lightning to properly handle a `0` strike count.
- Ignore warnings for Last Month's Archive data that does not yet exist.

### Changed

- Use a more efficient SQL query when calculating Access Wind Peak.
- `FeelsF` and `FeelsC` now report `NULL` instead of `0`, if unset.
- FontAwesome to v5.15.2.

## [[3.2.0]](https://www.acuparse.com/releases/v3-2-0/) - 2021-01-07

### Fixed

- Updated Lightning logic.
- CSS not loading properly in Firefox.
    - Prefetch not yet enabled in Firefox.
- Empty SQL file in Docker backup task.

### Changed

- AcuRite is now advertising the 5-in-1 as the Iris for at least the last quarter.
    - Updated Doc/Settings/Variables/DB references to reflect the new name.
    - Add **or** `Iris` to Access `5N1` parsing.
        - AcuRite *could* switch `mt` via a firmware update.
- Updated archiving in local & combined cam scripts.

## [[3.1.2]](https://www.acuparse.com/releases/v3-1-2/) - 2021-01-03

### Fixed

- Moonrise/Moonset always displaying in UTC.
- All Time Light Archive readings displaying weekly values.
- Backup running evey minute in Docker container.

### Added

- Note in settings and docs to only enter characters for MAC addresses.
    - Saving settings will now properly check for unwanted MAC characters.
- dockerignore and container image cleanup.
- Additional Date/Time Formatting in Settings.
    - JSON API now uses the ISO 8601 format by default.
- Nullmailer configuration added to Docker container.
    - [Details](https://docs.acuparse.com/DOCKER/#email)
- Sensor Status API endpoint @ `/api/system/status`.

### Changed

- Header/Footer updated.
- Updated Copyright for 2021.
- Doc updates for Docker and V3 update.
    - Clarify migration process from local 2.10 to Docker.
    - Clarify that Docker image `acuparse/acuparse` DOES NOT include a Database.
    - More details about required environment variables.

## [[3.1.1]](https://www.acuparse.com/releases/v3-1-1/) - 2020-11-26

### Fixed

- Using Tower Data for Upload and Archive.
- Lightning Tower breaks Dashboard.
- Small UI improvements.
- Improve Access Stability (Especially during outages).

### Changed

- Time and Ping API moved to /system.

## [[3.1.0]](https://www.acuparse.com/releases/v3-1-0/) - 2020-11-15

### Added

- Upload data to Open Weather Map.
    - See [docs/external/OPENWEATHER.md](https://docs.acuparse.com/external/OPENWEATHER)

### Fixed

- Due North Wind showing `Error`.
- Network hangups when sending data to external providers/debug servers.
- Use Access date instead of system date when updating dailyrain.
    - The Access NTP can fall behind, causing dailyrain to clear at midnight due to timestamp errors.
- Smooth out dashboard time updates.
- Offline towers not actually showing offline and Battery status always `Normal` in sensor status.
- Display mode not displaying as expected

### Changed

- Minor doc updates.
- Standardized log format

## [[3.0.1]](https://www.acuparse.com/releases/v3-0-1/) - 2020-11-09

### Fixed

- Sending email with Mailgun and multiple admins causing cron failures.
- Cron failing when using an Atlas with no Lightning sensor.
- Windguru setting stays disabled and data not uploading as expected.

## [[3.0.0]](https://www.acuparse.com/releases/v3-0-0/) - 2020-10-25

See the [Version 3 Update Guide](https://docs.acuparse.com/updates/v3) for instructions.

### Added

- Support all Atlas readings (UV, Lightning, Light, Wind).
- Lightning Tower support.
- Windguru Support.
- Option to sort camera images ascending/descending.
- Option to display High/Low tower temp.
- Docker support and image.
    - See [docs/DOCKER.md](docs/DOCKER.md) for more details.
- RTSP added to webcam scripts.
- Button to send a test email.
- Mailgun support.
- Password entry verification when adding/editing a user.

### Fixed

- Basic MySQL error handling.
- Refined install/testing.
- Database optimizations.

### Changed

- JSON/HTML output moved to API.
    - See [docs/API.md](docs/API.md) for more details.
- Primary dependency's moved to composer.
    - Bootstrap to v4.5.3.
    - FontAwesome to v5.15.1.
    - instant.page to v5.1.0.
    - jQuery to v3.5.1.
    - Lightbox to v2.11.3.
- Update moonPhase script.
- Update jQuery UI Touch Punch from an updated fork.
- Update Copyright.
- Documentation updates.
- Chat moving from Keybase/Gitter to Slack.
    - [Get Slack Invite](https://communityinviter.com/apps/acuparse/docs).
- Major Code refactoring.
- Update checking moved to cron.
    - Now sends some basic telemetry data.
        - See [docs/other/TELEMETRY.md](docs/other/TELEMETRY.md) for more details.

## [[2.10.0]](https://www.acuparse.com/releases/v2-10-0/) - 2019-10-06

### Added

- Basic support for Lightning Tower.
- Support sending unknown sensors to MyAcuRite.

### Changed

- FontAwesome to 5.11.2.

## [[2.9.4]](https://www.acuparse.com/releases/v2-9-4/) - 2019-08-18

### Fixed

- Regression in Wind Direction. Removing Null.

## [[2.9.3]](https://www.acuparse.com/releases/v2-9-3/) - 2019-08-17

### Fixed

- Lightbox not loading.

## [[2.9.2]](https://www.acuparse.com/releases/v2-9-2/) - 2019-08-17

### Fixed

- Wind Direction Calculation.

### Changed

- Switch to htmlentities for Wunderground Upload.
- FontAwesome to v5.10.1.
- Lightbox to v2.11.1.
- InstantPage to v2.0.0.
- Mailing list to Google Apps.

## [[2.9.1]](https://www.acuparse.com/releases/v2-9-1/) - 2019-06-28

### Changed

- Update Docs and Support for Debian Buster.
- Move repo to GitLab.

### Added

- Implement CI.
- Build docs using mkdocs.

## [[2.9.0]](https://www.acuparse.com/releases/v2-9-0/) - 2019-05-18

### Changed

- FontAwesome to v5.8.2.
- JQuery to v3.4.1.
- Lightbox to V2.11.0.
- Updated README.md.
- Modified External Updater Docs.

### Added

- Support for Windy.com.

## [[2.8.0]](https://www.acuparse.com/releases/v2-8-0/) - 2019-03-16

### Changed

- Removed legacy MyAcuRite SmartHub uploading and always generate a system response.
- Removed DateTimePicker as is currently unused.
- FontAwesome to v5.7.2.
- Bootstrap to 4.3.1.

### Fixed

- Temperature Icon Display.

### Added

- Prefetch using instant.page V1.2.2.
- Get Tower data in JSON Format (see README.md).

## [[2.7.1]](https://www.acuparse.com/releases/v2-7-1/) - 2019-01-02

### Changed

- Bootstrap to V4.2.1.
- Font Awesome to V5.6.3.
- Cleanup variables in camera upload scripts.
- Update Copyright.

### Fixed

- Header display on tiny screens.

## [[2.7.0]](https://www.acuparse.com/releases/v2-7-0/) - 2018-12-01

### Added

- Display Mode.
- Twilight CSS Theme.
- Upload WU data to a Generic Server and WeatherPoly Documentation.

### Changed

- Migrated core CSS into base.css, minify, minor cleanup.
- Documentation Updates.
- Loading Icons.

### Fixed

- W3 HTML Formatting.

## [[2.6.1]](https://www.acuparse.com/releases/v2-6-1/) - 2018-11-13

### Fixed

- Minor HTML formatting.
- Access upload destination.

### Changed

- Font Awesome to 5.5.0.

## [[2.6.0]](https://www.acuparse.com/releases/v2-6-0/) - 2018-10-05

### Added

- Basic support for new Atlas sensor.

### Changed

- Font Awesome to 5.3.1.

## [[2.5.2]](https://www.acuparse.com/releases/v2-5-2/) - 2018-08-08

### Fixed

- Dew point when uploading using tower data.
- Structured data only displaying when Google Analytics enabled.

### Added

- Discuss timezone settings in install guide.

### Changed

- Bootstrap to v4.1.3.
- Font Awesome to 5.2.

## [[2.5.1]](https://www.acuparse.com/releases/v2-5-1/) - 2018-07-15

### Changed

- Bootstrap to v4.1.2.
- Documentation links to GitHub pages.

## [[2.5.0]](https://www.acuparse.com/releases/v2-5-0/) - 2018-07-07

### Changed

- Bootstrap to v4.1.1.
    - Major changes to HTML/CSS.
- PHP/HTML/CSS variable changes and restructuring.
- Encode special characters in WU upload string.
- Font Awesome to 5.1.
- jQuery DateTimePicker to 2.5.20.
- Update Google Analytics and moved to `<head>` (Search console won't verify a site with GA in the `<body>`).
- Ubuntu 18.04 LTS and PHP 7.2 support.

### Added

- Open Graph Tags and default sharing image.
- Notification on dashboard when offline.
- Upload data to Weathercloud.

## [[2.4.0]](https://www.acuparse.com/releases/v2-4-0/) - 2018-05-07

### Added

- Ability to select which readings to store and use for Barometer readings.

### Changed

- Minor code formatting.
- Minor tweaks to the placement of some options in settings.
- Moon icon updates.

## [[2.3.2-beta]](https://www.acuparse.com/releases/v2-3-2-beta/) - 2018-05-06

### Changed

- jQuery DateTimePicker to 2.5.19.
- jQuery to 3.3.1.
- Font Awesome to 5.0.12 and icon updates.
- Lightbox to v2.10.0.
- Installer and general security fixes.

## [[2.3.1]](https://www.acuparse.com/releases/v2-3-1/) - 2018-04-27

### Changed

- Findu does not play nice with https at the moment. Forcing nav link to be http.
- External updates will use the proper appname when building update packets.

## [[2.3.0]](https://www.acuparse.com/releases/v2-3-0/) - 2018-04-07

### Added

- Ability to set a Tower as the source for Temp/Humidity when sending updates externally/archiving.

### Changed

- Minor formatting and documentation updates.

## [[2.2.3]](https://www.acuparse.com/releases/v2-2-3/) - 2018-04-03

### Changed

- Tower sensors now support the indoor/outdoor monitors for temp/humidity readings.
- Admin settings formatting.
- Minor formatting and documentation updates.

## [[2.2.2]](https://www.acuparse.com/releases/v2-2-2/) - 2018-02-26

### Added

- Timezone to Access response.
- Script to change the upload server locally on the Access. Removing the DNS redirect requirement.

### Changed

- Updated smartHUB EoL to 2019-03-01 due to AcuRite extending service.

### Fixed

- Wind readings in the archive worded incorrectly.
- Access updates not saving to DB.

## [[2.2.1]](https://www.acuparse.com/releases/v2-2-1/) - 2018-02-23

### Changed

- Documentation cleanup and updates.
- Modified HUB response to prevent firmware updates. Acuparse now sends it's own response back to the HUB.
- Login cookies so they will work as expected.

### Added

- Restriction to prevent sending HUB updates to MyAcuRite after EoL.

### Fixed

- Changing config settings from the UI broke the Access upload path.
- SSL certificate for atlasapi.myacurite.com API has the wrong hostname. Disabled SSL hostname checks.

## [[2.2.0]](https://www.acuparse.com/releases/v2-2-0/) - 2018-02-02

### Added

- Support for the new Access as it replaces the smartHUB.
- SSL Support.

### Changed

- Modified the cookie expiration time in account.php to reflect the same as in session.php.

## [[2.1.9]](https://www.acuparse.com/releases/v2-1-9/) - 2018-01-23

### Changed

- Modified Archive display format to align with the display format on the homepage.
- Updates page will now display notes from previous versions and file formatting changes.

### Fixed

- When disabling logging in settings, it was not being properly written to the config file.
- Watermark was not displaying imperial measurements when enabled.

### Added

- Option to hide alternate measurements from being displayed.

## [[2.1.8]](https://www.acuparse.com/releases/v2-1-8/) - 2017-12-30

### Fixed

- Tower admin not displaying proper privacy status.

### Added

- Added back Weather Underground Camera Upload. WU changed their mind.

## [[2.1.7]](https://www.acuparse.com/releases/v2-1-7/) - 2017-12-03

### Changed

- Default font to Open Sans for better visibility in the clean css template.
    - Moved unneeded styles to a new template file.
- Reformatted Archive display.

### Removed

- Support for uploading images to Weather Underground due to it being decommissioned 15 Dec 2017 :(

## [[2.1.6]](https://www.acuparse.com/releases/v2-1-6/) - 2017-09-23

### Changed

- Minor bugfixes and improvements. Changed redirect paths and form label.

## [[2.1.5]](https://www.acuparse.com/releases/v2-1-5/) - 2017-08-08

### Changed

- Camera archive back and forward buttons corrected to display as expected.
- Updated datetimepicker from source.
- Moving to Debian Stretch.

## [[2.1.4]](https://www.acuparse.com/releases/v2-1-4/) - 2017-07-30

### Changed

- Moved 3rd party scripts around and made changes to support a commercial non GPL version.
- Fixed - Dew point showing &#8457; temp as the &#8451; temp in metric mode.
- Fixed - SQL Trim was not re-enabling xtower as expected.

## [[2.1.3]](https://www.acuparse.com/releases/v2-1-3/) - 2017-07-15

### Changed

- jQuery to 3.2.1.
- Modified localtime response again. This time with the correct regex. Rain data clears properly now.

## [[2.1.2]](https://www.acuparse.com/releases/v2-1-2/) - 2017-06-16

### Changed

- Undoing changes to MyAcuRite localtime response. It breaks rainfall data.

## [[2.1.1]](https://www.acuparse.com/releases/v2-1-1/) - 2017-06-10

### Changed

- Updated wrong installer path in docs/INSTALL.md.
- Minor updates to outage notifications. They should now send as expected.
- Minor changes to update checking.

### Removed

- References to the chat server.

### Added

- Added Event Scheduler check in cron since it's off by default. (A better way to cleanup database tables is on the
  roadmap.)
- Google Captcha added to contact form.
- MyAcuRite is terrible keeping time. They send the hour for the min and sec in their response.
    - Modify the response to the HUB and send server time instead.

## [[2.1.0]](https://www.acuparse.com/releases/v2-1-0/) - 2017-03-14

### Added

- Initial open source release.
