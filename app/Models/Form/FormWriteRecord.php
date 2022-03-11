<?php

namespace App\Models\Form;

use App\Models\Workflow\Workflow;
use Database\Factories\Form\FormWriteRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormWriteRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const UPDATED_AT = null;

    protected $table = 'form_write_record';

    protected $fillable = ['workflow_id', 'user_id', 'result', 'flag'];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    protected static function newFactory(): FormWriteRecordFactory
    {
        return FormWriteRecordFactory::new();
    }
}
