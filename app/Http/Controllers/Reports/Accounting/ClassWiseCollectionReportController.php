<?php

namespace App\Http\Controllers\Reports\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassWiseCollectionReportController extends Controller
{
    public function showReport(Request $request)
    {

    $schoolId = auth()->user()->school_id;
    $fromDate = input('from_date');
    $toDate = input('to_date');
    $classId = input('class_id');
    $componentId = input('component_id');

    $headerData = DB::table('components')
        ->select('id', 'name')
        ->whereIn('id', $componentId)
        ->where('school_id', $schoolId)
        // ->where('class_id', $classId)
        ->get();

    $componentIdss = $headerData->pluck('id')->toArray();

    $data = DB::table('payments')
        ->leftJoin('payment_details', 'payments.id', '=', 'payment_details.payments_id')
        ->leftJoin('student_classes', 'payments.class_id', '=', 'student_classes.id')
        ->leftJoin('schools', 'payments.school_id', '=', 'schools.id')
        ->leftJoin('components', 'payment_details.component_id', '=', 'components.id')
        ->selectRaw("
        payments.class_id,
        student_classes.name as class_name,
        schools.title as school_name,
        components.name as component_name,
        COUNT(DISTINCT payments.student_id) as student_count,
        SUM(payment_details.amount) as total_amount,
        COUNT(payment_details.component_id) as component_count,
        schools.steb_number as steb_number,schools.institute_code as code,schools.address as address")
        ->where('payments.school_id', $schoolId)
        ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('payments.date', [$fromDate, $toDate]);
        })
        ->when($classId, function ($query) use ($classId) {
            $query->where('payments.class_id', $classId);
        })
        ->whereIn('payment_details.component_id', $componentIdss)
        ->groupBy('payments.class_id', 'student_classes.name', 'components.name')
        ->get()
        
        ->groupBy('class_id');
        
    // ->toSql();

   

    
    $formattedData = [];
    foreach ($data as $classId => $classData) {
        $classRow = [
            'class_name' => $classData->first()->class_name,
            'student_count' => $classData->first()->student_count,
            'total_amount' => $classData->sum('total_amount'),
            'component_count' => $classData->sum('component_count'),
            'components' => []
        ];
        foreach ($headerData as $component) {
            $componentData = $classData->firstWhere('component_name', $component->name);
            $classRow['components'][$component->id]['name'] = $component->name;
            $classRow['components'][$component->id]['count'] = $componentData ? $componentData->component_count : 0;
            $classRow['components'][$component->id]['amount'] = $componentData ? $componentData->total_amount : 0;
        }
        $formattedData[] = $classRow;
    }

    $singleData = $data->first();

    $school_name = $singleData ? $singleData->pluck('school_name')->first() : '';
    $steb_number = $singleData ? $singleData->pluck('steb_number')->first() : '';
    $code = $singleData ? $singleData->pluck('code')->first() : '';
    $address = $singleData ? $singleData->pluck('address')->first() : '';

    $multipleData = [
        'headerData' => $headerData,
        'data' => $formattedData,
        'school_name' => $school_name,
        'formDate' => $fromDate,
        'toDate' => $toDate,
        'steb_number' => $steb_number,
        'code' => $code,
        'address' => $address,

     
    ];

    return returnData(2000, $multipleData);
}
    public function getComponentData()
    {
        $schoolId = input('school_id');

        $data['data'] = DB::table('components')
            ->where('status', 1)
            ->where('school_id', $schoolId)->get();
        return returnData(2000, $data);
    }

}
