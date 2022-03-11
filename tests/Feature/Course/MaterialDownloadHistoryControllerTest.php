<?php

namespace Tests\Feature\Course;

use App\Models\Auth\User;
use App\Models\Material\CourseMaterial;
use App\Models\Material\MaterialDownloadHistory;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MaterialDownloadHistoryControllerTest extends TestCase
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
        $response = $this->get('/api/materialDownloadHistory');

        $response->assertOk();
    }

    public function testCanCreate()
    {
        $response = $this->post('/api/materialDownloadHistory', [
            'course_material_id' => CourseMaterial::factory()->create()->id,
            'opened_counts' => 0,
            'downloaded_counts' => 0,
            'reading_time' => '00:00:20',
        ]);

        $response->assertCreated();
    }

    public function testCanUpdate()
    {
        $history = MaterialDownloadHistory::factory()->create();

        $response = $this->put('/api/materialDownloadHistory/'.$history->id, [
            'opened_counts' => $history->opened_counts + 1,
            'downloaded_counts' => $history->downloaded_counts,
            'reading_time' => '00:20:00',
        ]);
        $response->assertNoContent();
    }

    public function testCanShow()
    {
        $response = $this->get('/api/materialDownloadHistory/'.MaterialDownloadHistory::factory()->create()->id);
        $response->assertOk();
    }

    public function testCanDelete()
    {
        $response = $this->delete('/api/materialDownloadHistory/'.MaterialDownloadHistory::factory()->create()->id);
        $response->assertNoContent();
    }
}
