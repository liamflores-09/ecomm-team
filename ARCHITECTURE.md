# Architecture

## Overview

**Ecomm Dept Hub** — a monolithic Laravel 12 application for managing the e-commerce department's daily operations, including content creation, design tasks, brand catalogs, scheduling, and team reporting.

| Aspect | Detail |
|--------|--------|
| Framework | Laravel 12 |
| PHP | ^8.2 |
| Templating | Blade (server-rendered) |
| Frontend | Vanilla JS + ApexCharts + FullCalendar.js |
| Database | SQLite (default), MySQL supported |
| Auth | Custom username/password, session-based |
| Timezone | Asia/Manila |

---

## System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                      BROWSER                            │
│  Blade Views + Inline JS + ApexCharts + FullCalendar    │
└──────────────────────────┬──────────────────────────────┘
                           │ HTTP
┌──────────────────────────▼──────────────────────────────┐
│                     LARAVEL 12                           │
│  ┌─────────────┐  ┌──────────────┐  ┌────────────────┐ │
│  │   Routes     │  │ Controllers  │  │   Middleware   │ │
│  │  (web.php)   │──│  (8 total)   │──│  auth, admin,  │ │
│  │              │  │              │  │  catalog.manager│ │
│  └─────────────┘  └──────┬───────┘  └────────────────┘ │
│                          │                               │
│  ┌──────────────┐  ┌─────▼──────┐  ┌────────────────┐  │
│  │   Models      │  │  Support   │  │ Notifications  │  │
│  │  (10 Eloquent)│  │ TaskLabels  │  │ (5 classes,    │  │
│  │              │  │             │  │  database-only) │  │
│  └──────┬───────┘  └────────────┘  └────────────────┘  │
│         │                                               │
│  ┌──────▼───────────────────────────────────────────┐   │
│  │              SQLite / MySQL                       │   │
│  │  22 tables (8 infrastructure + 11 domain + 3    │   │
│  │  pivot/notification)                              │   │
│  └──────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

---

## Modules

### 1. Authentication

**Files**: `LoginController`, `auth/login.blade.php`, `AdminMiddleware`

- Username + password login (no email)
- Session-based with CSRF protection
- Admin role (`manager`) gets redirected to `/admin` on login
- No Breeze/Jetstream/Fortify — fully custom

### 2. Dashboard

**Files**: `DashboardController`, `dashboard.blade.php`

- Welcome banner with role-colored header
- EOD submission status strip
- Weekly and monthly task sum stat cards
- ApexCharts weekly activity bar chart
- Quick access links (role-dependent — content sees Posting Procedure, Requirements, Data Gathering)
- Recent daily logs table

### 3. End-of-Day (Daily Logs)

**Files**: `DailyLogController`, `end-of-day.blade.php`, `DailyLog` model

- 5 configurable task columns per role (loaded from `task_categories` via `TaskLabels` helper, cached 1 hour)
- Attendance tracking (present/late/absent)
- `updateOrCreate` — one log per user per day
- Remarks field
- Notifies managers on first submission
- ActivityLog audit trail on create/update/delete
- Paginated history view (20 per page)

### 4. Calendar

**Files**: `CalendarController`, `calendar.blade.php`, `CalendarEvent`, `CalendarTask`, `CalendarCategory` models

- FullCalendar.js integration with JSON event endpoint
- **Events**: title, start/end datetime, location, description, category, attendees (many-to-many)
- **Tasks**: title, due date, assigned role, status (pending/done), parent task for subtasks
- **Categories**: color-coded (default: Deck, Meeting Proposals, Appointment Schedules)
- Subtask toggle auto-completes parent when all children done
- Role-based visibility: managers see all, others see team-wide events + own events
- Notifications on event creation, task assignment, and task completion

### 5. Brand Catalogs

**Files**: `BrandCatalogController`, `brand-catalogs.blade.php`, `Brand`, `BrandCatalog` models

- Brand management (admin): name, description, classification (Tech / Design-Consumer / Both), logo upload
- Catalog entries: title, notes, status (available/upcoming/seasonal), link or file upload
- Paginated card grid (6 per page) with brand/classification filters
- File storage on `public` disk (`catalogs/` directory)
- Notify all users on new catalog
- Manager/Researcher role required for CRUD (enforced by `CatalogManagerMiddleware`)

### 6. Notifications

**Files**: `NotificationController`, 5 notification classes

| Notification | Trigger | Recipients |
|-------------|---------|-----------|
| `CalendarEventNotification` | Event created | All users |
| `CalendarTaskNotification` | Task assigned | Assigned role + managers |
| `CalendarTaskCompletedNotification` | Task/subtasks done | Creator + managers |
| `NewBrandCatalog` | Catalog added | All users |
| `EodSubmitted` | EOD first submission | Managers only |

- All database-only (no email/SMS)
- Bell icon in header with unread count
- Read all, clear all, delete individual

### 7. Profile

**Files**: `ProfileController`, `profile.blade.php`

- Hero card with DiceBear avatar, name, badges
- Edit first name, last name, mobile number, gender
- Optional password change

### 8. Team Directory

**Files**: `DashboardController@team`, `team.blade.php`

- All users grouped by role
- Role tabs for filtering
- Manager/Lead displayed as 2-col leader cards
- Others as 3-col member cards
- Viber contact links

