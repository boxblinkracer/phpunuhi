# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

run: ## Starts Docker and installs all dependencies
	cd dev && docker-compose down | true;
	cd dev && docker-compose up -d
	sleep 10
	docker exec -it phpunuhi bash -c 'cd /app/devops/dev/scripts && sh install.sh'
	docker exec -it phpunuhi bash -c 'cd /app && make dev'
	docker exec -it phpunuhi bash -c 'cd /app && make help'
	docker exec -it phpunuhi bash