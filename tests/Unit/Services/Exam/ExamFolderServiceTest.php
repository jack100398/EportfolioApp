<?php

namespace Tests\Unit\Services\Exam;

use App\Models\Auth\User;
use App\Models\Exam\ExamFolder;
use App\Models\Exam\ExamQuestion;
use App\Services\Exam\ExamFolderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamFolderServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExamFolderService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ExamFolderService();
    }

    public function testUpdateCanThrowException()
    {
        $this->expectException(ModelNotFoundException::class);

        $folder = ExamFolder::factory()->create();

        $this->service->update($folder->id + 1, [
            'name' => 'newName',
        ]);
    }

    public function testCanGetFolderQuestions()
    {
        $folder = ExamFolder::factory()
            ->has(ExamQuestion::factory(10))
            ->create();

        $folderQuestions = $this->service->getFolderQuestions($folder->id);

        $this->assertTrue($folderQuestions->count() === 10);
    }

    public function testCanGiveAuthorizationToUser()
    {
        $folder = ExamFolder::factory()->create();
        $user = User::factory()->create(['deleted_at'=>null]);

        $this->service->giveAuthorizationToUser($folder->id, $user->id);

        $user = $folder->refresh()->authUsers()->find($user->id);
        $this->assertNotNull($user);
    }
}
