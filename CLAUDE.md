# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## System Overview

This is a Human Capital Management (HCM) system built with vanilla PHP, MySQL, Tailwind CSS, and JavaScript. The system focuses on compensation management, HR intelligence, payroll processing, and benefits administration.

## Database Commands

The system uses MySQL database `hcm_system`. Use these commands to interact with the database:

```bash
# Connect to MySQL and view database structure
"C:\xampp\mysql\bin\mysql.exe" -u root hcm_system

# Quick table descriptions
"C:\xampp\mysql\bin\mysql.exe" -u root hcm_system -e "DESCRIBE employees;"
"C:\xampp\mysql\bin\mysql.exe" -u root hcm_system -e "DESCRIBE departments;"
"C:\xampp\mysql\bin\mysql.exe" -u root hcm_system -e "DESCRIBE positions;"
"C:\xampp\mysql\bin\mysql.exe" -u root hcm_system -e "DESCRIBE employee_compensation;"

# Initialize database (if needed)
"C:\xampp\mysql\bin\mysql.exe" -u root < database/hcm_system.sql
"C:\xampp\mysql\bin\mysql.exe" -u root hcm_system < database/sample_data.sql
```

## Project Architecture

### Directory Structure
```
HCM/
├── api/                    # REST API endpoints
│   ├── auth.php           # Authentication (login, logout, token validation)
│   ├── dashboard.php      # Dashboard statistics and metrics
│   ├── employees.php      # Employee CRUD operations
│   ├── payroll.php        # Payroll management and calculations
│   └── profile.php        # User profile management
├── config/                # Configuration files
│   ├── auth.php          # JWT, sessions, permissions, CORS
│   └── database.php      # Database connection settings
├── database/             # Database schema and sample data
│   ├── hcm_system.sql    # Main database schema
│   └── sample_data.sql   # Sample/test data
├── includes/             # Core PHP classes and utilities
│   ├── ApiResponse.php   # Standardized API response formatting
│   ├── auth_helper.php   # Authentication helper functions
│   ├── Database.php      # Singleton database connection class
│   └── JWT.php          # JWT token handling
├── postman/              # API testing collection and documentation
├── views/                # Frontend PHP pages
│   ├── login.php        # Login page
│   ├── index.php        # Dashboard
│   ├── employees.php    # Employee management interface
│   ├── payroll.php      # Payroll management interface
│   └── includes/        # Shared view components
└── postman/              # Postman collection for API testing
```

### Database Architecture

The system uses a normalized MySQL database with these core entities:

- **users**: Authentication and user management
- **employees**: Core employee data and relationships
- **departments**: Organizational structure
- **positions**: Job titles and salary ranges
- **employee_compensation**: Salary and compensation details
- **employee_leaves**: Leave management
- **employee_insurance**: Benefits and insurance tracking

Key relationships:
- Users ↔ Employees (1:1 via user_id)
- Employees ↔ Departments (many:1)
- Employees ↔ Positions (many:1)
- Employees ↔ Compensation (1:many with is_active flag)

### Authentication & Authorization

- **JWT-based API authentication** with 24-hour access tokens and 7-day refresh tokens
- **Role-based permissions**: admin, hr, manager, employee
- **Session management** for web interface with 2-hour timeout
- **Rate limiting**: 100 requests per hour per IP
- **CORS support** for frontend integration

Roles and permissions are defined in `config/auth.php:USER_ROLES`.

## Development Workflow

### Running the Application

1. **Start XAMPP**: Ensure Apache and MySQL services are running
2. **Database Setup**: Import schema and sample data (see Database Commands above)
3. **Access Application**:
   - Web interface: `http://localhost/HCM/views/`
   - API endpoints: `http://localhost/HCM/api/`

### API Testing

Use the Postman collection in `postman/` directory:
1. Import `HCM_API_Collection.json`
2. Set environment variables:
   - `base_url`: `http://localhost/HCM/api`
3. Test authentication with default credentials:
   - Username: `admin`
   - Password: `admin123`

### Default Test Credentials

- **Admin**: username `admin`, password `admin123` (full system access)
- **Sample users**: `maria.santos` (HR Manager), `robert.garcia` (IT Manager), password `admin123`

## Code Patterns

### Database Access
- Use the singleton `Database` class in `includes/Database.php`
- PDO with prepared statements for all database operations
- Connection configuration in `config/database.php`

### API Responses
- All API endpoints use standardized responses via `ApiResponse` class
- Consistent JSON format with `success`, `message`, `timestamp`, and `data` fields
- HTTP status codes: 200 (success), 201 (created), 400 (validation), 401 (auth), 403 (forbidden), 404 (not found)

### Authentication Flow
1. Login via `POST /api/auth/login` returns JWT tokens
2. Include `Authorization: Bearer <token>` header in subsequent requests
3. Validate permissions using role-based access control in `config/auth.php`

### Security Measures
- Password hashing with PHP's `password_hash()`
- CSRF protection for web forms
- Input validation and sanitization
- SQL injection prevention via prepared statements
- Security headers configured in `config/auth.php:SECURITY_HEADERS`

## Key Features

- **Employee Management**: CRUD operations with detailed profiles, departments, and positions
- **Payroll System**: Salary calculations, deductions, and payroll period management
- **Compensation Planning**: Salary structures and performance-based compensation
- **Benefits Administration**: Insurance tracking and HMO management
- **Dashboard Analytics**: HR metrics and business intelligence
- **Leave Management**: Leave requests, approvals, and tracking
- **Role-based Access**: Granular permissions for different user types

## Testing & Debugging

- Debug files are available in root directory for testing specific components
- API endpoints can be tested individually using the Postman collection
- Database test scripts: `test_db.php`, `test-db-connection.php`
- Enable PHP error reporting for development debugging