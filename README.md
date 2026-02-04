# Employee Management API

A comprehensive RESTful API for managing employees and their attendance records, built with Laravel 12, featuring authentication, CRUD operations, email notifications, and report generation.

## 🚀 Features

- **Authentication System**
  - User registration with validation
  - Login/Logout with Sanctum tokens
  - Password reset functionality
  - Secure stateless authentication

- **Employee Management**
  - Complete CRUD operations
  - Unique employee identifiers
  - Soft delete support
  - Validation and error handling

- **Attendance Tracking**
  - Check-in/Check-out recording
  - Automatic date tracking
  - Hours worked calculation
  - Filter by employee and date range

- **Email Notifications**
  - Queued email jobs
  - Check-in notifications
  - Check-out notifications with hours worked
  - Mailpit integration for local testing

- **Report Generation**
  - PDF reports with professional formatting
  - Excel exports with styling
  - Filterable by date range and employee
  - Downloadable files

- **API Documentation**
  - OpenAPI 3.0 specification
  - PHP 8 attributes (not docblocks)
  - Comprehensive endpoint documentation

- **Testing**
  - Complete test coverage
  - Feature and unit tests
  - Factory and seeder support

- **CI/CD**
  - GitHub Actions workflow
  - Automated testing on PRs
  - Multiple branch support

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Docker Desktop
- Git

## 🛠️ Tech Stack

- **Framework:** Laravel 12
- **Authentication:** Laravel Sanctum
- **Database:** MySQL 8.0
- **Queue:** Redis
- **Mail:** Mailpit (local development)
- **Testing:** PHPUnit / Pest
- **PDF Generation:** laravel-snappy with wkhtmltopdf
- **Excel Export:** Laravel Excel
- **API Docs:** OpenAPI v3 with PHP attributes
- **Container:** Laravel Sail (Docker)

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/Himbaza123h/employee-management-api.git
cd employee-management-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Install and Configure Laravel Sail

```bash
# Install Sail
composer require laravel/sail --dev

# Install Sail with services
php artisan sail:install

# Select: mysql, redis, mailpit

# Create alias (optional but recommended)
alias sail='./vendor/bin/sail'
```

### 4. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Start Sail containers
sail up -d

# Generate application key
sail artisan key:generate
```

### 5. Configure Environment Variables

Update your `.env` file:

```env
APP_NAME="Employee Management API"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=employee_management
DB_USERNAME=sail
DB_PASSWORD=password

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@employeemanagement.local"

QUEUE_CONNECTION=database
```

### 6. Install Additional Packages

```bash
# Authentication
sail composer require laravel/sanctum

# Reports
sail composer require maatwebsite/excel
sail composer require barryvdh/laravel-snappy

# OpenAPI
sail composer require vyuldashev/laravel-openapi

# Testing (optional - use Pest instead of PHPUnit)
sail composer require pestphp/pest --dev --with-all-dependencies
sail composer require pestphp/pest-plugin-laravel --dev
sail artisan pest:install
```

### 7. Install wkhtmltopdf (PDF Generation)

```bash
# Access Sail container
sail root-shell

# Install wkhtmltopdf
apt-get update
apt-get install -y wkhtmltopdf

# Exit container
exit
```

### 8. Publish Configuration Files

```bash
# Sanctum
sail artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Excel
sail artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"

# Snappy
sail artisan vendor:publish --provider="Barryvdh\Snappy\ServiceProvider"
```

### 9. Database Setup

```bash
# Run migrations
sail artisan migrate

# Seed database (optional)
sail artisan db:seed
```

### 10. Start Queue Worker

```bash
# In a new terminal
sail artisan queue:work

# Or run in background
sail artisan queue:work --daemon
```

## 🎯 Usage

### Starting the Application

```bash
# Start all containers
sail up -d

# View logs
sail artisan pail

# Stop containers
sail down
```

### Access Points

- **API:** http://localhost/api
- **Mailpit (Email Testing):** http://localhost:8025
- **MySQL:** localhost:3306

### API Endpoints

#### Authentication

```
POST   /api/auth/register          - Register new user
POST   /api/auth/login             - Login user
POST   /api/auth/logout            - Logout user (authenticated)
GET    /api/auth/user              - Get authenticated user
POST   /api/auth/forgot-password   - Send password reset link
POST   /api/auth/reset-password    - Reset password
```

#### Employees

```
GET    /api/employees              - List all employees
POST   /api/employees              - Create new employee
GET    /api/employees/{id}         - Get employee details
PUT    /api/employees/{id}         - Update employee
DELETE /api/employees/{id}         - Delete employee
```

#### Attendance

```
GET    /api/attendances                    - List attendances (with filters)
POST   /api/attendances                    - Record check-in
GET    /api/attendances/{id}               - Get attendance details
PUT    /api/attendances/{id}               - Update attendance
DELETE /api/attendances/{id}               - Delete attendance
POST   /api/attendances/{id}/check-out     - Record check-out
```

Query Parameters for filtering:
- `employee_id` - Filter by employee
- `from_date` - Filter from date (YYYY-MM-DD)
- `to_date` - Filter to date (YYYY-MM-DD)
- `per_page` - Items per page (default: 15)

#### Reports

```
GET    /api/reports/attendance/pdf     - Generate PDF report
GET    /api/reports/attendance/excel   - Generate Excel report
```

Query Parameters:
- `employee_id` - Filter by employee
- `from_date` - Start date (YYYY-MM-DD)
- `to_date` - End date (YYYY-MM-DD)

### Example API Calls

#### Register

```bash
curl -X POST http://localhost/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "Password123!",
    "password_confirmation": "Password123!"
  }'
