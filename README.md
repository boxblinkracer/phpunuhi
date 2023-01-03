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

