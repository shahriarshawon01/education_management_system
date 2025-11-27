<?php

namespace App\Http\Controllers\Canteen;

use Carbon\Carbon;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\InvoiceDetails;
use App\Models\CanteenConfigure;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Canteen\CanteenDailySale;

class CanteenDailySaleController extends Controller
{
    public function __construct()
    {
        $this->model = new CanteenDailySale();
    }

    public function index()
    {
        if (!can('canteen_daily_sale_view')) {
            return $this->notPermitted();
        }

        try {
            $keyword = request()->input('keyword');
            $paymentDate = request()->input('date') ?? date('Y-m-d');
             $saleType = request()->input('sale_type');
             $memberType = request()->input('member_type');

            $data = $this->model
                ->with(['mealTime:id,name'])
                ->when($paymentDate, function ($query) use ($paymentDate) {
                    $query->where('date', $paymentDate);
                })
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%{$keyword}%")
                            ->orWhere('phone', 'LIKE', "%{$keyword}%")
                            ->orWhere('amount', 'LIKE', "%{$keyword}%");
                    });
                })
                ->when($saleType, function ($query) use ($saleType) {
                    $query->where('sale_type', 'Like', "%$saleType%");
                })  
                ->when($memberType, function ($query) use ($memberType) {
                    $query->where('member_type', 'Like', "%$memberType%");
                })
                ->orderBy('id', 'desc')
                ->paginate(request()->input('perPage'));

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function store(Request $request)
    {
        if (!can('canteen_daily_sale_add')) {
            return $this->notPermitted();
        }

        try {
            $input = $request->all();
            $menuArray = [];
            if (!empty($input['menu_id'])) {
                if (is_string($input['menu_id'])) {
                    $menuArray = json_decode($input['menu_id'], true) ?: [];
                } elseif (is_array($input['menu_id'])) {
                    $menuArray = $input['menu_id'];
                }
            }
            $input['menu_id'] = $menuArray;
            $payment = new CanteenDailySale();
            $validator = $payment->validate($input);
            if ($validator->fails()) {
                return returnData(3000, $validator->errors());
            }

            $input['menu_id'] = json_encode($menuArray);
            $input['month'] = isset($input['date']) ? Carbon::parse($input['date'])->month : null;
            $input['school_id'] = Auth::user()->school_id ?? null;
            $input['created_by'] = Auth::id();

            $memberType = $request->member_type;
            $input['member_id'] = match ($memberType) {
                1 => $request->student_id,
                2 => $request->employee_primary_id,
                default => null,
            };

            if (isset($input['sale_type'])) {
                if ($input['sale_type'] == 1) {
                    $input['voucher_number'] = $this->generateUniqueInvoiceNumber();
                } elseif ($input['sale_type'] == 2) {
                    $input['invoice_generate'] = 1;
                }
            }

            $sale = CanteenDailySale::create($input);

            return returnData(2000, $sale, 'Daily sale has been recorded successfully!');

        } catch (\Exception $e) {
            return returnData(5000, $e->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    private function generateUniqueInvoiceNumber()
    {
        do {
            $invoiceNumber = 'INV-CASH-' . date('Ymd') . '-' . rand(1000, 9999);
            $existsInPayments = CanteenDailySale::where('voucher_number', $invoiceNumber)->exists();
        } while ($existsInPayments);

        return $invoiceNumber;
    }
    public function addCashSaleInvoice(Request $request)
    {
        if (!can('canteen_daily_sale_add')) {
            return $this->notPermitted();
        }

        try {
            $dataList = collect($request->all());
            $authUserId = auth()->id();

            if ($dataList->isEmpty()) {
                return returnData(5000, null, 'No data provided.');
            }
            $cashSales = $dataList->where('sale_type', 1)->values();

            if ($cashSales->isEmpty()) {
                return returnData(3000, null, 'No cash sales found to generate invoice.');
            }

            $alreadyGenerated = $cashSales->where('invoice_generate', 1);
            $toGenerate = $cashSales->where('invoice_generate', 0);

            if ($toGenerate->isEmpty()) {
                $date = $alreadyGenerated->first()['date'] ?? now()->toDateString();
                return returnData(3000, null, "Invoice already created for {$date}.");
            }

            $paymentDate = $toGenerate->first()['date'] ?? now()->toDateString();
            $totalAmount = $cashSales->sum(fn($item) => floatval($item['amount'] ?? 0));

            $existingInvoice = Invoice::whereDate('date', $paymentDate)
                ->where('invoice_category', 5)
                ->where('invoice_type', 3)
                ->first();

            $componentId = CanteenConfigure::where('status', 1)
                ->value('component');

            $dateObj = Carbon::parse($paymentDate);
            $month = strtolower($dateObj->format('F'));
            $formattedMonth = strtolower($dateObj->format('M'));
            $formattedYear = $dateObj->format('y');
            $invoiceCode = "canteen-cash-sale-{$formattedMonth}-{$formattedYear}";

            if ($existingInvoice) {
                $existingInvoice->update([
                    'total_amount' => $totalAmount,
                    'total_payable' => $totalAmount,
                    'updated_by' => $authUserId,
                    'updated_at' => now(),
                ]);

                InvoiceDetails::where('invoice_id', $existingInvoice->id)
                    ->update([
                        'invoice_amount' => $totalAmount,
                        'payable_amount' => $totalAmount,
                        'updated_at' => now(),
                    ]);

                $invoice = $existingInvoice;
                $message = "Cash sale invoice updated successfully!";
            } else {
                $invoice = Invoice::create([
                    'school_id' => auth()->user()->school_id ?? 1,
                    'date' => $paymentDate,
                    'to_date' => $paymentDate,
                    'month' => $month,
                    'invoice_code' => $invoiceCode,
                    'total_amount' => $totalAmount,
                    'total_waiver' => 0,
                    'total_payable' => $totalAmount,
                    'invoice_type' => 3,
                    'invoice_type_id' => null,
                    'is_advance' => 0,
                    'invoice_category' => 5,
                    'created_by' => $authUserId,
                ]);

                InvoiceDetails::create([
                    'invoice_id' => $invoice->id,
                    'components_id' => $componentId,
                    'invoice_amount' => $totalAmount,
                    'waiver_amount' => 0,
                    'invoice_category' => 5,
                    'payable_amount' => $totalAmount,
                ]);

                $message = "Cash sale invoice generated successfully!";
            }

            $paymentIds = $cashSales->pluck('id')->toArray();

            DB::table('canteen_daily_sales')
                ->whereIn('id', $paymentIds)
                ->update([
                    'invoice_generate' => 1,
                    'updated_at' => now(),
                ]);

            return returnData(2000, $invoice, $message);
        } catch (\Exception $e) {
            return returnData(5000, $e->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        if (!can('canteen_daily_sale_view')) {
            return $this->notPermitted();
        }

        try {
            $payment = $this->model
                ->with([
                    'mealTime:id,name',
                    'creator:id,username',
                    'school:id,title,phone,email,address',
                    
                ])
                ->find($id);


            if (!$payment) {
                return returnData(5000, 'Cash payment not found!');
            }
            $menus = $payment->menus ?? [];
            $data = [
                'payment' => [
                    'id' => $payment->id,
                    'name' => $payment->name,
                    'phone' => $payment->phone,
                    'amount' => $payment->amount,
                    'voucher_number' => $payment->voucher_number,
                    'invoice_generate' => $payment->invoice_generate,
                    'date' => $payment->date,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                    'meal_time' => $payment->mealTime,
                    'created_by' => $payment->createdBy,
                       'school' => $payment->school,
                ],
                'menus' => $menus,
            ];

            return returnData(2000, $data);

        } catch (\Exception $e) {
            return returnData(5000, $e->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

//     public function show($id)
// {
//     if (!can('canteen_daily_sale_view')) {
//         return $this->notPermitted();
//     }

//     try {
//         $payment = $this->model
//             ->with([
//                 'mealTime:id,name',
//                 'creator:id,username',
//                 'school:id,title,phone,email,address',
//             ])
//             ->find($id);

//         if (!$payment) {
//             return returnData(5000, 'Cash payment not found!');
//         }

//         $menus = $payment->menus ?? [];

//         $data = [
//             'payment' => [
//                 'id' => $payment->id,
//                 'name' => $payment->name,
//                 'phone' => $payment->phone,
//                 'amount' => $payment->amount,
//                 'voucher_number' => $payment->voucher_number,
//                 'invoice_generate' => $payment->invoice_generate,
//                 'date' => $payment->date,
//                 'created_at' => $payment->created_at,
//                 'updated_at' => $payment->updated_at,
//                 'meal_time' => $payment->mealTime,
//                 'created_by' => $payment->creator,
//                 'school' => $payment->school, // ✅ এখন আসবে
//             ],
//             'menus' => $menus,
//         ];

//         return returnData(2000, $data);

//     } catch (\Exception $e) {
//         return returnData(5000, $e->getMessage(), 'Whoops, Something Went Wrong..!!');
//     }
// }


    public function destroy($id)
    {
        if (!can('canteen_daily_sale_delete')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->where('id', $id)->first();

            if (!$data) {
                return returnData(5000, null, 'Data not found');
            }
            if ($data->invoice_generate == 1) {
                return returnData(5000, null, 'Invoice has already been generated. Please contact accountant to delete invoice!');
            }

            $data->delete();

            return returnData(2000, $data, 'Successfully Deleted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}
