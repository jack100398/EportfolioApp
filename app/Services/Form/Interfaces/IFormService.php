<?php

namespace App\Services\Form\Interfaces;

use App\Models\Form\Form;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IFormService
{
    public function create(array $data): Form;

    public function getManyByPagination(array $condition): LengthAwarePaginator;

    public function getById(int $id): Form;

    public function getReviewedByPagination(array $condition): LengthAwarePaginator;

    public function getWorkflowFormByPagination(array $conditions): LengthAwarePaginator;

    public function getReviewedForm(int $id): ?Form;

    public function updateForm(array $formRequest, Form $form, array $questionType): Form;

    public function storeFormCopy(Form $form, array $request): Form;

    public function batchUpdateFormReviewed(array $form_ids, int $reviewed): Collection;

    public function updateFormEnable(int $id, bool $is_enabled): void;

    public function updateFormBaseSetting(int $id, array $conditions): Form;
}
