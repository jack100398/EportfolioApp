<?php

use App\Http\Controllers\Admin\ModuleController;
// use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Course\CourseFormAuthController;
use App\Http\Controllers\Course\CourseMaterialController;
use App\Http\Controllers\Course\CourseMemberController;
use App\Http\Controllers\Course\CoursePlaceController;
use App\Http\Controllers\Course\CreditController;
use App\Http\Controllers\Course\FeedbackController;
use App\Http\Controllers\Course\MaterialController;
use App\Http\Controllers\Course\MaterialDownloadHistoryController;
use App\Http\Controllers\Course\Survey\CourseSurveyRecordController;
use App\Http\Controllers\Course\Survey\SurveyController;
use App\Http\Controllers\Course\Survey\SurveyQuestionController;
use App\Http\Controllers\DefaultCategoryController;
use App\Http\Controllers\Exam\ExamController;
use App\Http\Controllers\Exam\ExamFolderController;
use App\Http\Controllers\Exam\ExamQuestionController;
use App\Http\Controllers\Exam\ExamResultController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Form\FormController;
use App\Http\Controllers\Form\FormWriteRecordController;
use App\Http\Controllers\NominalRole\NominalRoleController;
use App\Http\Controllers\NominalRole\NominalRoleUserController;
use App\Http\Controllers\TrainingProgram\OccupationalClassController;
use App\Http\Controllers\TrainingProgram\TrainingProgramAttachmentController;
use App\Http\Controllers\TrainingProgram\TrainingProgramCategoryController;
use App\Http\Controllers\TrainingProgram\TrainingProgramController;
use App\Http\Controllers\TrainingProgram\TrainingProgramStepController;
use App\Http\Controllers\TrainingProgram\TrainingProgramStepTemplateController;
use App\Http\Controllers\TrainingProgram\TrainingProgramUnitController;
use App\Http\Controllers\TrainingProgram\TrainingProgramUserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\Workflow\DefaultWorkflowController;
use App\Http\Controllers\Workflow\IgnoreThresholdFormController;
use App\Http\Controllers\Workflow\ManualFormController;
use App\Http\Controllers\Workflow\ProcessController;
use App\Http\Controllers\Workflow\ThresholdFormController;
use App\Http\Controllers\Workflow\WorkflowController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/register', [AuthenticationController::class, 'register']);

// Route::apiResource('auditTrail', AuditTrailController::class);

Route::apiResource('form', FormController::class);
Route::get('/form/reviewed/list/', [FormController::class, 'showReviewedFormList']);
Route::patch('/form/reviewed/update', [FormController::class, 'updateReviewedForm']);
Route::patch('/form/update/base/{id}', [FormController::class, 'updateFormBaseSetting']);
Route::patch('/form/update/enabled/{id}', [FormController::class, 'formEnabled']);
Route::post('/form/store/copy', [FormController::class, 'formCopy']);
Route::post('/form/sendForm/list/', [FormController::class, 'getFormSendList']);
Route::apiResource('formWriteRecord', FormWriteRecordController::class);
Route::get('/formWriteRecord/detail/{workflowId}', [FormWriteRecordController::class, 'getResultFormWriteRecord']);
Route::apiResource('files', FileController::class);

Route::apiResource('exam/question', ExamQuestionController::class)->except('index');
Route::apiResource('exam/folder', ExamFolderController::class);
Route::apiResource('exam/result', ExamResultController::class)->except('index');
Route::post('exam/result/{id}/autoMark', [ExamResultController::class, 'autoMarkScore']);
Route::post('exam/result/{id}/manualMark', [ExamResultController::class, 'manualMarkScore']);
Route::get('exam/template', [ExamController::class, 'showTemplates']);
Route::apiResource('exam', ExamController::class);
Route::get('exam/{id}/show', [ExamController::class, 'showWithoutAnswer']);

Route::apiResource('assessmentType', CourseFormAuthController::class);

Route::apiResource('courseMember', CourseMemberController::class);

Route::apiResource('manualForm', ManualFormController::class);
Route::get('manualForm/program/{programId}', [ManualFormController::class, 'getByProgram']);

Route::get('courseMaterial/download/{id}', [CourseMaterialController::class, 'downloadCourseMaterial']);
Route::apiResource('courseMaterial', CourseMaterialController::class);
Route::apiResource('materialDownloadHistory', MaterialDownloadHistoryController::class);
Route::apiResource('defaultWorkflow', DefaultWorkflowController::class);
Route::apiResource('threshold', ThresholdFormController::class);
Route::apiResource('ignoreThresholdForm', IgnoreThresholdFormController::class);
Route::post('ignoreThresholdForm/stores', [IgnoreThresholdFormController::class, 'stores']);
Route::get('ignoreThresholdForm/showUser/{userId}/{originThresholdId}', [IgnoreThresholdFormController::class, 'showUser']);

Route::apiResource('feedback', FeedbackController::class);

