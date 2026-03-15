# Asset-Tracker API

A Laravel REST API for managing **Assets** and their **Inspections**. Each asset can have many inspections (one-to-many relationship).

## Prerequisites

- **Docker Desktop** (Docker + Docker Compose). No PHP or Composer required on the host.

## Environment Setup

When **cloning the repo** (as opposed to using `laravel.build`), you must configure environment variables:

1. **Copy the environment file:**

   ```bash
   cp .env.example .env
   ```
   On Windows: `copy .env.example .env`

2. **Generate the application key** (after Sail is running):

   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

   If running locally without Sail: `php artisan key:generate`

3. **Configure database variables** for PostgreSQL via Sail (see [Database Configuration](#database-configuration) below).

Note: Projects created with `laravel.build` already include a configured `.env` file.

## Installation

### Option A: Fresh project (laravel.build)

1. Create the project:

   ```bash
   curl -s "https://laravel.build/asset-tracker?with=pgsql" | bash
   cd asset-tracker
   ```

2. Start the application:

   ```bash
   ./vendor/bin/sail up -d
   ```

3. Run migrations and seed the database:

   ```bash
   ./vendor/bin/sail artisan migrate
   ./vendor/bin/sail artisan db:seed
   ```

### Option B: Clone existing repo

1. Install dependencies:

   ```bash
   composer install
   ```

2. Copy `.env.example` to `.env` and configure it (see [Environment Setup](#environment-setup)).

3. If no `docker-compose.yml` exists, run `php artisan sail:install` and select PostgreSQL.

4. Start Sail and generate the key:

   ```bash
   ./vendor/bin/sail up -d
   ./vendor/bin/sail artisan key:generate
   ```

5. Run migrations and seed:

   ```bash
   ./vendor/bin/sail artisan migrate
   ./vendor/bin/sail artisan db:seed
   ```

The API will be available at **http://localhost** (port 80) or **http://localhost:8000** (port 8000).

## Useful Sail Commands

- `./vendor/bin/sail up -d` – Start containers (detached)
- `./vendor/bin/sail down` – Stop containers
- `./vendor/bin/sail artisan migrate` – Run migrations
- `./vendor/bin/sail artisan db:seed` – Run seeders
- `./vendor/bin/sail shell` – Open shell inside app container

## Testing

Run the test suite with:

**With Sail:**

```bash
./vendor/bin/sail artisan test
```

**Without Sail:**

```bash
php artisan test
```

Or: `composer test`

The test suite uses PHPUnit and includes feature tests for the Asset API (create, validation, unique serial number, status enum, GET with latest 3 inspections, and 404 for missing assets).

> **Note:** Tests use `DB_DATABASE=testing`. With Sail + PostgreSQL, ensure a `testing` database exists, or run migrations against it once (e.g. `./vendor/bin/sail artisan migrate --database=testing` after creating the DB).

## API Documentation

Base URL: `http://localhost/api` (or `http://localhost:8000/api`)

### POST /api/assets

Create a new asset.

**Request body (JSON):**

| Field         | Type   | Required | Description                                      |
|---------------|--------|----------|--------------------------------------------------|
| name          | string | Yes      | Asset name (max 255 chars)                       |
| serial_number | string | Yes      | Unique serial number                             |
| status        | string | Yes      | One of: `active`, `inactive`, `maintenance`      |

**Example request:**

```bash
curl -X POST http://localhost/api/assets \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Laptop Dell","serial_number":"DL-001","status":"active"}'
```

**Example response (201 Created):**

```json
{
  "id": 11,
  "name": "Laptop Dell",
  "serial_number": "DL-001",
  "status": "active",
  "created_at": "2025-03-12T10:00:00.000000Z",
  "updated_at": "2025-03-12T10:00:00.000000Z"
}
```

**Validation errors (422):** Returns JSON with validation messages for invalid or duplicate `serial_number`.

---

### GET /api/assets/{id}

Retrieve an asset with its latest 3 inspections.

**Example request:**

```bash
curl http://localhost/api/assets/1
```

**Example response (200 OK):**

```json
{
  "id": 1,
  "name": "Laptop Dell XPS 15",
  "serial_number": "DL-XPS-001",
  "status": "active",
  "created_at": "2025-03-12T10:00:00.000000Z",
  "updated_at": "2025-03-12T10:00:00.000000Z",
  "inspections": [
    {
      "id": 5,
      "asset_id": 1,
      "inspector_name": "Jane Doe",
      "passed": true,
      "notes": "Routine inspection.",
      "created_at": "2025-03-12T10:05:00.000000Z",
      "updated_at": "2025-03-12T10:05:00.000000Z"
    }
  ]
}
```

**404 Not Found:** Returned when the asset does not exist.

## Database

### Database Configuration

When using Laravel Sail with PostgreSQL, set these variables in your `.env`:

| Variable        | Value      | Purpose                                  |
|-----------------|------------|------------------------------------------|
| `DB_CONNECTION` | `pgsql`    | Use PostgreSQL (not sqlite)              |
| `DB_HOST`       | `pgsql`    | Sail Docker service name (not 127.0.0.1) |
| `DB_PORT`       | `5432`     | PostgreSQL port                          |
| `DB_DATABASE`   | `laravel`  | Database name                            |
| `DB_USERNAME`   | `sail`     | Default Sail DB user                     |
| `DB_PASSWORD`   | `password` | Default Sail DB password                 |

`DB_HOST=pgsql` references the Docker service name so the app container can reach PostgreSQL on the Docker network.

### Schema

- **PostgreSQL** (via Laravel Sail)
- **Assets:** `name`, `serial_number`, `status`
- **Inspections:** `asset_id`, `inspector_name`, `passed` (boolean), `notes`

The seeder populates 10 sample assets with 2–5 inspections each.

## Future Improvements

- `GET /api/assets` – List all assets with pagination
- `PUT /api/assets/{id}` – Update asset
- `DELETE /api/assets/{id}` – Delete asset
- `POST /api/assets/{id}/inspections` – Add inspection to an asset

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
