# 🎯 DEPLOYMENT CHECKLIST

## Pre-Deployment Verification ✅

### Code Quality
- ✅ All routes protected with auth middleware
- ✅ Role-based authorization enforced
- ✅ Form Requests validate all inputs
- ✅ Controllers have error handling
- ✅ Comprehensive logging implemented
- ✅ Models have proper relationships
- ✅ Migrations are reversible
- ✅ Error pages created (403, 404)

### Security
- ✅ Superadmin routes behind `role:superadmin` middleware
- ✅ Password hashed with bcrypt
- ✅ Policies enforce authorization
- ✅ CSRF protection on all POST/PATCH/DELETE
- ✅ No hardcoded credentials
- ✅ Validation on all user inputs
- ✅ Exception handling prevents info leakage

### Database
- ✅ PostgreSQL configured as default
- ✅ Schema path set for Supabase
- ✅ All migrations have up/down methods
- ✅ Foreign keys defined
- ✅ Indexes on frequently queried columns
- ✅ Relationships defined in models

### Configuration
- ✅ `.env.example` created with all variables
- ✅ `APP_DEBUG=false` for production
- ✅ `APP_ENV=production`
- ✅ Session encryption enabled
- ✅ PayMongo configuration documented

---

## Deployment Steps

### 1. Environment Setup
```bash
# Copy example config
cp .env.example .env

# Update with your values
nano .env
# Set:
# - APP_KEY (run: php artisan key:generate)
# - DB credentials for Supabase
# - PayMongo keys
```

### 2. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 3. Database Migration
```bash
php artisan migrate --force
```

### 4. Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Storage Permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Web Server Configuration
Ensure public directory points to `public/` folder

### 7. SSL Certificate
Ensure HTTPS is enabled (Let's Encrypt recommended)

---

## Post-Deployment Testing

### User Flows
- [ ] **Consumer Registration**
  - Register as consumer
  - Verify email
  - Login successful
  - Can view dashboard

- [ ] **Farm Owner Registration**
  - Submit farm application
  - Application shows as pending
  - Superadmin can see pending request
  - Superadmin approves request
  - User account created
  - Farm owner can login

- [ ] **Payment Flow**
  - Click subscribe
  - Select plan (1/6/12 month)
  - PayMongo link opens
  - Complete payment
  - Webhook processes successfully
  - Subscription created in database
  - User sees success page with days remaining

### Authorization
- [ ] Anonymous user cannot access `/super-admin/dashboard`
- [ ] Consumer cannot access superadmin routes
- [ ] Client cannot approve farm requests
- [ ] Superadmin can approve/reject requests
- [ ] Accessing restricted route shows 403 error page

### Error Handling
- [ ] Validation errors display proper messages
- [ ] Database errors logged, user sees friendly message
- [ ] 404 page displays for missing resources
- [ ] 403 page displays for unauthorized access
- [ ] Logs in `storage/logs/laravel.log` contain events

---

## Monitoring

### Daily Checks
```bash
# Check recent errors
tail -50 storage/logs/laravel.log | grep -i error

# Check for failed jobs (if using queue)
php artisan queue:work

# Monitor disk space
df -h
```

### Weekly Tasks
- [ ] Review error logs for patterns
- [ ] Check payment webhook success rate
- [ ] Verify subscription status accuracy
- [ ] Monitor database performance

### Monthly Tasks
- [ ] Review user registration metrics
- [ ] Audit authorization logs
- [ ] Check for security vulnerabilities
- [ ] Update dependencies

---

## Rollback Plan

If issues occur:

```bash
# Restore previous database state
php artisan migrate:rollback

# Clear all caches
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear

# Restart PHP-FPM or web server
systemctl restart php-fpm
# or for Apache
systemctl restart apache2
```

---

## Performance Optimization (Post-Launch)

### Database Optimization
```bash
# Add indexes for frequent queries
php artisan migrate

# Check slow queries
EXPLAIN ANALYZE SELECT * FROM subscriptions WHERE user_id = 1;
```

### Caching Strategy
- Cache configuration: `php artisan config:cache`
- Cache routes: `php artisan route:cache`
- Cache views: `php artisan view:cache`

### Query Optimization
- Use eager loading: `with('subscriptions')`
- Implement pagination: `->paginate(20)`
- Add query indexes on foreign keys

---

## Disaster Recovery

### Backups
- [ ] Database backups daily (Supabase handles this)
- [ ] File uploads backed up (use S3 or Supabase Storage)
- [ ] `.env` file backed up securely

### Recovery Procedure
1. Restore database from backup
2. Verify file uploads accessible
3. Clear application cache
4. Test critical user flows

---

## Support Contacts

- **Laravel Documentation**: https://laravel.com/docs
- **Supabase Documentation**: https://supabase.com/docs
- **PayMongo Documentation**: https://developers.paymongo.com

---

## Sign-Off

- [ ] All tests passed
- [ ] Logs reviewed for errors
- [ ] Performance acceptable
- [ ] Security checklist complete
- [ ] Backup strategy confirmed
- [ ] Team trained on system

**Deployment Date**: ________________
**Deployed By**: ________________
**Verified By**: ________________

---

## Critical Alert Contacts

Ensure these are configured in your server alerts:
- PHP errors → log to `storage/logs/laravel.log`
- Database connection failures → notify admin
- Payment webhook failures → notify payment team
- User authentication failures → monitor for attacks
