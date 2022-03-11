<?php

namespace Tests\Feature\Exam;

use App\Models\Auth\User;
use App\Models\Exam\ExamFolder;
use App\Models\Exam\ExamQuestion;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ExamFolderControllerTest extends TestCase
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
        $response = $this->get('/api/exam/folder');

        $response->assertOk();
    }

    public function testCanShow()
    {
        $folder = ExamFolder::factory()->create();
        $response = $this->get('/api/exam/folder/'.$folder->id);

        $response->assertOk();
    }

    public function testShowCanReturnNoContent()
    {
        $folder = ExamFolder::factory()->create();
        $response = $this->get('/api/exam/folder/'.$folder->id + 1);

        $response->assertNotFound();
    }

    public function testCanCreateFolder()
    {
        $response = $this->post('/api/exam/folder', [
            'name' => 'Test',
            'parent_id' => null,
            'type' => ExamFolder::TYPE_PERSONAL,
            'created_by' => User::factory()->create()->id,
        ]);

        $response->assertCreated();
    }

    public function testCreateFolderCanDetectParentIdNotExists()
    {
        $id = ExamFolder::factory()->create()->id;
        $response = $this->post('/api/exam/folder', [
            'name' => 'Test',
            'parent_id' => $id + 1,
            'type' => ExamFolder::TYPE_PERSONAL,
            'created_by' => 0,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCanUpdate()
    {
        $folder = ExamFolder::factory()->create();
        $response = $this->patch('/api/exam/folder/'.$folder->id, [
            'name' => 'newName',
        ]);

        $response->assertNoContent();
    }

    public function testUpdateCanReturnNotFound()
    {
        $folder = ExamFolder::factory()->create();
        $response = $this->patch('/api/exam/folder/'.$folder->id + 1, [
            'name' => 'newName',
        ]);

        $response->assertNotFound();
    }

    public function testCanDelete()
    {
        $folder = ExamFolder::factory()->create();
        $response = $this->delete('/api/exam/folder/'.$folder->id);

        $response->assertNoContent();
    }
}
