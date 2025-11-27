<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApproveLeave extends Model
{
    protected $table = 'approve_leaves';

    use HasFactory;

    protected $guarded = [];

    public function validate($input = [])
    {
        $validate = Validator::make($input, []);

        return $validate;
    }
}
