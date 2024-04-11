.PHONY: up down build install ci bash init

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose up --build

install:
	docker exec -it jwt-provider composer install

ci:
	docker exec -it jwt-provider composer ci

bash:
	docker exec -it jwt-provider bash

init: install