```

#### Login

```bash
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "Password123!"
  }'
```

#### Create Employee (Authenticated)

```bash
curl -X POST http://localhost/api/employees \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Jane",
    "last_name": "Smith",
    "email": "jane@example.com",
    "employee_identifier": "EMP001",
    "phone_number": "+1234567890"
  }'
```

#### Record Check-In

```bash
curl -X POST http://localhost/api/attendances \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "employee_id": 1
  }'
```

#### Record Check-Out

```bash
curl -X POST http://localhost/api/attendances/1/check-out \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🧪 Testing

### Run All Tests

```bash
# Using PHPUnit
sail artisan test

# Using Pest
sail artisan test --pest

# With coverage
sail artisan test --coverage

# Specific test file
sail artisan test tests/Feature/Auth/AuthTest.php
```

### Test Structure

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── RegisterTest.php
│   │   ├── LoginTest.php
│   │   ├── ForgotPasswordTest.php
│   │   └── ResetPasswordTest.php
│   ├── Employee/
│   │   └── EmployeeTest.php
│   ├── Attendance/
│   │   └── AttendanceTest.php
│   └── Report/
│       └── ReportTest.php
└── Unit/
```

## 📚 Project Structure

```
employee-management-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── EmployeeController.php
│   │   │       ├── AttendanceController.php
│   │   │       └── ReportController.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   ├── Employee/
│   │   │   └── Attendance/
│   │   └── Resources/
│   │       ├── UserResource.php
│   │       ├── EmployeeResource.php
│   │       └── AttendanceResource.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Employee.php
│   │   └── Attendance.php
│   ├── Jobs/
│   │   └── SendAttendanceNotification.php
│   ├── Notifications/
│   │   └── AttendanceRecordedNotification.php
│   └── Exports/
│       └── AttendanceExport.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── routes/
│   └── api.php
├── tests/
│   ├── Feature/
│   └── Unit/
├── resources/
│   └── views/
│       └── reports/
│           └── attendance-pdf.blade.php
└── .github/
    └── workflows/
        └── tests.yml
```

## 🔄 GitHub Actions

The project includes a GitHub Actions workflow that automatically runs tests on:
- Pull requests to `development`, `master`, or `main` branches
- Pushes to `development`, `master`, or `main` branches

Configuration file: `.github/workflows/tests.yml`

## 🐛 Troubleshooting

### Common Issues

**Issue: wkhtmltopdf not found**
```bash
sail root-shell
apt-get update && apt-get install -y wkhtmltopdf
exit
```

**Issue: Queue not processing**
```bash
# Check queue worker is running
sail artisan queue:work

# Clear failed jobs
sail artisan queue:flush
```

**Issue: Email not sending**
```bash
# Check Mailpit is running
# Visit http://localhost:8025

# Restart queue worker
sail artisan queue:restart
```

**Issue: Permission denied**
```bash
# Fix storage permissions
sail artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### Clear Caches

```bash
sail artisan cache:clear
sail artisan config:clear
sail artisan route:clear
sail artisan view:clear
```

## 📝 Development Notes

### Database Conventions

- Use snake_case for table and column names
- All timestamps are in UTC
- Soft deletes enabled for employees
- Foreign keys have ON DELETE CASCADE

### Code Style

- Follow PSR-12 coding standards
- Use PHP 8.2+ features (typed properties, constructor promotion)
- Use strict typing: `declare(strict_types=1);`
- OpenAPI attributes instead of docblocks

### Security

- All routes except auth are protected by Sanctum middleware
- Passwords hashed using bcrypt
- CSRF protection disabled for API routes
- Rate limiting applied to authentication endpoints

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the MIT license.

## 📞 Support

For support, email himbazaalain022@gmail.com or open an issue in the repository.

## ✨ Credits

Built with Laravel 12 and modern PHP practices.

---

**Note:** This is a technical assignment project showcasing Laravel development skills including authentication, CRUD operations, queue jobs, email notifications, and report generation.
