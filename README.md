# Asset-Tracker API

A Laravel REST API for managing **Assets** and their **Inspections**. Each asset can have many inspections (one-to-many relationship).

## Prerequisites

- **Docker Desktop** (Docker + Docker Compose). No PHP or Composer required on the host.

## Installation

### 1. Create the project (if starting fresh)

```bash
curl -s "https://laravel.build/asset-tracker?with=pgsql" | bash
cd asset-tracker
```

### 2. Start the application

```bash
./vendor/bin/sail up -d
```

### 3. Run migrations and seed the database

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
