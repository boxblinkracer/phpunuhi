<p align="center">
   <img src="/.github/assets/logo.jpg">
</p>

![Build Status](https://github.com/boxblinkracer/phpunuhi/actions/workflows/ci_pipe.yml/badge.svg)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/boxblinkracer/phpunuhi)
![GitHub commits since latest release (by date)](https://img.shields.io/github/commits-since/boxblinkracer/phpunuhi/latest)
![Packagist Downloads](https://img.shields.io/packagist/dt/boxblinkracer/phpunuhi?color=green&logo=packagist)

Welcome to PHPUnuhi - The easy composable framework to validate and manage translations!

Unuhi? This is Hawaiian for "translate" or "translation".

Why did I create it? Just curiosity and because I needed something like this :)
The framework is free, there's no guarantee or claim to anything.

**WHAT IT IS, AND WHAT IT'S NOT!!**

This is a dev-tool. It can be used improve pipelines, workflows and QA processes.
It can also be used to get some quick start in adding languages or new sentences.
But it is NO replacement for services or people that/who translate in a professional way with context and knowledge about the software.
Please keep that in mind to avoid misinterpreting the goal of this framework.

Now that you know this, let's get started!

<!-- TOC -->

* [1. Basic Concept](#1-basic-concept)
* [2. Installation](#2-installation)
* [3. Configuration](#3-configuration)
* [4. Commands](#4-commands)
    * [4.1 Validate Command](#41-validate-command)
    * [4.2 Fix Structure Command](#42-fix-structure-command)
    * [4.3 Export Command](#43-export-command)
    * [4.4 Import Command](#44-import-command)
    * [4.5 Status Command](#45-status-command)
    * [4.6 Translate Command](#46-translate-command)
    * [4.7 List Translations Command](#47-list-translations-command)
* [5. Use Cases](#5-use-cases)
    * [5.1 Validation in CI pipeline](#51-validation-in-ci-pipeline)
    * [5.2 Working with external translation agencies](#52-working-with-external-translation-agencies)
    * [5.3 Live WebEdit with HTML](#53-live-webedit-with-html)
    * [5.4 Automatic Translation with Google, DeepL, ...](#54-automatic-translation-with-google-deepl-)
* [6. Warning](#6-warning)
* [7. Appendix](#7-appendix)
    * [7.1 Storage Formats](#71-storage-formats)
        * [7.1.1 JSON](#711-json)
        * [7.1.2 INI](#712-ini)
        * [7.1.3 PHP](#713-php)
        * [7.1.4 Shopware 6](#714-shopware-6)
        * [7.1.5 YAML](#715-yaml)
    * [7.2 Filters](#72-filters)
    * [7.3 Groups](#73-groups)
    * [7.4 Case Styles](#74-case-styles)
    * [7.5 Rules](#75-rules)
        * [7.5.1 Nesting Depth](#751-nesting-depth)
        * [7.5.2 Key Length](#752-key-length)
        * [7.5.3 Disallowed Texts](#753-disallowed-texts)
        * [7.5.4 Duplicate Content](#754-duplicate-content)
    * [7.6 PHP ENV Variables](#76-php-env-variables)
    * [7.7 Exchange Formats](#77-exchange-formats)
        * [7.7.1 CSV](#771-csv)
        * [7.7.2 HTML / WebEdit](#772-html--webedit)
    * [7.8 Translator Services](#78-translator-services)
        * [7.8.1 DeepL](#781-deepl)
        * [7.8.2 Google Cloud Translate](#782-google-cloud-translate)
        * [7.8.3 Google Web Translate](#783-google-web-translate)
        * [7.8.4 OpenAI GPT Translate](#784-openai-gpt-translate)
    * [7.9 Validation Reports](#79-validation-reports)
        * [7.9.1 JUnit Report](#791-junit-report)

<!-- TOC -->

## 1. Basic Concept

This is a framework that helps you to **validate and maintain translations**.

Although it's not dependent on a specific platform, you can use it perfectly with Shopware 6 and other platforms.

For example, Shopware 6 has snippets based on JSON files.
If you develop plugins for this platform, you can build translation sets in PHPUnuhi that contain all your files for the individual languages, like EN, DE, NL, and whatever you support.
PHPUnuhi helps you to make sure you didn't forget any translations, screwed up structures across your language files and even
helps you to export and import or translate your entries.

One of the benefits of this framework is the approach of decoupled **Storage formats**, **Exchange formats** and **Translation services**.
You can combine any storage format (JSON, INI, DB, ...) with any exchange format for import + exports (CSV, HTML, ...) or use any of the provided
translation services (Google, DeepL, OpenAI). This makes PHPUnuhi a great **composable framework for translations**.

```mermaid
  graph TD;
      Storage-Format --> Exchange-Format;
      Storage-Format --> Translation-Service;
      Exchange-Format --> Storage-Format;
      Translation-Service --> Storage-Format;
```

**Key Benefits**

* Validates translations against structure, missing values, and consistency based on CamelCase, KebabCase and more.
* Platform independent and composable framework with different components.
* Exchange formats such as CSV and HTML
* Live WebEdit with HTML exchange format
* Automatic translation using OpenAI (experimental), DeepL, Google and more

<p align="center">
   <img src="/.github/assets/supported-systems.jpg">
</p>


> Missing your platform or file format? Feel free to contribute :)

## 2. Installation

You can use PHPUnuhi with Composer. Just install it with this script.

```
composer require boxblinkracer/phpunuhi
```

You can then run it with this command, once you have a configuration.

```
php vendor/bin/phpunuhi validate
```

## 3. Configuration

The whole configuration is done using XML.

You can create different **Translation-Sets** in a configuration.

A single **Translation-Set** contains one or more **locales**.

A locale is usually defined by a **single file** that contains the actual **translations** in this locale.
But depending on the used storage format, it could also automatically search something in the database, etc.

This means, a single **Translation-Set** consists of **multiple locales** that should all match in their structure,
but have different values for their translations.

How you define such a Translation-Set is totally up to you.

Let's create a new **phpunuhi.xml** file (or rename it to something else).

```xml

<phpunuhi
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="./vendor/boxblinkracer/phpunuhi/config.xsd"
>
    <translations>

        <set name="Storefront">
            <locales>
                <locale name="de">./snippets/storefront/de.json</locale>
                <locale name="en">./snippets/storefront/en.json</locale>
            </locales>
        </set>

    </translations>
</phpunuhi>
```

This was a simple configuration, but you can also do way more.
Look at this one:

```xml

<phpunuhi
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="./vendor/boxblinkracer/phpunuhi/config.xsd"
>
    <translations>

        <set name="Storefront JSON">
            <format>
                <json indent="4" sort="true"/>
            </format>
            <locales>
                <locale name="de">./snippets/de.json</locale>
                <locale name="en">./snippets/en.json</locale>
            </locales>
        </set>

        <set name="Products">
            <format>
                <shopware6 entity="product"/>
            </format>
            <filter>
                <exclude>
                    <key>meta_*</key>
                </exclude>
            </filter>
            <locales>
                <locale name="de-DE"/>
                <locale name="en-GB"/>
            </locales>
        </set>

    </translations>
</phpunuhi>
```

Every set can have its own **storage format** (default is JSON).

Some storage formats, such as JSON, have individual attributes that can be defined in the Translation-Set.

> Read more about optional attributes in the storage-format section in the appendix below.

## 4. Commands

### 4.1 Validate Command

Start the validation of your translations by running this command:

```bash 
# loads configuration phpunuhi.xml as default
php vendor/bin/phpunuhi validate 

# provide custom configuration
php vendor/bin/phpunuhi validate --configuration=./translations.xml

# generate a junit report in a custom folder
php vendor/bin/phpunuhi validate --report-format=junit --report-output=.reports/junit.xml
```

**Invalid structure**

The command will check if all locales of a Translation-Set have the **same structure**.
If not, you might have forgotten something ;)

<p align="center">
   <img src="/.github/assets/validation-structure.png">
</p>

**Missing translations**

If missing translations (**empty values**) are found, the validation process will fail.
This helps against forgetting certain translations in any of your locales.

<p align="center">
   <img src="/.github/assets/validation-empty.png">
</p>

**Invalid Case-Styles**

If you have provided a list of allowed case-styles, the validation command will automatically test
if all your translation keys, match your provided case styles.

**Maximum nesting levels**

If you have provided a rule for nestingDepth, then the validator will also verify the nesting level
on storages that support nesting (JSON, PHP, ...)

### 4.2 Fix Structure Command

If your storage is not matching, you can easily use the fixing command to make sure they are in sync.
Please note, that this will only create empty translations so that the structure is the same.

The final translations are not 100% valid in the end...only existing!

```bash 
# Fixes all sets of the configuration
php vendor/bin/phpunuhi fix:structure

# Fixes only a provided set of your configuration
php vendor/bin/phpunuhi fix:structure --set="storefront"
```

<p align="center">
   <img src="/.github/assets/fix.png">
</p>

### 4.3 Export Command

You can export your translations **into a CSV file**, a HTML WebEdit spreadsheet, or other supported exchange formats.
These files can then be passed on to an external translator or company.

Every row will contain the translation key, and every column in that row will be a different translation (in case of CSV files).

```bash 
# default export in default exchange format CSV
php vendor/bin/phpunuhi export 

# default export in specific exchange format
php vendor/bin/phpunuhi export ... --format=html

# provide custom export folder
php vendor/bin/phpunuhi export ... --dir=./exports

# only export single set "storefront"
php vendor/bin/phpunuhi export ... --set="storefront"

# only export translation entries that are not yet completely translated in all locales.
php vendor/bin/phpunuhi export ... --empty
```

> For more options and arguments of the formats please see the appendix below!

<p align="center">
   <img src="/.github/assets/csv.png">
</p>

### 4.4 Import Command

You can import your translations **from a CSV file** or other supported exchange formats.
This will automatically update the storage (JSON, ...) that has been assigned to the imported translation set.

> It's recommended to use version control to verify changes, before approving them.

```bash 
# import from default format CSV
php vendor/bin/phpunuhi import --set=storefront --file=storefront.csv

# import with other exchange format
php vendor/bin/phpunuhi import ... --format=html
```

### 4.5 Status Command

Use this command to get the coverage status of your translations.

```bash 
php vendor/bin/phpunuhi status 
```

<p align="center">
   <img src="/.github/assets/status.png">
</p>

### 4.6 Translate Command

PHPUnuhi includes the option to use external services to automatically translate missing values for you.

The **translate** command will search for empty values in your translations.

If an empty translation is found, it will request a translation from your external service.
The text that will be translated in this request, is the translation from another language.

For instance, if your "English" translation is empty, PHPUnuhi will find your "German" text and send it to the external service.
The English result is then saved in your storage.

```bash 
# translate using GoogleWeb
php vendor/bin/phpunuhi translate --service=googleweb

# translate using DeepL
php vendor/bin/phpunuhi translate --service=deepl --deepl-key=xyz

# translate only the set "storefront"
php vendor/bin/phpunuhi translate ... --set="storefront"

# force the translation of "en". This will translate everything, not only empty values
php vendor/bin/phpunuhi translate ...  --force=en

# define what locale will be used as source for the translation. 
# If not defined, any locale with an existing value will be used.
php vendor/bin/phpunuhi translate ...  --source=en
```

> For more options and arguments of the formats please see the appendix below!

<p align="center">
   <img src="/.github/assets/translate.png">
</p>

### 4.7 List Translations Command

This command allows you to output all available translation keys in your Translation-Sets.
Use this to debug and analyzse your translations.

```bash 
php vendor/bin/phpunuhi list:translations 
```

## 5. Use Cases

Here are a few use cases and ideas to get you started.

### 5.1 Validation in CI pipeline

One of the typical things you want to make sure is, that your plugin/software doesn't miss any
required translations.

This can be done directly within your CI pipeline. Just install your dependencies and run the validation command.
The exit value of this command will automatically stop your pipeline if an error is detected.

### 5.2 Working with external translation agencies

External translation agencies often require CSV exports.
You can easily generate and export a CSV file for your partner agencies.

Once they have adjusted their translation, they can send you the file back and you simply
import it again with the import command.

### 5.3 Live WebEdit with HTML

If you have a test or staging system, you can even go one step further.
Just imagine setting up a cronjob that runs after a deployment, or as scheduled job.
This cronjob could trigger the HTML export of PHPUnuhi with an output directory to a folder that is available within your DocRoot.
That HTML file might then be exposed with something like this **https://stage.my-shop.com/snippets**.

Everyone who wants to either see all translations, or even modify them, can easily do this in their browser.
And because you use a cronjob to generate the HTML, it's always automatically updated.

### 5.4 Automatic Translation with Google, DeepL, ...

You can automatically translate your missing (or all) translations by using an external service.
This can be GoogleWeb, GoogleCloud or even DeepL.
Provide your API key (if required for service) and see the magic happening.

> A double check and approval in your version control would still be a good thing.

## 6. Warning

Please keep these things in mind:

* Translations services are not always correct! Please always verify automatically translated texts.
* If you are using a storage format that is directly connected to a database, make sure to create a backup before importing translations!

## 7. Appendix

### 7.1 Storage Formats

Storage formats define how your translations are stored.
Every format has its own loading and saving implementation.

The following formats are currently supported.

#### 7.1.1 JSON

| Format Attributes | Default | Description                             | 
|-------------------|---------|-----------------------------------------|
| indent            | 2       | Set a custom JSON indent for the spaces |
| sort              | false   | Turn on or off the alphabetical sorting |

The JSON format means that your files are stored in separate JSON files.
Every locale has its own JSON file.
The JSON structure across all files of a set should match.

```xml

<set name="sample">
    <format>
        <json indent="4" sort="true"/>
    </format>
    <locales>
        <locale name="de">./snippets/de.json</locale>
        <locale name="en">./snippets/en.json</locale>
    </locales>
</set>
```

#### 7.1.2 INI

| Format Attributes | Default | Description                             | 
|-------------------|---------|-----------------------------------------|
| sort              | false   | Turn on or off the alphabetical sorting |

| Locale Attribute | Default | Description                                  | 
|------------------|---------|----------------------------------------------|
| iniSection       |         | Section name of the locale [iniSection="de"] |

The INI format means that your files are stored in separate INI files.
Every locale has its own INI file.
The INI structure across all files of a set should match.

It's also possible to have all translations in a single INI file.
For this, you might want to use the **iniSection** feature and just assign the same INI file to all locales, but with different sections.

```xml

<set name="sample">
    <format>
        <ini sort="true"/>
    </format>
    <locales>
        <locale name="de">./snippets/de.ini</locale>
        <locale name="en">./snippets/en.ini</locale>
    </locales>
</set>

<set name="sample">
<format>
    <ini indent="4" sort="true"/>
</format>
<locales>
    <locale name="de" iniSection="de-DE">./snippets/snippets.ini</locale>
    <locale name="en" iniSection="en-GB">./snippets/snippets.ini</locale>
</locales>
</set>
```

#### 7.1.3 PHP

| Format Attributes | Default | Description                             | 
|-------------------|---------|-----------------------------------------|
| sort              | false   | Turn on or off the alphabetical sorting |

Some platforms have translations based on PHP arrays.
This means that these files build a key-value array of translations which is then simply returned.

Consuming services can then simply "require" that file and therefore load the translation array.

This storage type makes sure to read and also write PHP files that return a single array object.

```xml

<set name="sample">
    <format>
        <php sort="true"/>
    </format>
    <locales>
        <locale name="de">./snippets/de.php</locale>
        <locale name="en">./snippets/en.php</locale>
    </locales>
</set>
```

#### 7.1.4 Shopware 6

| Format Attributes | Default | Description                            | 
|-------------------|---------|----------------------------------------|
| entity            |         | The entity your Translation-Set covers |

The Shopware 6 format allows you to use PHPUnuhi directly on the database and the Shopware entities.

What do we mean with entities? These are real objects of the platform, stored within the database.
This means **snippets**, **products**, **salutations**, **shipping methods** and more. Basically, everything that has a **_translation** table in the database.

Just imagine running the **status command** and see a translation coverage of all your products in your shop. Nice, isn't it? Or let DeepL translate your data automatically?!

To access the database of Shopware, you can either make sure the ENV variables for the connection are correctly set up,
or provide your custom credentials in the **php** section of the configuration XML.

Please keep in mind, snippets are handled in a different way in the database.
To make life easier for you, we've added a fake entity name **snippet** that automatically connects to the snippet table instead of an entity translation table.

```xml

<phpunuhi>
    <php>
        <env name="DB_HOST" value="127.0.0.1"/>
        <env name="DB_PORT" value="3306"/>
        <env name="DB_USER" value=""/>
        <env name="DB_PASSWD" value=""/>
        <env name="DB_DBNAME" value="shopware"/>
    </php>

    <translations>

        <set name="Products">
            <format>
                <shopware6 entity="product"/>
            </format>
            <locales>
                <locale name="de-DE"/>
                <locale name="en-GB"/>
            </locales>
        </set>

        <set name="Snippets">
            <format>
                <shopware6 entity="snippet"/>
            </format>
            <locales>
                <locale name="de-DE"/>
                <locale name="en-GB"/>
            </locales>
        </set>

    </translations>
</phpunuhi>
```

#### 7.1.5 YAML

| Format Attributes | Default | Description                             | 
|-------------------|---------|-----------------------------------------|
| indent            | 2       | Set a custom YAML indent for the spaces |
| sort              | false   | Turn on or off the alphabetical sorting |

The YAML format means that your files are stored in separate YAML files.
Every locale has its own YAML file.
The YAML structure across all files of a set should match.

```xml

<set name="sample">
    <format>
        <yaml indent="4" sort="true"/>
    </format>
    <locales>
        <locale name="de">./snippets/de.yaml</locale>
        <locale name="en">./snippets/en.yaml</locale>
    </locales>
</set>
```

### 7.2 Filters

It's possible to use filters to modify the list of covered translation keys.

You can either use a **include** or **exclude** list.
Include means, only these fields will be loaded, and exclude means, everything except those fields.
A combination is not possible.

You can also use **placeholders** using the * character.

```xml

<set>
    <filter>
        <include>
            <key>name</key>
        </include>
        <exclude>
            <key>custom_fields</key>
            <key>meta_*</key>
        </exclude>
    </filter>
</set>
```

### 7.3 Groups

Some storage formats automatically bundle translations into groups.
This means, that more translations belong to one "thing".
That thing depends on the type of storage format.

For instance, in Shopware 6, a group is a "entity".
So for a Translation-Set on "products", 1 group stands for a specific product, and has multiple translations for the different product properties.

If a group is detected, the exchange formats, should handle these in a correct way.
A CSV format, has a separate column for groups, and the import should also work correctly.

The HTML format on the other hand, shows a matching style in the table, so you know that the
translations all belong to this group.

### 7.4 Case Styles

To keep consistency across all your translation keys, it's possible to set a list of allowed case styles.
The **validate** command, will automatically test, if all your translation keys match at least one of the provided styles.

If no style is provided, then case-style tests are skipped.

The following styles are possible:

* camel
* pascal
* kebab
* lower
* upper
* snake
* start
* number

```xml

<set>
    <styles>
        <style>pascal</style>
        <style>kebab</style>
    </styles>
</set>
```

In addition to global case-styles, you can also set specific styles on **specific levels** if you have a nested storage such as JSON or PHP.

You can even mix it with styles that do not have a level.
In that case, styles without levels, are globally checked for every level that does not already have a specific style for its level.

Here is an example that would be "valid" for this key: **global.businessEvents.mollie_checkout_order_success**.
Pascal case is only checked on level 1, and not on 0 and 2.

```xml

<set>
    <styles>
        <style level="0">snake</style>
        <style level="2">snake</style>
        <style>camel</style>
    </styles>
</set>
```

### 7.5 Rules

You can add additional rules to extend the validation of your Translation-Sets.
Please see the list below for all supported rules.

```xml

<set>
    <rules>
        <duplicateContent>false</duplicateContent>
        <nestingDepth>3</nestingDepth>
        ...
    </rules>
</set>
```

#### 7.5.1 Nesting Depth

The nesting-depth rule allows you to throw an error once the maximum depth is reached within a nested storage type.
This helps you to keep your depth in control.

```xml

<nestingDepth>3</nestingDepth>
```

#### 7.5.2 Key Length

The key-length rule allows you to throw an error once the maximum length of a key is reached

```xml

<keyLength>20</keyLength>
```

#### 7.5.3 Disallowed Texts

Provide a list of texts that must not occur in any of your translations.
You can use this for anything you want, like swearing words, political incorrect phrases and more.

```xml

<disallowedTexts>
    <text>wordA</text>
    <text>wordB-phrase</text>
</disallowedTexts>
```

#### 7.5.4 Duplicate Content

Sometimes you want to keep translations clean and reduced by avoiding duplicate values within a locale.
Just imagine the simple translation value "Save" occurring multiple times in a single locale?
Wouldn't it be better to just have it in 1 single translation entry in your file?

In this case you can use this rule.
Once set to **false**, the validator will automatically warn you, if you have a translation value configured multiple times within a single locale.

```xml

<duplicateContent>false</duplicateContent>
```

### 7.6 PHP ENV Variables

The XML configuration allows you to create custom ENV variables.
Depending on the components you use in PHPUnuhi, some require specific ENV variables, such as the Shopware 6 database connection.
These can either be set by exporting the ENV variable on your server, or by simply providing them in the XML configuration.

```xml

<phpunuhi>
    <php>
        <env name="DB_HOST" value="127.0.0.1"/>
        <env name="DB_PORT" value="3306"/>
        <env name="DB_USER" value=""/>
        <env name="DB_PASSWD" value=""/>
        <env name="DB_DBNAME" value="shopware"/>
    </php>
</phpunuhi>
```

### 7.7 Exchange Formats

Exchange formats define how you export and import translation data.
The main purpose is to send it out to a translation company or just someone else,
and be able to import it back into your system again.

The following formats are currently supported.

#### 7.7.1 CSV

* Format: "csv"

| Command | Argument        | Description                                             |
|---------|-----------------|---------------------------------------------------------|
| export  | --csv-delimiter | Custom delimiter for the CSV file [--csv-delimiter=";"] |
| import  | --csv-delimiter | Custom delimiter for the CSV file [--csv-delimiter=";"] |

The CSV format is a well known and solid format for interoperability.
You can open CSV files with Microsoft Excel, Apple Numbers as well as simple text editors or more.
The only downside with Excel and Numbers is, that they might force you to save the updated file in their own formats (just pay attention to this).

The benefit is that you can simply open all translation in a spreadsheet.
Every translation key has its own row, and all locale-values have their own column in that row.

<p align="center">
   <img src="/.github/assets/csv.png">
</p>

#### 7.7.2 HTML / WebEdit

* Format: "html"

The HTML export helps you to export all translations into a single HTML file.
You can then open this file in your browser and immediately start to edit your translations.

Once finished, just click on "save translations". This will download a **html.txt** file that
you can import again into your system with the format **html** in PHPUnuhi.

<p align="center">
   <img src="/.github/assets/html.png">
</p>

### 7.8 Translator Services

Translators are supported (external) services that automatically translate empty values for you.
These services usually require an API key that needs to be provided for PHPUnuhi.

#### 7.8.1 DeepL

* Service: "deepl"

| Command   | Argument       | Description                                           |
|-----------|----------------|-------------------------------------------------------|
| translate | --deepl-key    | Your DeepL API Key. [--deepl-key=xyz]                 |
| translate | --deepl-formal | just provide this flag if you want a formal language. |

DeepL is one of the leading translation services.
If you have an API Key from DeepL, you can send your translation requests directly to their API.

DeepL allows you to either translate to a formal or informal language.
This option is only available for some target languages, just like "German" ("du" vs. "Sie").
You can request a formal language by simply applying the argument "--deepl-formal" to the translate command.

#### 7.8.2 Google Cloud Translate

* Service: "googlecloud"

| Command   | Argument     | Description               |
|-----------|--------------|---------------------------|
| translate | --google-key | Your Google Cloud API Key |

Google Cloud Translation allows you to use the AI services of Google.
If you have an API Key, you can easily provide it with the corresponding argument when running the translation command.

#### 7.8.3 Google Web Translate

* Service: "googleweb"

This service just consumes the web page of Google.
So it's the same thing that you would do when just visiting the Google search page.
Because of this, it can happen, that a massive number of requests might lead to a temporary blocking of your IP address.

This is more meant for educational purposes.
Although it works, you should consider getting a real Google API key for commercial and serious usage of their services.

#### 7.8.4 OpenAI GPT Translate

* Service: "openai"

| Command   | Argument     | Description     |
|-----------|--------------|-----------------|
| translate | --openai-key | Your OpenAI Key |

This type of translator uses the latest OpenAI technology to translate your texts.
Let AI help you to translate your texts.

To use it, you need to create an API key at www.openai.com and provide it as argument.
That's it!

This was indeed a last minute addon, but it works quite good.
If you have any tweaks, feel free to contribute :)

### 7.9 Validation Reports

It's possible to generate reports after running a **validation** command. This helps you to use
the results in different systems and platforms.

The validation command has 2 arguments **--report-format=xyz** and **--report-output=abc** to provide
a certain format and a custom output filename. Use this to generate reports based on validation results.

#### 7.9.1 JUnit Report

You can generate a JUnit XML report by providing the following arguments when starting the validation.

* Report Format: "junit"
