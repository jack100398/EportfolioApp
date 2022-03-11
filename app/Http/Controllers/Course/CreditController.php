<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Course\CreateCreditRequest;
use App\Http\Requests\Course\UpdateCreditRequest;
use App\Models\Course\Credit;
use App\Services\Course\CreditService;
use Illuminate\Http\JsonResponse;

class CreditController extends BaseApiController
{
    private CreditService $service;

    public function __construct(CreditService $creditService)
    {
        $this->service = $creditService;
    }

    public function index(): JsonResponse
    {
        return $this->respondOk($this->service->getManyByPagination(10));
    }

    public function getHospitalCreditList(): JsonResponse
    {
        return $this->respondOk($this->service->getHospitalCredits());
    }

    public function getContinueCreditList(int $parentId): JsonResponse
    {
        return $this->respondOk($this->service->getContinueCredits($parentId));
    }

    public function show(int $id): JsonResponse
    {
        $credit = $this->service->getCreditById($id);

        return $this->respondOk($credit);
    }

    public function store(CreateCreditRequest $request): JsonResponse
    {
        $id = $this->service->create($request->all());

        return $this->respondCreated($id);
    }

    public function update(int $id, UpdateCreditRequest $request): JsonResponse
    {
        $this->service->update($id, $request->all());

        return $this->respondNoContent();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteCreditById($id);

        return $this->respondNoContent();
    }

    public function getByYear(int $year): JsonResponse
    {
        return $this->respondOk($this->service->getByYear($year));
    }
}
