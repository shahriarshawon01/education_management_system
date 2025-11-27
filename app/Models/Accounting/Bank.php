<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Bank extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'account_name',
        'account_number',
        'branch',
        'routing_number',
        'swift_code',
        'address',
        'status',
        'school_id',
    ];

    public function validate($input)
    {
        return Validator::make($input, [
            'name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'routing_number' => 'required|integer',
        ]);
    }
}
