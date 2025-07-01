<?php

namespace App\Services\Exercise;

use App\Models\Exercise;
use App\Repositories\ExerciseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ExerciseService
{
    protected ExerciseRepository $exerciseRepository;

    public function __construct(ExerciseRepository $exerciseRepository)
    {
        $this->exerciseRepository = $exerciseRepository;
    }

    public function getExercises(int $page, int $perPage = 50): LengthAwarePaginator
    {
        $cacheKey = "exercises_page_{$page}_per_page_{$perPage}";
        return Cache::remember($cacheKey, 60*60 , function () use ($perPage){
            return $this->exerciseRepository->getAllExercises($perPage);
        });
    }

    public function getExerciseById(int $id): ?Exercise
    {
        $cacheKey = "exercise_{$id}";

        return Cache::remember($cacheKey, 60 * 60, function () use ($id) {
            return $this->exerciseRepository->getExerciseById($id);
        });
    }
}
