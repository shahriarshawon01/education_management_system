<?php

namespace App\Models;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    use ModelScopes;
    use HasFactory;
    protected $guarded = [];

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
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id', 'id');
    }
    public function schools()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
    public function sessions()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id', 'id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
    public function components()
    {
        return $this->hasMany(PaymentDetails::class, 'payments_id', 'id');
    }
}
