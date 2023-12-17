# Changes in PHPUnuhi

All notable changes of PHPUnuhi releases are documented in this file
using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [1.14.0]

### Added

- Added new attribute **basePath** in `<locales>` tag. This allows you to use the placeholder **%base_path%** in
  filenames of your locales.
- Added option to import additional configuration files into the root configuration file using the `<import>` tag. This
  allows to have decentralized configuration files that can be imported into the main configuration file.

### Fixed

- Fix broken **indent** settings for JSON and YAML storages (thx @matthiashamacher)

## [1.13.0]

### Added

- Add storage format option **eol-last** to all file based formats. This will automatically add a new line at the end of
  the file. (thx @JoshuaBehrens)
- Add new **line number** data to errors and reports of file based storage formats. (thx @matthiashamacher)

### Fixed

- Fix broken **sort** attribute detection in storage format. Sometimes a FALSE got recognized as TRUE.

## [1.12.0]

### Added

- Added new **JSON** reporter that creates a JSON file with all validation errors.
- Add option to register **custom translator services* by using the TranslatorFactory **register** command.
- Add option to register **custom exchange formats* by using the ExchangeFactory **register** command.

### Changed

- Translator Services are now in charge of handling protection markers, because for some services the automatic
  encryption of markers would lead to problems.

### Fixed

- Fixed problem when using OpenAI to translate values in combination with protection markers. This should now work.

## [1.11.0]

### Added

- Add option to register **custom storages* by using the StorageFactory **register** command.
- Add first support for **PO** files. This covers the **msgid** and **msgstr** values.
- Add new **word** count statistic to the status command.
- Add better error output on OpenAI translation errors, like "Quota exceeded" and more.
- Add new **migration** command, to migrate from Storage A to Storage B.

### Changed

- Improved OpenAI creativity by using a new **temperature** value. This allows to control the creativity of the AI.
- Improved **StorageInterface* definition for more future proven implementations.

### Fixed

- Fixed problem with **[[punt]]** placeholder when using googleweb for translations. These placeholders are now
  correctly converted into "."

## [1.10.0]

### Added

- Add new placeholders **%locale%**, **%locale_lc%** and **%locale_uc%** in file name paths. This allows to reuse the
  name of the locale in multiple spots within the path.
- Added new **protect** node in sets. This helps to avoid that static placeholder strings like e.g. %firstname%, ... or
  even static terms get accidentally translated by translation services. (thx @hhoechtl for this idea)

## [1.9.0]

### Added

- Add brand new **YAML** storage format (thx @matthiashamacher)
- Add new argument **source=xxx** to translate command. This defines what locale should be used as base language for
  translations.
- Add new option **--empty** to export command. This only exports translation entries that are not yet 100% translated.

### Changed

- Improved error outputs when loading configuration files without existing translation-sets. There will now be a better
  error message for this.
- Add special keyword **dev** to composer to show this is a dev-tool and ask if --dev should be used to install it. (thx
  @xabbuh)

## [1.8.1]

### Fixed

- Fix bug where duplicateContent rule was automatically validated if an empty **<rule>** tag was existing without a
  duplicateContent rule.
- Fix broken HTML import due to wrong GroupId recognition

## [1.8.0]

### Added

- Add new **duplicateContent** rule to avoid the same translation values within a single locale.

### Changed

- Refactoring RuleValidators by moving them into a separate folder and scope

## [1.7.0]

### Added

- Add new **number** case-style validator.
- Add new **keyLength** rule. Validate a maximum length of your keys.
- Add new *disallowedTexts** rule. Provide a list of texts that must not appear in any of your translations.

### Fixed

- Fix problem with locale filenames that have whitespaces before or after the filename. Trim() has been added ;)

## [1.6.0]

### Added

- Add new **rules** to Translation-Sets. The first rule is **nestingDepth** that allows you to validate against a
  maximum number of levels within a nested storage.

### Fixed

- Fixed problem `/usr/bin/php: bad interpreter: No such file or directory` when running on a MAC.

