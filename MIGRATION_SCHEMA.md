# B2B2C Farm Platform - Complete Database Schema & Migration Guide

## Overview
Performance-optimized database architecture with indexed queries, eager loading support, and proper relationships for a multi-role e-commerce farm platform.

---

## Migrations to Create (In Execution Order)

### Keep (Laravel Default - DO NOT DELETE)
1. `0001_01_01_000000_create_users_table.php` - Users, password resets, sessions
2. `0001_01_01_000001_create_cache_table.php` - Cache storage
3. `0001_01_01_000002_create_jobs_table.php` - Job queue management

### Delete (Old Implementation)
```
2026_02_01_100230_create_client_requests_table.php
2026_02_01_122700_add_contact_info_to_users_table.php
2026_02_03_135319_add_status_to_users_table.php
2026_02_03_144933_add_auth_fields_to_client_requests.php
2026_02_06_000000_add_missing_columns_to_users.php
```

### New Migrations (In Order)

---

## 1. `2026_02_09_100000_create_roles_table.php`
**Models**: `Role.php`

Creates roles lookup table for RBAC (Role-Based Access Control).

**Table: `roles`**
- `id` (PK)
- `name` (VARCHAR, UNIQUE, INDEX)
- `display_name` (VARCHAR)
- `description` (TEXT)
- `timestamps`

**Indexed Columns**: `name`

**Performance Notes**: Enables efficient role lookups and filtering

---

## 2. `2026_02_09_100100_refactor_users_table.php`
**Models**: Enhanced `User.php`

Refactors existing users table to support multi-role authentication and KYC verification.

**Table: `users` (Modifications)**
- `phone` (VARCHAR, AFTER email)
- `status` (ENUM: active|inactive|suspended|pending_verification, INDEX)
- `phone_verified_at` (TIMESTAMP)
- `last_login_at` (TIMESTAMP)
- `kyc_verified` (BOOLEAN, INDEX, DEFAULT false)

**Indexed Columns**: `email`, `role`, `status`, `kyc_verified`

**Performance Notes**: 
- Indexes on `role` enable quick role-based queries
- `status` index for filtering active/inactive users
- `kyc_verified` index for permission checks

---

## 3. `2026_02_09_100200_create_farm_owners_table.php`
**Models**: `FarmOwner.php`

Business profiles for farm/livestock sellers with location and subscription tracking.

**Table: `farm_owners`**
- `id` (PK)
- `user_id` (FK->users, UNIQUE, INDEX)
- `farm_name` (VARCHAR)
- `farm_address` (TEXT)
- `city` (VARCHAR, INDEX)
- `province` (VARCHAR, INDEX)
- `postal_code` (VARCHAR)
- `latitude` (DECIMAL 10,8)
- `longitude` (DECIMAL 11,8)
- `business_registration_number` (VARCHAR, UNIQUE)
- `permit_status` (ENUM: pending|approved|rejected|expired, INDEX)
- `permit_expiry_date` (DATE)
- `subscription_status` (ENUM: active|inactive|suspended, INDEX)
- `monthly_revenue` (DECIMAL 15,2)
- `total_products` (INT)
- `total_orders` (INT)
- `average_rating` (DECIMAL 3,2, INDEX)
- `timestamps`, `soft_deletes`

**Indexed Columns**: `user_id`, `permit_status`, `subscription_status`, `city`, `province`, `average_rating`

**Query Scopes**:
- `Active()` - active subscriptions
- `PermitApproved()` - verified sellers
- `TopRated()` - by rating
- `ByCity()` / `ByProvince()` - location-based
- `WithinDistance()` - geo-proximity queries
- `WithStats()` - aggregated data

---

## 4. `2026_02_09_100300_create_subscriptions_table.php`
**Models**: `Subscription.php`

PayMongo-integrated subscription plans with tiered product/order limits.

