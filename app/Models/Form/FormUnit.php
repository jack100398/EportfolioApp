<?php

namespace App\Models\Form;

use Database\Factories\Form\FormUnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormUnit extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'form_unit';

    protected $fillable = ['form_id', 'unit_id'];

    protected static function newFactory(): FormUnitFactory
    {
        return FormUnitFactory::new();
    }
}