### Changed

- The **fix:structure** command is now also called if no translations where created. This helps to re-write files and
  fix indention, sortings and more.

## [1.5.0]

### Added

- Add new **level** for case-style validations. This helps to set specific styles on some levels within a
  multi-hierarchy translation format, such as JSON or PHP.

### Changed

- Switched from **doctrine/dbal** to plain PDO to allow a wider range of integrations without a dependency to a specific
  dbal version.

### Fixed

- Fixed wrong header output of version number in CLI commands

## [1.4.0]

### Added

- Add new **case-styles** for translations. This allows you to validate against translation keys. Select from a wide
  range of styles like CamelCase, PascalCase, KebabCase and more.
- Add new **junit** reporter for the validation command. This creates a JUnit XML file that can be used in upcoming
  processes.

### Changed

- Improved **output of the validation** command. Errors are now shown in a more compact and meaningful way.
- The **translate** command now shows a warning if auto-translation is not possible because no existing translation has
  been found as base-value.

### Fixed

- Fixed wrong "created" count on **fix:structure**. It showed 0 created if the last translation-set didn't get any new
  translations.

## [1.3.0]

### Added

- Add support for **database snippets** in Shopware 6 storage using the entity name **snippet**.
- The Shopware 6 storage format does now support "fix:structure" for snippet entities. Missing snippets will now be
  automatically generated with empty values.
- Add warning output that the Shopware 6 storage format currently doesn't allow to insert new entity translation rows.

### Fixed

- Fixed problems with broken "Umlaute" in Shopware 6 entities storage
- Fix problem with wrong translation coverage. A set with 0/0 was displayed as 0% coverage, but it's actually 100%.
- Fix problem with "fix:structure" when creating new snippets for translations-sets with groups. It accidentally used
  the full ID instead of the correct key name for the "key" identifier.
- Fix crash when parsing invalid json files.

## [1.2.0]

### Added

- Add new **list:translations** command to see all IDs of Translation-Sets
- Add new **Filters** with allow and exclude lists. This also supports wildards with '*'
- Add new **groups** of translations. This is required for a 3rd dimension. E.g. translations of products where every
  product has multiple rows with properties and different languages (columns).
- Add option to inject **PHP** server variables that can be used in different areas.
- Add Dbal/Connection to be used in MySQL driven storage formats.
- Add new Shopware 6 Storage format with full support of entities.

### Changed

- Changed the technical usage of the translation **key** to an **ID**. The ID is the unique identifier within a locale,
  and the key the plain name of the translation which can exist multiple times in a locale.
- Removed **<file>** nodes in locale. Please use **<locale>** instead.
- **<locales>** is now required as wrapper for locales. This helps to make the configuration cleaner.
- Storage formats do now have to be inside a **<format**> tag instead of configuring it a attribute in a
  Translation-Set. Also the format is now a custom XML node to allow a better XSD experience.

### Fixed

- DeepL does not support **formality** options for all languages. This has been fixed by only applying the option for
  allowed languages.

## [1.1.0]

### Added

- Add new `config.xsd` for configuration file to use autocompletion in IDEs and editors. Use either from the composer
  dependency `vendor/boxblinkracer/phpunuhi/config.xsd` or
  online `https://raw.githubusercontent.com/boxblinkracer/phpunuhi/main/config.xsd`
- Add new "PHP" Storage for array based translations
- Add coverage result of all sets (total) to status command.
- OpenAI Translator and other translators now throw exceptions if no API key was set

### Changed

- Rename command "fix" to "fix:structure"
- Update TranslatorInterface for a better dynamic registration approach. Also options can now be dynamically registered
  for different CLI commands.
- Import and Export interfaces for Exchange formats are now combined as ExchangeFormat. The interface is now also
  designed to allow dynamic CLI commands and dynamic registration + lookup.

### Fixed

- Include referenced translation file from configuration in error message when translation file is not found

## [1.0.1]

### Fixed

- Just PHPStan fixes

## [1.0.0]

### Added

- Initial version

