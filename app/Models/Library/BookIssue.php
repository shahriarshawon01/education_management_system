<?php

namespace App\Models\Library;


use App\Models\Student;
use App\Models\Scopes\ModelScopes;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookIssue extends Model
{
    protected $table = 'book_issues';
    protected $fillable = ['school_id','type','member_id','total_books','issue_date','created_by','updated_by'];
    use ModelScopes;
    use HasFactory;
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'type' => 'required',
            'issue_date' => 'required',
        ]);
        return $validate;
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    } 
    public function staff()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
   

public function lib_member()
{
    return $this->belongsTo(Membership::class, 'member_id')
        ->leftJoin('students', 'memberships.member_id', '=', 'students.id')
        ->leftJoin('teachers', 'memberships.member_id', '=', 'teachers.id')
        ->leftJoin('staff', 'memberships.member_id', '=', 'staff.id')
        ->select(
            'memberships.*',
            DB::raw("
                CASE 
                    WHEN memberships.type = 1 THEN students.student_name_en
                    WHEN memberships.type = 2 THEN teachers.name
                    WHEN memberships.type = 3 THEN staff.name
                    ELSE NULL
                END as member_name"),
            DB::raw('students.student_roll as student_roll'),
            DB::raw('teachers.employee_id as employee_id'),
            DB::raw('staff.employee_id as employee_id')
        );
}




}
