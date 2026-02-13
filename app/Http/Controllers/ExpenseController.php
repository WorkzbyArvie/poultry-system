<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Supplier;
use App\Models\FarmOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    private function getFarmOwner()
    {
        return FarmOwner::where('user_id', Auth::id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $farmOwner = $this->getFarmOwner();
        
        $query = Expense::byFarmOwner($farmOwner->id)
            ->with(['supplier:id,company_name', 'recordedBy:id,name'])
            ->select('id', 'expense_number', 'supplier_id', 'recorded_by', 'category', 'description', 'total_amount', 'expense_date', 'payment_status');

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('expense_date', $date->month)
                  ->whereYear('expense_date', $date->year);
        }

        $expenses = $query->latest('expense_date')->paginate(20);

        $stats = Cache::remember("farm_{$farmOwner->id}_expense_stats", 300, function () use ($farmOwner) {
            return [
                'total_this_month' => Expense::byFarmOwner($farmOwner->id)
                    ->whereMonth('expense_date', now()->month)
                    ->whereYear('expense_date', now()->year)
                    ->sum('total_amount'),
                'unpaid' => Expense::byFarmOwner($farmOwner->id)->where('payment_status', 'unpaid')->sum('total_amount'),
                'by_category' => Expense::byFarmOwner($farmOwner->id)
                    ->whereMonth('expense_date', now()->month)
                    ->selectRaw('category, SUM(total_amount) as total')
                    ->groupBy('category')
                    ->pluck('total', 'category'),
            ];
        });

        return view('farmowner.expenses.index', compact('expenses', 'stats'));
    }

    public function create()
    {
        $farmOwner = $this->getFarmOwner();
        $suppliers = Supplier::byFarmOwner($farmOwner->id)->active()->select('id', 'company_name')->get();

        return view('farmowner.expenses.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $farmOwner = $this->getFarmOwner();

        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category' => 'required|in:feeds,medications,utilities,equipment,labor,maintenance,transportation,packaging,miscellaneous',
            'description' => 'required|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_cost' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            'receipt_number' => 'nullable|string|max:100',
            'payment_method' => 'nullable|in:cash,bank_transfer,check,credit,gcash',
            'payment_status' => 'required|in:paid,unpaid,partial',
            'amount_paid' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['farm_owner_id'] = $farmOwner->id;
        $validated['recorded_by'] = Auth::id();

        Expense::create($validated);
        Cache::forget("farm_{$farmOwner->id}_expense_stats");

        return redirect()->route('expenses.index')->with('success', 'Expense recorded.');
    }

    public function show(Expense $expense)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($expense->farm_owner_id !== $farmOwner->id, 403);

        $expense->load(['supplier', 'recordedBy', 'approvedBy']);

        return view('farmowner.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($expense->farm_owner_id !== $farmOwner->id, 403);

        $suppliers = Supplier::byFarmOwner($farmOwner->id)->active()->select('id', 'company_name')->get();

        return view('farmowner.expenses.edit', compact('expense', 'suppliers'));
    }

    public function update(Request $request, Expense $expense)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($expense->farm_owner_id !== $farmOwner->id, 403);

        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category' => 'required|in:feeds,medications,utilities,equipment,labor,maintenance,transportation,packaging,miscellaneous',
            'description' => 'required|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_cost' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            'payment_status' => 'required|in:paid,unpaid,partial',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);
        Cache::forget("farm_{$farmOwner->id}_expense_stats");

        return redirect()->route('expenses.show', $expense)->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($expense->farm_owner_id !== $farmOwner->id, 403);

        $expense->delete();
        Cache::forget("farm_{$farmOwner->id}_expense_stats");

        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }

    public function markPaid(Request $request, Expense $expense)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($expense->farm_owner_id !== $farmOwner->id, 403);

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,check,credit,gcash',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $expense->update([
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'amount_paid' => $expense->total_amount,
            'payment_date' => now(),
        ]);

        Cache::forget("farm_{$farmOwner->id}_expense_stats");

        return redirect()->route('expenses.show', $expense)->with('success', 'Expense marked as paid.');
    }
}
