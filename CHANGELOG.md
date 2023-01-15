# Changes in PHPUnuhi

All notable changes of SVRUnit releases are documented in this file
using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [unreleased]

### Added

- Add new **list:translations** command to see all IDs of Translation-Sets
- Add new **Filters** with allow and exclude lists. This also supports wildards with '*'
- Add new Shopware 6 Storage format with full support of entities.
- Add new **groups** of translations. This is required for a 3rd dimension. E.g. translations of products where every product has multiple rows with properties and different languages (columns).

### Changed

- Changed the technical usage of the translation **key** to an **ID**. The ID is the unique identifier within a locale, and the key the plain name of the translation which can exist multiple times in a locale.

## [1.1.0]

### Added

- Add new `config.xsd` for configuration file to use autocompletion in IDEs and editors. Use either from the composer dependency `vendor/boxblinkracer/phpunuhi/config.xsd` or online `https://raw.githubusercontent.com/boxblinkracer/phpunuhi/main/config.xsd`
- Add new "PHP" Storage for array based translations
- Add coverage result of all sets (total) to status command.
- OpenAI Translator and other translators now throw exceptions if no API key was set

### Changed

- Rename command "fix" to "fix:structure"
- Update TranslatorInterface for a better dynamic registration approach. Also options can now be dynamically registered for different CLI commands.
- Import and Export interfaces for Exchange formats are now combined as ExchangeFormat. The interface is now also designed to allow dynamic CLI commands and dynamic registration + lookup.

### Fixed

- Include referenced translation file from configuration in error message when translation file is not found

## [1.0.1]

### Fixed

- Just PHPStan fixes

## [1.0.0]

### Added

- Initial version

