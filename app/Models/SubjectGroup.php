<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class SubjectGroup extends Model
{
    protected $table = 'subject_groups';
    use HasFactory;

    protected $guarded = [];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required'
        ]);

        return $validate;
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'subject_group_id');
    }
}