### 9. Static Content Pages

**Files**: `DashboardController`, various Blade views

| Page | Audience |
|------|----------|
| Posting Procedure | Content role |
| Data Gathering | Content role |
| E-commerce Requirements | Content role |
| Price Calculator | All users |
| Important Links | All users |

### 10. Admin Panel

**Files**: `AdminController`, `AdminBrandController`, `admin/*.blade.php`

Protected by `AdminMiddleware` (role = `manager`).

#### Admin Dashboard (`/admin`)
- KPI cards (total users, active today, logs this week, completion rate)
- Role activity area charts (7-day)
- Today's pulse with per-role status
- Quick actions
- Recent activity feed

#### User Management (`/admin/users`)
- CRUD with create/edit modals
- Role assignment
- Password management
- Activity logging

#### Daily Logs (`/admin/daily-logs`)
- Role filtering
- Calendar day view with selectable days
- Weekly history
- Per-role breakdown with mini ApexCharts sparklines
- Top contributors per role
- 14-day day-by-day history

#### Reports (`/admin/reports`)
- Monthly reports with role filtering
- Weekly breakdown by member
- Share/pivot data per task type (task_1..task_5)
- Per-member monthly breakdown
- 12-month year overview
- Per-role grouped data for "All Roles" view

#### Brand Management (`/admin/brands`)
- CRUD with logo upload
- Classification stats
- Prevents deletion if catalogs exist

---

## User Roles

| Role | Admin | EOD Tracking | Sidebar Access |
|------|-------|-------------|----------------|
| `manager` | Yes | No | Admin panel + all user pages |
| `lead` | No | Yes | Standard pages |
| `content` | No | Yes | Standard + Posting Procedure, Requirements, Data Gathering |
| `graphics` | No | Yes | Standard pages |
| `researcher` | No | Yes | Standard pages |
| `backend` | No | Yes | Standard pages |

---

## Data Model

### Entity Relationships

```
User ──┬── hasMany ── DailyLog
       ├── hasMany ── Schedule
       ├── hasMany ── ActivityLog
       └── belongsToMany ── CalendarEvent (attendees)

Brand ──── hasMany ── BrandCatalog

CalendarCategory ──┬── hasMany ── CalendarEvent
                   └── hasMany ── CalendarTask

CalendarEvent ──── belongsToMany ── User (attendees)

CalendarTask ──── hasMany ── CalendarTask (subtasks via parent_id)
```

### Tables

**Domain Tables**

| Table | Purpose | Key Constraints |
|-------|---------|----------------|
| `daily_logs` | Per-user daily task tracking | Unique: (user_id, date) |
| `task_categories` | Per-role configurable task labels | |
| `schedules` | User scheduling (unused) | Unique: (user_id, date) |
| `activity_logs` | Audit trail for all actions | |
| `brands` | Brand master list (~290 seeded) | |
| `brand_catalogs` | Catalog entries per brand | FK: brand_id |
| `calendar_categories` | Calendar event/task categories | |
| `calendar_events` | Calendar events | FK: category_id, created_by |
| `calendar_event_attendees` | Event-user pivot | Unique: (event_id, user_id) |
| `calendar_tasks` | Tasks with subtask support | Self-ref FK: parent_id |

**Infrastructure Tables**

| Table | Purpose |
|-------|---------|
| `users` | Authentication + profile |
| `sessions` | Session storage |
| `cache` / `cache_locks` | Cache + locks |
| `jobs` / `job_batches` / `failed_jobs` | Queue infrastructure |
| `notifications` | Database notifications |
| `password_reset_tokens` | Password resets |

---

## Middleware

| Alias | File | Logic |
|-------|------|-------|
| `auth` | Laravel built-in | Requires authentication |
| `admin` | `AdminMiddleware.php` | Aborts 403 if role !== `manager` |
| `catalog.manager` | `CatalogManagerMiddleware.php` | Aborts 403 if role not in `['manager', 'researcher']` |

---

## Key Patterns

1. **No API layer** — all routes are web routes; JSON endpoints exist only for FullCalendar.js consumption
2. **No service layer** — controller methods contain business logic directly
3. **Inline CSS** — all styling via `@section('styles')` in Blade (Tailwind v4 in composer but unused)
4. **Database-backed everything** — sessions, cache, queue, notifications all use database driver
5. **Activity logging** — manual `ActivityLog::create()` calls in controllers
6. **Task labels** — dynamic per-role labels from `task_categories`, cached 1 hour via `TaskLabels` helper
7. **Dark mode** — CSS custom properties with `data-theme="dark"` toggle
8. **Command palette** — Ctrl+K search across all pages
9. **Pure Blade + vanilla JS** — no Livewire, Inertia, or Filament
10. **File storage** — Laravel `public` disk for logos and catalog files

---

## Notification Flow

```
Action occurs (EOD submit, catalog add, calendar event, etc.)
    │
    ▼
Controller creates Notification via Notification::create()
    │
    ▼
Stored in `notifications` table (database-only)
    │
    ▼
Header bell icon polls /notifications endpoint
    │
    ▼
Shows unread count + dropdown with last 30 notifications
    │
    ▼
User can mark read, clear all, or delete individual
```
