# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

status: ## Status command
	cd ../../.. && php bin/phpunuhi status --configuration=./tests/playground/xml/phpunuhi.xml

validate: ## Validate command
	cd ../../.. && php bin/phpunuhi validate --configuration=./tests/playground/xml/phpunuhi.xml
