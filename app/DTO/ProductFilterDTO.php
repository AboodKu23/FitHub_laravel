<?php

namespace App\DTO;

class ProductFilterDTO
{
    public ?string $status;
    public ?int $categoryId;
    public ?string $search;
    public int $page;
    public int $perPage;

    public function __construct(array $data)
    {
        $this->status = $data['status'] ?? null;
        $this->categoryId = isset($data['category_id']) ? (int)$data['category_id'] : null;
        $this->search = $data['search'] ?? null;
        $this->perPage = isset($data['per_page']) ? (int)$data['per_page'] : 15;
        $this->page = isset($data['page']) ? (int)$data['page'] : 1;
    }
}