**Table: `subscriptions`**
- `id` (PK)
- `farm_owner_id` (FK->farm_owners, INDEX)
- `plan_type` (ENUM: starter|professional|enterprise, INDEX)
- `monthly_cost` (DECIMAL 10,2)
- `product_limit` (INT)
- `order_limit` (INT)
- `commission_rate` (DECIMAL 5,2) - Platform commission percentage
- `status` (ENUM: active|paused|cancelled|expired, INDEX)
- `started_at` (TIMESTAMP)
- `ends_at` (TIMESTAMP, INDEX)
- `renewal_at` (TIMESTAMP)
- `paymongo_subscription_id` (VARCHAR, UNIQUE)
- `paymongo_payment_method_id` (VARCHAR)
- `timestamps`, `soft_deletes`

**Indexed Columns**: `farm_owner_id`, `status`, `plan_type`, `ends_at`

**Query Scopes**:
- `Active()` - unexpired active subscriptions
- `Expiring()` - expiring within 7 days
- `ByPlan()` - filter by plan type
- `Expired()` - past due subscriptions
- `WithFarmOwner()` - eager load

---

## 5. `2026_02_09_100400_create_products_table.php`
**Models**: `Product.php`

Comprehensive product/livestock catalog with inventory, pricing, and analytics.

**Table: `products`**
- `id` (PK)
- `farm_owner_id` (FK->farm_owners, INDEX)
- `sku` (VARCHAR, UNIQUE, INDEX)
- `name` (VARCHAR)
- `description` (TEXT)
- `category` (ENUM: live_stock|breeding|fighting_cock|eggs|feeds|equipment|other, INDEX)
- `status` (ENUM: active|inactive|out_of_stock, INDEX)
- `quantity_available` (INT, INDEX)
- `quantity_sold` (INT)
- `price` (DECIMAL 12,2)
- `cost_price` (DECIMAL 12,2)
- `attributes` (JSON) - {breed, age, weight, health_status, etc}
- `unit` (VARCHAR) - piece|kg|liter|dozen|etc
- `minimum_order` (INT)
- `discount_percentage` (DECIMAL 5,2)
- `image_url` (VARCHAR)
- `image_urls` (JSON) - Array of URLs for galleries
- `view_count` (INT)
- `favorite_count` (INT)
- `average_rating` (DECIMAL 3,2, INDEX)
- `review_count` (INT)
- `published_at` (TIMESTAMP, INDEX)
- `timestamps`, `soft_deletes`

**Indexed Columns**: `farm_owner_id`, `category`, `status`, `sku`, `quantity_available`, `average_rating`, `published_at`

**Query Scopes**:
- `Active()` - sale-ready items
- `ByCategory()` - category filtering
- `ByFarmOwner()` - seller's products
- `Available()` - in-stock items
- `Popular()` - by view count
- `TopRated()` - by ratings
- `WithFarmOwner()` - eager load farm details
- `SearchByName()` - full-text search
- `ByPriceRange()` - price filtering
- `OutOfStock()` - inventory check
- `Published()` - visible items

---

## 6. `2026_02_09_100500_create_orders_table.php`
**Models**: `Order.php`

Consumer purchase orders with PayMongo payment tracking and delivery status.

**Table: `orders`**
- `id` (PK)
- `order_number` (VARCHAR, UNIQUE, INDEX)
- `consumer_id` (FK->users, INDEX)
- `farm_owner_id` (FK->farm_owners, INDEX)
- `subtotal` (DECIMAL 12,2)
- `shipping_cost` (DECIMAL 10,2)
- `tax` (DECIMAL 10,2)
- `discount` (DECIMAL 10,2)
- `total_amount` (DECIMAL 12,2)
- `status` (ENUM: pending|confirmed|processing|ready_for_pickup|shipped|delivered|cancelled|refunded, INDEX)
- `payment_status` (ENUM: unpaid|partial|paid|refunded)
- `payment_method` (VARCHAR) - paymongo|cod|etc
- `paymongo_payment_id` (VARCHAR, UNIQUE)
- `delivery_type` (ENUM: delivery|pickup)
- `delivery_address` (TEXT)
- `delivery_city` (VARCHAR)
- `delivery_province` (VARCHAR)
- `delivery_postal_code` (VARCHAR)
- `scheduled_delivery_at` (TIMESTAMP)
- `delivered_at` (TIMESTAMP, INDEX)
- `notes` (TEXT)
- `item_count` (INT)
- `timestamps`, `soft_deletes`

