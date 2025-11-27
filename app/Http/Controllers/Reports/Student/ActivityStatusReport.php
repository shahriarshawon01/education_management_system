<?php

namespace App\Http\Controllers\Reports\Student;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class  ActivityStatusReport extends Controller
{
    public function showReport(Request $request)
    {

        
         $schoolId = $request->input('school_id');
         $sessionId = $request->input('session_year_id');
         $classId = $request->input('class_id');
         $fromDate = $request->input('from_date');
         $toDate = $request->input('to_date');
 
         
         $previousYearStart = Carbon::parse($fromDate)->subYear()->startOfYear();
         $previousYearEnd = Carbon::parse($toDate)->subYear()->endOfYear();
 
         
         $data = DB::table('students')
             ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
             ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
             ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
             ->selectRaw("
                 COUNT(DISTINCT CASE WHEN students.gender = 1 THEN students.id END) as activeMale,
                 COUNT(DISTINCT CASE WHEN students.gender = 2 THEN students.id END) as activeFemale,
                 COUNT(DISTINCT CASE WHEN students.gender IN (1, 2) THEN students.id END) as activeTotal,

                 COUNT(DISTINCT CASE WHEN students.status = 0 AND students.gender = 1 THEN students.id END) as inActiveMale,
                 COUNT(DISTINCT CASE WHEN students.status = 0 AND students.gender = 2 THEN students.id END) as inActiveFemale,
                 COUNT(DISTINCT CASE WHEN students.status = 0 AND students.gender IN (1, 2) THEN students.id END) as inActiveTotal,
 
                 COUNT(DISTINCT CASE WHEN students.status = 2 AND students.gender = 1 THEN students.id END) as drop_male,
                 COUNT(DISTINCT CASE WHEN students.status = 2 AND students.gender = 2 THEN students.id END) as drop_female,
                 COUNT(DISTINCT CASE WHEN students.status = 2 THEN students.id END) as drop_total,
 
                 schools.title as school_name,
                 session_years.title as session_name,
                 student_classes.name as class_name
             ")
             ->when($classId, function($q) use($classId){
                $q->where('students.class_id', $classId);
             })
             ->when($sessionId, function($q) use($sessionId){
                $q ->where('students.session_year_id', $sessionId);
             })
            //  ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            //      $query->whereBetween('students.created_at', [$fromDate, $toDate]);
            //  })
             ->groupBy('students.class_id')
             ->get();
             
            //  ddA($data);
        
         $singleData = $data->first();
 
         $school_name = $singleData ? $singleData->school_name : '';
         $session_name = $singleData ? $singleData->session_name : '';
 
         
         $multipleData = [
             'data' => $data,
             'school_name' => $school_name,
             'session_name' => $session_name,
             'formDate' => $fromDate,
             'toDate' => $toDate,
         ];
 
    
         return returnData(2000, $multipleData);
    }
}
