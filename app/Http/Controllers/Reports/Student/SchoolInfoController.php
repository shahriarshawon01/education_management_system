<?php

namespace App\Http\Controllers\Reports\Student;

use App\Http\Controllers\Controller;
use App\Models\School;

class SchoolInfoController extends Controller
{
    public function getSchoolInfo()
    {
        $school = School::first();

        return response()->json([
            'name' => $school->name ?? 'TMSS Public School & College',
            'address' => $school->address ?? 'Joypur Para, Bogura-5800',
            'school_logo' => $school->logo ?? null
        ]);
    }
}