**Indexed Columns**: `consumer_id`, `farm_owner_id`, `order_number`, `status`, `payment_status`, `created_at`, `delivered_at`

**Query Scopes**:
- `ForConsumer()` - buyer's orders
- `ForFarmOwner()` - seller's received orders
- `Pending()` - awaiting confirmation
- `Confirmed()` / `Processing()` / `Delivered()` - status filters
- `Paid()` / `Unpaid()` - payment filters
- `ByStatus()` / `ByPaymentStatus()` - flexible filtering
- `WithItems()` - eager load order items
- `RecentFirst()` - chronological sorting
- `ByDeliveryType()` - delivery vs pickup
- `ByDateRange()` - temporal filtering

---

## 7. `2026_02_09_100600_create_order_items_table.php`
**Models**: `OrderItem.php`

Line items for orders with captured product state at purchase time.

**Table: `order_items`**
- `id` (PK)
- `order_id` (FK->orders, INDEX)
- `product_id` (FK->products, INDEX)
- `quantity` (INT)
- `unit_price` (DECIMAL 12,2)
- `total_price` (DECIMAL 12,2)
- `product_attributes` (JSON) - Snapshot of product state at order time
- `refunded_at` (TIMESTAMP)
- `timestamps`

**Indexed Columns**: `order_id`, `product_id`

**Performance Notes**: Prevents issues from product data changes after purchase

---

## 8. `2026_02_09_100700_create_staff_table.php`
**Models**: `Staff.php`

Admin team members with role-based permissions and farm assignment.

**Table: `staff`**
- `id` (PK)
- `user_id` (FK->users, UNIQUE, INDEX)
- `farm_owner_id` (FK->farm_owners, INDEX, NULLABLE)
- `created_by` (FK->users)
- `staff_role` (ENUM: super_admin|admin|manager|warehouse|delivery|support, INDEX)
- `status` (ENUM: active|inactive|suspended, INDEX)
- `permissions` (JSON) - Array of permission strings
- `assigned_at` (TIMESTAMP)
- `last_activity_at` (TIMESTAMP)
- `timestamps`, `soft_deletes`

**Indexed Columns**: `user_id`, `farm_owner_id`, `staff_role`, `status`

**Query Scopes**:
- `Active()` - enabled staff
- `ByRole()` - role filtering
- `ByFarmOwner()` - farm-specific staff
- `SuperAdmins()` - platform admins
- `WithUser()` - eager load user details

**Methods**:
- `hasPermission()` - permission checking
- `grantPermission()` / `revokePermission()` - dynamic permissions

---

## 9. `2026_02_09_100800_create_documents_table.php`
**Models**: `Document.php`

KYC document storage with verification tracking (National IDs, permits, certificates).

**Table: `documents`**
- `id` (PK)
- `user_id` (FK->users, INDEX)
- `farm_owner_id` (FK->farm_owners, INDEX, NULLABLE)
- `document_type` (ENUM: national_id|passport|business_permit|tax_certificate|health_certificate|other, INDEX)
- `document_name` (VARCHAR)
- `file_path` (VARCHAR) - storage path
- `file_name` (VARCHAR)
- `mime_type` (VARCHAR)
- `file_size` (BIGINT)
- `status` (ENUM: pending|verified|rejected|expired, INDEX)
- `rejection_reason` (TEXT)
- `expiry_date` (DATE)
- `verified_at` (TIMESTAMP)
- `verified_by` (FK->users, NULLABLE)
- `timestamps`, `soft_deletes`

**Indexed Columns**: `user_id`, `farm_owner_id`, `document_type`, `status`, `created_at`

