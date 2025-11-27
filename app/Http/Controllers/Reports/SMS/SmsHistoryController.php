<?php

namespace App\Http\Controllers\Reports\SMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmsHistoryController extends Controller
{
    public function showReport(Request $request)
    {
        $fromDate = $request->created_at ? $request->created_at . ' 00:00:00' : null;
        $toDate = $request->updated_at ? $request->updated_at . ' 23:59:59' : null;

        $data = DB::table('sms_history')
            ->select('*')
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $responseData = [
            'data' => $data,
        ];

        return returnData(2000, $responseData);
    }
}
