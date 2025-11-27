<?php

namespace App\Models;

use App\Models\Department;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parents extends Model
{
    use HasFactory;

    protected $table = 'parents';
    protected $guarded = [];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'name' => 'required',
            'phone' => 'required',
            'gender' => 'required',
            'profession' => 'required',
            'income' => 'required',
        ]);

        return $validate;
    }
    protected $casts = [
        'file' => 'json',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }
}
