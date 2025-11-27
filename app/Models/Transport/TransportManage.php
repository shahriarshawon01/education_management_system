<?php

namespace App\Models\Transport;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class TransportManage extends Model
{
    protected $table = 'transport_manages';
    protected $fillable = ['school_id','route_id','transport_name','transport_no','licence_no','register_date','running_date','color','total_seat','description'];
    use ModelScopes;
    use HasFactory;
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'route_id' => 'required',
            'transport_name' => 'required',
            'transport_no' => 'required',
            'licence_no' => 'required',
            'register_date' => 'required',
        ]);
        return $validate;
    }
    public function routes()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id', 'id');
    }
}
