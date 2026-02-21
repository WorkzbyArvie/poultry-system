<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\FarmOwnerController;
use App\Http\Controllers\FarmOwnerAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ConsumerRegistrationController;
use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\EggController;
use App\Http\Controllers\ChickenController;
use App\Http\Controllers\FlockController;
use App\Http\Controllers\VaccinationController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| 1. Public / Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

// Farm Owner Public Routes
Route::get('/farm-owner/login', [FarmOwnerAuthController::class, 'show_login'])->name('farmowner.login');
Route::post('/farm-owner/login', [FarmOwnerAuthController::class, 'login'])->name('farmowner.login.store');
Route::get('/farm-owner/register', [FarmOwnerAuthController::class, 'show_register'])->name('farmowner.register');
Route::post('/farm-owner/register', [FarmOwnerAuthController::class, 'register'])->name('farmowner.register.store');

Route::get('/register', function () {
    return view('auth.register-select');
})->name('register');

Route::get('/client/register', function () {
    return view('auth.client-register');
})->name('client.register');

Route::get('/consumer/register', function () {
    return view('auth.consumer-register');
})->name('consumer.register');

Route::post('/client/register', [ClientRequestController::class, 'store'])->name('client.request.store');
Route::post('/consumer/register', [ConsumerRegistrationController::class, 'store'])->name('consumer.store');

// PayMongo Webhook (no auth, no CSRF)
Route::post('/webhooks/paymongo', [SubscriptionController::class, 'handleWebhook'])->name('webhooks.paymongo');

