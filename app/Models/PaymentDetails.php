<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetails extends Model
{
    protected $table = 'payment_details';
    use ModelScopes;
    use HasFactory;
    protected $fillable =
        [
            'payments_id',
            'component_id',
            'invoice_category',
            'paid_by_type',
            'amount',
            'waiver',
            'payed'
        ];

    public function components()
    {
        return $this->belongsTo(Component::class, 'component_id', 'id');
    }
}
