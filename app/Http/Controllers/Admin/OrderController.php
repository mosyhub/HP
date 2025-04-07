<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.order.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validStatuses = ['pending', 'to_ship', 'to_deliver', 'completed', 'cancelled'];
        
        $request->validate([
            'status' => ['required', 'in:' . implode(',', $validStatuses)]
        ]);
        
        // Handle both AJAX and form submissions
        if ($request->ajax()) {
            try {
                $order->update(['status' => $request->status]);
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        } else {
            // Form submission
            try {
                $order->update(['status' => $request->status]);
                return redirect()->back()->with('success', 'Order status updated successfully!');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to update order status: ' . $e->getMessage());
            }
        }
    }
}