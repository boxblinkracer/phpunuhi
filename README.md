<p align="center">
   <img src="/.github/assets/home-logo.png">
</p>

![Build Status](https://github.com/boxblinkracer/phpunuhi/actions/workflows/ci_pipe.yml/badge.svg)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/boxblinkracer/phpunuhi)
![GitHub commits since latest release (by date)](https://img.shields.io/github/commits-since/boxblinkracer/phpunuhi/latest)
![Build Status](https://github.com/boxblinkracer/phpunuhi/actions/workflows/nightly_build.yml/badge.svg)

Welcome to PHPUnuhi - The easy framework to validate and manage translation files!

Unuhi? This is Hawaiian for "translate" or "translation".
Now that you know this, let's get started!

## Basic Concept

This is a framework that helps you to **validate and maintain translation files**.
At the moment it only supports JSON based files.

Although it's not dependent on a specific platform, you can use it perfectly with Shopware 6 and other platforms.

For example, Shopware 6 has snippets based on JSON files.
If you develop plugins for this platform, you can build translation sets in PHPUnuhi that contain all your files for the individual languages, like EN, DE, NL, and whatever you support.
PHPUnuhi helps you to make sure you didn't forget any translations, screwed up structures across your language files and even
helps you to export and import your translations.

One of the benefits is, that this framework does not require anything else than your translation files.
This makes it a perfect fit for your CI/CD pipelines.


<p align="center">
   <img src="/.github/assets/works-with.jpg">
</p>


> Missing your platform or file format? Feel free to contribute :)

## Installation

### Composer

You can also use PHPUnuhi with Composer. Just install it with this script.

```
composer require boxblinkracer/phpunuhi
```

You can then run it with this command, once you have a configuration.

```
php vendor/bin/phpunuhi validate
```

## Configuration

The whole configuration is done using a XML file.
You can create different translation sets with different files and settings.

Configure a **translation set** for every bundle (scope) of your translations.
Such a set can then contain multiple files with your individual language and locale values.

Just create a new **phpunuhi.xml** file (or rename it to something else).

```xml

<phpunuhi>
    <translations>

        <set name="Storefront">
            <file locale="de">./snippets/storefront/de.json</file>
            <file locale="en">./snippets/storefront/en.json</file>
        </set>

        <set name="Admin" format="json">
            <file locale="de">./snippets/admin/de.json</file>
            <file locale="en">./snippets/admin/en.json</file>
        </set>

    </translations>
</phpunuhi>
```

Every set can have its own storage format (default is JSON).
Every translation file can have its own locale (which is used in a few spots - experimental at the moment).

## Validate Command

You can then start the validation of your translation files by running this command.

```bash 
# loads configuration phpunuhi.xml as default
php vendor/bin/phpunuhi validate 

# provide custom configuration
php vendor/bin/phpunuhi validate --configuration=./translations.xml
```

### Validations

#### 1. Invalid structure

The command will check if all files of a translation set have the **same structure**.
If not, you might have forgotten something ;)

<p align="center">
   <img src="/.github/assets/validation-structure.png">
</p>

#### 2. Missing translations

As soon as an **empty string** is found, the validation process will notify you about it.
This helps against forgetting certain translations in any of your files.

<p align="center">
   <img src="/.github/assets/validation-empty.png">
</p>

## Export Command

You can export your translations **into a CSV file** or other supported exchange formats.
These files can then be passed on to an external translator or company.
Every row will contain the translation key, and every column in that row will be a different translation (in case of CSV files).

```bash 
# default export in format CSV
php vendor/bin/phpunuhi export 

# default export in other format
php vendor/bin/phpunuhi export ... --format=html

# provide custom export folder
php vendor/bin/phpunuhi export ... --dir=./exports

# only export single set
php vendor/bin/phpunuhi export ... --set="my set"

# set custom delimiter for CSV export
php vendor/bin/phpunuhi export ... --csv-delimiter=";"
```

<p align="center">
   <img src="/.github/assets/csv.png">
</p>

## Import Command

You can import your translations **from a CSV file** or other supported exchange formats.
This will automatically update the JSON files that have been assigned to the imported translation set.

> It's recommended to use version control (GIT, ...) to verify the changes before approving them.

```bash 
# import from default format CSV
php vendor/bin/phpunuhi import --set=storefront --file=storefront.csv

# import with other exchange format
php vendor/bin/phpunuhi import ... --format=html

# intent of 4 spaces in saved JSON
php vendor/bin/phpunuhi import ... --json-intent=4

# sort JSON based files alphabetically
php vendor/bin/phpunuhi import ... --json-sort

# import CSV with custom delimiter
php vendor/bin/phpunuhi import ... --csv-delimiter=";"
```

## Advanced

### Storage Formats

Storage formats define how your translations are stored.
Every format has its own loading and saving implementation.

The following formats are currently supported.

#### JSON

The JSON format means that your files are stored in separate JSON files.
Every locale or language has its own JSON file.
The JSON structure across all your related files of a translation set should match.

### Exchange Formats

Exchange formats define how you export and import translation data.
The main purpose is to be able to send it out to a translating company or just someone als,
and be able to import it back into your system afterwards.

The following formats are currently supported.

#### CSV

The CSV format is a well known and solid format for interoperability.
You can open CSV files with Microsoft Excel, Apple Numbers as well as simple text editors or more.
The only downside with Excel and Numbers is, that they might force you to save the updated file in their own formats (just pay attention to this).

The benefit is that you can simply open all translation in an easy spreadsheet way.
Every row is the translation key, and every locale and language has its won column in that row.

<p align="center">
   <img src="/.github/assets/csv.png">
</p>

#### HTML

**IN PROGRESS**
