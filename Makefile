COMPOSER_CMD=composer
PHIVE_CMD=phive

PHPUNIT_CMD=vendor/bin/phpunit
PHPSTAN_CMD=tools/phpstan
PHPCS_CMD=tools/phpcs
README_TESTER_CMD=tools/readme-tester

.DEFAULT_GOAL=all

.PHONY: all
all: test analyze

.PHONY: clean
clean:
	rm -rf vendor
	rm -rf tools
	rm -f composer.lock

.PHONY: test
test: phpunit readme-tester

.PHONY: phpunit
phpunit: vendor/installed $(PHPUNIT_CMD)
	$(PHPUNIT_CMD)

.PHONY: readme-tester
readme-tester: vendor/installed $(README_TESTER_CMD)
	$(README_TESTER_CMD)

.PHONY: analyze
analyze: phpstan phpcs

.PHONY: phpstan
phpstan: vendor/installed $(PHPSTAN_CMD)
	$(PHPSTAN_CMD) analyze src -l 7 src

.PHONY: phpcs
phpcs: composer.lock $(PHPCS_CMD)
	$(PHPCS_CMD) src --standard=PSR2
	$(PHPCS_CMD) tests --standard=PSR2

composer.lock: composer.json
	@echo composer.lock is not up to date

vendor/installed: composer.lock
	$(COMPOSER_CMD) install
	touch $@

$(PHPSTAN_CMD):
	$(PHIVE_CMD) install phpstan

$(PHPCS_CMD):
	$(PHIVE_CMD) install phpcs

$(README_TESTER_CMD):
	$(PHIVE_CMD) install hanneskod/readme-tester:1 --force-accept-unsigned
