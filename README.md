# Dormitory Management System

A Laravel MVC final-project implementation for managing dormitory rooms, tenants, students, applications, payments, visitors, reports, imports, and role-based dashboards.

## Developers

- Add developer name 1
- Add developer name 2
- Add developer name 3

## Stack

- Laravel 13
- Blade templates with `x-layout` and `x-nav-link` components
- Tailwind CSS
- Eloquent ORM
- SQLite by default, with MySQL/PostgreSQL supported through `.env`

## Default Accounts

After seeding:

- Admin: `admin@example.com`
- Password: `password`

Students created by the factory also use `password`.

## Main Features

- Authentication: login, registration, logout, hashed passwords, sessions
- Roles: `admin` and `student`
- Middleware: `auth` and custom `role` middleware
- Admin dashboard statistics and analytics
- Room, student, tenant, payment, application, and visitor log management
- Student room applications, assignment view, profile update, and payment history
- Application approval/rejection with email notification
- Activity log table for audit-ready events
- QR room access code stored per room
- Search and filters on management screens
- Dark mode toggle
- Mobile responsive sidebar layout
- REST API resources for rooms, students, tenants, payments, and room applications
- Report exports for occupancy, tenants, payments, and assignments in `pdf`, `xlsx`, `csv`, and `json`
- CSV/XLSX upload inputs for student and payment imports

## Database Tables

- `users`
- `students`
- `rooms`
- `tenants`
- `payments`
- `room_applications`
- `visitor_logs`
- `activity_logs`
- Laravel support tables for sessions, cache, jobs, and password reset tokens

## Eloquent Relationships

- `User hasOne Student`
- `Student belongsTo Room`
- `Room hasMany Students`
- `Student hasMany Payments`
- `Room hasMany Tenants`
- `Student hasMany RoomApplications`

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

For SQLite, make sure `.env` uses:

```env
DB_CONNECTION=sqlite
```

For MySQL or PostgreSQL, update the `DB_*` variables in `.env` and rerun migrations.

## Deployment

- Hosting link: add deployed URL here
- Recommended free hosts: Render, Railway, InfinityFree, 000WebHost, or another Laravel-capable service

## Web Routes

- `/login`
- `/register`
- `/student/dashboard`
- `/admin/dashboard`
- `/admin/rooms`
- `/admin/students`
- `/admin/tenants`
- `/admin/applications`
- `/admin/payments`
- `/admin/visitor-logs`
- `/admin/reports`

## API Routes

All API resources support RESTful `GET`, `POST`, `PUT/PATCH`, and `DELETE` operations.

```text
GET|POST       /api/rooms
GET|PUT|PATCH|DELETE /api/rooms/{room}
GET|POST       /api/students
GET|PUT|PATCH|DELETE /api/students/{student}
GET|POST       /api/tenants
GET|PUT|PATCH|DELETE /api/tenants/{tenant}
GET|POST       /api/payments
GET|PUT|PATCH|DELETE /api/payments/{payment}
GET|POST       /api/room-applications
GET|PUT|PATCH|DELETE /api/room-applications/{room_application}
```

## Import Format

Student CSV headers:

```csv
name,email,student_number,course,year_level,phone
```

Payment CSV headers:

```csv
student_number,amount,payment_date,due_date,method,reference_number,status,notes
```

The interface accepts `.csv` and `.xlsx` extensions. For production-grade XLSX parsing or true binary PDF generation, add packages such as Laravel Excel and DomPDF.
