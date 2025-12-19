# 🌌 Star Wars Search Backend

Backend API built with **Laravel**, responsible for searching **People** and **Movies** from Star Wars, returning details and generating **metrics asynchronously**.

The project follows a **clean and simple architecture**, designed to be easy to read, maintain and extend.

---

## ✨ Features

- 🔍 **Search**: Search for People or Movies.
- 📄 **Details**: Fetch detailed information by ID.
- 📊 **Metrics**: Endpoint with aggregated data.
- ⚡ **Asynchronous**: Metrics processing using queues (Redis).
- 🧠 **Clean Architecture**: Clear separation of responsibilities.
- ♻️ **Design Patterns**: DTO + Domain pattern.
- 🚀 **Performance**: Fast responses with Redis cache via Docker.

---

## 🧰 Technologies

- **PHP 8+** (Laravel 10/11)
- **Docker & Docker Compose**
- **Redis** (Cache & Queue)
- **External API**: [SWAPI](https://swapi.dev/)

---

## 🧱 Architecture Overview

The backend follows a **clear and linear flow**:

- **Controller**: Receives HTTP requests, validates input, and delegates execution.
- **Service**: Contains business logic and orchestrates processes.
- **Repository**: Communicates with external APIs (SWAPI).
- **Domain**: Represents business entities.
- **DTO**: Defines API response format.

---

## 🚀 Running the project with Docker

### 1️⃣ Requirements
- **Docker & Docker Compose** (Required)
- **PHP >= 8.0** (Recommended for local dev/IDE support)
- **Composer** (Recommended for managing packages locally)

### 2️⃣ Installation & Setup

```bash
# Clone the repository
git clone <REPOSITORY_URL>
cd backend

# Environment setup
cp .env.example .env
```

**Edit .env for Docker:**
```env
APP_PORT=80
REDIS_HOST=redis
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
```

### 3️⃣ Start the Environment

```bash
# Build and start all services (app, redis, queue, scheduler)
docker-compose up -d --build

# Install dependencies (first time)
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

```

The API will be available at: `http://localhost`

### 4️⃣ Troubleshooting (Permissions)

If you encounter **Permission Denied** errors related to the `storage` or `bootstrap/cache` folders (very common in Linux/Docker environments), run the following command to fix permissions:

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### 5️⃣ Useful Docker Commands

**Running Queues (Metrics):**
The queue worker is already defined in `docker-compose.yml`, but you can run it manually or restart it using:
```bash
# Run the metrics queue worker specifically
docker-compose exec app php artisan queue:work --queue=metrics

# Run the general queue worker
docker-compose exec app php artisan queue:work
```

**General Commands:**
```bash
# View logs
docker-compose logs -f

# Restart services
docker-compose restart

# Stop environment
docker-compose down

# Access terminal inside the container
docker-compose exec app bash
```

---

## 👨‍💻 Author

Developed by **Maycon Rick**
_Backend focused on clarity, scalability, and clean architecture using Docker containers._