# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

status: ## Status command
	cd ../../.. && php bin/phpunuhi status --configuration=./tests/playground/resx/phpunuhi.xml

list: ## List available translation IDs
	cd ../../.. && php bin/phpunuhi list:translations --configuration=./tests/playground/resx/phpunuhi.xml

fix-structure: ## Fix Keys
	cd ../../.. && php bin/phpunuhi fix:structure --configuration=./tests/playground/resx/phpunuhi.xml

validate: ## Validate command
	cd ../../.. && php bin/phpunuhi validate --configuration=./tests/playground/resx/phpunuhi.xml  --report-format=junit --report-output=./tests/playground/resx/.exports/junit.xml

translate: ## Translate
	cd ../../.. && php bin/phpunuhi translate --configuration=./tests/playground/resx/phpunuhi.xml --service=googleweb
