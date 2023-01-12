# Changes in PHPUnuhi

All notable changes of SVRUnit releases are documented in this file
using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [unreleased]

### Added

- OpenAI Translator now throws exception if no API key was set
- `config.xsd` for autocompletion in IDEs and editors. Use either from the composer dependency `vendor/boxblinkracer/phpunuhi/config.xsd` or online `https://raw.githubusercontent.com/boxblinkracer/phpunuhi/main/config.xsd`
- Added new PHP Storage for Array based translations
- Add coverage of all sets (total) to status command.

### Changed

- fix command is now fix:structure command
- Update TranslatorInterface for a better dynamic registration approach. Also options can now be dynamically registered for different CLI commands.

### Fixed

- Include referenced translation file from configuration in error message when translation file is not found

## [1.0.1]

### Fixed

- Just PHPStan fixes

## [1.0.0]

### Added

- Initial version