/*
|--------------------------------------------------------------------------
| 2. Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. THIS IS THE ONLY DASHBOARD ROUTE YOU NEED
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        if ($user->role === 'superadmin') {
            // This sends you to the correct orange/black portal
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->role === 'client') {
            return redirect()->route('client.dashboard');
        }

        // Fallback for others (like consumers)
        return view('dashboard'); 
    })->name('dashboard');

    // --- Super Admin Portal ---
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/super-admin/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');
        Route::get('/super-admin/farm-owners', [SuperAdminController::class, 'farm_owners'])->name('superadmin.farm_owners');
        Route::get('/super-admin/farm-owners/{id}', [SuperAdminController::class, 'show_farm_owner'])->name('superadmin.show_farm_owner');
        Route::post('/super-admin/farm-owners/{id}/approve', [SuperAdminController::class, 'approve_farm_owner'])->name('superadmin.approve_farm_owner');
        Route::post('/super-admin/farm-owners/{id}/reject', [SuperAdminController::class, 'reject_farm_owner'])->name('superadmin.reject_farm_owner');
        Route::get('/super-admin/orders', [SuperAdminController::class, 'orders'])->name('superadmin.orders');
        Route::get('/super-admin/subscriptions', [SuperAdminController::class, 'subscriptions'])->name('superadmin.subscriptions');
        Route::get('/super-admin/users', [SuperAdminController::class, 'users'])->name('superadmin.users');
    });
    
    // --- Farm Owner Routes (Authenticated) ---
    Route::middleware('role:farm_owner')->prefix('farm-owner')->group(function () {
        // Dashboard & Profile
        Route::get('/dashboard', [FarmOwnerController::class, 'dashboard'])->name('farmowner.dashboard');
        Route::get('/profile', [FarmOwnerController::class, 'profile'])->name('farmowner.profile');
        Route::put('/profile', [FarmOwnerController::class, 'update_profile'])->name('farmowner.update_profile');
        Route::get('/subscriptions', [FarmOwnerController::class, 'subscriptions'])->name('farmowner.subscriptions');
        Route::post('/logout', [FarmOwnerAuthController::class, 'logout'])->name('farmowner.logout');

        // Flock Management
        Route::resource('flocks', FlockController::class);
        Route::post('/flocks/{flock}/record', [FlockController::class, 'addRecord'])->name('flocks.record');

        // Vaccination & Health
        Route::resource('vaccinations', VaccinationController::class);
        Route::get('/vaccinations-upcoming', [VaccinationController::class, 'upcoming'])->name('vaccinations.upcoming');

        // Supply/Inventory Management
        Route::resource('supplies', SupplyController::class);
        Route::post('/supplies/{supply}/stock-in', [SupplyController::class, 'stockIn'])->name('supplies.stockIn');
        Route::post('/supplies/{supply}/stock-out', [SupplyController::class, 'stockOut'])->name('supplies.stockOut');
        Route::get('/supplies-alerts', [SupplyController::class, 'lowStock'])->name('supplies.alerts');

        // Supplier Management
        Route::resource('suppliers', SupplierController::class);

        // HR - Employees
        Route::resource('employees', EmployeeController::class);

        // HR - Attendance
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk');
        Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
        Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');
        Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

        // Payroll
        Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
        Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
        Route::get('/payroll/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::post('/payroll/{payroll}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
        Route::post('/payroll/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('payroll.markPaid');
        Route::post('/payroll/generate-batch', [PayrollController::class, 'generateBatch'])->name('payroll.generateBatch');

        // Finance - Expenses
        Route::resource('expenses', ExpenseController::class);
        Route::post('/expenses/{expense}/mark-paid', [ExpenseController::class, 'markPaid'])->name('expenses.markPaid');

        // Finance - Income
        Route::resource('income', IncomeController::class);

        // Logistics - Drivers
        Route::resource('drivers', DriverController::class);

        // Logistics - Deliveries
        Route::resource('deliveries', DeliveryController::class);
        Route::post('/deliveries/{delivery}/assign-driver', [DeliveryController::class, 'assignDriver'])->name('deliveries.assignDriver');
        Route::post('/deliveries/{delivery}/dispatch', [DeliveryController::class, 'dispatch'])->name('deliveries.dispatch');
        Route::post('/deliveries/{delivery}/mark-delivered', [DeliveryController::class, 'markDelivered'])->name('deliveries.markDelivered');
        Route::post('/deliveries/{delivery}/mark-failed', [DeliveryController::class, 'markFailed'])->name('deliveries.markFailed');
        Route::get('/delivery-schedule', [DeliveryController::class, 'schedule'])->name('deliveries.schedule');

        // Reports & Analytics (DSS)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
        Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
        Route::get('/reports/production', [ReportController::class, 'production'])->name('reports.production');
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/delivery', [ReportController::class, 'delivery'])->name('reports.delivery');
        Route::get('/reports/payroll', [ReportController::class, 'payroll'])->name('reports.payroll');
        Route::get('/reports/export/{type}', [ReportController::class, 'exportCsv'])->name('reports.export');
    });

    // --- Product Routes ---
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/farm-owner/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/farm-owner/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/farm-owner/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/farm-owner/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/farm-owner/products/{product}', [ProductController::class, 'delete'])->name('products.delete');
    Route::patch('/farm-owner/products/{product}/stock', [ProductController::class, 'update_stock'])->name('products.update-stock');

    // --- Order Routes ---
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/cart/add', [OrderController::class, 'cart_add'])->name('cart.add');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'place_order'])->name('orders.place');
    Route::post('/farm-owner/orders/{order}/confirm', [OrderController::class, 'confirm_order'])->name('orders.confirm');
    
    // --- Old Routes (kept for backward compatibility) ---

    // --- Subscription & Payment System ---
    Route::get('/subscribe', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscribe/pay', [SubscriptionController::class, 'pay'])->name('subscription.pay');
    Route::get('/payment/success', [SubscriptionController::class, 'success'])->name('payment.success');

    // --- Profile Management ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Client Dashboard ---
    Route::get('/client/dashboard', function () {
        $user = Auth::user();
        // Fallback for days remaining
        $daysRemaining = $user->user_subscription_end ? now()->diffInDays($user->user_subscription_end, false) : 30;
        $daysRemaining = $daysRemaining > 0 ? (int)$daysRemaining : 0;
        
        return view('client.dashboard', compact('daysRemaining'));
    })->name('client.dashboard');
});

require __DIR__.'/auth.php';