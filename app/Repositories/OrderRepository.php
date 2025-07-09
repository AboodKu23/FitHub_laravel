<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    public function create(array $order): Order
    {
        return Order::create($order);
    }

    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    public function getUserOrders(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Order::with(['orderItems.product'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getOrderById(int $userId, int $orderId): Order
    {
        return Order::with(['orderItems.product', 'user'])
            ->where('user_id', $userId)
            ->findOrFail($orderId);
    }
}
