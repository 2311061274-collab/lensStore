<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders as lifetime_value' => function ($q) {
                $q->where('status', '!=', 'cancelled');
            }], 'total')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('tag')) {
            if ($request->tag === 'vip') {
                $query->having('lifetime_value', '>=', 20_000_000);
            } elseif ($request->tag === 'new') {
                $query->having('orders_count', '=', 1);
            } elseif ($request->tag === 'first') {
                $query->having('orders_count', '=', 0);
            }
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        abort_unless($customer->role === 'customer', 404);

        $customer->load(['orders' => fn ($q) => $q->latest()->with('items')]);

        $lifetimeValue = $customer->orders->where('status', '!=', 'cancelled')->sum('total');
        $orderCount = $customer->orders->count();

        $abandonedCart = Cart::with('product')
            ->where('user_id', $customer->id)
            ->get();

        $tag = 'Thường';
        if ($lifetimeValue >= 20_000_000) {
            $tag = 'VIP';
        } elseif ($orderCount === 0) {
            $tag = 'Chưa mua';
        } elseif ($orderCount === 1) {
            $tag = 'Mới';
        }

        $notes = DB::table('customer_notes')
            ->where('user_id', $customer->id)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.customers.show', compact(
            'customer',
            'lifetimeValue',
            'orderCount',
            'abandonedCart',
            'tag',
            'notes'
        ));
    }

    public function storeNote(Request $request, User $customer)
    {
        abort_unless($customer->role === 'customer', 404);

        $data = $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        DB::table('customer_notes')->insert([
            'user_id' => $customer->id,
            'admin_id' => auth()->id(),
            'note' => $data['note'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Đã lưu ghi chú tư vấn.');
    }
}
