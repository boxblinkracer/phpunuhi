# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

status: ## Status command
	cd ../../.. && php bin/phpunuhi status --configuration=./tests/playground/php/phpunuhi.xml

list: ## List available translation IDs
	cd ../../.. && php bin/phpunuhi list:translations --configuration=./tests/playground/ini/phpunuhi.xml

validate: ## Validate command
	cd ../../.. && php bin/phpunuhi validate --configuration=./tests/playground/php/phpunuhi.xml  --report-format=junit --report-output=./tests/playground/php/.exports/junit.xml

csv: ## Exports a CSV
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/php/phpunuhi.xml --format=csv --dir=./tests/playground/php/.exports

structure: ## Fix Structure
	cd ../../.. && php bin/phpunuhi fix:structure --configuration=./tests/playground/php/phpunuhi.xml

html: ## Exports a HTML
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/php/phpunuhi.xml --format=html --dir=./tests/playground/php/.exports

translate: ## Translate
	cd ../../.. && php bin/phpunuhi translate --configuration=./tests/playground/php/phpunuhi.xml --service=googleweb

migrate: ## Migrate command
	cd ../../.. && php bin/phpunuhi migrate --configuration=./tests/playground/php/phpunuhi.xml --output=json
