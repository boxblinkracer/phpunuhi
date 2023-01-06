# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

bin: ## Runs all tests via bin/phpunuhi
	cd ../.. && php bin/phpunuhi --version
	cd ../.. && php bin/phpunuhi list
	cd ../.. && php bin/phpunuhi validate --configuration=./tests/e2e/files/valid/phpunuhi.xml
	cd ../.. && php bin/phpunuhi export --configuration=./tests/e2e/files/valid/phpunuhi.xml --dir=.export --delimiter=","
	cd ../.. && php bin/phpunuhi import --configuration=./tests/e2e/files/import/phpunuhi.xml --set=storefront --file=./tests/e2e/files/import/import.csv --delimiter="," --intent=3 --sort

phar: ## Runs all tests with the PHAR
	php ../../build/phpunuhi.phar --version
	php ../../build/phpunuhi.phar list
	php ../../build/phpunuhi.phar validate --configuration=files/valid/phpunuhi.xml
	php ../../build/phpunuhi.phar export --configuration=files/valid/phpunuhi.xml --dir=.export --delimiter=","
	php ../../build/phpunuhi.phar import --configuration=files/import/phpunuhi.xml --set=storefront --file=files/import/import.csv --delimiter="," --intent=3 --sort
