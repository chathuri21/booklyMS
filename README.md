# BooklyMS

Laravel-based microservices project for a simple booking/appointments domain.

## Services

- **API Gateway**: `api-gateway/` (Laravel)
  - Routes live in `api-gateway/routes/api.php`
- **User Service**: `services/user-service/` (Laravel) — runs on port **8001**
- **Appointment Service**: `services/appointment-service/` (Laravel) — runs on port **8002**
- **Notification Service**: `services/notification-service/` (Laravel) — present in repo (not wired in `docker-compose.yml` yet)

## Docker quickstart (recommended)

### Prerequisites

- Docker + Docker Compose

### Start

From the repo root:

```bash
docker compose up --build
```

This starts:

- **MySQL**: `localhost:3307` (container port 3306)
- **RabbitMQ**:
  - AMQP: `localhost:5673`
  - Management UI: `localhost:15673` (default credentials `guest` / `guest`)
- **User Service**: `http://localhost:8001`
- **Appointment Service**: `http://localhost:8002`
- **Workers**:
  - `queue-worker` (user-service) runs `php artisan queue:work ...`
  - `appointment-worker` (appointment-service) runs `php artisan consume:user-events`

### Database credentials (Docker)

The `mysql` container is configured as:

- **host**: `mysql` (from other containers) / `127.0.0.1` (from your machine)
- **port**: `3306` (containers) / `3307` (your machine)
- **username**: `microservice_user`
- **password**: `StrongPassword123!`
- **databases**: `user_service_db`, `appointment_service_db` (created by `docker/mysql/init.sql`)

### Configure each service `.env`

Each Laravel app ships with a default `.env.example` using SQLite; for Docker you’ll usually want MySQL.

For each service:

```bash
cp .env.example .env
php artisan key:generate
```

Then update DB settings in the service’s `.env` (example):

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=user_service_db   # or appointment_service_db
DB_USERNAME=microservice_user
DB_PASSWORD=StrongPassword123!
```

Run migrations inside the container (example for user-service):

```bash
docker compose exec user-service php artisan migrate
```

## Local development (without Docker)

If you prefer running services directly on your machine, you’ll need:

- PHP + Composer
- A MySQL instance (or switch each service to SQLite)
- RabbitMQ (optional, depending on which flows you test)

Example (user service):

```bash
cd services/user-service
composer install
cp .env.example .env
php artisan key:generate
php artisan serve --port=8001
```

## API Gateway routes (current)

Defined in `api-gateway/routes/api.php`:

- `POST /api/login`
- REST resource: `/api/appointments` (index/show/store/update/destroy)

## Repo layout

```text
.
├─ api-gateway/
├─ services/
│  ├─ user-service/
│  ├─ appointment-service/
│  └─ notification-service/
├─ docker/
│  ├─ mysql/
│  └─ nginx/
└─ docker-compose.yml
```

## Notes

- `docker/nginx/default.conf` references `api-gateway`, but the root `docker-compose.yml` does not currently start an Nginx or `api-gateway` container. If you want, I can wire those into Compose as the next step.

