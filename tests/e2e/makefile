# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

bin: ## Runs all tests via bin/phpunuhi
	cd ../.. && php bin/phpunuhi validate --configuration=./tests/e2e/phpunuhi.xml
	cd ../.. && php bin/phpunuhi export --configuration=./tests/e2e/phpunuhi.xml

phar: ## Runs all tests with the PHAR
	php ../../build/phpunuhi.phar validate --configuration=phpunuhi.xml
	php ../../build/phpunuhi.phar export --configuration=phpunuhi.xml
