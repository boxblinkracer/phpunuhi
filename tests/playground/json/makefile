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
	cd ../../.. && php bin/phpunuhi validate --configuration=./tests/playground/json/phpunuhi.xml --report-format=junit --report-output=./tests/playground/json/.exports/junit.xml

validate-mess: ## Validate mess command
	cd ../../.. && php bin/phpunuhi validate:mess --configuration=./tests/playground/json/phpunuhi.xml --report-format=junit --report-output=./tests/playground/json/.exports/junit.xml

fix-mess: ## Fix mess command
	cd ../../.. && php bin/phpunuhi fix:mess --configuration=./tests/playground/json/phpunuhi.xml

validate-json: ## Validate command with json report
	cd ../../.. && php bin/phpunuhi validate --configuration=./tests/playground/json/phpunuhi.xml --report-format=json --report-output=./tests/playground/json/.exports/report.json

structure: ## Fix Structure
	cd ../../.. && php bin/phpunuhi fix:structure --configuration=./tests/playground/json/phpunuhi.xml

keys: ## Fix Keys
	cd ../../.. && php bin/phpunuhi fix:keys --configuration=./tests/playground/json/phpunuhi.xml

html: ## Exports a HTML
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/json/phpunuhi.xml --format=html --empty --dir=./tests/playground/json/.exports

csv: ## Exports a CSV
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/json/phpunuhi.xml --format=csv --empty --dir=./tests/playground/json/.exports

csvimport: ## Imports a CSV
	cd ../../.. && php bin/phpunuhi import --configuration=./tests/playground/json/phpunuhi.xml --format=csv --set=json --file=./tests/playground/json/.exports/json.csv

htmlimport: ## Imports a HTML
	cd ../../.. && php bin/phpunuhi import --configuration=./tests/playground/json/phpunuhi.xml --format=html --set=json --file=./tests/playground/json/import.html.txt

translate: ## Translate
	cd ../../.. && php bin/phpunuhi translate --configuration=./tests/playground/json/phpunuhi.xml --service=googleweb

migrate: ## Migrate command
	cd ../../.. && php bin/phpunuhi migrate --configuration=./tests/playground/json/phpunuhi.xml --output=ini
