<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveCategory extends Model
{
    protected $table = 'leave_categories';

    use HasFactory;

    protected $fillable = ['title', 'note', 'total_leave'];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'title' => 'required',
            'note' => 'required',
            'total_leave' => 'required',
        ]);

        return $validate;
    }
}
