# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

status: ## Status command
	cd ../../.. && php bin/phpunuhi status --configuration=./tests/playground/yaml/phpunuhi.xml

list: ## List available translation IDs
	cd ../../.. && php bin/phpunuhi list:translations --configuration=./tests/playground/yaml/phpunuhi.xml

validate: ## Validate command
	cd ../../.. && php bin/phpunuhi validate --configuration=./tests/playground/yaml/phpunuhi.xml  --report-format=junit --report-output=./tests/playground/yaml/.exports/junit.xml

validate-json: ## Validate command with json report
	cd ../../.. && php bin/phpunuhi validate --configuration=./tests/playground/yaml/phpunuhi.xml  --report-format=json --report-output=./tests/playground/yaml/.exports/report.json

csv: ## Exports a CSV
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/yaml/phpunuhi.xml --format=csv --dir=./tests/playground/yaml/.exports

structure: ## Fix Structure
	cd ../../.. && php bin/phpunuhi fix:structure --configuration=./tests/playground/yaml/phpunuhi.xml

html: ## Exports a HTML
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/yaml/phpunuhi.xml --format=html --dir=./tests/playground/yaml/.exports

translate: ## Translate
	cd ../../.. && php bin/phpunuhi translate --configuration=./tests/playground/yaml/phpunuhi.xml --service=googleweb

migrate: ## Migrate command
	cd ../../.. && php bin/phpunuhi migrate --configuration=./tests/playground/yaml/phpunuhi.xml --output=po
