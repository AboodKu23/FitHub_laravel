<?php

namespace App\Services\Seller;

use App\DTO\ProductFilterDTO;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\Contracts\FileUploadService;
use Illuminate\Support\Str;

class ProductService
{
    protected ProductRepository $productRepository;
    protected FileUploadService $fileUploadService;

    public function __construct(ProductRepository $productRepository, FileUploadService $fileUploadService)
    {
        $this->productRepository = $productRepository;
        $this->fileUploadService = $fileUploadService;
    }

    public function createNewProduct(array $data): array
    {
        $data['sku'] = $this->generateSKU();
        if (!empty($data['images']))
        {
            $data['images'] = $this->fileUploadService->uploadMultipleFiles($data['images'], 'products');
        }
        $product = $this->productRepository->create($data);
        if (!$product)
        {
            return [
                'success' => false,
                'message' => 'Product not created'
            ];
        }
        return [
            'success' => true,
            'message' => 'Product created',
            'product' => $product
        ];
    }

    public function updateProduct(int $productId, array $data): array
    {
        $product = $this->productRepository->getProductById($productId);
        if (!$product)
        {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        if (isset($data['images'])){
            $this->fileUploadService->deleteMultipleFiles($product->images);

            $data['images'] = $this->fileUploadService->uploadMultipleFiles($data['images'], 'products');
        }
        $data['status'] = 'pending';
        $isUpdated = $this->productRepository->update($product, $data);
        if (!$isUpdated)
        {
            return [
                'success' => false,
                'message' => 'Product not updated'
            ];
        }

        return [
            'success' => true,
            'message' => 'Product updated',
            'product' => $product
        ];

    }

    public function deleteProduct(int $productId): array
    {
        $product = $this->productRepository->getProductById($productId);
        if (!$product)
        {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        $this->fileUploadService->deleteMultipleFiles($product->images);
        $isDeleted = $this->productRepository->delete($product);
        if (!$isDeleted)
        {
            return [
                'success' => false,
                'message' => 'Product not deleted'
            ];
        }
        return [
            'success' => true,
            'message' => 'Product deleted',
        ];
    }

    public function getSellerProductsWithFilters(int $sellerId, ProductFilterDTO $productFilterDTO): array
    {
        $product = $this->productRepository->getSellerProductsWithFilters($sellerId, $productFilterDTO);
        if (!$product)
        {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        return [
            'success' => true,
            'product' => $product
        ];
    }

    public function getProductDetails(int $productId): array
    {
        $product = $this->productRepository->getProductById($productId);
        if (!$product)
        {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }
        return [
            'success' => true,
            'product' => $product
        ];
    }
    public function generateSKU(): string
    {
        do {
            $sku = 'PRD-' . strtoupper(Str::random(8));
        } while ($this->productRepository->findBySku($sku));

        return $sku;
    }
}
