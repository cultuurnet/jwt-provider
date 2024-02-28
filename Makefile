.PHONY: up down build install install-npm install-githooks bash init

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose up --build

install:
	docker exec -it jwt-provider composer install

bash:
	docker exec -it jwt-provider bash

init: install