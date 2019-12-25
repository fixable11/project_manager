up: docker-up
init: docker-down-clear docker-pull docker-build docker-up manager-init manager-migrations
test-unit: manager-test-unit

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

manager-init: manager-composer-install manager-assets-install

manager-composer-install:
	docker-compose exec manager-php-fpm composer install

manager-assets-install:
	docker-compose exec manager-node npm install

test:
	docker-compose exec manager-php-fpm php bin/phpunit

manager-test-unit:
	docker-compose run --rm manager-php-fpm php bin/phpunit --testsuite=unit

#PHPStan - PHP Static Analysis Tool
stan:
	docker-compose exec manager-php-fpm vendor/bin/phpstan analyze src

#Code sniffer
sniff:
	docker-compose exec manager-php-fpm ./vendor/bin/phpcs --error-severity=1 --warning-severity=8 --colors ./src; \
	docker-compose exec manager-php-fpm ./vendor/bin/phpcs --error-severity=1 --warning-severity=8 --colors --report=summary ./src; return 0;

build-production:
	docker build --pull --file=manager/docker/production/nginx.docker --tag ${REGISTRY_ADDRESS}/manager-nginx:${IMAGE_TAG} manager
	docker build --pull --file=manager/docker/production/php-fpm.docker --tag ${REGISTRY_ADDRESS}/manager-php-fpm:${IMAGE_TAG} manager
	docker build --pull --file=manager/docker/production/php-cli.docker --tag ${REGISTRY_ADDRESS}/manager-php-cli:${IMAGE_TAG} manager

push-production:
	docker push ${REGISTRY_ADDRESS}/manager-nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/manager-php-fpm:${IMAGE_TAG}

deploy-production:
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -o StrictHostKeyChecking=no -P ${PRODUCTION_PORT} docker-compose-production.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_APP_SECRET=${MANAGER_APP_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_DB_PASSWORD=${MANAGER_DB_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose --build -d'

perm:
	sudo chown ${USER}:${USER} ./manager -R

bash:
	docker-compose exec manager-php-fpm bash

frontend-bash:
	docker-compose exec manager-node sh

frontend-watch:
	docker-compose exec manager-node npm run watch

manager-migrations:
	docker-compose run --rm manager-php-fpm php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker-compose exec manager-php-fpm php bin/console doctrine:fixtures:load --no-interaction

