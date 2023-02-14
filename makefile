# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

install: ## Installs all prod dependencies
	composer install --no-dev

dev: ## Installs all dev dependencies
	composer install --ignore-platform-req=ext-intl

clean: ## Clears all dependencies
	rm -rf vendor/*

#------------------------------------------------------------------------------------------------

phpcheck: ## Starts the PHP syntax checks
	@find ./src -name '*.php' | xargs -n 1 -P4 php -l

phpmin: ## Starts the PHP compatibility checks
	@php vendor/bin/phpcs -p --standard=PHPCompatibility --extensions=php --runtime-set testVersion 7.2 ./src

csfix: ## Starts the PHP CS Fixer
	PHP_CS_FIXER_IGNORE_ENV=1 php ./vendor/bin/php-cs-fixer fix --config=./.php_cs.php --dry-run

stan: ## Starts the PHPStan Analyser
	php ./vendor/bin/phpstan analyse --memory-limit 1G -c ./.phpstan.neon

phpunit: ## Runs all tests
	XDEBUG_MODE=coverage php ./vendor/bin/phpunit --configuration=./.phpunit.xml -v --coverage-html ./.reports/phpunit/coverage

phpinsights: ## Starts PHPInsights
	@php -d memory_limit=2000M vendor/bin/phpinsights --no-interaction

svrunit: ## Runs all SVRUnit tests
	php vendor/bin/svrunit test --configuration=./svrunit.xml --debug --report-junit --report-html

#------------------------------------------------------------------------------------------------

pr: ## Runs and prepares everything for a pull request
	PHP_CS_FIXER_IGNORE_ENV=1 php ./vendor/bin/php-cs-fixer fix --config=./.php_cs.php
	php vendor/bin/phpinsights analyse --fix --no-interaction --min-quality=0 --min-complexity=0 --min-architecture=0 --min-style=0
	@make phpcheck -B
	@make phpmin -B
	@make phpunit -B
	@make stan -B
	@make phpinsights -B

#------------------------------------------------------------------------------------------------

build: ## Builds PHPUnuhi and creates phpunuhi.phar
	@make install -B
	@echo "===================================================================="
	@echo "verifying if phar files can be created....phar.readonly has to be OFF"
	@php -i | grep phar.readonly
	@php -i | grep "Loaded Configuration"
	@cd scripts && php build.php

release: ## Create a ZIP file in the build folder
	cd build && zip phpunuhi.zip phpunuhi.phar
	cd build && rm -rf phpunuhi.phar
