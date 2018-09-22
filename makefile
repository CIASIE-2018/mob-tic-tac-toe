MAKEFLAGS += --silent

.PHONY: help install start test test-api lint

.DEFAULT_GOAL := help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

require: ## Add a new dependency. Usage: make require slim/slim "^3.0"
	docker-compose run webserver composer $(MAKECMDGOALS)

%:
	@:

install: ## Install dependencies
	docker-compose run webserver composer install

start: ## Start application
	docker-compose up -d

run: ## Start application and show logs
	docker-compose up

stop: ## Stop application
	docker-compose stop

test: ## Launch tests
	docker-compose run webserver ./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox app
