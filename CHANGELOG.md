# Acuparse
## AcuRite®‎ smartHUB and IP Camera Data Processing, Display, and Upload.

# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [[2.1.1]](http://www.acuparse.com/releases/v2-1-1/) - 2017-06-10
### Changed
- Updated wrong installer path in docs/INSTALL.md
- Minor updates to outage notifications. They should now send as expected.
- Minor changes to update checking.

### Removed
- References to the chat server.

### Added
- Added Event Scheduler check in cron since it's off by default. (A better way to cleanup database tables is on the roadmap.)
- Google Captcha added to contact form.
- MyAcuRite is terrible keeping time. They send the hour for the min and sec in their response. Modify the response to the HUB and send server time instead.

## [[2.1.0]](http://www.acuparse.com/releases/v2-1-0/) - 2017-03-14
### Added
- Initial open source release.