<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetails extends Model
{
    use HasFactory;
    use ModelScopes;
    protected $guarded = [];

    public function components()
    {
        return $this->belongsTo(Component::class, 'components_id', 'id');
    }
}
