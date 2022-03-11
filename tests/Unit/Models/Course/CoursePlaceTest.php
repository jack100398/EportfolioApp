<?php

namespace Tests\Unit\Models\Course;

use App\Models\Course\CoursePlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CoursePlaceTest extends TestCase
{
    use RefreshDatabase;

    public function testModelsCanBeInstantiated(): void
    {
        $coursePlace = CoursePlace::factory()->make();

        $this->assertTrue($coursePlace  instanceof CoursePlace);
    }

    public function testSaveToDatabase(): void
    {
        $coursePlace = CoursePlace::factory()->make();

        $coursePlace->save();

        $this->assertIsNumeric($coursePlace->id);
    }

    public function testHasChild(): void
    {
        $coursePlace = CoursePlace::factory()
                        ->has(CoursePlace::factory(3), 'children')->create();

        $placeCount = CoursePlace::where('parent_id', '=', $coursePlace->id)->count();

        $this->assertTrue($placeCount === 3);
    }

    public function testHasParent(): void
    {
        $coursePlace = CoursePlace::factory()
                        ->for(CoursePlace::factory(), 'parent')->create();

        $parentId = $coursePlace->parent->id;

        $this->assertTrue($parentId === $coursePlace->parent_id);
    }
}
