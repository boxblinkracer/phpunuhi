# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help



#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

bin: ## Runs all tests via bin/phpunuhi
	make runTests BINARY="cd ../.. && php bin/phpunuhi" DIR="./tests/e2e"
	#--------------------------------------------------------------------------------------------------

phar: ## Runs all tests with the PHAR
	make runTests BINARY="php ../../build/phpunuhi.phar" DIR="."

runTests:
	$(BINARY) --version
	$(BINARY) list
	#--------------------------------------------------------------------------------------------------
	# JSON
	$(BINARY) validate --configuration=$(DIR)/formats/json/phpunuhi.xml
	$(BINARY) export --configuration=$(DIR)/formats/json/phpunuhi.xml --dir=$(DIR)/.exports
	#--------------------------------------------------------------------------------------------------
	# INI
	$(BINARY) validate --configuration=$(DIR)/formats/ini/phpunuhi.xml
	$(BINARY) export --configuration=$(DIR)/formats/ini/phpunuhi.xml --dir=$(DIR)/.exports
	#--------------------------------------------------------------------------------------------------
	# PHP
	$(BINARY) validate --configuration=$(DIR)/formats/php/phpunuhi.xml
	$(BINARY) export --configuration=$(DIR)/formats/php/phpunuhi.xml --dir=$(DIR)/.exports
	#--------------------------------------------------------------------------------------------------
