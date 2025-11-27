<?php

namespace App\Models\Transport;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class TransportRoute extends Model
{
    protected $table = 'transport_routes';
    protected $fillable = ['school_id','route_name','route_fare','route_length','completing_hour','from','to'];
    use ModelScopes;
    use HasFactory;
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'route_name' => 'required',
            'route_fare' => 'required',
            'route_length' => 'required',
            'completing_hour' => 'required',
            'from' => 'required',
            'to' => 'required',
        ]);
        return $validate;
    }
}