// Training Program
Route::prefix('trainingProgram')->group(function () {
    Route::get('{id}/userRecord', [TrainingProgramController::class, 'userRecord']);
    Route::get('user/{id}/stepRecord', [TrainingProgramUserController::class, 'stepRecord']);
    Route::get('{id}/sync', [TrainingProgramController::class, 'getSyncedProgram']);
    Route::post('sync', [TrainingProgramController::class, 'sync']);
    Route::delete('sync/{fromId}/{toId}', [TrainingProgramController::class, 'unSync']);
    Route::get('{id}/attachment', [TrainingProgramAttachmentController::class, 'getByTrainingProgramId']);
    Route::get('{id}/authUnit', [TrainingProgramController::class, 'showAuthUnit']);
    Route::post('{id}/authUnit', [TrainingProgramController::class, 'storeAuthUnit']);
    Route::delete('{id}/authUnit/{unitId}', [TrainingProgramController::class, 'destroyAuthUnit']);
    Route::get('{id}/step/template', [TrainingProgramStepTemplateController::class, 'getByTrainingProgramId']);
    Route::get('{id}/category/{unitId}', [TrainingProgramCategoryController::class, 'getByTrainingProgramId']);
    Route::get('user/{id}/step', [TrainingprogramStepController::class, 'userSteps']);
    Route::post('copy', [TrainingProgramController::class, 'copy']);
    Route::apiResource('category', TrainingProgramCategoryController::class)->except('index')->names('program.category');
    Route::apiResource('attachment', TrainingProgramAttachmentController::class)->except('index');
    Route::apiResource('unit', TrainingProgramUnitController::class)->except('index', 'update')->names('program.unit');
    Route::apiResource('user', TrainingProgramUserController::class)->except('index')->names('program.user');
    Route::apiResource('step/template', TrainingProgramStepTemplateController::class)->except('index')->names('program.template');
    Route::apiResource('step', TrainingProgramStepController::class)->except('index')->names('program.step');
});
Route::apiResource('occupationalClass', OccupationalClassController::class);
Route::apiResource('trainingProgram', TrainingProgramController::class);

Route::put('/survey/copy/{id}', [SurveyController::class, 'copy']);
Route::apiResource('survey', SurveyController::class);

Route::apiResource('surveyQuestion', SurveyQuestionController::class);
Route::apiResource('courseSurveyRecord', CourseSurveyRecordController::class);

Route::apiResource('workflow', WorkflowController::class);
Route::get('/workflow/error/index', [WorkflowController::class, 'getByErrorIndex']);
Route::get('/workflow/type/thresholdForm', [WorkflowController::class, 'getThresholdWorkflow']);
Route::get('/workflow/batch/{id}', [WorkflowController::class, 'getCanBatchModifyWorkflow']);

Route::apiResource('process', ProcessController::class);
Route::patch('/process/return/{id}', [ProcessController::class, 'returnWorkflow']);
Route::patch('/process/disagree/{id}', [ProcessController::class, 'disagree']);
Route::patch('/process/updateSignBy/{id}', [ProcessController::class, 'updateSignBy']);
Route::patch('/process/updateRole/{id}', [ProcessController::class, 'updateRole']);
Route::patch('/process/batch/update', [ProcessController::class, 'updateBatchModifyProcess']);
Route::apiResource('defaultCategory', DefaultCategoryController::class);

Route::apiResource('nominalRole', NominalRoleController::class);
Route::apiResource('nominalRoleUser', NominalRoleUserController::class)->except('index', 'update');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('course/{id}/share/{programCategoryId}', [CourseController::class, 'share']);
    Route::get('coursePlace/getList/{id}', [CoursePlaceController::class, 'getByParentId']);
    Route::apiResource('coursePlace', CoursePlaceController::class);
    Route::get('unit/getAll', [UnitController::class, 'getAll']);
    Route::post('unit/user', [UnitController::class, 'addUser']);
    Route::apiResource('unit', UnitController::class);
    Route::get('credit/getByYear/{year}', [CreditController::class, 'getHospitalCreditList']);
    Route::get('credit/getHospitalCreditList', [CreditController::class, 'getHospitalCreditList']);
    Route::get('credit/getContinueCreditList/{id}', [CreditController::class, 'getContinueCreditList']);
    Route::apiResource('credit', CreditController::class);
    Route::get('course/getTargetList', [CourseController::class, 'getCourseTargetList']);
    Route::apiResource('course', CourseController::class);
    Route::post('course/search', [CourseController::class, 'search']);
    Route::post('unit/user', [UnitController::class, 'addUser']);
    Route::get('unit/getAll', [UnitController::class, 'getAll']);
    Route::apiResource('unit', UnitController::class);

    Route::get('material/downloadMaterialFolder/{id}', [MaterialController::class, 'downloadMaterialFolder']);
    Route::get('material/downloadMaterial/{id}', [MaterialController::class, 'downloadMaterial']);
    Route::get('material/getAuthUnits/{id}', [MaterialController::class, 'getAuthUnits']);
    Route::get('material/getAuthUsers/{id}', [MaterialController::class, 'getAuthUsers']);
    Route::post('material/deAuthUser', [MaterialController::class, 'deAuthUser']);
    Route::post('material/deAuthUnit', [MaterialController::class, 'deAuthUnit']);
    Route::post('material/authUnit', [MaterialController::class, 'authUnit']);
    Route::post('material/authUser', [MaterialController::class, 'authUser']);
    Route::apiResource('material', MaterialController::class);
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    Route::apiResource('users', UserController::class);
    Route::post('/users/info', [UserController::class, 'info']);

    Route::apiResource('roles', RoleController::class);
    Route::get('/roles/{roleId}/permissions', [PermissionController::class, 'showUserPermissions']);
    Route::put('/roles/{roleId}/permissions', [PermissionController::class, 'updateUserPermissions']);

    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('modules', ModuleController::class);
});
