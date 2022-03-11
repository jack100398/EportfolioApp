<?php

namespace Tests\Unit\Models\Course;

use App\Models\Course\Credit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $credit = Credit::factory()->make();

        $this->assertTrue($credit  instanceof Credit);
    }

    public function testSaveToDatabase(): void
    {
        $credit = Credit::factory()->make();

        $credit->save();

        $this->assertIsNumeric($credit->id);
    }

    public function testHasChild(): void
    {
        $credit = Credit::factory()
                        ->has(Credit::factory(3), 'children')->create();

        $creditCount = Credit::where('parent_id', '=', $credit->id)->count();

        $this->assertTrue($creditCount === 3);
    }

    public function testHasParent(): void
    {
        $credit = Credit::factory()
                        ->for(Credit::factory(), 'parent')->create();

        $parentId = $credit->parent->id;

        $this->assertTrue($parentId === $credit->parent_id);
    }
}
