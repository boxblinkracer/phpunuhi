.PHONY: help
.DEFAULT_GOAL := help


# -------------------------------------------------------
# minimum php version to use PHPUnuhi
PHP_MIN_VERSION := $(shell php -f php_min_version.php -- --echo)
# minimum code coverage for unit tests in PHPUnit
PHPUNIT_MIN_COVERAGE := 38
# This is the main score indicator across the whole source code.
MUTANTS_MIN_MSI := 90
# This is the score for code that is actually covered by tests. This shows you how effective the tests really are.
MUTANTS_MIN_COVERED_MSI := 90
# -------------------------------------------------------


#------------------------------------------------------------------------------------------------

help:
	@echo ""
	@echo ""
	@echo "PHPUNUHI PROJECT"
	@echo "--------------------------------------------------------------------------------------------"
	@echo "   * PHP_MIN_VERSION: $(PHP_MIN_VERSION)"
	@echo ""
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

prod: ##1 Installs all prod dependencies
	composer install --no-dev

dev: ##1 Installs all dev dependencies
	composer install --ignore-platform-req=ext-intl

clean: ##1 Clears all dependencies
	rm -rf vendor/*
	rm -rf .reports
	rm -rf .svrunit
	rm -rf .build

build: ##1 Builds PHPUnuhi and creates phpunuhi.phar
	@echo "===================================================================="
	@echo "verifying if phar files can be created....phar.readonly has to be OFF"
	@php -i | grep phar.readonly
	@php -i | grep "Loaded Configuration"
	@cd devops/scripts && php build.php

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

arkitect: ##3 Starts the PHPArkitect Analyser
	php ./vendor/bin/phparkitect check

phpunit: ##3 Runs all tests
	XDEBUG_MODE=coverage php ./vendor/bin/phpunit -v --coverage-html ./.reports/phpunit/coverage --coverage-clover ./.reports/phpunit/clover/index.xml
	php vendor/bin/coverage-check ./.reports/phpunit/clover/index.xml $(PHPUNIT_MIN_COVERAGE)

infection: ##3 Starts all Infection/Mutation tests
	rm -rf ./.reports/infection
	@XDEBUG_MODE=coverage php vendor/bin/infection --configuration=./infection.json --min-msi=$(MUTANTS_MIN_MSI) --min-covered-msi=$(MUTANTS_MIN_COVERED_MSI) --threads=4 --log-verbosity=all --debug

svrunit: ##3 Runs all SVRUnit tests
	php vendor/bin/svrunit test --configuration=./svrunit.xml --debug --report-junit --report-html

#------------------------------------------------------------------------------------------------

check-release: ##4 Checks if everything is fine to release the provided version
ifndef version
	$(error version is not set)
endif
	php tests/scripts/check_xsd.php $(version)

artifact: ##4 Create a ZIP file in the build folder
	cd .build && zip phpunuhi.zip phpunuhi.phar

docker: ##4 Builds the Docker image
ifndef version
	$(error version is not set)
endif
	rm -f ./devops/docker_release/phpunuhi.phar || true
	docker rmi -f $(shell docker images boxblinkracer/phpunuhi -q) || true
	cp ./.build/phpunuhi.phar ./devops/docker_release/phpunuhi.phar
	cd ./devops/docker_release && DOCKER_BUILDKIT=1 docker build --no-cache -t boxblinkracer/phpunuhi:$(version) .