**Query Scopes**:
- `Pending()` / `Verified()` / `Rejected()` - status filters
- `Expired()` - past-due documents
- `ByUser()` / `ByFarmOwner()` - owner filters
- `ByType()` - document type filtering
- `NeedsVerification()` - pending or expired

**Methods**:
- `markAsVerified()` - approve document
- `reject()` - reject with reason
- `isExpired()` - expiry checking

---

## 10. `2026_02_09_100900_create_notifications_table.php`
**Models**: `Notification.php`

Email/SMS/In-app/Push notification queue with delivery tracking.

**Table: `notifications`**
- `id` (PK)
- `user_id` (FK->users, INDEX)
- `title` (VARCHAR)
- `message` (TEXT)
- `type` (ENUM: order|payment|product|promotion|system|alert, INDEX)
- `channel` (ENUM: email|sms|in_app|push, INDEX)
- `data` (JSON) - Contextual data {order_id, product_id, etc}
- `is_read` (BOOLEAN, INDEX)
- `read_at` (TIMESTAMP)
- `sent_at` (TIMESTAMP)
- `external_id` (VARCHAR) - For email service provider tracking
- `status` (ENUM: pending|sent|failed|bounced, INDEX)
- `failure_reason` (TEXT)
- `timestamps`

**Indexed Columns**: `user_id`, `type`, `channel`, `is_read`, `created_at`

**Query Scopes**:
- `ForUser()` - user's notifications
- `Unread()` / `Read()` - read status filters
- `ByType()` / `ByChannel()` - filtering
- `Sent()` / `Failed()` / `Pending()` - delivery status
- `Recent()` - time range filtering
- `OrderByRecent()` - chronological sorting

**Methods**:
- `markAsRead()` / `markAsUnread()` - read tracking
- `markAsSent()` / `markAsFailed()` - delivery status updates

---

## Models Created

### 1. **User.php** (Enhanced)
- Multi-role support (farm_owner, consumer, super_admin, staff)
- Polymorphic relationships to FarmOwner/Staff
- KYC verification tracking
- Query optimization scopes

**Key Relationships**:
```php
hasOne(FarmOwner)
hasOne(Staff)
hasMany(Order, 'consumer_id')
hasMany(Notification)
hasMany(Document)
hasMany(Staff, 'created_by')
```

### 2. **Role.php** (New)
- Lookup table for RBAC
- Simple relationships

### 3. **FarmOwner.php** (New)
- Business profile with location (lat/lng for geo-queries)
- Permit tracking
- Aggregated stats (revenue, products, orders, rating)
- Geo-proximity scope using Haversine formula

**Key Scopes**: `Active()`, `PermitApproved()`, `TopRated()`, `WithinDistance()`

### 4. **Subscription.php** (Refactored)
- PayMongo integration
- Tiered plans (starter/professional/enterprise)
- Commission tracking
- Expiry monitoring

**Key Scopes**: `Active()`, `Expiring()`, `Expired()`

### 5. **Product.php** (New)
- Comprehensive inventory management
- Multi-image gallery (JSON)
- Flexible attributes (breed, age, weight, etc via JSON)
- Analytics (views, favorites, ratings)
- Category filtering with enums

**Key Scopes**: `Active()`, `Popular()`, `TopRated()`, `SearchByName()`, `ByPriceRange()`

### 6. **Order.php** (New)
- Consumer purchase orders
- PayMongo payment tracking
- Delivery type support (delivery/pickup)
- Order status workflow

**Key Scopes**: `ForConsumer()`, `ForFarmOwner()`, `Paid()`, `ByStatus()`

### 7. **OrderItem.php** (New)
- Order line items
- Product state snapshot (JSON attributes)
- Refund tracking

### 8. **Staff.php** (New)
- Admin/delivery/support team profiles
- Role-based access with JSON permissions
- Activity tracking

**Key Scopes**: `Active()`, `ByRole()`, `SuperAdmins()`

