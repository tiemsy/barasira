.PHONY: install dev serve vite build swagger routes cache-clear cache-opt test lint fmt

# Variables
PHP ?= php
ARTISAN := $(PHP) artisan
NPM ?= npm

install:
    @composer install
    @$(NPM) install

serve:
    @$(ARTISAN) serve

vite:
    @$(NPM) run dev

dev:
    @$(MAKE) -j2 serve vite

build:
    @$(NPM) run build

swagger:
	@$(ARTISAN) swagger:generate
	@echo "Swagger generated in storage/api-docs/"

routes:
    @$(ARTISAN) route:list --path=api

cache-clear:
    @$(ARTISAN) optimize:clear

cache-opt:
    @$(ARTISAN) config:cache && $(ARTISAN) route:cache && $(ARTISAN) view:cache

test:
    @$(PHP) -d zend.enable_gc=0 vendor/bin/phpunit

lint:
    @vendor/bin/pint --test

fmt:
    @vendor/bin/pint
