<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    protected $table = 'addresses';
    use ModelScopes;
    use HasFactory;

    protected $fillable = [
        'school_id',
        'addressable_id',
        'addressable_type',
        'type',
        'village',
        'post_office',
        'union',
        'upazila',
        'district',
        'division'
    ];

    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'type' => 'required',
            'village' => 'required',
        ]);
        return $validate;
    }

    public function districts()
    {
        return $this->belongsTo(District::class, 'district', 'id');
    }
}
