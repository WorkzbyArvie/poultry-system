<?php

namespace App\Http\Controllers;

use App\Models\Flock;
use App\Models\FlockRecord;
use App\Models\Order;
use App\Models\Expense;
use App\Models\IncomeRecord;
use App\Models\SupplyItem;
use App\Models\Delivery;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\FarmOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function getFarmOwner()
    {
        return FarmOwner::where('user_id', Auth::id())->firstOrFail();
    }

    public function index()
    {
        return view('farmowner.reports.index');
    }

    // Financial Summary Report
    public function financial(Request $request)
    {
        $farmOwner = $this->getFarmOwner();
        
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth();
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfMonth();

        // Income breakdown
        $income = IncomeRecord::byFarmOwner($farmOwner->id)
            ->byDateRange($startDate, $endDate)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $totalIncome = $income->sum('total');

        // Expense breakdown
        $expenses = Expense::byFarmOwner($farmOwner->id)
            ->byDateRange($startDate, $endDate)
            ->selectRaw('category, SUM(total_amount) as total')
            ->groupBy('category')
            ->get();

        $totalExpenses = $expenses->sum('total');

        // Net profit
        $netProfit = $totalIncome - $totalExpenses;

        // Daily trend
        $dailyTrend = DB::table('income_records')
            ->where('farm_owner_id', $farmOwner->id)
            ->whereBetween('income_date', [$startDate, $endDate])
            ->selectRaw('DATE(income_date) as date, SUM(amount) as income')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('farmowner.reports.financial', compact(
            'income', 'totalIncome', 'expenses', 'totalExpenses', 'netProfit', 'dailyTrend', 'startDate', 'endDate'
        ));
    }

    // Flock/Production Report
    public function production(Request $request)
    {
        $farmOwner = $this->getFarmOwner();
        
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth();
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfMonth();

        // Flock summary
        $flocks = Flock::byFarmOwner($farmOwner->id)
            ->active()
            ->select('id', 'batch_name', 'flock_type', 'current_count', 'mortality_count', 'initial_count')
            ->get()
            ->map(function ($flock) {
                $flock->mortality_rate = $flock->mortality_rate;
                $flock->survival_rate = $flock->survival_rate;
                return $flock;
            });

        // Cache flock IDs to prevent repeated queries
        $flockIds = Flock::byFarmOwner($farmOwner->id)->pluck('id');

        // Production stats (eggs)
        $eggProduction = FlockRecord::whereIn('flock_id', $flockIds)
            ->whereBetween('record_date', [$startDate, $endDate])
            ->selectRaw('SUM(eggs_collected) as collected, SUM(eggs_broken) as broken')
            ->first();

        // Mortality stats
        $mortalityStats = FlockRecord::whereIn('flock_id', $flockIds)
            ->whereBetween('record_date', [$startDate, $endDate])
            ->selectRaw('SUM(mortality_today) as total_mortality')
            ->first();

        // Feed consumption
        $feedConsumption = FlockRecord::whereIn('flock_id', $flockIds)
            ->whereBetween('record_date', [$startDate, $endDate])
            ->selectRaw('SUM(feed_consumed_kg) as total_feed')
            ->first();

        // Daily egg production trend
        $eggTrend = FlockRecord::whereIn('flock_id', $flockIds)
            ->whereBetween('record_date', [$startDate, $endDate])
            ->selectRaw('record_date, SUM(eggs_collected) as eggs')
            ->groupBy('record_date')
            ->orderBy('record_date')
            ->get();

        return view('farmowner.reports.production', compact(
            'flocks', 'eggProduction', 'mortalityStats', 'feedConsumption', 'eggTrend', 'startDate', 'endDate'
        ));
    }

    // Inventory Report
    public function inventory(Request $request)
    {
        $farmOwner = $this->getFarmOwner();

        // Current stock levels
        $supplies = SupplyItem::byFarmOwner($farmOwner->id)
            ->select('id', 'name', 'category', 'quantity_on_hand', 'minimum_stock', 'unit_cost', 'expiration_date', 'status')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        // Stock value by category
        $stockValue = SupplyItem::byFarmOwner($farmOwner->id)
            ->selectRaw('category, SUM(quantity_on_hand * unit_cost) as value')
            ->groupBy('category')
            ->get();

        $totalValue = $stockValue->sum('value');

        // Low stock alerts
        $lowStock = SupplyItem::byFarmOwner($farmOwner->id)->lowStock()->count();
        $outOfStock = SupplyItem::byFarmOwner($farmOwner->id)->outOfStock()->count();
        $expiringSoon = SupplyItem::byFarmOwner($farmOwner->id)->expiringSoon(30)->count();

        return view('farmowner.reports.inventory', compact(
            'supplies', 'stockValue', 'totalValue', 'lowStock', 'outOfStock', 'expiringSoon'
        ));
    }

    // Sales Report
    public function sales(Request $request)
    {
        $farmOwner = $this->getFarmOwner();
        
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth();
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfMonth();

        // Order stats
        $orders = Order::where('farm_owner_id', $farmOwner->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('status')
            ->get();

        $totalOrders = $orders->sum('count');
        $totalSales = $orders->where('status', 'completed')->sum('total');

        // Top customers (join with users table since Order has consumer_id)
        $topCustomers = Order::where('orders.farm_owner_id', $farmOwner->id)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->join('users', 'orders.consumer_id', '=', 'users.id')
            ->selectRaw('users.name as customer_name, COUNT(*) as order_count, SUM(orders.total_amount) as total')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Daily sales trend
        $salesTrend = Order::where('farm_owner_id', $farmOwner->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('farmowner.reports.sales', compact(
            'orders', 'totalOrders', 'totalSales', 'topCustomers', 'salesTrend', 'startDate', 'endDate'
        ));
    }

    // Delivery Report
    public function delivery(Request $request)
    {
        $farmOwner = $this->getFarmOwner();
        
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth();
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfMonth();

        // Delivery stats
        $deliveryStats = Delivery::byFarmOwner($farmOwner->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $totalDeliveries = $deliveryStats->sum('count');
        $completedDeliveries = $deliveryStats->where('status', 'delivered')->sum('count');
        $successRate = $totalDeliveries > 0 ? round(($completedDeliveries / $totalDeliveries) * 100, 1) : 0;

        // Driver performance
        $driverPerformance = Delivery::byFarmOwner($farmOwner->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('driver_id')
            ->with('driver:id,name')
            ->selectRaw('driver_id, COUNT(*) as total, SUM(CASE WHEN status = \'delivered\' THEN 1 ELSE 0 END) as completed')
            ->groupBy('driver_id')
            ->get()
            ->map(function ($item) {
                $item->success_rate = $item->total > 0 ? round(($item->completed / $item->total) * 100, 1) : 0;
                return $item;
            });

        // COD collection (cod_collected is boolean, so count collected vs total COD amount)
        $codStats = Delivery::byFarmOwner($farmOwner->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(cod_amount) as total_cod, SUM(CASE WHEN cod_collected = true THEN cod_amount ELSE 0 END) as collected_cod')
            ->first();

        return view('farmowner.reports.delivery', compact(
            'deliveryStats', 'totalDeliveries', 'completedDeliveries', 'successRate', 'driverPerformance', 'codStats', 'startDate', 'endDate'
        ));
    }

    // HR/Payroll Report
    public function payroll(Request $request)
    {
        $farmOwner = $this->getFarmOwner();
        
        $month = $request->filled('month') ? Carbon::parse($request->month) : now();

        // Monthly payroll summary
        $payrollSummary = Payroll::byFarmOwner($farmOwner->id)
            ->whereMonth('period_start', $month->month)
            ->whereYear('period_start', $month->year)
            ->with('employee:id,first_name,last_name,position')
            ->get();

        $totalGross = $payrollSummary->sum('gross_pay');
        $totalDeductions = $payrollSummary->sum('total_deductions');
        $totalNet = $payrollSummary->sum('net_pay');

        // Payroll by department
        $byDepartment = Payroll::byFarmOwner($farmOwner->id)
            ->whereMonth('period_start', $month->month)
            ->whereYear('period_start', $month->year)
            ->join('employees', 'payroll.employee_id', '=', 'employees.id')
            ->selectRaw('employees.department, SUM(payroll.net_pay) as total')
            ->groupBy('employees.department')
            ->get();

        // Employee headcount
        $headcount = Employee::byFarmOwner($farmOwner->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('farmowner.reports.payroll', compact(
            'payrollSummary', 'totalGross', 'totalDeductions', 'totalNet', 'byDepartment', 'headcount', 'month'
        ));
    }

    // Dashboard Summary (DSS - Decision Support)
    public function dashboard()
    {
        $farmOwner = $this->getFarmOwner();

        $data = Cache::remember("farm_{$farmOwner->id}_dss_dashboard", 300, function () use ($farmOwner) {
            $today = now();
            $startOfMonth = $today->copy()->startOfMonth();
            $endOfMonth = $today->copy()->endOfMonth();

            // Key metrics
            $metrics = [
                'total_birds' => Flock::byFarmOwner($farmOwner->id)->active()->sum('current_count'),
                'monthly_income' => IncomeRecord::byFarmOwner($farmOwner->id)
                    ->whereBetween('income_date', [$startOfMonth, $endOfMonth])->sum('amount'),
                'monthly_expenses' => Expense::byFarmOwner($farmOwner->id)
                    ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('total_amount'),
                'pending_orders' => Order::where('farm_owner_id', $farmOwner->id)
                    ->where('status', 'pending')->count(),
                'pending_deliveries' => Delivery::byFarmOwner($farmOwner->id)->byStatus('pending')->count(),
                'low_stock_items' => SupplyItem::byFarmOwner($farmOwner->id)->lowStock()->count(),
                'employees_active' => Employee::byFarmOwner($farmOwner->id)->active()->count(),
            ];

            // Calculate profit margin
            $metrics['profit'] = $metrics['monthly_income'] - $metrics['monthly_expenses'];
            $metrics['profit_margin'] = $metrics['monthly_income'] > 0 
                ? round(($metrics['profit'] / $metrics['monthly_income']) * 100, 1) 
                : 0;

            // Alerts
            $alerts = [];

            if ($metrics['low_stock_items'] > 0) {
                $alerts[] = ['type' => 'warning', 'message' => "{$metrics['low_stock_items']} items are low on stock"];
            }

            $overdueTasks = Delivery::byFarmOwner($farmOwner->id)
                ->whereDate('scheduled_date', '<', $today)
                ->whereIn('status', ['pending', 'assigned'])
                ->count();

            if ($overdueTasks > 0) {
                $alerts[] = ['type' => 'danger', 'message' => "{$overdueTasks} overdue deliveries"];
            }

            $expiringItems = SupplyItem::byFarmOwner($farmOwner->id)->expiringSoon(7)->count();
            if ($expiringItems > 0) {
                $alerts[] = ['type' => 'warning', 'message' => "{$expiringItems} items expiring within 7 days"];
            }

            return compact('metrics', 'alerts');
        });

        return view('farmowner.reports.dashboard', $data);
    }

    // Export report to CSV
    public function exportCsv(Request $request, string $type)
    {
        $farmOwner = $this->getFarmOwner();
        
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : now()->startOfMonth();
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : now()->endOfMonth();

        $filename = "{$type}_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($farmOwner, $type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'financial':
                    fputcsv($file, ['Date', 'Type', 'Category/Source', 'Description', 'Amount']);
                    
                    // Income
                    IncomeRecord::byFarmOwner($farmOwner->id)
                        ->byDateRange($startDate, $endDate)
                        ->orderBy('income_date')
                        ->chunk(100, function ($records) use ($file) {
                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->income_date->format('Y-m-d'),
                                    'Income',
                                    $record->category,
                                    $record->description,
                                    $record->amount,
                                ]);
                            }
                        });

                    // Expenses
                    Expense::byFarmOwner($farmOwner->id)
                        ->byDateRange($startDate, $endDate)
                        ->orderBy('expense_date')
                        ->chunk(100, function ($records) use ($file) {
                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->expense_date->format('Y-m-d'),
                                    'Expense',
                                    $record->category,
                                    $record->description,
                                    -$record->total_amount,
                                ]);
                            }
                        });
                    break;

                case 'inventory':
                    fputcsv($file, ['Item Name', 'Category', 'Quantity', 'Unit', 'Unit Cost', 'Total Value', 'Status']);
                    
                    SupplyItem::byFarmOwner($farmOwner->id)
                        ->orderBy('category')
                        ->chunk(100, function ($items) use ($file) {
                            foreach ($items as $item) {
                                fputcsv($file, [
                                    $item->name,
                                    $item->category,
                                    $item->quantity_on_hand,
                                    $item->unit,
                                    $item->unit_cost,
                                    $item->quantity_on_hand * $item->unit_cost,
                                    $item->status,
                                ]);
                            }
                        });
                    break;

                default:
                    fputcsv($file, ['No data available for this report type']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
