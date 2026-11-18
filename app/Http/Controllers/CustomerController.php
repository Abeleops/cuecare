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
        $sort = $request->input('sort', 'full_name');
        abort_unless(in_array($sort, ['customer_code', 'full_name', 'created_at'], true), 422);
        $customers = Customer::query()->when($request->filled('search'), fn ($query) => $query->where(function ($query) use ($request): void { $term = '%'.$request->string('search').'%'; $query->where('full_name', 'like', $term)->orWhere('phone', 'like', $term)->orWhere('email', 'like', $term); }))->orderBy($sort)->paginate(25)->withQueryString();
        return view('customers.index', ['customers' => $customers]);
    }

    public function store(CustomerRequest $request, AuditLogger $audit): RedirectResponse
    {
        $customer = Customer::query()->create([...$request->validated(), 'customer_code' => 'CUST-'.str_pad((string) (Customer::withTrashed()->max('id') + 1), 6, '0', STR_PAD_LEFT)]);
        $audit->record('customer_created', $customer);
        return back()->with('status', 'Customer created.');
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());
        return back()->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer, AuditLogger $audit): RedirectResponse
    {
        $customer->delete();
        $audit->record('customer_deleted', $customer);
        return back()->with('status', 'Customer deleted.');
    }
}
