<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource with pagination and search.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Return paginated results with all fields
        $customers = $query->withCount('orders')
                           ->withSum('orders', 'total_amount')
                           ->with(['orders' => function($q) {
                               $q->latest()->limit(1);
                           }])
                           ->paginate(10);

        return CustomerResource::collection($customers);
    }
}
