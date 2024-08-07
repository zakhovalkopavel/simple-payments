SHELL  := /bin/bash
include .env

init:
	make up status \
	&& make symfony-set-env \
	&& make restart \
	&& ( make symfony-init-project || true)\
	&& make kill \
	&& make symfony-set-files-permissions \
	&& sudo rm -rf ./docker/var/lib/mysql \
	&& make restart \
	&& sleep 20 \
	&& make symfony-migrate
create-project: setup-env restart symfony-create-project symfony-set-env

restart: kill up status
kill:
	docker-compose kill && docker-compose rm -vf
up:
	docker-compose up -d --remove-orphans
status:
	docker-compose ps && docker-compose logs --tail=100
logs:
	docker-compose logs -f
pull:
	docker-compose pull

nginx-conf-reload:
	docker-compose exec nginx bin/sh -c 'nginx -t && nginx -s reload'
php-bash:
	docker-compose exec php bash

setup-env:
	cp .env.example .env

symfony-init-project:
	docker-compose exec php bash -c 'composer install && cd ${PHP_PROJECT_NAME} && composer install'
symfony-migrate:
	docker-compose exec php bash -c 'cd ${PHP_PROJECT_NAME} && php bin/console doctrine:migrations:migrate'

symfony-create-migration-and-migrate:
	docker-compose exec php bash -c 'cd ${PHP_PROJECT_NAME} && php bin/console doctrine:migrations:diff && php bin/console doctrine:migrations:migrate'
symfony-create-project:
	docker-compose exec php bash -c 'composer create-project symfony/skeleton:"${SYMFONY_VERSION}" ${PHP_PROJECT_NAME} \
	&& cd ${PHP_PROJECT_NAME} && composer require symfony/orm-pack && composer require --dev symfony/maker-bundle \
	&& composer require symfony/serializer-pack'
symfony-set-env:
	docker-compose exec php bash -c '\
	sudo rm -f ./${PHP_PROJECT_NAME}/.env \
	&& touch ./${PHP_PROJECT_NAME}/.env \
	&& echo "APP_ENV=${APP_ENV}" >> ./${PHP_PROJECT_NAME}/.env \
	&& echo "APP_SECRET=${APP_SECRET}" >> ./${PHP_PROJECT_NAME}/.env \
	&& echo "DATABASE_URL=${DATABASE_URL}" >> ./${PHP_PROJECT_NAME}/.env \
	&& echo "APP_DEBUG=${APP_DEBUG}" >> ./${PHP_PROJECT_NAME}/.env \
	&& echo "SHIFT4_API_KEY=${SHIFT4_API_KEY}" >> ./${PHP_PROJECT_NAME}/.env \
	&& chown root:root ./${PHP_PROJECT_NAME}/.env \
	&& chmod 644 ./${PHP_PROJECT_NAME}/.env'
symfony-regenerate-keys:
	docker-compose exec php bash -c 'cd ./${PHP_PROJECT_NAME} \
	&& php bin/console make:command regenerate-app-secret \
	&& php bin/console regenerate-app-secret \
	&& php bin/console secrets:generate-keys'
symfony-set-files-permissions:
	sudo chown -R $$USER:www-data ./source/${PHP_PROJECT_NAME}/
	sudo find ./source/${PHP_PROJECT_NAME}/ -type f -exec chmod 644 {} \;
	sudo find ./source/${PHP_PROJECT_NAME}/ -type d -exec chmod 755 {} \;
	sudo chmod 777 -R ./source/${PHP_PROJECT_NAME}/var/cache/
	sudo chmod 777 -R ./source/${PHP_PROJECT_NAME}/var/log/
symfony-create-entity:
	docker-compose exec php bash -c 'cd ${PHP_PROJECT_NAME} && php bin/console make:entity'