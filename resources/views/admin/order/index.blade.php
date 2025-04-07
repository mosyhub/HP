@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Orders Management</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    @if($orders->isEmpty())
        <div class="alert alert-info">
            No orders found.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('M j, Y') }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ $order->items->sum('quantity') }}</td>
                        <td>${{ number_format($order->total_price, 2) }}</td>
                        <td>
    <form id="status-form-{{ $order->id }}" action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="form-inline">
        @csrf
        @method('PATCH')
        <div class="d-flex align-items-center">
            <select name="status" class="form-select form-select-sm status-select mr-2" style="width: auto; display: inline-block;">
                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="to_ship" {{ $order->status == 'to_ship' ? 'selected' : '' }}>To Ship</option>
                <option value="to_deliver" {{ $order->status == 'to_deliver' ? 'selected' : '' }}>To Deliver</option>
                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                Save
            </button>
        </div>
    </form>
    <span class="badge bg-{{ 
        $order->status == 'completed' ? 'success' : 
        ($order->status == 'cancelled' ? 'danger' : 
        ($order->status == 'pending' ? 'secondary' : 'warning')) 
    }}">
        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
    </span>
</td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
