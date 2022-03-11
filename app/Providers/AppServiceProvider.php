<?php

namespace App\Providers;

use App\Services\AuditTrailService;
use App\Services\File\FileService;
use App\Services\Form\FormService;
use App\Services\Form\FormUnitService;
use App\Services\Form\FormWriteRecordService;
use App\Services\Form\Interfaces\IFormService;
use App\Services\Form\Interfaces\IFormUnitService;
use App\Services\Form\Interfaces\IFormWriteRecordService;
use App\Services\Interfaces\IDefaultWorkflowService;
use App\Services\Interfaces\IIgnoreThresholdFormService;
use App\Services\Interfaces\IManualFormService;
use App\Services\Interfaces\IProcessService;
use App\Services\Interfaces\IScheduleSendWorkflowFormService;
use App\Services\Interfaces\IThresholdFormService;
use App\Services\Interfaces\IWorkflowService;
use App\Services\Workflow\DefaultWorkflowService;
use App\Services\Workflow\IgnoreThresholdFormService;
use App\Services\Workflow\ManualFormService;
use App\Services\Workflow\ProcessService;
use App\Services\Workflow\ScheduleSendWorkflowFormService;
use App\Services\Workflow\ThresholdFormService;
use App\Services\Workflow\WorkflowService;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FileService::class, FileService::class);
        $this->app->bind(IFormService::class, FormService::class);
        $this->app->bind(IFormWriteRecordService::class, FormWriteRecordService::class);
        $this->app->bind(IManualFormService::class, ManualFormService::class);
        $this->app->bind(IThresholdFormService::class, ThresholdFormService::class);
        $this->app->bind(IIgnoreThresholdFormService::class, IgnoreThresholdFormService::class);
        $this->app->bind(IScheduleSendWorkflowFormService::class, ScheduleSendWorkflowFormService::class);
        $this->app->bind(IDefaultWorkflowService::class, DefaultWorkflowService::class);
        $this->app->bind(IFormUnitService::class, FormUnitService::class);
        $this->app->bind(IWorkflowService::class, WorkflowService::class);
        $this->app->bind(IProcessService::class, ProcessService::class);

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::listen(function (QueryExecuted $query) {
            $auditService = new AuditTrailService();
            $auditService->auditSql($query);
        });
    }
}
