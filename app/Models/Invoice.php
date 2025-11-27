<?php

namespace App\Models;

use App\Models\Employee\Employee;
use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    use ModelScopes;
    protected $fillable = [
        'invoice_type',
        'invoice_category',
        'invoice_type_id',
        'is_advance',
        'invoice_code',
        'total_amount',
        'total_waiver',
        'total_payable',
        'date',
        'to_date',
        'month',
        'created_by',
        'updated_by',
        'school_id'
    ];

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'invoice_type_id', 'id');
    }

      public function employee()
    {
        return $this->belongsTo(Employee::class, 'invoice_type_id', 'id');
    }
    
    public function guests()
    {
        return $this->belongsTo(Guest::class, 'invoice_type_id', 'id');
    }

    public function schools()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
    public function components()
    {
        return $this->hasMany(InvoiceDetails::class, 'invoice_id', 'id');
    }
    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }
}
