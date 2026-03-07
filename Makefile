.PHONY: help build up down restart logs logs-app logs-nginx logs-mysql shell bash shell-root bash-root composer install update artisan migrate migrate-fresh seed key cache-clear cache-config test test-pest test-unit test-feature test-filter test-coverage clean setup setup-ssl setup-full setup-hosts remove-hosts ps stats mysql mysql-root opcache-status phpinfo storage-link check-docker check-requirements

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build Docker images
	docker compose build --no-cache

up: ## Start all containers
	docker compose up -d

down: ## Stop all containers
	docker compose down

restart: ## Restart all containers
	docker compose restart

logs: ## Show logs from all containers
	docker compose logs -f

logs-app: ## Show logs from app container
	docker compose logs -f app

logs-nginx: ## Show logs from nginx container
	docker compose logs -f nginx

logs-mysql: ## Show logs from mysql container
	docker compose logs -f mysql

shell: ## Open shell in app container
	docker compose exec app sh

bash: ## Open bash shell in app container
	docker compose exec app bash

shell-root: ## Open shell as root in app container
	docker compose exec -u root app sh

bash-root: ## Open bash shell as root in app container
	docker compose exec -u root app bash

composer: ## Run composer command (usage: make composer CMD="install")
	docker compose exec app composer $(CMD)

install: ## Install PHP dependencies
	docker compose exec app composer install

update: ## Update PHP dependencies
	docker compose exec app composer update

artisan: ## Run artisan command (usage: make artisan CMD="migrate")
	docker compose exec app php artisan $(CMD)

migrate: ## Run database migrations
	docker compose exec app php artisan migrate

migrate-fresh: ## Fresh migration with seeding
	docker compose exec app php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker compose exec app php artisan db:seed

key: ## Generate application key
	docker compose exec app php artisan key:generate

cache-clear: ## Clear all caches
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear

cache-config: ## Cache configuration
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache

test: ## Run tests
	docker compose exec app php artisan test

test-pest: ## Run Pest tests
	docker compose exec app ./vendor/bin/pest

test-unit: ## Run unit tests only
	docker compose exec app ./vendor/bin/pest tests/Unit

test-feature: ## Run feature tests only
	docker compose exec app ./vendor/bin/pest tests/Feature

test-filter: ## Run tests matching filter (usage: make test-filter FILTER="SimulateFixture")
	docker compose exec app ./vendor/bin/pest --filter=$(FILTER)

test-coverage: ## Run tests with coverage
	docker compose exec app ./vendor/bin/pest --coverage

setup: ## Initial setup (install dependencies, generate key, migrate)
	@if [ ! -f .env ]; then \
		echo "Creating .env file from .env.example..."; \
		cp .env.example .env || echo ".env.example not found. Please create .env manually."; \
	fi
	docker compose exec app composer install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan storage:link || true
	docker compose exec app php artisan migrate

clean: ## Remove all containers, volumes and images
	docker compose down -v --rmi all

ps: ## Show running containers
	docker compose ps

stats: ## Show container resource usage
	docker stats

mysql: ## Connect to MySQL
	docker compose exec mysql mysql -u couple_balance_user -ppassword couple_balance

mysql-root: ## Connect to MySQL as root
	docker compose exec mysql mysql -u root -proot

opcache-status: ## Check OPcache status
	docker compose exec app php -r "var_dump(opcache_get_status());"

phpinfo: ## Show PHP configuration
	docker compose exec app php -i | grep -i opcache

storage-link: ## Create storage symbolic link
	docker compose exec app php artisan storage:link

check-docker: ## Check if Docker is installed and running
	@if ! command -v docker > /dev/null; then \
		echo "Docker is not installed."; \
		exit 1; \
	fi
	@if ! docker info > /dev/null 2>&1; then \
		echo "Docker is not running."; \
		exit 1; \
	fi
	@echo "Docker is installed and running"

check-requirements: check-docker ## Check all requirements
	@echo "All requirements met"

setup-hosts: ## Add couple-balance.local to hosts file
	@echo "Adding couple-balance.local to /etc/hosts..."
	@if grep -q "couple-balance.local" /etc/hosts; then \
		echo "Already exists"; \
	else \
		echo "127.0.0.1 couple-balance.local" | sudo tee -a /etc/hosts > /dev/null; \
	fi

remove-hosts: ## Remove couple-balance.local from hosts file
	@sudo sed -i '' '/couple-balance.local/d' /etc/hosts 2>/dev/null || true

setup-ssl: ## Generate SSL certificates using mkcert
	@echo "Setting up SSL certificates..."
	@if ! command -v mkcert > /dev/null; then \
		brew install mkcert; \
	fi
	@if [ ! -f "$(HOME)/Library/Application Support/mkcert/rootCA.pem" ]; then \
		mkcert -install; \
	fi
	@mkdir -p docker/nginx/ssl
	cd docker/nginx/ssl && mkcert couple-balance.local "*.couple-balance.local" localhost 127.0.0.1 ::1

setup-full: ## Complete setup
	@$(MAKE) setup-hosts
	@$(MAKE) setup-ssl
	@if [ ! -f .env ]; then cp .env.example .env; fi
	@$(MAKE) build
	@$(MAKE) up
	@sleep 10
	@$(MAKE) install
	@$(MAKE) key
	@docker compose exec app php artisan storage:link || true
	@$(MAKE) migrate
	@echo ""
	@echo "Setup complete!"
	@echo "https://couple-balance.local/"
