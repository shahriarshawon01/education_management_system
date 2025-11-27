<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class admission_subjectwise_marks_mapping extends Model
{
    use HasFactory;
    use ModelScopes;
    protected $table='admission_subjectwise_marks_mappings';
    protected $primaryKey='id';
    protected $softDelete = true;
    protected $fillable=[
        'marks_entry_id',
        'applicant_id',
        'school_id',
        'subject_id',
        'subject_marks',
        'status',
    ];


    public function subjectname()
    {
        return $this->belongsTo(admission_selection_subject_mapping::class, 'subject_id', 'id');
    }
}

