run:
	php -S 127.0.0.1:8000 -t public
clear:
	php bin/console cache:clear
migration:
	php bin/console make:migration
migrate:
	php bin/console doctrine:migrations:migrate
drop:
	php bin/console doctrine:database:drop --force
create:
	php bin/console doctrine:database:create
update:
	php bin/console doctrine:schema:update --force
entity:
	php bin/console make:entity
controller:
	php bin/console make:controller
fixture:
	php bin/console make:fixture
loadAll:
	php bin/console doctrine:fixtures:load --append
voter:
	php bin/console make:voter
route:
	php bin/console debug:route
router:
	php bin/console debug:router
