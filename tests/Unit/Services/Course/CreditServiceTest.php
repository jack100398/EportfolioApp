<?php

namespace Tests\Unit\Services\Course;

use App\Models\Course\Credit;
use App\Services\Course\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditServiceTest extends TestCase
{
    use RefreshDatabase;

    private CreditService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CreditService();
    }

    public function testCanGet()
    {
        $creditId = Credit::factory()->create()->id;

        $subject = $this->service->getCreditById($creditId);

        $this->assertTrue($subject instanceof Credit);
    }

    public function testCanInsert()
    {
        $data = Credit::factory()->make()->toArray();

        $creditId = $this->service->create($data);

        $this->assertTrue($creditId > 0);
    }

    public function testCanUpdate()
    {
        $data = ['credit_name' => 'new'];

        $creditId = Credit::factory()->create(['credit_name' => 'old'])->id;

        $this->service->update($creditId, $data);

        $creditName = Credit::find($creditId)->credit_name;

        $this->assertSame($creditName, 'new');
    }

    public function testCanDelete()
    {
        $creditId = Credit::factory()->create()->id;
        $this->assertTrue($creditId > 0);

        $this->service->deleteCreditById($creditId);
        $credit = Credit::find($creditId);
        $this->assertNull($credit);
    }
}
