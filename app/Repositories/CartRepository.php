<?php

namespace App\Repositories;

use App\Models\Cart;
use Illuminate\Support\Collection;

class CartRepository
{
    public function create(array $cart)
    {
        return Cart::create($cart);
    }

    public function update(Cart $cart, array $cartData): bool
    {
        return $cart->update($cartData);
    }

    public function delete(Cart $cart): bool
    {
        return $cart->delete();
    }

    public function clearUserCart(int $userId): bool
    {
        return Cart::where('user_id', $userId)->delete();
    }

    public function getUserCartItems(int $userId): Collection
    {
        return Cart::with('product')
            ->where('user_id', $userId)
            ->get();
    }

    public function findUserCartItem(int $userId, inr $productId): ?Cart
    {
        return Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function findUserCartItemById(int $userId, int $cartId): ?Cart
    {
        return Cart::where('user_id', $userId)
            ->find($cartId);
    }
}
