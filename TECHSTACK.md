# Tech Stack

## Backend

| Component       | Technology          | Version   |
|----------------|---------------------|-----------|
| Language        | PHP                 | ^8.2      |
| Framework       | Laravel             | ^12.0     |
| ORM             | Eloquent            | (bundled) |
| Queue           | Database driver     |           |
| Cache           | Database driver     |           |
| Session         | Database driver     |           |

## Frontend

| Component       | Technology          | Version   |
|----------------|---------------------|-----------|
| Templating      | Blade               | (bundled) |
| CSS Framework   | Tailwind CSS        | ^4.0      |
| Build Tool      | Vite                | ^7.0      |
| HTTP Client     | Axios               | ^1.11     |
| Plugin          | laravel-vite-plugin | ^2.0      |

## Database

| Component       | Technology          |
|----------------|---------------------|
| Default         | SQLite              |
| Supported       | MySQL (configured)  |

## Testing

| Component       | Technology          | Version   |
|----------------|---------------------|-----------|
| Framework       | PHPUnit             | ^11.5     |
| Mocking         | Mockery             | ^1.6      |
| Fake Data       | FakerPHP            | ^1.23     |
| Collision       | nunomaduro/collision| ^8.6      |

## Dev Tools

| Component       | Technology          | Version   |
|----------------|---------------------|-----------|
| Code Style      | Laravel Pint        | ^1.24     |
| REPL            | Laravel Tinker      | ^2.10     |
| Logging         | Laravel Pail        | ^1.2      |
| Docker          | Laravel Sail        | ^1.41     |
| Task Runner     | concurrently        | ^9.0      |

## Architecture

- **Stack**: Monolithic Laravel application
- **Frontend**: Server-rendered Blade templates with Tailwind CSS
- **State**: Database-backed sessions, cache, and queues
- **Dev workflow**: `composer dev` runs server, queue worker, log viewer, and Vite in parallel

## Models

| Model              | Purpose                    |
|-------------------|----------------------------|
| User              | Authentication & profiles  |
| Brand             | Brand management           |
| BrandCatalog      | Brand catalog entries      |
| Schedule          | Scheduling                 |
| CalendarEvent     | Calendar events            |
| CalendarTask      | Calendar tasks             |
| CalendarCategory  | Calendar categories        |
| TaskCategory      | Task categories            |
| DailyLog          | Daily activity logs        |
| ActivityLog       | System activity tracking   |
