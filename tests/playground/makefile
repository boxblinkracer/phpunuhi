# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

validate: ## Validate command
	cd ../.. && php bin/phpunuhi validate --configuration=./tests/playground/phpunuhi.xml


translate: ## Translate command
	cd ../.. && php bin/phpunuhi translate --configuration=./tests/playground/phpunuhi.xml --service=googleweb --force=nl


