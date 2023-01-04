[<img src="https://raw.githubusercontent.com/wiki/boxblinkracer/phpunuhi/assets/logo.png">]()

![Build Status](https://github.com/boxblinkracer/phpunuhi/actions/workflows/ci_pipe.yml/badge.svg) ![GitHub release (latest by date)](https://img.shields.io/github/v/release/boxblinkracer/phpunuhi) ![GitHub commits since latest release (by date)](https://img.shields.io/github/commits-since/phpunuhi/phpunuhi/latest) ![Build Status](https://github.com/boxblinkracer/phpunuhi/actions/workflows/nightly_build.yml/badge.svg)

Welcome to PHPUnuhi - An easy tool to work with translation files!

## Installation

PHPUnuhi is based on PHP. So you need to have PHP installed.

### PHAR File

PHPUnuhi is available as `phar` file.
Just download the ZIP file, extract it and you are ready to go.

```
curl -O https:// 
unzip -o phpunuhi.zip
rm -f phpunuhi.zip
```

### Composer

You can also use PHPUnuhi with Composer. Just install it with this script.

```
composer require boxblinkracer/phpunuhi
```

You can then run it with this command

```
php vendor/bin/phpunuhi ...
```

## Configuration

The whole configuration is done using a XML file.
You can create different translation suites with different files and settings.

Configure a **translation** node for every scope of translation.
This scope can then contain multiple files that need to match and only vary in their content.

```xml

<phpunuhi>
    <translations>
        <translation name="Storefront">
            <file locale="de">./snippets/storefront/de.json</file>
            <file locale="en">./snippets/storefront/en.json</file>
        </translation>
        <translation name="Admin">
            <file locale="de">./snippets/admin/de.json</file>
            <file locale="en">./snippets/admin/en.json</file>
        </translation>
    </translations>
</phpunuhi>
```

## Validate Command

You can then start the validation of your translation files by running this command.

```bash 
php vendor/bin/phpunuhi validate --configuration=./phpunuhi.xml
```

## Export Command

You can easily export your translations into a CSV file that can be passed on to an external translator or company.
Every row will contain the translation key, and every column will be a different translation.

```bash 
php vendor/bin/phpunuhi export --configuration=./phpunuhi.xml

# provide custom export folder
php vendor/bin/phpunuhi export --configuration=./phpunuhi.xml --dir=.exports

# only export single suite
php vendor/bin/phpunuhi export --configuration=./phpunuhi.xml --suite="my suite"
```

## Import Command

You can easily import your translations from a CSV file.
This will automatically update the JSON files that have been assigned to the imported translation suite.

```bash 
php vendor/bin/phpunuhi import --configuration=./phpunuhi.xml --suite=storefront --file=./storefront.csv

# intent of 4 spaces
php vendor/bin/phpunuhi import --configuration=./phpunuhi.xml --suite=storefront --file=./storefront.csv --intent=4

# sort JSON alphabetically
php vendor/bin/phpunuhi import --configuration=./phpunuhi.xml --suite=storefront --file=./storefront.csv --sort
```