### 9. **Document.php** (New)
- KYC document verification
- File tracking (path, size, mime type)
- Expiry management

**Key Scopes**: `Pending()`, `Verified()`, `Expired()`, `NeedsVerification()`

### 10. **Notification.php** (New)
- Multi-channel notifications (email, SMS, in-app, push)
- Delivery tracking
- Type-based routing

**Key Scopes**: `Unread()`, `ByChannel()`, `Sent()`, `Failed()`

---

## Performance Optimization Strategies

### Eager Loading Recommendations

```php
// Products with farm owner
Product::with('farmOwner:id,farm_name,average_rating')->active()->get()

// Orders with all data
Order::with(['items.product', 'consumer:id,name,email', 'farmOwner:id,farm_name'])->get()

// Farm owners with stats
FarmOwner::with('subscription', 'products')->topRated()->get()

// Notifications with context
Notification::with('user:id,name,email')->unread()->get()
```

### Index Strategies

1. **Foreign Keys**: Always indexed for joins (user_id, farm_owner_id, order_id, product_id)

2. **Status Columns**: Indexed for WHERE clauses (status, payment_status, state, is_read)

3. **Search/Filter**: Indexed (sku, email, category, permission-related fields)

4. **Sorting/Range**: Indexed (created_at, ends_at, average_rating, quantity_available)

5. **Composite Indexes** (Consider for common queries):
   - `(farm_owner_id, status, created_at)` on orders
   - `(farm_owner_id, category, status)` on products
   - `(user_id, is_read, created_at)` on notifications

### Query Optimization Tips

```php
// ✅ Good: Select only needed columns
Product::select('id', 'name', 'price', 'farm_owner_id')->get()

// ✅ Good: Use scopes for reusable logic
$active_farms = FarmOwner::active()->topRated()->with('subscription')->get()

// ✅ Good: Paginate large result sets
Order::where('status', 'delivered')->paginate(25)

// ✅ Avoid: N+1 queries
Product::all()->each(fn($p) => $p->farmOwner->farm_name) // ❌ Bad
Product::with('farmOwner')->get() // ✅ Good
```

---

## Migration Execution Commands

Run these in order:

```bash
# Keep existing
# php artisan migrate (will include default 3)

# Delete old migrations manually or run:
# rm database/migrations/2026_02_01_*.php
# rm database/migrations/2026_02_03_*.php
# rm database/migrations/2026_02_06_*.php

# Run fresh migration
php artisan migrate:refresh --force

# Or seed with admin
php artisan migrate:refresh --seeder=AdminUserSeeder --force
```

---

## Database Relationships Diagram

```
users (1) ──── (1) farm_owners
      │
      ├─── (1) staff
      ├─── (Many) orders (as consumer_id)
      ├─── (Many) documents
      ├─── (Many) notifications
      └─── (Many) subscriptions (legacy)

farm_owners (1) ──── (Many) products
             │
             ├──── (Many) orders (as farm_owner_id)
             ├──── (Many) subscriptions
             ├──── (Many) documents
             └──── (Many) staff

orders (1) ──── (Many) order_items
       │
       └──── (Many) items.products

notifications (Many) ──── (1) users
```

---

## Configuration Notes

1. **Soft Deletes**: FarmOwner, Subscription, Product, Order, Staff, Document use soft deletes for data retention
2. **JSON Columns**: Attributes, permissions, data, image_urls use JSON for flexibility
3. **Enum Types**: Use MySQL ENUM for status fields (constraints at DB level)
4. **Timestamps**: All tables include created_at, updated_at for audit trails
5. **Foreign Keys**: CASCADE/SET NULL configured appropriately for data integrity

---

## Next Steps

1. Delete old migrations
2. Run: `php artisan migrate:refresh --force`
3. Create seeders for:
   - Roles (super_admin, admin, farm_owner, consumer, staff)
   - Subscription plans (starter, professional, enterprise)
   - Test farm owners and products
4. Update API endpoints to use new scopes
5. Add authorization policies using Permission model
