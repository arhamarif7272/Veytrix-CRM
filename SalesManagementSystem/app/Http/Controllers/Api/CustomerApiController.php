<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
        }

        return response()->json($query->orderBy('name')->paginate(20));
    }

    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function search(Request $request)
    {
        $term = $request->input('q');
        $customers = Customer::where('name', 'like', "%{$term}%")
            ->orWhere('company', 'like', "%{$term}%")
            ->limit(10)
            ->get(['id', 'name', 'company', 'email']);

        return response()->json($customers);
    }
}
