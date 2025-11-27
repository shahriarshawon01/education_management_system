<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class admission_selection_mapping extends Model
{
    use HasFactory;

    protected $fillable=[
        'admission_subject_id',
        'school_id',
        // 'starting_time',
        // 'ending_time',
        // 'total_seat',
    ];
}
