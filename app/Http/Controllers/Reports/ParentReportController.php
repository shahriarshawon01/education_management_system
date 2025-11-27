<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classId = $request->class_id;
        $statusId = $request->status;

        $data = DB::table('students')
            ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('parents', 'students.parent_id', '=', 'parents.id')
            ->selectRaw(
                "students.*,
                schools.title as school_name,
                schools.steb_number as steb_number,
                schools.institute_code as code,
                schools.address as address,
                parents.income as income,
                parents.profession as profession,
                student_classes.name as class_name",
            )
            ->where('students.school_id', $schoolId)
            ->when($classId, function ($query) use ($classId) {
                $query->where('students.class_id', $classId);
            })
            ->when(isset($statusId), function ($query) use ($statusId) {
                $query->where('students.status', $statusId);
            })
            ->get();
// dd($data);
      

        $single = $data->first();

        $responseData = [
            'data' => $data,
             'total_parents' => $data->pluck('parent_id')->unique()->count(),
            'school_name' => $single ? $single->school_name : '',
            'steb_number' => $single ? $single->steb_number : '',
            'code' => $single ? $single->code : '',
            'address' => $single ? $single->address : '',
        ];

        return returnData(2000, $responseData,);
    }
}
