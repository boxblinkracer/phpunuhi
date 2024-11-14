# Central Makefile

MAKEFILE_DIR := $(dir $(realpath $(lastword $(MAKEFILE_LIST))))

#--------------------------------------------------------------------------------
#--------------------------------------------------------------------------------
TEST_FOLDER := ./tests/playground/$(FOLDER)/phpunuhi.xml

BINARY := cd ../../.. && php bin/phpunuhi
DOCKER := docker run --rm -it -v $(MAKEFILE_DIR)$(FOLDER):/app boxblinkracer/phpunuhi:dev-main
#--------------------------------------------------------------------------------
#--------------------------------------------------------------------------------
# TOGGLE BETWEEN BINARY OR DOCKER
EXECUTE := $(BINARY) --configuration=$(TEST_FOLDER)
# EXECUTE := $(DOCKER) --configuration=./phpunuhi.xml

REPORT_FORMAT := junit
EXPORT_FORMAT := html
SPELLING_SERVICE := aspell
TRANSLATION_SERVICE := googleweb
#--------------------------------------------------------------------------------
#--------------------------------------------------------------------------------

help:
	@printf "\033[33mStatus:\033[0m\n"
	@grep -Eh '^[a-zA-Z_-]+:.*?##1 .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?##1 "}; {printf "\033[33m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "--------------------------------------------------------------------------------------------"
	@printf "\033[36mValidation:\033[0m\n"
	@grep -Eh '^[a-zA-Z_-]+:.*?##2 .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?##2 "}; {printf "\033[36m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "--------------------------------------------------------------------------------------------"
	@printf "\033[32mReports:\033[0m\n"
	@grep -Eh '^[a-zA-Z_-]+:.*?##3 .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?##3 "}; {printf "\033[32m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "--------------------------------------------------------------------------------------------"
	@printf "\033[35mOthers:\033[0m\n"
	@grep -Eh '^[a-zA-Z_-]+:.*?##4 .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?##4 "}; {printf "\033[35m  - %-30s\033[0m %s\n", $$1, $$2}'

#--------------------------------------------------------------------------------

services: ##1 services
	$(EXECUTE) services

status: ##1 status
	$(EXECUTE) status

list-translations: ##1 list:translations
	$(EXECUTE) list:translations

list-translation-keys: ##1 list:translation-keys
	$(EXECUTE) list:translation-keys

#--------------------------------------------------------------------------------

validate-all: ##2 validate:all
	$(EXECUTE) validate:all --report-format=$(REPORT_FORMAT) --report-output=./tests/playground/$(FOLDER)/.exports

validate-mess: ##2 validate:mess
	$(EXECUTE) validate:mess --report-format=$(REPORT_FORMAT) --report-output=./tests/playground/$(FOLDER)/.exports

validate-coverage: ##2 validate:coverage
	$(EXECUTE) validate:coverage

validate-structure: ##2 validate:structure
	$(EXECUTE) validate:structure

validate-spelling: ##2 validate:spelling
	$(EXECUTE) validate:spelling --service=$(SPELLING_SERVICE)

validate-similarity: ##2 validate:similarity
	$(EXECUTE) validate:similarity

#--------------------------------------------------------------------------------

export: ##3 export
	$(EXECUTE) export --format=$(EXPORT_FORMAT) --dir=./tests/playground/$(FOLDER)/.exports

import: ##3 import
	$(EXECUTE) import --format=$(EXPORT_FORMAT) --dir=./tests/playground/$(FOLDER)/.exports

#--------------------------------------------------------------------------------

fix-structure: ##4 fix:structure
	$(EXECUTE) fix:structure

fix-mess: ##4 fix:mess
	$(EXECUTE) fix:mess

fix-spelling: ## fix:spelling
	$(EXECUTE) fix:spelling --service=$(SPELLING_SERVICE)

translate: ##4 translate
	$(EXECUTE) translate --service=$(TRANSLATION_SERVICE)

migrate: ##4 migrate
	$(EXECUTE) migrate --output=json

scan: ##4 scan
	$(EXECUTE) scan:usage --dir=./tests/playground/json/templates --scanner=mjml --verbose
