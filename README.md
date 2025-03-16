# Translation Management Service

This is a Laravel-based Translation Management Service API designed to showcase clean, scalable, and secure code. The service features multi-locale support, translation tagging, optimized endpoints (including a high-performance JSON export), and Swagger/OpenAPI documentation.

## Features

- **Multi-Locale Support:** Manage translations across multiple languages.
- **Contextual Tagging:** Associate translations with relevant contexts (e.g., mobile, desktop, web).
- **Full CRUD Endpoints:** Create, update, view, search, and delete translations and locales.
- **JSON Export Endpoint:** Optimized for large datasets with efficient streaming.
- **Swagger/OpenAPI Documentation:** Interactive API docs available at `/api/documentation`.
- **Dockerized Setup:** Fully containerized using Docker and Docker Compose.
- **High Test Coverage:** Unit and feature tests provided.

## Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/mubashirmalik1/translation-management.git
cd translation-management
```
### 2. Copy the Environment File

```bash
cp .env.example .env
```

### 3. Build the Docker Containers

```bash
docker-compose up -d --build
```
### 4. Run Migrations and Seed the Database
**Once the containers are up, open a shell inside the app container:**
```bash
docker-compose exec app bash
```
**Then run the following commands:**
```bash
php artisan key:generate
php artisan migrate --seed
php artisan l5-swagger:generate
```
### 5. Access the Service
The API is available at:  
`http://localhost:9000/api`

**Example Endpoints:**
- **Translations:** `http://localhost:9000/api/translations`
- **Locales:** `http://localhost:9000/api/locales`

## Swagger Documentation

View interactive API documentation at:  
`http://localhost:9000/api/documentation`
