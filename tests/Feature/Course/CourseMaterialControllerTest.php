<?php

namespace Tests\Feature\Course;

use App\Models\Auth\User;
use App\Models\Course\Course;
use App\Models\File;
use App\Models\Material\CourseMaterial;
use App\Models\Material\Material;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class CourseMaterialControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    public function testCanShowIndex()
    {
        $response = $this->get('/api/courseMaterial');

        $response->assertOk();
    }

    public function testCanCreateCourseMaterial()
    {
        $material = Material::factory()->create();
        $response = $this->post('/api/courseMaterial', [
            'course_id' => Course::factory()->create()->id,
            'material_id' => $material->id,
            'description' => File::find($material->source)->name,
            'required' => '00:00:15',
            'opened_at' => now(),
            'ended_at' => now()->addDays(5),
        ]);

        $response->assertCreated();
    }

    public function testCanUpdateCourseMaterial()
    {
        $courseMaterial = CourseMaterial::factory()->create();

        $response = $this->put('/api/courseMaterial/'.$courseMaterial->id, [
            'material_id' => $courseMaterial->material_id,
            'description' => '測試',
            'required_time' => 70,
            'opened_at' => null,
            'ended_at' => null,
        ]);
        $response->assertNoContent();
    }

    public function testCanShowCourseMaterial()
    {
        $response = $this->get('/api/courseMaterial/'.CourseMaterial::factory()->create()->id);
        $response->assertOk();
    }

    public function testCanDeleteCourseMaterial()
    {
        $response = $this->delete('/api/courseMaterial/'.CourseMaterial::factory()->create()->id);
        $response->assertNoContent();
    }

    public function testCanDownloadCourseMaterial()
    {
        $material = $this->createMaterial();

        $createResponse = $this->post('/api/courseMaterial', [
            'course_id' => Course::factory()->create()->id,
            'material_id' => $material->id,
            'description' => File::find($material->source)->name,
            'required' => '00:00:15',
            'opened_at' => now(),
            'ended_at' => now()->addDays(5),
        ]);
        $createResponse->assertCreated();

        $response = $this->get('/api/courseMaterial/download/'.$createResponse->json('id'));

        $this->assertTrue($response->baseResponse instanceof StreamedResponse);
    }

    public function testDownloadNotFound()
    {
        $material = $this->createMaterial();

        $createResponse = $this->post('/api/courseMaterial', [
            'course_id' => Course::factory()->create()->id,
            'material_id' => $material->id,
            'description' => File::find($material->source)->name,
            'required' => '00:00:15',
            'opened_at' => now(),
            'ended_at' => now()->addDays(5),
        ]);
        $createResponse->assertCreated();

        $material->update(['source' => 'abc']);

        $response = $this->get('/api/courseMaterial/download/'.$createResponse->json('id'));
        $response->assertNotFound();
    }

    private function createMaterial(): Material
    {
        Storage::fake('local');
        $directory = 'avatars';
        $response = $this->post('/api/material', [
            'directory' => $directory,
            'type' => 0,
            'source' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $response->assertCreated();

        $material = Material::find($response->json('id'));

        Storage::disk('local')
            ->assertExists($directory.'/'.$material->source);

        return $material;
    }
}
