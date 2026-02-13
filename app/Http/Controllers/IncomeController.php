<?php

namespace App\Http\Controllers;

use App\Models\IncomeRecord;
use App\Models\Order;
use App\Models\FarmOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class IncomeController extends Controller
{
    private function getFarmOwner()
    {
        return FarmOwner::where('user_id', Auth::id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $farmOwner = $this->getFarmOwner();
        
        $query = IncomeRecord::byFarmOwner($farmOwner->id)
            ->with('recordedBy:id,name')
            ->select('id', 'income_number', 'order_id', 'source', 'description', 'amount', 'income_date', 'payment_method', 'recorded_by');

        if ($request->filled('source')) {
            $query->bySource($request->source);
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('income_date', $date->month)
                  ->whereYear('income_date', $date->year);
        }

        $incomes = $query->latest('income_date')->paginate(20);

        $stats = Cache::remember("farm_{$farmOwner->id}_income_stats", 300, function () use ($farmOwner) {
            return [
                'total_this_month' => IncomeRecord::byFarmOwner($farmOwner->id)
                    ->whereMonth('income_date', now()->month)
                    ->whereYear('income_date', now()->year)
                    ->sum('amount'),
                'by_source' => IncomeRecord::byFarmOwner($farmOwner->id)
                    ->whereMonth('income_date', now()->month)
                    ->selectRaw('source, SUM(amount) as total')
                    ->groupBy('source')
                    ->pluck('total', 'source'),
            ];
        });

        return view('farmowner.income.index', compact('incomes', 'stats'));
    }

    public function create()
    {
        return view('farmowner.income.create');
    }

    public function store(Request $request)
    {
        $farmOwner = $this->getFarmOwner();

        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'source' => 'required|in:egg_sales,chicken_sales,manure_sales,chick_sales,feed_sales,other',
            'description' => 'required|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
            'payment_method' => 'nullable|in:cash,bank_transfer,check,gcash,credit',
            'reference_number' => 'nullable|string|max:100',
            'customer_name' => 'nullable|string|max:255',
            'customer_contact' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['farm_owner_id'] = $farmOwner->id;
        $validated['recorded_by'] = Auth::id();

        IncomeRecord::create($validated);
        Cache::forget("farm_{$farmOwner->id}_income_stats");

        return redirect()->route('income.index')->with('success', 'Income recorded.');
    }

    public function show(IncomeRecord $income)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($income->farm_owner_id !== $farmOwner->id, 403);

        $income->load(['order', 'recordedBy']);

        return view('farmowner.income.show', compact('income'));
    }

    public function edit(IncomeRecord $income)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($income->farm_owner_id !== $farmOwner->id, 403);

        return view('farmowner.income.edit', compact('income'));
    }

    public function update(Request $request, IncomeRecord $income)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($income->farm_owner_id !== $farmOwner->id, 403);

        $validated = $request->validate([
            'source' => 'required|in:egg_sales,chicken_sales,manure_sales,chick_sales,feed_sales,other',
            'description' => 'required|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
            'payment_method' => 'nullable|in:cash,bank_transfer,check,gcash,credit',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $income->update($validated);
        Cache::forget("farm_{$farmOwner->id}_income_stats");

        return redirect()->route('income.show', $income)->with('success', 'Income updated.');
    }

    public function destroy(IncomeRecord $income)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($income->farm_owner_id !== $farmOwner->id, 403);

        $income->delete();
        Cache::forget("farm_{$farmOwner->id}_income_stats");

        return redirect()->route('income.index')->with('success', 'Income deleted.');
    }
}
