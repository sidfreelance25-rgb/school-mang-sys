# SchoolMS — School Management System

A beginner-friendly Laravel web application for a single school. It covers students, teachers, parents, classes, attendance, timetables, examinations, homework, fees, expenses, notices, reports, and school settings with role-based access.

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+
- SQLite (default) or MySQL

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan storage:link
php artisan serve
```

Open `http://127.0.0.1:8000` and sign in with one of the demo accounts below.

## Demo accounts

Password for all seeded users: `password`

| Role | Email |
| --- | --- |
| School Admin | `admin@school.test` |
| Principal | `principal@school.test` |
| Teacher | `anita@school.test` |
| Accountant | `accountant@school.test` |
| Student | `student@school.test` |
| Parent | `parent@school.test` |

## Main modules

- Dashboard with role-aware statistics
- Student admission, profiles, promotion, documents
- Teacher and parent management
- Academic sessions, classes, sections, subjects
- Student and teacher attendance
- Class timetables with teacher conflict checks
- Examinations, marks entry, report cards
- Homework and submission tracking
- Fee structures, invoices, partial payments, receipts
- Expense tracking
- Notices and leave requests
- Reports for admissions, attendance, fees, exams, and expenses
- School settings and basic user management

## Roles

Admin and Principal manage school configuration and people. Teachers handle attendance, homework, and marks. Accountants manage fees and expenses. Students and parents have read-focused portals for academics, homework, notices, and fees.
