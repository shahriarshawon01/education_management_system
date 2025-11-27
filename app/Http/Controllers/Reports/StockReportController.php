<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $productId = $request->product_id;
        $categoryId = $request->category_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $statusId = $request->status;

        $data = DB::table('stock_outs')
            ->leftJoin('schools', 'stock_outs.school_id', '=', 'schools.id')
            ->leftJoin('stock_ins', 'stock_outs.stock_id', '=', 'stock_ins.id')
            ->leftJoin('store_products', 'stock_outs.product_id', '=', 'store_products.id')
            ->leftJoin('store_categories', 'store_products.category_id', '=', 'store_categories.id')
            ->selectRaw(
                "stock_outs.*,
                schools.title as school_name,
                schools.steb_number as school_steb,
                schools.reg_number as registration_no,
                schools.institute_code as institute_code,
                schools.address as school_address,
                stock_ins.product_code as product_code,
                stock_ins.purchase_date as purchase_date,
                stock_ins.purchase_price as purchase_price,
                stock_ins.sale_price as sale_price,
                stock_ins.purchase_quantity as purchase_quantity,
                stock_ins.sale_quantity as sale_quantity,

                store_products.name as product_name,
                store_categories.name as category_name",
            )
            ->where('stock_outs.school_id', $schoolId)
            ->when($productId, function ($query) use ($productId) {
                $query->where('store_products.id', $productId);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('store_categories.id', $categoryId);
            })
            ->when(isset($statusId), function ($query) use ($statusId) {
                $query->where('stock_outs.status', $statusId);
            })
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('stock_outs.sale_date', [$fromDate, $toDate]);
            })
            ->get();

        $single = $data->first();

        $responseData = [
            'data' => $data,
            'school_name' => $single ? $single->school_name : '',
            'registration_no' => $single ? $single->registration_no : '',
            'school_steb' => $single ? $single->school_steb : '',
            'school_address' => $single ? $single->school_address : '',
        ];

        return returnData(2000, $responseData,);
    }
}
