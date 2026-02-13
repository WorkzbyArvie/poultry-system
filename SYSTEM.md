# Poultry Farm Management System

A comprehensive B2B2C e-commerce and farm management platform built with Laravel 12, Supabase (PostgreSQL), and Tailwind CSS.

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Technology Stack](#technology-stack)
3. [User Roles](#user-roles)
4. [Module Overview](#module-overview)
5. [Database Schema](#database-schema)
6. [Directory Structure](#directory-structure)
7. [Routes Reference](#routes-reference)
8. [Setup Instructions](#setup-instructions)
9. [Environment Configuration](#environment-configuration)

---

## System Overview

This system provides end-to-end management for poultry farm operations, including:

- **Farm Operations**: Flock management, vaccination schedules, daily monitoring
- **Inventory Management**: Supply tracking, stock alerts, supplier management
- **HR & Payroll**: Employee records, attendance, payroll processing
- **Finance**: Expense tracking, income records, profit/loss analysis
- **Logistics**: Driver management, delivery scheduling, COD collection
- **Sales**: Product catalog, order processing, customer management
- **Decision Support**: Real-time dashboards, reports, alerts

---

## Technology Stack

| Component | Technology | Version |
|-----------|------------|---------|
| Backend | Laravel | 12.49.0 |
| PHP | PHP | 8.5.2 |
| Database | PostgreSQL (Supabase) | 17.6 |
| Frontend | Blade + Tailwind CSS | 4.1 |
| Build Tool | Vite | 7.3.1 |
| Authentication | Laravel Breeze | - |
| Session | File-based | 120 min |

---

## User Roles

### SuperAdmin
- Full system access
- Manage all farm owners and their subscriptions
- View system-wide analytics
- Route prefix: `/superadmin/`

### Farm Owner
- Manage their own farm operations
- Access all modules (flocks, inventory, HR, finance, logistics)
- View farm-specific reports and DSS dashboard
- Route prefix: `/farm-owner/`

### Client (Customer)
- Place orders from product catalog
- Track order status and deliveries
- View order history
- Route prefix: `/client/`

---

## Module Overview

### 1. Flock Management
**Files**: `FlockController`, `Flock`, `FlockRecord` models

| Feature | Description |
|---------|-------------|
| Flock Registration | Create flocks with breed, type (layer/broiler), initial count |
| Daily Records | Log feed consumption, water, eggs, mortality |
| Age Tracking | Automatic age calculation from acquisition date |
| Performance Metrics | Mortality rate, production rate calculations |

### 2. Vaccination Management
**Files**: `VaccinationController`, `Vaccination` model

| Feature | Description |
|---------|-------------|
| Vaccine Scheduling | Plan vaccinations with due dates |
| Status Tracking | Pending, completed, overdue status |
| Flock Association | Link vaccinations to specific flocks |
| Reminders | Due/overdue alerts on dashboard |

### 3. Supply & Inventory
**Files**: `SupplyController`, `SupplyItem`, `StockTransaction` models

| Feature | Description |
|---------|-------------|
| Item Management | Track feeds, vaccines, equipment, etc. |
| Stock In/Out | Record purchases and consumption |
| Reorder Alerts | Low stock and expiry warnings |
| Transaction History | Full audit trail of stock movements |

### 4. Supplier Management
**Files**: `SupplierController`, `Supplier` model

| Feature | Description |
|---------|-------------|
| Supplier Registry | Company info, contact details |
| Category Classification | Feeds, vaccines, equipment, chicks |
| Payment Terms | COD, Net 15/30/60, credit limits |
| Outstanding Balance | Track payables per supplier |

### 5. Employee Management
**Files**: `EmployeeController`, `Employee` model

| Feature | Description |
|---------|-------------|
| Employee Records | Personal info, contact, address |
| Employment Details | Department, position, hire date |
| Government IDs | SSS, PhilHealth, Pag-IBIG, TIN |
| Status Tracking | Active, on leave, inactive |

### 6. Attendance Management
**Files**: `AttendanceController`, `Attendance` model

| Feature | Description |
|---------|-------------|
| Daily Attendance | Bulk entry by date |
| Status Types | Present, absent, late, half-day, leave |
| Time Tracking | In/out times, overtime hours |
| Summary View | Monthly attendance reports |

### 7. Payroll Management
**Files**: `PayrollController`, `Payroll` model

| Feature | Description |
|---------|-------------|
| Payroll Generation | Based on attendance and daily rate |
| Deductions | SSS, PhilHealth, Pag-IBIG, tax |
| Payment Status | Pending, paid tracking |
| Net Pay Calculation | Automatic computation |

### 8. Expense Tracking
**Files**: `ExpenseController`, `Expense` model

| Feature | Description |
|---------|-------------|
| Expense Categories | Feeds, utilities, salaries, transport |
| Receipt Tracking | Reference numbers |
| Supplier Linking | Associate with suppliers |
| Payment Methods | Cash, bank, GCash, credit |

### 9. Income Records
**Files**: `IncomeController`, `IncomeRecord` model

| Feature | Description |
|---------|-------------|
| Income Sources | Egg sales, chicken sales, order payments |
| Customer Tracking | Link to customers |
| Payment Status | Received, receivable |
| Daily/Monthly Totals | Aggregated views |

### 10. Driver Management
**Files**: `DriverController`, `Driver` model

| Feature | Description |
|---------|-------------|
| Driver Registry | Contact info, address |
| Vehicle Info | Type, plate number |
| License Tracking | Expiry alerts |
| Performance | Total deliveries, rating |

### 11. Delivery Management
**Files**: `DeliveryController`, `Delivery` model

| Feature | Description |
|---------|-------------|
| Delivery Scheduling | Date, address, customer info |
| Driver Assignment | Assign available drivers |
| Status Tracking | Pending → Assigned → Dispatched → Delivered |
| COD Collection | Track cash on delivery |

### 12. Reports & DSS
**Files**: `ReportController`

| Report | Content |
|--------|---------|
| DSS Dashboard | Key metrics, alerts, quick actions |
| Financial Report | Income vs expenses, profit margin |
| Production Report | Flock performance, egg production |
| Delivery Report | Success rate, driver performance |
| Payroll Report | Monthly payroll summary |

---

## Database Schema

### Core Tables (13)
```
users              - All system users with role field
roles              - Role definitions (future RBAC)
farm_owners        - Extended profile for farm owners
subscriptions      - Subscription plans and status
products           - Product catalog
orders             - Customer orders
order_items        - Order line items
staff              - Staff members (legacy)
documents          - File attachments
notifications      - System notifications
cache              - Laravel cache table
jobs               - Queue jobs
migrations         - Migration tracking
```

### New Module Tables (12)
```
flocks             - Flock batches (breed, type, count)
flock_records      - Daily flock monitoring
vaccinations       - Vaccination schedules
suppliers          - Supplier registry
supply_items       - Inventory items
stock_transactions - Stock in/out records
employees          - Employee records
attendance         - Daily attendance
payroll            - Payroll records
expenses           - Expense tracking
income_records     - Income records
drivers            - Delivery drivers
deliveries         - Delivery schedules
```

### Key Relationships
```
User (farm_owner) ──┬── Flocks ──── FlockRecords
                    │         └─── Vaccinations
                    ├── Suppliers ── SupplyItems ── StockTransactions
                    ├── Employees ── Attendance
                    │            └── Payroll
                    ├── Expenses
                    ├── IncomeRecords
                    ├── Drivers ──── Deliveries
                    └── Orders ───── OrderItems
                                └─── Deliveries
```

---

## Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── FlockController.php
│   │   ├── VaccinationController.php
│   │   ├── SupplyController.php
│   │   ├── SupplierController.php
│   │   ├── EmployeeController.php
│   │   ├── AttendanceController.php
│   │   ├── PayrollController.php
│   │   ├── ExpenseController.php
│   │   ├── IncomeController.php
│   │   ├── DriverController.php
│   │   ├── DeliveryController.php
│   │   └── ReportController.php
│   └── Requests/
│       └── (Form request validation classes)
├── Models/
│   ├── Flock.php
│   ├── FlockRecord.php
│   ├── Vaccination.php
│   ├── Supplier.php
│   ├── SupplyItem.php
│   ├── StockTransaction.php
│   ├── Employee.php
│   ├── Attendance.php
│   ├── Payroll.php
│   ├── Expense.php
│   ├── IncomeRecord.php
│   ├── Driver.php
│   └── Delivery.php
└── Policies/

database/
├── migrations/
│   ├── 2026_02_10_create_flocks_table.php
│   ├── 2026_02_10_create_flock_records_table.php
│   ├── ... (12 new migrations)
└── seeders/

resources/views/
├── farmowner/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── partials/
│   │   └── sidebar.blade.php
│   ├── flocks/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── show.blade.php
│   │   └── edit.blade.php
│   ├── vaccinations/
│   ├── supplies/
│   ├── suppliers/
│   ├── employees/
│   ├── attendance/
│   ├── payroll/
│   ├── expenses/
│   ├── income/
│   ├── drivers/
│   ├── deliveries/
│   └── reports/
├── client/
├── superadmin/
└── layouts/
```

---

## Routes Reference

### Farm Owner Routes (`/farm-owner/`)

| Method | URI | Controller@Action | Description |
|--------|-----|-------------------|-------------|
| GET | /flocks | FlockController@index | List flocks |
| GET | /flocks/create | FlockController@create | New flock form |
| POST | /flocks | FlockController@store | Create flock |
| GET | /flocks/{flock} | FlockController@show | View flock |
| PUT | /flocks/{flock} | FlockController@update | Update flock |
| POST | /flocks/{flock}/record | FlockController@addDailyRecord | Add daily record |
| GET | /vaccinations | VaccinationController@index | List vaccinations |
| POST | /vaccinations | VaccinationController@store | Create vaccination |
| POST | /vaccinations/{id}/complete | VaccinationController@markComplete | Mark done |
| GET | /supplies | SupplyController@index | List inventory |
| POST | /supplies/{item}/stock-in | SupplyController@stockIn | Record purchase |
| POST | /supplies/{item}/stock-out | SupplyController@stockOut | Record usage |
| GET | /supplies/alerts | SupplyController@alerts | Low stock alerts |
| GET | /employees | EmployeeController@index | List employees |
| GET | /attendance | AttendanceController@index | Daily attendance |
| POST | /attendance/bulk | AttendanceController@bulkStore | Bulk save |
| GET | /payroll | PayrollController@index | Payroll list |
| POST | /payroll/generate | PayrollController@generate | Generate payroll |
| GET | /expenses | ExpenseController@index | Expense list |
| GET | /income | IncomeController@index | Income list |
| GET | /drivers | DriverController@index | Driver list |
| GET | /deliveries | DeliveryController@index | Delivery list |
| POST | /deliveries/{id}/dispatch | DeliveryController@dispatch | Mark dispatched |
| POST | /deliveries/{id}/complete | DeliveryController@complete | Mark delivered |
| GET | /reports | ReportController@index | Reports menu |
| GET | /reports/dashboard | ReportController@dashboard | DSS dashboard |
| GET | /reports/financial | ReportController@financial | Financial report |
| GET | /reports/production | ReportController@production | Production report |

---

## Setup Instructions

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL (Supabase account)

### Installation

```bash
# Clone repository
git clone <repository-url>
cd Poultry-System

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database (see Environment Configuration)

# Run migrations
php artisan migrate

# Seed admin user
php artisan db:seed --class=AdminUserSeeder

# Build assets
npm run build

# Start server
php artisan serve
```

### Default Credentials
```
SuperAdmin:
  Email: superadmin@poultry.com
  Password: SuperAdmin@2026
```

---

## Environment Configuration

### Database (Supabase PostgreSQL)
```env
DB_CONNECTION=pgsql
DB_HOST=db.mcqpgsqgicxvzvskuxwl.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=<your-password>
```

### Session & Cache
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_STORE=file
```

### Application
```env
APP_NAME="Poultry Farm System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

---

## Development Notes

### Adding New Modules
1. Create migration: `php artisan make:migration create_<table>_table`
2. Create model: `php artisan make:model <ModelName>`
3. Create controller: `php artisan make:controller <Name>Controller`
4. Add routes in `routes/web.php`
5. Create views in `resources/views/farmowner/<module>/`

### View Layout Pattern
All farm owner views extend the layout:
```blade
@extends('farmowner.layouts.app')

@section('title', 'Page Title')
@section('header', 'Page Header')
@section('subheader', 'Optional subheader')

@section('header-actions')
<!-- Action buttons -->
@endsection

@section('content')
<!-- Page content -->
@endsection
```

### Controller Pattern
```php
class ExampleController extends Controller
{
    public function index()
    {
        $items = Model::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);
            
        return view('farmowner.module.index', compact('items'));
    }
}
```

---

## Support

For issues or questions, contact the development team.

---

*Last Updated: {{ now()->format('Y-m-d') }}*
