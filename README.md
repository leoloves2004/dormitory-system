# Dormitory Management System

A Laravel MVC final-project implementation for managing dormitory rooms, tenants, students, applications, payments, visitors, reports, imports, and role-based dashboards.

## Developers

- Add developer name 1
- Add developer name 2
- Add developer name 3

## Stack

- Laravel 13
- Blade templates with layout, navigation, sidebar, and reusable link components
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
- Middleware: admin-only, student-only, guest restriction, authentication protection, and reusable role middleware
- Form Request validation for registration, rooms, room applications, payments, tenants, students, profile updates, and API writes
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
- `Student belongsTo User`
- `Student hasMany RoomApplications`
- `Student belongsToMany Rooms through Tenants`
- `Room hasMany RoomApplications`
- `Room belongsToMany Students through Tenants`
- `Tenant belongsTo Student`
- `Tenant belongsTo Room`
- `Tenant hasMany Payments`
- `Payment belongsTo Tenant`
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
name,email,student_number,course,year_level,contact_number
```

Payment CSV headers:

```csv
student_number,amount,payment_date,due_date,payment_method,reference_number,status,notes
```

The interface accepts `.csv` and `.xlsx` extensions. For production-grade XLSX parsing or true binary PDF generation, add packages such as Laravel Excel and DomPDF.

## GitHub Setup

```bash
git init
git add .
git commit -m "Initial dormitory management system"
git branch -M main
git remote add origin https://github.com/your-username/dormitory-system.git
git push -u origin main
```

Suggested commit sequence:

```bash
git add database app/Models
git commit -m "Add dormitory database schema and models"
git add app/Http routes
git commit -m "Add web and API controllers"
git add resources tests README.md
git commit -m "Add Blade UI, tests, and documentation"
```

## Deployment Guide

For Render or Railway:

1. Create a new web service from the GitHub repository.
2. Set environment variables from `.env.example`.
3. Use `composer install --no-dev --optimize-autoloader && npm ci && npm run build` as the build command.
4. Use `php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT` as the start command.

For shared Laravel hosting:

1. Upload the project files.
2. Point the web root to `public`.
3. Configure `.env`, database credentials, and `APP_KEY`.
4. Run migrations and seeders from the hosting terminal.
