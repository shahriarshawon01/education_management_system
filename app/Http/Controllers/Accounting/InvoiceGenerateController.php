<?php

namespace App\Http\Controllers\Accounting;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\InvoiceDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InvoiceGenerateController extends Controller
{
    public function index()
    {
        if (!can('generate_invoice_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $class = request()->input('class_id');
            $department = request()->input('department_id');
            $invoice_category = request()->input('invoice_category');
            $invoice_type = request()->input('invoice_type');

            $data = Invoice::withCount('payments')
                ->with([
                    'schools:id,title',
                    'student' => function ($q) {
                        $q->select('id', 'student_name_en', 'student_roll', 'session_year_id', 'class_id');
                    },
                    'student.sessions' => function ($q) {
                        $q->select('id', 'title');
                    },
                    'student.classes' => function ($q) {
                        $q->select('id', 'name');
                    },
                    'createdUser' => function ($q) {
                        $q->select('id', 'username');
                    },
                    'updatedUser' => function ($q) {
                        $q->select('id', 'username');
                    },
                    'employee' => function ($q) {
                        $q->select('id', 'name', 'employee_id', 'department_id', 'designation_id', 'employee_type');
                    },
                    'employee.department' => function ($q) {
                        $q->select('id', 'name');
                    },
                    'employee.designation' => function ($q) {
                        $q->select('id', 'name');
                    },
                ])
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('invoices.invoice_code', 'like', "%$keyword%");
                })
                ->when($class, function ($query) use ($class) {
                    $query->whereHas('student', function ($q) use ($class) {
                        $q->where('class_id', $class);
                    });
                })
                ->when($department, function ($query) use ($department) {
                    $query->whereHas('student', function ($q) use ($department) {
                        $q->where('department_id', $department);
                    });
                })
                ->when($invoice_category, function ($query) use ($invoice_category) {
                    $query->where('invoices.invoice_category', $invoice_category);
                })
                ->when($invoice_type, function ($query) use ($invoice_type) {
                    $query->where('invoices.invoice_type', $invoice_type);
                })
                ->orderBy('id', 'desc')
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
        if (!can('generate_invoice_add')) {
            return $this->notPermitted();
        }

        try {

            if ($request->totalValue < 50) {
                return returnData(5000, null, 'At least one component amount is required. <strong>Total amount must be 50 or higher.</strong>');
            }

            $schoolId = auth()->user()->school_id;
            $createdBy = auth()->user()->id;
            $students = is_array($request->students) ? $request->students : [$request->students];
            foreach ($students as $studentId) {
                $studentRoll = Student::where('id', $studentId)->value('student_roll');
                $category = 'academic';
                $formattedRoll = str_pad($studentRoll, 2, '0', STR_PAD_LEFT);

                $month = strtolower(date('M', strtotime($request->date)));
                $year = date('Y', strtotime($request->date));

                if (isset($request->is_advance) && $request->is_advance == 1) {
                    $invoiceCode = "{$category}-{$month}-{$year}-{$formattedRoll}-advance";
                } else {
                    $invoiceCode = "{$category}-{$month}-{$year}-{$formattedRoll}";
                }

                $totalAmount = 0;
                $totalWaiver = 0;
                $totalPayable = 0;

                $date = $request->date ?? now()->toDateString();
                $toDate = now()->toDateString();
                $month = strtolower(date('F', strtotime($date)));

                $invoice = new Invoice();
                $invoice->school_id = $schoolId;
                $invoice->created_by = $createdBy;
                $invoice->date = $request->date;
                $invoice->to_date = $toDate;
                $invoice->invoice_type = 1;
                $invoice->invoice_category = 1;
                $invoice->invoice_type_id = $studentId;
                $invoice->invoice_code = $invoiceCode;

                $invoice->is_advance = $request->is_advance ?? 0;
                $invoice->month = $month;

                $invoice->save();

                foreach ($request->components as $component) {
                    if ($component['value'] > 0) {

                        $waiver = DB::table('student_waivers')
                            ->where('student_id', $studentId)
                            ->where('component_id', $component['id'])
                            ->first();

                        $waiverAmount = 0;
                        if ($waiver) {
                            if ($waiver->type == 1) {
                                $waiverAmount = ($component['value'] * $waiver->value) / 100;
                            } elseif ($waiver->type == 2) {
                                $waiverAmount = $waiver->value;
                            }
                        }

                        $waiverAmount = round($waiverAmount);
                        $netPayable = $component['value'] - $waiverAmount;

                        $totalAmount += $component['value'];
                        $totalWaiver += $waiverAmount;
                        $totalPayable += $netPayable;

                        $invoiceDetail = new InvoiceDetails();
                        $invoiceDetail->invoice_id = $invoice->id;
                        $invoiceDetail->components_id = $component['id'];
                        $invoiceDetail->invoice_amount = $component['value'];
                        $invoiceDetail->waiver_amount = $waiverAmount;
                        $invoiceDetail->payable_amount = $netPayable;
                        $invoiceDetail->invoice_category = 1;
                        $invoiceDetail->save();
                    }
                }
                $invoice->total_amount = $totalAmount;
                $invoice->total_waiver = $totalWaiver;
                $invoice->total_payable = $totalPayable;
                $invoice->save();
            }

            return returnData(2000, null, 'Invoice Generation Successful');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        if (!can('generate_invoice_view')) {
            return $this->notPermitted();
        }
        $data['invoice'] = DB::table('invoices')
            ->leftJoin('students', function ($join) {
                $join->on('invoices.invoice_type_id', '=', 'students.id')
                    ->where('invoices.invoice_type', 1);
            })
            ->leftJoin('session_years', 'students.session_year_id', '=', 'session_years.id')
            ->leftJoin('student_classes', 'students.class_id', '=', 'student_classes.id')
            ->leftJoin('employees', function ($join) {
                $join->on('invoices.invoice_type_id', '=', 'employees.id')
                    ->where('invoices.invoice_type', 2);
            })
            ->leftJoin('employee_departments', 'employees.department_id', '=', 'employee_departments.id')
            ->leftJoin('employee_designations', 'employees.designation_id', '=', 'employee_designations.id')
            ->leftJoin('users as createdUser', 'invoices.created_by', '=', 'createdUser.id')
            ->leftJoin('users as updatedUser', 'invoices.updated_by', '=', 'updatedUser.id')

            ->where('invoices.id', $id)
            ->select(
                'invoices.*',
                'students.student_name_en',
                'students.student_roll',
                'students.session_year_id',
                'session_years.title as session_title',
                'student_classes.name as class_name',
                'employees.name as employee_name',
                'employees.employee_id',
                'employee_departments.name as department_name',
                'employee_designations.name as designation_name',
                'createdUser.username as created_by_username',
                'updatedUser.username as updated_by_username'
            )
            ->first();

        $data['invoice_details'] = DB::table('invoice_details')
            ->leftJoin('components', 'invoice_details.components_id', '=', 'components.id')
            ->where('invoice_details.invoice_id', $id)
            ->select(
                'invoice_details.*',
                'components.name as component_name'
            )
            ->get();

        return returnData(2000, $data);
    }

    public function edit($id)
    {
        try {
            $invoice = Invoice::where('id', $id)
                ->with([
                    'student:id,student_name_en,student_roll,class_id,session_year_id',
                    'components' => function ($component) {
                        $component->with('components:id,name');
                        $component->selectRaw('*, invoice_amount AS value, waiver_amount as waiver, payable_amount as net_pay');
                    }
                ])->first();

            if (!$invoice) {
                return returnData(5000, null, 'Invoice not found');
            }

            $className = $invoice->student->classes->name ?? null;
            $sessionTitle = $invoice->student->sessions->title ?? null;

            $data = [
                'invoice' => $invoice,
                'class_id' => $className,
                'session_id' => $sessionTitle,
            ];

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('generate_invoice_update')) {
            return $this->notPermitted();
        }
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return returnData(5000, null, 'Invoice not found');
            }

            $updatedBy = auth()->user()->id;
            $studentId = $request->student_id;

            $totalAmount = 0;
            $totalWaiver = 0;
            $totalPayable = 0;

            $date = $request->date ?? now()->toDateString();
            $toDate = now()->toDateString();
            $month = strtolower(date('F', strtotime($date)));

            // Generate invoice code based on is_advance
            $studentRoll = Student::where('id', $studentId)->value('student_roll');
            $category = 'academic';
            $formattedRoll = str_pad($studentRoll, 2, '0', STR_PAD_LEFT);
            $monthCode = strtolower(date('M', strtotime($date)));
            $year = date('Y', strtotime($date));

            if (isset($request->is_advance) && $request->is_advance == 1) {
                $invoiceCode = "{$category}-{$monthCode}-{$year}-{$formattedRoll}-advance";
            } else {
                $invoiceCode = "{$category}-{$monthCode}-{$year}-{$formattedRoll}";
            }

            $invoice->school_id = auth()->user()->school_id;
            $invoice->updated_by = $updatedBy;
            $invoice->date = $request->date;
            $invoice->to_date = $toDate;
            $invoice->invoice_type_id = $studentId;
            $invoice->invoice_code = $invoiceCode;
            $invoice->is_advance = $request->is_advance ?? 0;
            $invoice->invoice_type = 1;
            $invoice->invoice_category = 1;
            $invoice->month = $month;

            foreach ($request->components as $component) {
                if ($component['value'] > 0) {
                    $waiver = DB::table('student_waivers')
                        ->where('student_id', $studentId)
                        ->where('component_id', $component['component_id'])
                        ->first();

                    $waiverAmount = 0;
                    if ($waiver) {
                        $waiverAmount = ($waiver->type == 1)
                            ? ($component['value'] * $waiver->value) / 100
                            : $waiver->value;
                    }

                    $netPayable = $component['value'] - $waiverAmount;

                    $totalAmount += $component['value'];
                    $totalWaiver += $waiverAmount;
                    $totalPayable += $netPayable;

                    InvoiceDetails::updateOrCreate(
                        ['invoice_id' => $invoice->id, 'components_id' => $component['component_id']],
                        [
                            'invoice_amount' => $component['value'],
                            'waiver_amount' => $waiverAmount,
                            'payable_amount' => $netPayable,
                            'invoice_category' => 1,
                        ]
                    );
                }
            }

            $invoice->total_amount = $totalAmount;
            $invoice->total_waiver = $totalWaiver;
            $invoice->total_payable = $totalPayable;

            $invoice->save();

            return returnData(2000, null, 'Invoice Update Successful');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('generate_invoice_delete')) {
            return $this->notPermitted();
        }

        $check_payment = Payment::where('invoice_id', $id)->first();
        if ($check_payment) {
            return returnData(3000, null, 'Invoice cannot be deleted as it has related payments.');
        }
        InvoiceDetails::where('invoice_id', $id)->delete();
        Invoice::where('id', $id)->delete();
        return returnData(2000, null, 'Invoice deleted successfully.');
    }
}
