<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'in:customer_code,full_name,created_at'],
        ]);
        $search = trim($filters['search'] ?? '');
        $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search).'%';
        $customers = Customer::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($term): void {
                $query->where('full_name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('email', 'like', $term);
            }))
            ->orderBy($filters['sort'] ?? 'full_name')
            ->paginate(25)
            ->withQueryString();

        return view('customers.index', ['customers' => $customers]);
    }

    public function store(CustomerRequest $request, AuditLogger $audit): RedirectResponse
    {
        $customer = Customer::query()->create([
            ...$request->validated(),
            'customer_code' => 'CUST-'.str_pad((string) (Customer::withTrashed()->max('id') + 1), 6, '0', STR_PAD_LEFT),
        ]);
        $audit->record('customer_created', $customer);

        return back()->with('status', 'Customer created.');
    }

    public function update(CustomerRequest $request, Customer $customer, AuditLogger $audit): RedirectResponse
    {
        $customer->update($request->validated());
        $audit->record('customer_updated', $customer);

        return back()->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer, AuditLogger $audit): RedirectResponse
    {
        $customer->delete();
        $audit->record('customer_deleted', $customer);

        return back()->with('status', 'Customer deleted.');
    }
}
