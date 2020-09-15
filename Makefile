####################
# LOCAL
####################

uid := $(shell id -u)
gid := $(shell id -g)

up: docker-up
down: docker-down
restart: docker-down docker-up

init: docker-down-clear docker-build docker-up composer-install

docker-up:
	export uid=$(uid) gid=$(gid); \
	docker-compose -f ./docker-compose-local.yml up -d

docker-down:
	export uid=$(uid) gid=$(gid); \
	docker-compose -f ./docker-compose-local.yml down --remove-orphans

docker-down-clear:
	export uid=$(uid) gid=$(gid); \
	docker-compose -f ./docker-compose-local.yml down -v --remove-orphans

docker-pull:
	export uid=$(uid) gid=$(gid); \
	docker-compose -f ./docker-compose-local.yml pull

docker-build:
	export uid=$(uid) gid=$(gid); \
	docker-compose -f ./docker-compose-local.yml build

docker-ps:
	export uid=$(uid) gid=$(gid); \
	docker-compose -f ./docker-compose-local.yml ps

docker-top:
	export uid=$(uid) gid=$(gid); \
	docker-compose -f ./docker-compose-local.yml top

composer-install:
	export uid=$(uid) gid=$(gid); \
	docker-compose -f ./docker-compose-local.yml run --rm php-fpm composer install
