# BooklyMS

Laravel-based microservices project for a simple booking/appointments domain.

## Architecture

```text
client → nginx :8000 → api-gateway
                         ├─ /register, /login, /me → user-service
                         └─ /appointments*        → appointment-service

user-service ──(user.created via RabbitMQ topic exchange)──┬─→ appointment-worker  (user snapshots)
                                                           └─→ notification-worker (welcome email + snapshot)
```

- **API Gateway** (`api-gateway/`) — the single public entry point (behind nginx on port **8000**). Verifies JWTs locally, then proxies to internal services with trusted `X-User-Id` / `X-User-Role` headers.
- **User Service** (`services/user-service/`) — registration, login, profile. Issues **JWTs** (HS256) signed with a secret shared with the gateway. Publishes `user.created` events to RabbitMQ.
- **Appointment Service** (`services/appointment-service/`) — appointment CRUD, scoped to the calling user. Keeps a local `user_snapshots` table fed by `user.created` events.
- **Notification Service** (`services/notification-service/`) — consumes `user.created`, stores a user snapshot, and sends a welcome email (log mailer by default).

### Authentication

- Login/register return a JWT containing `sub`, `name`, `email`, `role`, `exp` (default TTL 60 min).
- The gateway verifies the signature locally (no call to user-service per request) via the shared `JWT_SECRET`.
- Internal services do **not** see the JWT — they trust the gateway's identity headers, which is safe only because they are unreachable except through the Docker network. Ports 8001/8002 are published for local development convenience; do not expose them in production.

## Docker quickstart

### Prerequisites

- Docker + Docker Compose

### Configure environment

`docker-compose.yml` reads infrastructure credentials from `docker/.env` (gitignored):

```bash
cp docker/.env.example docker/.env
```

Fill in MySQL (`MYSQL_ROOT_PASSWORD`, `MYSQL_USER`, `MYSQL_PASSWORD`, …) and RabbitMQ (`RABBITMQ_DEFAULT_USER`, `RABBITMQ_DEFAULT_PASS`).

Each Laravel app also needs its own `.env`:

```bash
cd <service-dir>
cp .env.example .env
php artisan key:generate
```

Key values per service:

- All services: `DB_HOST=mysql`, `DB_PORT=3306`, DB name per service (`user_service_db`, `appointment_service_db`, `notification_service_db` — created by `docker/mysql/init.sql`)
- user-service and api-gateway: the **same** `JWT_SECRET` (generate one with `openssl rand -base64 48`)
- Services using RabbitMQ: `RABBITMQ_HOST=rabbitmq`
- api-gateway: `USER_SERVICE_URL=http://user-service:8000`, `APPOINTMENT_SERVICE_URL=http://appointment-service:8000`

### Start

```bash
docker compose up --build
```

This starts:

- **nginx + API Gateway**: `http://localhost:8000` — the public API
- **User Service**: `http://localhost:8001` (dev only)
- **Appointment Service**: `http://localhost:8002` (dev only)
- **MySQL**: `localhost:3307` (container port 3306)
- **RabbitMQ**: AMQP `localhost:5673`, management UI `localhost:15673`
- **Workers** (wait for healthy MySQL/RabbitMQ):
  - `queue-worker` — user-service `queue:work` (publishes events)
  - `appointment-worker` — `consume:user-events` (user snapshots)
  - `notification-worker` — `consume:user-events` (welcome emails + snapshots)

Run migrations:

```bash
make migrate   # or: docker compose exec <service> php artisan migrate
```

## API (through the gateway)

All public traffic goes through `http://localhost:8000/api`:

| Method | Route | Auth | Description |
|---|---|---|---|
| POST | `/register` | – | create account, returns JWT |
| POST | `/login` | – | returns JWT |
| GET | `/me` | Bearer JWT | current user (from token claims) |
| GET | `/appointments` | Bearer JWT | list own appointments |
| POST | `/appointments` | Bearer JWT | book (`title`, `start_at`, `end_at?`, `notes?`) |
| PUT | `/appointments/{id}` | Bearer JWT | update own appointment |
| DELETE | `/appointments/{id}` | Bearer JWT | cancel (soft delete) |

Example:

```bash
TOKEN=$(curl -s -X POST localhost:8000/api/login \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"email":"you@example.com","password":"secret"}' | jq -r .data.access_token)

curl localhost:8000/api/appointments -H "Authorization: Bearer $TOKEN" -H "Accept: application/json"
```

## CI/CD

GitHub Actions (`.github/workflows/main.yml`), on push/PR to `main`:

1. **Build & test** — matrix over all four apps: composer install, `key:generate`, PHPStan (user-service), `php artisan test`.
2. **Build & push images** — on push to `main` only, after tests pass: builds each service's Docker image and pushes to
   - **GHCR**: `ghcr.io/<owner>/<service>:latest` and `:<git-sha>`
   - **Docker Hub**: `docker.io/<DOCKERHUB_USERNAME>/<service>` (same tags)

Required repository secrets for Docker Hub: `DOCKERHUB_USERNAME`, `DOCKERHUB_TOKEN` (GHCR uses the built-in `GITHUB_TOKEN`).

## Local development (without Docker)

```bash
cd services/user-service
composer install
cp .env.example .env
php artisan key:generate
php artisan serve --port=8001
```

You'll need PHP + Composer, MySQL (or SQLite), and RabbitMQ depending on the flows you test. Note the gateway expects Docker-network hostnames by default — adjust `USER_SERVICE_URL` / `APPOINTMENT_SERVICE_URL` if running services on localhost.

## Testing

Each app has its own suite (SQLite in-memory, no infrastructure needed):

```bash
docker compose exec user-service php artisan test
docker compose exec user-service vendor/bin/phpstan analyse app
```

## Repo layout

```text
.
├─ .github/workflows/main.yml   # CI/CD pipeline
├─ Makefile
├─ api-gateway/                 # public entry, JWT verification, proxying
├─ services/
│  ├─ user-service/             # auth + JWT issuing, event publisher
│  ├─ appointment-service/      # appointment CRUD, event consumer
│  └─ notification-service/     # notifications, event consumer
├─ docker/
│  ├─ .env.example              # copy to docker/.env (gitignored)
│  ├─ mysql/init.sql            # creates the three service databases
│  └─ nginx/default.conf        # nginx → api-gateway FastCGI
└─ docker-compose.yml
```

## Useful Makefile commands

```bash
make up
make ps
make migrate
make fresh
make down
```
