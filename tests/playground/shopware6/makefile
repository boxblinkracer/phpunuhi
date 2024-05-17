# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

status: ## Status command
	cd ../../.. && php bin/phpunuhi status --configuration=./tests/playground/shopware6/phpunuhi.xml

list: ## List available translation IDs
	cd ../../.. && php bin/phpunuhi list:translations --configuration=./tests/playground/shopware6/phpunuhi.xml

validate: ## Validate command
	cd ../../.. && php bin/phpunuhi validate --configuration=./tests/playground/shopware6/phpunuhi.xml  --report-format=junit --report-output=./tests/playground/shopware6/.exports/junit.xml

structure: ## Fix Structure
	cd ../../.. && php bin/phpunuhi fix:structure --configuration=./tests/playground/shopware6/phpunuhi.xml

csv: ## Exports a CSV
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/shopware6/phpunuhi.xml --format=csv --dir=./tests/playground/shopware6/.exports

html: ## Exports a HTML
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/shopware6/phpunuhi.xml --format=html --dir=./tests/playground/shopware6/.exports

json: ## Exports a JSON
	cd ../../.. && php bin/phpunuhi export --configuration=./tests/playground/shopware6/phpunuhi.xml --format=json --dir=./tests/playground/shopware6/.exports

translate: ## Translate
	cd ../../.. && php bin/phpunuhi translate --configuration=./tests/playground/shopware6/phpunuhi.xml --service=googleweb

import: ## Import, provide format format=json
	cd ../../.. && php bin/phpunuhi import --configuration=./tests/playground/shopware6/phpunuhi.xml --set=categories --format=$(format) --file=./tests/playground/shopware6/.exports/categories.$(format)
	cd ../../.. && php bin/phpunuhi import --configuration=./tests/playground/shopware6/phpunuhi.xml --set=products --format=$(format) --file=./tests/playground/shopware6/.exports/products.$(format)
	cd ../../.. && php bin/phpunuhi import --configuration=./tests/playground/shopware6/phpunuhi.xml --set=snippets --format=$(format) --file=./tests/playground/shopware6/.exports/snippets.$(format)
	cd ../../.. && php bin/phpunuhi import --configuration=./tests/playground/shopware6/phpunuhi.xml --set=state-machine-states --format=$(format) --file=./tests/playground/shopware6/.exports/state-machine-states.$(format)
	cd ../../.. && php bin/phpunuhi import --configuration=./tests/playground/shopware6/phpunuhi.xml --set=state-machines --format=$(format) --file=./tests/playground/shopware6/.exports/state-machines.$(format)