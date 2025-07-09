<?php

namespace App\Repositories;

use App\DTO\ProductFilterDTO;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductRepository
{
    public function create(array $product): Product
    {
        return Product::create($product);
    }

    public function update(Product $product, array $productData): bool
    {
        return $product->update($productData);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function getProductById(int $productId): ?Product
    {
        return Product::with(['category', 'seller'])->find($productId);
    }

    public function getSellerProductsWithFilters(int $sellerId, ProductFilterDTO $filter): LengthAwarePaginator
    {
        $query = Product::with(['category'])
            ->where('seller_id', $sellerId)
            ->latest();

        if ($filter->status)
        {
            $query->where('status', $filter->status);
        }

        if ($filter->categoryId)
        {
            $query->where('category_id', $filter->categoryId);
        }

        if ($filter->search){
            $query->where(function ($q) use ($filter){
                $q->where('name', 'like', "%{$filter->search}%")
                    ->orWhere('sku', 'like', "%{$filter->search}%");
            });
        }
        return $query->paginate($filter->perPage,['*'], 'page', $filter->page);
    }

    public function getApprovedProducts(ProductFilterDTO $filter): LengthAwarePaginator
    {
        $query = Product::with(['category', 'seller'])
            ->where('status', 'approved')
            ->where('is_active', true)
            ->latest();

        if ($filter->categoryId)
        {
            $query->where('category_id', $filter->categoryId);
        }

        if ($filter->search){
            $query->where(function ($q) use ($filter){
                $q->where('name', 'like', "%{$filter->search}%")
                    ->orWhere('brand', 'like', "%{$filter->search}%");
            });
        }

        return $query->paginate($filter->perPage,['*'], 'page', $filter->page);
    }

    public function findByCategory(int $categoryId): Collection
    {
        return Product::with('seller')
            ->where('category_id', $categoryId)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->get();
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::where('sku', $sku)->first();
    }

    public function updateStatus(Product $product, string $status): Product
    {
        $product->update([
            'status' => $status,
        ]);

        return $product->fresh();
    }
}
