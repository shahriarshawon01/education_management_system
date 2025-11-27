<?php

namespace App\Http\Controllers\Accounting;

use App\Helpers\Helper;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\InvoiceDetails;
use App\Models\PaymentDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StudentPaymentController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new Payment();
    }

    public function index()
    {
        if (!can('student_payment_view')) {
            return $this->notPermitted();
        }

        try {
            $keyword = request()->input('keyword');
            $class = request()->input('class_id');
            $section = request()->input('section_id');
            $paymentStatus = request()->input('payment_method');
            $invoiceCategory = request()->input('invoice_category');

            $data = $this->model::select(
                'payments.*',
                'students.student_name_en',
                'student_classes.name as class_name',
                'departments.department_name as student_department',
                'session_years.title as session_title',
                'students.student_roll',
                'sections.name as section_name',
                'created_users.username as created_by',
                'updated_users.username as updated_by',
                'schools.title as school_name',
                'e.name as employee_name',
                'e.phone as employee_phone',
                'e.employee_id',
                'e.employee_type',
                'employee_departments.name as employee_department',
                'employee_designations.name as employee_designation',
                'inv.invoice_code',
                'inv.total_payable',
                'inv.is_advance',
                DB::raw('(SELECT SUM(p2.total_payed) FROM payments AS p2 WHERE p2.invoice_id = payments.invoice_id) AS invoice_total_paid'),
                DB::raw('(SELECT SUM(p3.total_payed) FROM payments AS p3 WHERE p3.invoice_id = payments.invoice_id AND p3.id < payments.id) AS previous_payments')
            )
                ->leftJoin('students', function ($join) {
                    $join->on('students.id', '=', 'payments.paid_by_id')
                        ->where('payments.paid_by_type', 1);
                })
                ->leftJoin('sections', 'sections.id', '=', 'students.section_id')
                ->leftJoin('departments', 'departments.id', '=', 'students.department_id')

                ->leftJoin('student_classes', 'student_classes.id', '=', 'payments.class_id')
                ->leftJoin('session_years', 'session_years.id', '=', 'payments.session_year_id')
                ->leftJoin('users as created_users', 'created_users.id', '=', 'payments.created_by')
                ->leftJoin('users as updated_users', 'updated_users.id', '=', 'payments.updated_by')
                ->leftJoin('schools', 'schools.id', '=', 'payments.school_id')

                ->leftJoin('employees as e', function ($join) {
                    $join->on('e.id', '=', 'payments.paid_by_id')
                        ->where('payments.paid_by_type', 2);
                })
                ->leftJoin('employee_departments', 'employee_departments.id', '=', 'e.department_id')
                ->leftJoin('employee_designations', 'employee_designations.id', '=', 'e.designation_id')
                ->leftJoin('invoices as inv', 'inv.id', '=', 'payments.invoice_id')
                ->when($paymentStatus, function ($query) use ($paymentStatus) {
                    $query->where('payments.payment_type', 'like', "%$paymentStatus%");
                })
                ->when($invoiceCategory, function ($query) use ($invoiceCategory) {
                    $query->where('payments.invoice_category', 'like', "%$invoiceCategory%");
                })
                ->when($keyword, function ($q) use ($keyword) {
                    $q->where(function ($sub) use ($keyword) {
                        $sub->where('students.student_name_en', 'like', "%$keyword%")
                            ->orWhere('students.student_roll', 'like', "%$keyword%")
                            ->orWhere('e.name', 'like', "%$keyword%")
                            ->orWhere('e.employee_id', 'like', "%$keyword%");
                    });
                })
                ->when($class, function ($query) use ($class) {
                    $query->where('payments.class_id', $class);
                })
                ->when($section, function ($query) use ($section) {
                    $query->where('students.section_id', $section);
                })
                ->orderBy('payments.id', 'desc')
                ->paginate(input('perPage'));

            return returnData(2000, $data);

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if (!can('student_payment_add')) {
            return $this->notPermitted();
        }

        DB::beginTransaction();
        try {
            $school_id = auth()->user()->school_id;
            $createdBy = auth()->user()->id;
            $category = $request->input('payment_category');

            if ($category == 5) {
                if ($request->has('components') && is_array($request->components)) {
                    foreach ($request->components as $comp) {
                        $payment = Payment::create([
                            'school_id' => $school_id,
                            'invoice_id' => $comp['invoice_id'],
                            'invoice_category' => $comp['invoice_category'],
                            'paid_by_type' => 3, // Guest
                            'paid_by_id' => null,
                            'class_id' => null,
                            'session_year_id' => null,
                            'bank_id' => $request->input('bank_id'),
                            'total_payed' => $comp['payable_amount'],
                            'payment_type' => $request->input('payment_type'),
                            'date' => $request->input('date'),
                            'voucher_number' => $this->generateUniqueInvoiceNumber(),
                            'created_by' => $createdBy,
                        ]);

                        PaymentDetails::create([
                            'payments_id' => $payment->id,
                            'component_id' => (int) $comp['components_id'],
                            'invoice_category' => $comp['invoice_category'],
                            'paid_by_type' => 3,
                            'paid_by_id' => null,
                            'amount' => $comp['invoice_amount'],
                            'waiver' => $comp['waiver_amount'],
                            'payed' => $comp['payable_amount'],
                        ]);
                    }
                }

                DB::commit();
                return returnData(2000, null, 'Guest Payment Successful');
            }


            // for academic,canteen,dormitory,transport
            $invoice_id = $request->input('invoice_id');
            $new_payment_amount = $request->input('totalPayable');

            $paymentFor = (int) $request->input('payment_for');
            $payer_type = null;
            $payer_id = null;


            if ($paymentFor == 1) {
                $payer_type = 1;
                $payer_id = $request->input('student_id');
            } elseif ($paymentFor == 2) {
                $payer_type = 2;
                $payer_id = $request->input('employee_primary_id');
            } elseif ($paymentFor == 3) {
                $payer_type = 3;
                $payer_id = $request->input('guest_id');
            } else {
                return returnData(5000, null, 'Invalid payment for type selected.');
            }

            if (!$invoice_id || !$payer_type || !$payer_id) {
                return returnData(5000, null, 'Missing required payment information.');
            }

            $invoice = Invoice::where('id', $invoice_id)->first();

            if (!$invoice) {
                return returnData(5000, null, 'Invoice not found.');
            }

            if ($request->late_fine) {
                $fineComponent = collect($request->input('components'))->firstWhere('id', 28);

                if ($fineComponent) {
                    $existingFine = InvoiceDetails::where('invoice_id', $invoice_id)
                        ->where('components_id', 28)
                        ->first();

                    if (!$existingFine) {
                        InvoiceDetails::create([
                            'invoice_id' => $invoice_id,
                            'components_id' => 28,
                            'invoice_amount' => $fineComponent['invoice_amount'],
                            'waiver_amount' => $fineComponent['waiver_amount'],
                            'payable_amount' => $fineComponent['payable_amount'],
                        ]);

                        $invoice->total_payable += $fineComponent['payable_amount'];
                        $invoice->total_amount += $fineComponent['invoice_amount'];
                        $invoice->save();
                    }
                }
            }
            $invoice = Invoice::where('id', $invoice_id)->first();
            $total_paid = Payment::where('invoice_id', $invoice_id)->sum('total_payed');
            if (($total_paid + $new_payment_amount) > $invoice->total_payable) {
                return returnData(5000, null, 'Payment amount exceeds the invoice total payable.');
            }

            $payment = Payment::create([
                'school_id' => $school_id,
                'invoice_id' => $invoice_id,
                'invoice_category' => $request->input('payment_category'),
                'paid_by_type' => $payer_type,
                'paid_by_id' => $payer_id,
                'class_id' => $request->input('class_id'),
                'session_year_id' => $request->input('session_id'),
                'bank_id' => $request->input('bank_id'),
                'total_payed' => $new_payment_amount,
                'payment_type' => $request->input('payment_type'),
                'date' => $request->input('date'),
                'voucher_number' => $this->generateUniqueInvoiceNumber(),
                'created_by' => $createdBy,
            ]);

            // late fine issue 
            if ($request->has('components') && is_array($request->components)) {
                foreach ($request->input('components') as $data) {
                    $componentId = (int) $data['components_id'];
                    if ($componentId === 28 && !$request->late_fine) {
                        $fineExists = InvoiceDetails::where('invoice_id', $invoice_id)
                            ->where('components_id', 28)
                            ->exists();

                        if (!$fineExists) {
                            continue;
                        }
                    }

                    PaymentDetails::create([
                        'school_id' => $school_id,
                        'payments_id' => $payment->id,
                        'component_id' => (int) $data['components_id'],
                        'invoice_category' => (int) $data['invoice_category'],
                        'paid_by_type' => $payer_type,
                        'paid_by_id' => $payer_id,
                        'amount' => $data['invoice_amount'],
                        'waiver' => $data['waiver_amount'],
                        'payed' => $data['payable_amount'],
                    ]);
                }
            }

            DB::commit();
            return returnData(2000, null, 'Payment Successful');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        if (!can('student_payment_view')) {
            return $this->notPermitted();
        }

        try {
            $payment = DB::table('payments')
                ->select(
                    'payments.*',
                    // student info
                    'students.student_name_en',
                    'students.student_roll',
                    'students.merit_roll',
                    'student_classes.name as class_name',
                    'sections.name as section_name',
                    'departments.department_name as student_department',
                    // employee info
                    'e.name as employee_name',
                    'e.employee_id',
                    'e.phone as employee_phone',
                    'employee_departments.name as employee_department',
                    'employee_designations.name as employee_designation',
                    // school/session/invoice
                    'schools.title as school_title',
                    'schools.phone as school_phone',
                    'schools.email as school_email',
                    'schools.address as school_address',
                    'session_years.title as session_title',
                    'invoices.invoice_code',
                    'invoices.month as invoice_month'
                )
                // join students if paid_by_type = 1
                ->leftJoin('students', function ($join) {
                    $join->on('students.id', '=', 'payments.paid_by_id')
                        ->where('payments.paid_by_type', 1);
                })
                ->leftJoin('sections', 'sections.id', '=', 'students.section_id')
                ->leftJoin('departments', 'departments.id', '=', 'students.department_id')
                ->leftJoin('student_classes', 'student_classes.id', '=', 'payments.class_id')
                // join employees if paid_by_type = 2
                ->leftJoin('employees as e', function ($join) {
                    $join->on('e.id', '=', 'payments.paid_by_id')
                        ->where('payments.paid_by_type', 2);
                })
                ->leftJoin('employee_departments', 'employee_departments.id', '=', 'e.department_id')
                ->leftJoin('employee_designations', 'employee_designations.id', '=', 'e.designation_id')

                // common joins
                ->leftJoin('schools', 'schools.id', '=', 'payments.school_id')
                ->leftJoin('session_years', 'session_years.id', '=', 'payments.session_year_id')
                ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
                ->where('payments.id', $id)
                ->first();

            if (!$payment) {
                return returnData(5000, 'Payment not found!');
            }

            $paymentComp = DB::table('payment_details')
                ->select(
                    'payment_details.*',
                    'components.name as component_name'
                )
                ->leftJoin('components', 'components.id', '=', 'payment_details.component_id')
                ->where('payment_details.payments_id', $id)
                ->get();

            $data = [
                'payment' => $payment,
                'paymentComp' => $paymentComp,
            ];

            return returnData(2000, $data);

        } catch (\Exception $e) {
            return returnData(5000, $e->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    private function generateUniqueInvoiceNumber()
    {
        do {
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
            $existsInPayments = Payment::where('voucher_number', $invoiceNumber)->exists();
        } while ($existsInPayments);

        return $invoiceNumber;
    }

    public function edit($id)
    {
        try {
            $payment = DB::table('payments as p')
                ->where('p.id', $id)
                ->leftJoin('invoices as i', 'i.id', '=', 'p.invoice_id')
                ->leftJoin('students as s', function ($join) {
                    $join->on('s.id', '=', 'p.paid_by_id')->where('p.paid_by_type', 1);
                })
                ->leftJoin('employees as emp', function ($join) {
                    $join->on('emp.id', '=', 'p.paid_by_id')->where('p.paid_by_type', 2);
                })
                ->leftJoin('employee_designations as d', 'd.id', '=', 'emp.designation_id')
                ->leftJoin('employee_departments as dept', 'dept.id', '=', 'emp.department_id')
                ->leftJoin('student_classes as c', 'c.id', '=', 'p.class_id')
                ->leftJoin('session_years as sy', 'sy.id', '=', 'p.session_year_id')
                ->leftJoin('departments', 'departments.id', '=', 's.department_id')
                ->leftJoin('schools as sc', 'sc.id', '=', 'p.school_id')
                ->select(
                    'p.*',
                    'i.invoice_code',
                    's.student_name_en as student_name',
                    's.student_roll',
                    'departments.department_name as student_department',
                    'c.name as student_class',
                    'sy.title as session_name',
                    'sc.title as school_name',
                    'sc.phone as school_phone',
                    'sc.email as school_email',
                    'sc.address as school_address',
                    'emp.employee_id',
                    'emp.id as employee_primary_id',
                    'emp.name as employee_name',
                    'd.name as employee_designation',
                    'dept.name as employee_department',
                    'emp.employee_type'
                )
                ->first();

            if (!$payment) {
                return returnData(5000, null, 'Payment not found.');
            }

            $components = DB::table('payment_details as pd')
                ->where('pd.payments_id', $id)
                ->leftJoin('components as c', 'c.id', '=', 'pd.component_id')
                ->select(
                    'pd.*',
                    'c.name as component_name',
                    DB::raw('pd.amount as invoice_amount'),
                    DB::raw('COALESCE(pd.payed, 0) as paid'),
                    DB::raw('COALESCE(pd.payed, 0) as payable_amount'),
                    DB::raw('pd.amount as actual_payable'),
                    DB::raw('pd.waiver as waiver_amount')
                )
                ->get();

            $payment->total_payed = $components->sum('paid');

            return returnData(2000, [
                'payment' => $payment,
                'components' => $components,
            ]);

        } catch (\Exception $e) {
            return returnData(5000, null, $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('student_payment_update')) {
            return $this->notPermitted();
        }

        DB::beginTransaction();
        try {
            $payment = Payment::findOrFail($id);

            // Only update required fields
            $payment->update([
                'total_payed' => $request->input('totalPayable'),
                'payment_type' => $request->input('payment_type'),
                'date' => $request->input('date'),
                'bank_id' => $request->input('bank_id'),
                'updated_by' => auth()->id(),
            ]);

            PaymentDetails::where('payments_id', $payment->id)->delete();
            foreach ($request->input('components') as $data) {
                PaymentDetails::create([
                    'payments_id' => $payment->id,
                    'component_id' => $data['component_id'],
                    'amount' => $data['invoice_amount'],
                    'waiver' => $data['waiver_amount'],
                    'payed' => $data['payable_amount'],
                ]);
            }

            DB::commit();
            return returnData(2000, null, 'Payment updated successfully');

        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('student_payment_delete')) {
            return $this->notPermitted();
        }
        PaymentDetails::where('payments_id', $id)->delete();
        Payment::where('id', $id)->delete();
        return returnData(2000, null, 'Successfully Delete');
    }
}
