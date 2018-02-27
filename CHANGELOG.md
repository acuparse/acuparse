# Acuparse
## AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.

# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
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
- SSL Support

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
- Default font to Open Sans for better visibility in the clean css template. Moved unneeded styles to a new template file.
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
- jQuery to 3.2.1
- Modified localtime response again. This time with the correct regex. Rain data clears properly now.

## [[2.1.2]](https://www.acuparse.com/releases/v2-1-2/) - 2017-06-16
### Changed
- Undoing changes to MyAcurite localtime response. It breaks rainfall data.

## [[2.1.1]](https://www.acuparse.com/releases/v2-1-1/) - 2017-06-10
### Changed
- Updated wrong installer path in docs/INSTALL.md
- Minor updates to outage notifications. They should now send as expected.
- Minor changes to update checking.

### Removed
- References to the chat server.

### Added
- Added Event Scheduler check in cron since it's off by default. (A better way to cleanup database tables is on the roadmap.)
- Google Captcha added to contact form.
- MyAcurite is terrible keeping time. They send the hour for the min and sec in their response. Modify the response to the HUB and send server time instead.

## [[2.1.0]](https://www.acuparse.com/releases/v2-1-0/) - 2017-03-14
### Added
- Initial open source release.
