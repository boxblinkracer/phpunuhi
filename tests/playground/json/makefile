# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

status: ## Status command
	cd ../../.. && php bin/phpunuhi status --configuration=./tests/playground/json/phpunuhi.xml

list: ## List Translate
	cd ../../.. && php bin/phpunuhi list:translations --configuration=./tests/playground/json/phpunuhi.xml

validate: ## Validate command
	cd ../../.. && php bin/phpunuhi validate --configuration=./tests/playground/json/phpunuhi.xml

structure: ## Fix Structure
	cd ../../.. && php bin/phpunuhi fix:structure --configuration=./tests/playground/json/phpunuhi.xml

html: ## Exports a HTML
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/json/phpunuhi.xml --format=html --dir=./tests/playground/json/.exports

csv: ## Exports a CSV
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/json/phpunuhi.xml --format=csv --dir=./tests/playground/json/.exports

csvimport: ## Imports a CSV
	cd ../../.. && php bin/phpunuhi import --configuration=./tests/playground/json/phpunuhi.xml --format=csv --set=json --file=./tests/playground/json/.exports/json.csv

translate: ## Translate
	cd ../../.. && php bin/phpunuhi translate --configuration=./tests/playground/json/phpunuhi.xml --service=googleweb