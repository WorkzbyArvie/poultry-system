# 🏗️ SYSTEM ARCHITECTURE OVERVIEW

## Application Structure

```
Poultry-System/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Request handlers
│   │   ├── Middleware/         # EnsureUserRole.php
│   │   └── Requests/           # Form request validation
│   ├── Models/                 # Eloquent models with relationships
│   ├── Policies/               # ClientRequestPolicy.php
│   └── Providers/              # AppServiceProvider (policy registration)
├── routes/
│   ├── web.php                 # Protected routes with middleware
│   ├── auth.php                # Authentication routes
│   └── console.php             # Artisan commands
├── database/
│   ├── migrations/             # Database schema changes
│   ├── seeders/                # Test data
│   └── factories/              # Model factories
├── resources/
│   ├── views/                  # Blade templates
│   ├── css/                    # Styling
│   └── js/                     # JavaScript
├── bootstrap/app.php           # Exception handling
├── config/
│   ├── app.php                 # Application config
│   ├── auth.php                # Authentication config
│   ├── database.php            # Database config
│   └── services.php            # External service config
├── .env.example                # Configuration template
├── FIXES.md                    # Detailed fix documentation
├── IMPLEMENTATION_SUMMARY.md   # Executive summary
└── DEPLOYMENT_CHECKLIST.md     # Deployment guide
```

---

## User Roles & Permissions

### 🔐 Superadmin
**Capabilities**:
- View all pending farm applications
- Approve farm applications (creates user account)
- Reject farm applications
- View egg monitoring records
- View chicken monitoring records
- Manage staff accounts

**Routes**:
- `/super-admin/dashboard`
- `/admin/verifications`
- `/admin/verifications/{id}/approve`
- `/admin/verifications/{id}/reject`
- `/super-admin/eggs`
- `/super-admin/chickens`
- `/super-admin/staff/create`

### 👨‍🌾 Client (Farm Owner)
**Capabilities**:
- View own dashboard
- Manage subscription
- Track inventory
- Monitor farm activities
- Upload farm data

**Routes**:
- `/client/dashboard`
- `/subscribe`
- `/profile`
- `/profile/update`

### 👤 Consumer
**Capabilities**:
- View own dashboard
- Purchase eggs from listed farms
- View order history
- Manage profile

**Routes**:
- `/dashboard`
- `/profile`
- `/profile/update`

---

## Data Flow

### 1. Client (Farm Owner) Registration
```
Client Registration Form
    ↓
ClientRegistrationRequest (validates)
    ↓
ClientRequestController::store()
    ↓
Files uploaded to storage
    ↓
ClientRequest model created (status: pending)
    ↓
Superadmin sees in verifications dashboard
```

### 2. Client Approval Flow
```
Superadmin views pending applications
    ↓
Superadmin clicks "Approve"
    ↓
SuperAdminController::approveVerification()
    ↓
ClientRequestPolicy::approve() checks role
    ↓
User model created from ClientRequest data
    ↓
Logs recorded for audit trail
    ↓
ClientRequest status updated to "accepted"
    ↓
Client can now login
```

### 3. Consumer Registration
```
Consumer Registration Form
    ↓
ConsumerRegistrationRequest (validates)
    ↓
ConsumerRegistrationController::store()
    ↓
User model created with role: consumer
    ↓
User auto-logged in
    ↓
Redirected to dashboard
```

### 4. Payment & Subscription
```
User clicks "Subscribe"
    ↓
SubscriptionController::pay()
    ↓
PayMongo API creates payment link
    ↓
User completes payment
    ↓
PayMongo webhook calls handleWebhook()
    ↓
Subscription model created
    ↓
User role updated if needed
    ↓
Success page shows expiration date
```

---

## Key Components

### Middleware Stack

```
Route Request
    ↓
[web] - Cookie sessions
    ↓
[auth] - Authentication check
    ↓
[verified] - Email verification check
    ↓
[role:superadmin] - Role authorization
    ↓
Controller Method
```

### Authentication Flow

```
Login Form
    ↓
Auth::attempt() validates credentials
    ↓
Session created with user_id
    ↓
Request includes session token
    ↓
Auth middleware verifies session
    ↓
Auth::user() returns authenticated user
```

### Authorization Flow

```
Protected Action (e.g., approve farm)
    ↓
$this->authorize('approve', $clientRequest)
    ↓
ClientRequestPolicy::approve() checked
    ↓
Policy returns true/false
    ↓
If false → abort(403)
    ↓
If true → proceed
```

---

## Database Schema Relationships

```
users (laravel.app_users)
├── id (PK)
├── email (unique)
├── role (superadmin|client|consumer)
├── password (hashed)
└── status (active|inactive)
    │
    ├─→ subscriptions (1:Many)
    │   ├── id
    │   ├── user_id (FK)
    │   ├── plan (1_month|6_month|12_month)
    │   ├── status (active|expired)
    │   ├── expires_at
    │   └── payment_reference
    │
    └─→ inventory (1:Many)
        ├── id
        ├── user_id (FK)
        ├── item_name
        └── quantity

client_requests
├── id (PK)
├── owner_name
├── farm_name
├── email (unique)
├── password (hashed)
├── status (pending|accepted|rejected)
└── created_at

egg_monitoring (laravel.egg_monitoring)
├── id
├── date_collected
├── batch_source
├── good_trays
├── broken_eggs
└── recorded_by

chicken_monitoring (laravel.chicken_monitoring)
├── id
├── date_logged
├── batch_name
├── current_count
├── mortality_count
├── health_status
└── recorded_by
```

