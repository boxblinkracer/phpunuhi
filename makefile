# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


# -------------------------------------------------------
PHP_MIN_VERSION := 7.2
PHPUNIT_MIN_COVERAGE := 43
# -------------------------------------------------------


#------------------------------------------------------------------------------------------------

help:
	@echo ""
	@echo "PROJECT COMMANDS"
	@echo "--------------------------------------------------------------------------------------------"
	@printf "\033[33mInstallation:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##1 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##1 "}; {printf "\033[33m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "--------------------------------------------------------------------------------------------"
	@printf "\033[36mDevelopment:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##2 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##2 "}; {printf "\033[36m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "--------------------------------------------------------------------------------------------"
	@printf "\033[32mTests:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##3 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##3 "}; {printf "\033[32m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "--------------------------------------------------------------------------------------------"
	@printf "\033[35mDevOps:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##4 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##4 "}; {printf "\033[35m  - %-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

install: ##1 Installs all prod dependencies
	composer install --no-dev

dev: ##1 Installs all dev dependencies
	composer install --ignore-platform-req=ext-intl

clean: ##1 Clears all dependencies
	rm -rf vendor/*
	rm -rf .reports

build: ##1 Builds PHPUnuhi and creates phpunuhi.phar
	@make install -B
	@echo "===================================================================="
	@echo "verifying if phar files can be created....phar.readonly has to be OFF"
	@php -i | grep phar.readonly
	@php -i | grep "Loaded Configuration"
	@cd scripts && php build.php

#------------------------------------------------------------------------------------------------

pr: ##2 Runs and prepares everything for a pull request
	php vendor/bin/rector process
	PHP_CS_FIXER_IGNORE_ENV=1 php ./vendor/bin/php-cs-fixer fix --config=./.php_cs.php
	@make phpcheck -B
	@make phpmin -B
	@make stan -B
	@make phpmnd -B
	@make phpunit -B
	@make infection -B

phpunit-debug: ##2 Debug the unit test with group "debug"
	XDEBUG_MODE=coverage php ./vendor/bin/phpunit -v --group debug

#------------------------------------------------------------------------------------------------

phpcheck: ##3 Starts the PHP syntax checks
	@find ./src -name '*.php' | xargs -n 1 -P4 php -l

phpmin: ##3 Starts the PHP compatibility checks
	@php vendor/bin/phpcs -p --standard=PHPCompatibility --extensions=php --runtime-set testVersion $(PHP_MIN_VERSION) ./src

csfix: ##3 Starts the PHP CS Fixer
	PHP_CS_FIXER_IGNORE_ENV=1 php ./vendor/bin/php-cs-fixer fix --config=./.php_cs.php --dry-run

stan: ##3 Starts the PHPStan Analyser
	php ./vendor/bin/phpstan analyse --memory-limit 1G -c ./.phpstan.neon

phpmnd: ##3 Runs the checks for magic numbers
	php ./vendor/bin/phpmnd ./src

rector: ##3 Runs the Rector checks in dry run
	php vendor/bin/rector process --dry-run

phpunit: ##3 Runs all tests
	XDEBUG_MODE=coverage php ./vendor/bin/phpunit -v --coverage-html ./.reports/phpunit/coverage --coverage-clover ./.reports/phpunit/clover/index.xml
	php vendor/bin/coverage-check ./.reports/phpunit/clover/index.xml $(PHPUNIT_MIN_COVERAGE)

infection: ##3 Starts all Infection/Mutation tests
	rm -rf ./.reports/infection
	@XDEBUG_MODE=coverage php vendor/bin/infection --configuration=./infection.json --log-verbosity=all --debug

svrunit: ##3 Runs all SVRUnit tests
	php vendor/bin/svrunit test --configuration=./svrunit.xml --debug --report-junit --report-html

#------------------------------------------------------------------------------------------------

release: ##4 Create a ZIP file in the build folder
	cd build && zip phpunuhi.zip phpunuhi.phar
	cd build && rm -rf phpunuhi.phar
