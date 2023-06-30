# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

status: ## Status command
	cd ../../.. && php bin/phpunuhi status --configuration=./tests/playground/po/phpunuhi.xml

list: ## List Translate
	cd ../../.. && php bin/phpunuhi list:translations --configuration=./tests/playground/po/phpunuhi.xml

validate: ## Validate command
	cd ../../.. && php bin/phpunuhi validate --configuration=./tests/playground/po/phpunuhi.xml --report-format=junit --report-output=./tests/playground/po/.exports/junit.xml

structure: ## Fix Structure
	cd ../../.. && php bin/phpunuhi fix:structure --configuration=./tests/playground/po/phpunuhi.xml

translate: ## Translate
	cd ../../.. && php bin/phpunuhi translate --configuration=./tests/playground/po/phpunuhi.xml --service=googleweb

migrate: ## Migrate command
	cd ../../.. && php bin/phpunuhi migrate --configuration=./tests/playground/po/phpunuhi.xml --output=json
