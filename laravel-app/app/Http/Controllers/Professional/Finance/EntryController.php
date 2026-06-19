<?php

namespace App\Http\Controllers\Professional\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\ProfessionalFinanceEntry::with('category')
            ->where('professional_id', auth()->id())
            ->orderBy('due_date', 'desc');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        if ($request->has('month') && $request->month !== '') {
            $query->whereMonth('due_date', $request->month);
        }

        $entries = $query->paginate(20);

        return view('professional.finance.entries.index', compact('entries'));
    }

    public function create()
    {
        $categories = \App\Models\ProfessionalFinanceCategory::where('professional_id', auth()->id())->get();
        return view('professional.finance.entries.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\ProfessionalFinanceEntry::class);

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:revenue,expense',
            'category_id' => 'nullable|exists:professional_finance_categories,id',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,paid,cancelled',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($request->filled('category_id')) {
            $ownsCategory = \App\Models\ProfessionalFinanceCategory::where('id', $request->category_id)
                ->where('professional_id', auth()->id())
                ->exists();
            if (! $ownsCategory) {
                abort(403, 'Categoria financeira inválida.');
            }
        }

        \App\Models\ProfessionalFinanceEntry::create([
            'professional_id' => auth()->id(),
            'category_id' => $request->category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return redirect()->route('professional.finance.entries.index')->with('success', 'Lançamento registrado com sucesso.');
    }

    public function edit(\App\Models\ProfessionalFinanceEntry $entry)
    {
        $this->authorize('update', $entry);

        $categories = \App\Models\ProfessionalFinanceCategory::where('professional_id', auth()->id())->get();
        return view('professional.finance.entries.edit', compact('entry', 'categories'));
    }

    public function update(Request $request, \App\Models\ProfessionalFinanceEntry $entry)
    {
        $this->authorize('update', $entry);

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:revenue,expense',
            'category_id' => 'nullable|exists:professional_finance_categories,id',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,paid,cancelled',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($request->filled('category_id')) {
            $ownsCategory = \App\Models\ProfessionalFinanceCategory::where('id', $request->category_id)
                ->where('professional_id', auth()->id())
                ->exists();
            if (! $ownsCategory) {
                abort(403, 'Categoria financeira inválida.');
            }
        }

        $entry->update([
            'category_id' => $request->category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return redirect()->route('professional.finance.entries.index')->with('success', 'Lançamento atualizado com sucesso.');
    }

    public function destroy(\App\Models\ProfessionalFinanceEntry $entry)
    {
        $this->authorize('delete', $entry);

        $entry->delete();

        return redirect()->route('professional.finance.entries.index')->with('success', 'Lançamento excluído com sucesso.');
    }
}
