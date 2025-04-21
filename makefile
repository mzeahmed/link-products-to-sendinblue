RED=\033[0;31m
GREEN=\033[0;32m
YELLOW=\033[0;33m
BLUE=\033[0;34m
NO_COLOR=\033[0m

blocks_path = resources/blocks

help: ## Displays this help message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

init: ## Installs all dependencies
	@echo "$(YELLOW)Installing dependencies...$(NO_COLOR)"
	npm install
	composer install
	@echo "$(GREEN)Dependencies successfully installed$(NO_COLOR)"

u: ## Updates PHP dependencies
	@echo "$(YELLOW)Updating composer dependencies...$(NO_COLOR)"
	@composer update
	@echo "$(GREEN)Composer dependencies updated$(NO_COLOR)"

du: ## Dumps Composer autoload
	@echo "$(YELLOW)Updating Composer autoloader...$(NO_COLOR)"
	composer dump-autoload
	@echo "$(GREEN)Composer autoloader updated$(NO_COLOR)"

w: ## Watches CSS/JS assets for development with wp-scripts
	@echo "$(BLUE)Watching CSS/JS assets...$(NO_COLOR)"
	npm run watch

b: ## Builds CSS/JS assets for production with wp-scripts
	@echo "$(YELLOW)Building CSS/JS assets for production...$(NO_COLOR)"
	npm run build
	@echo "$(GREEN)Production build complete$(NO_COLOR)"

i18n: ## Generates translation .pot file
	@echo "$(YELLOW)Generating translation file...$(NO_COLOR)"
	php ./.bin/wp i18n make-pot . resources/i18n/link-products-to-sendinblue.pot
	@echo "$(BLUE)Translation file generated$(NO_COLOR)"

cs-i: ## Forces init phpcompatibility/php-compatibility
	@echo "$(YELLOW)Forcing init phpcompatibility/php-compatibility...$(NO_COLOR)"
	composer run phpcs:init
	@echo "$(GREEN)phpcompatibility/php-compatibility initialized$(NO_COLOR)"

cs: ## Runs PHP CodeSniffer in check mode
	@echo "$(YELLOW)Running PHP CodeSniffer...$(NO_COLOR)"
	composer run phpcs
	@echo "$(GREEN)PHP CodeSniffer finished$(NO_COLOR)"

cbf: ## Runs PHP CodeSniffer with auto-fix
	@echo "$(YELLOW)Running PHP CodeSniffer with auto-fix...$(NO_COLOR)"
	composer run phpcbf
	@echo "$(GREEN)PHP CodeSniffer finished$(NO_COLOR)"

stan: ## Runs PHPStan analysis
	@echo "$(YELLOW)Running PHPStan...$(NO_COLOR)"
	./vendor/bin/phpstan analyse -c phpstan.neon
	@echo "$(GREEN)PHPStan analysis complete$(NO_COLOR)"

pu: ## Updates wp-scripts packages
	@echo "$(GREEN)Updating wp-scripts packages...$(NO_COLOR)"
	npm run packages-update
	@echo "$(GREEN)wp-scripts packages updated$(NO_COLOR)"

cahe-d: ## Deletes cache directory
	@echo "$(YELLOW)Deleting cache directory...$(NO_COLOR)"
	rm -rf ./src/Domain/DI/CacheContainer.php
	@echo "$(GREEN)Cache directory deleted$(NO_COLOR)"
