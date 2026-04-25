migrate:
	docker exec -it user_service php artisan migrate
	docker exec -it appointment_service php artisan migrate

fresh:
	docker exec -it user_service php artisan migrate:fresh
	docker exec -it appointment_service php artisan migrate:fresh

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose down && docker compose up -d

ps:
	docker compose ps