<?php

namespace App\Services\Interfaces;

use App\Models\Exam\Exam;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IExamService
{
    public function getManyByPagination(int $perPage): LengthAwarePaginator;

    public function getById(int $id): Exam;
}
