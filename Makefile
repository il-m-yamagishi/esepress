install:
	@cd backend && sh scripts/install-composer.sh && php composer.phar install
.PHONY: install