---

## Error Handling Strategy

### Validation Errors
```
Form Submission
    ↓
Form Request validates
    ↓
If invalid → back()->withErrors()
    ↓
Display error messages to user
```

### Authorization Errors
```
Unauthorized action attempt
    ↓
Policy checks fail
    ↓
abort(403) triggered
    ↓
Exception handler catches
    ↓
Show 403.blade.php error page
```

### Database Errors
```
Query fails (e.g., unique constraint)
    ↓
Exception caught in try-catch
    ↓
Logged with context
    ↓
User sees friendly error message
    ↓
Developers can debug in logs
```

### Payment Errors
```
PayMongo API fails
    ↓
HTTP request fails
    ↓
Caught in try-catch
    ↓
User sees "Payment failed, try again"
    ↓
Logged for investigation
```

---

## Security Model

### Authentication
- Session-based authentication using Laravel's built-in system
- Password hashed with bcrypt (configurable rounds)
- Session encryption enabled in production
- CSRF tokens on all state-changing requests

### Authorization
- Role-based access control (superadmin, client, consumer)
- Policy-based fine-grained permissions
- Middleware enforces role requirements
- $this->authorize() in controllers

### Data Protection
- Password fields hidden from serialization
- Sensitive errors don't leak to client
- SQL injection prevented by Eloquent ORM
- XSS prevention through Blade escaping

### Logging & Auditing
- All administrative actions logged (approvals, rejections)
- User registration events logged
- Payment processing logged
- Errors logged with full context

---

## Deployment Architecture

```
Load Balancer / Reverse Proxy (Nginx)
    ↓
PHP-FPM (Application Server)
    ↓
Laravel Application
    ↓
PostgreSQL (Supabase)
    ↓
Storage (S3 / Supabase Storage)
    ↓
External Services:
├── PayMongo (Payments)
├── Email Provider (Notifications)
└── Monitoring Tools
```

---

## Performance Considerations

### Database Optimization
- **Indexes**: Foreign keys, status fields, date ranges
- **Eager Loading**: `User::with('subscriptions')`
- **Pagination**: `paginate(20)` instead of `get()`
- **Query Caching**: For frequently accessed data

### Application Optimization
- **Configuration Caching**: `config:cache`
- **Route Caching**: `route:cache`
- **View Compilation**: `view:cache`
- **Class Autoloading**: Optimize with Composer

### Monitoring Metrics
- Response time (target: < 200ms)
- Database query count (target: < 5 per request)
- Error rate (target: < 0.1%)
- Payment webhook success (target: > 99%)

---

## Scaling Strategy

### Horizontal Scaling
1. Multiple PHP-FPM instances behind load balancer
2. Shared PostgreSQL database (Supabase handles this)
3. Shared file storage (S3 or Supabase Storage)
4. Session storage in Redis (for clustering)

### Vertical Scaling
1. Increase PHP-FPM workers
2. Optimize database queries
3. Add database indexes
4. Implement caching layer

### Queue Implementation (Future)
1. Move email to queue
2. Process webhooks asynchronously
3. Generate reports in background
4. Use Laravel Queue with Redis/SQS

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| **Framework** | Laravel 12.0 |
| **Language** | PHP 8.2+ |
| **Database** | PostgreSQL (Supabase) |
| **Authentication** | Laravel Auth + Sessions |
| **Frontend Build** | Vite |
| **CSS** | Tailwind CSS |
| **Payment Gateway** | PayMongo |
| **File Storage** | Supabase Storage / S3 |
| **Testing** | PHPUnit, Pest |
| **Web Server** | Nginx + PHP-FPM |

---

## Development Workflow

### Local Development
```bash
# Install dependencies
composer install
npm install

# Generate app key
php artisan key:generate

# Create database
php artisan migrate

# Start development server
php artisan serve
npm run dev
```

### Testing
```bash
# Run tests
php artisan test

# Run with coverage
php artisan test --coverage
```

### Deployment
```bash
# Production build
npm run build
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
```

---

## Key Files Reference

| File | Purpose |
|------|---------|
| `routes/web.php` | All application routes |
| `app/Http/Controllers/` | Request handlers |
| `app/Http/Middleware/EnsureUserRole.php` | Role validation |
| `app/Models/` | Database models |
| `app/Policies/ClientRequestPolicy.php` | Authorization rules |
| `database/migrations/` | Schema changes |
| `.env.example` | Configuration template |
| `bootstrap/app.php` | Exception handling |

---

## Troubleshooting Guide

### "Access Denied" (403)
- Check user role: `Auth::user()->role`
- Check policy in `ClientRequestPolicy`
- Check middleware in `routes/web.php`

### "Page Not Found" (404)
- Check route definition in `routes/web.php`
- Check model exists in database
- Check view file exists

### "Payment Failed"
- Check PayMongo credentials in `.env`
- Check webhook URL is public
- Check logs: `tail storage/logs/laravel.log`

### "Database Connection Error"
- Verify `.env` database credentials
- Ensure PostgreSQL is running
- Check Supabase connection string

---

For detailed implementation information, see **[FIXES.md](./FIXES.md)**.
