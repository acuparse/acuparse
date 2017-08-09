# Acuparse
## AcuRite®‎ smartHUB and IP Camera Data Processing, Display, and Upload.

# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [[2.1.5]](https://www.acuparse.com/releases/v2-1-5/) - 2017-08-08
### Changed
- Camera archive back and forward buttons corrected to display as expected.
- Updated datetimepicker from source.
- Moving to Debian Stretch

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
