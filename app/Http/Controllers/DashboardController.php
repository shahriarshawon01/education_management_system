<?php

namespace App\Http\Controllers;

use App\Models\Attendance\AttendanceHistory;
use App\Models\Attendance\StudentAttendance;
use App\Models\StudentClass;
use App\Models\User;
use App\Models\Staff;
use App\Models\School;
use App\Helpers\Helper;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\NoticeBoard;
use App\Models\Library\BookAuthor;
use App\Models\Library\StockBooks;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Library\BookPublisher;
use App\Models\Dormitory\AssignDormitory;
use App\Models\Employee\Employee;
use Maatwebsite\Excel\Row;

class DashboardController extends Controller
{
    use Helper;

    public function __construct()
    {
    }
    public function dashboardData()
    {
        $user = auth()->user();
        if (!$user) {
            Auth::logout();
            return back()->withErrors(['message' => 'You Are Not User']);
        }
        $current_date = Carbon::now();

            if (isset($user->school_id)) {
                $data['student'] = Student::where('class_id', '!=', '29')->where('status', 1)->count();
            } else {
                $data['student'] = Student::where('status', 1)->count();
            }

            $data['teacher'] = Employee::where('status', 1)->Where('employee_type',1)->count();

            $employee_type=[
                1=>'Teacher',
                2=>'Staff',
                3=>'Support Staff'
            ];
            $teacher_count = Employee::where('status', 1)
                ->get()
                ->map(function ($item) use ($employee_type) {
                    $item->employee_type_name = $employee_type[$item->employee_type] ?? 'Unknown';
                    return $item;
                })
                ->groupBy('employee_type_name')
                ->map(function ($group) {
                    return $group->count();
                });
            $labels_with_values = collect( $teacher_count->toArray())->map(function($count, $key) {
                return $key . ' : ' . $count;
            })->toArray();
            $data['teacher_charts'] = [
                'series' => array_values($teacher_count->toArray()),
                'chartOptions' => [
                    'chart' => [
                        'height' => 300,
                        'stacked'=> true,
                        'type' => 'radialBar',
                    ],
                    'plotOptions' => [
                        'radialBar' => [
                            'offsetY' => 0,
                            'startAngle' => 0,
                            'endAngle' => 270,
                            'hollow' => [
                                'margin' => 5,
                                'size' => '30%',
                                'background' => 'transparent',
                            ],
                            'dataLabels' => [
                                'name' => ['show' => true],
                                'value' => ['show' => true],
                            ],
                        ],
                    ],
                    'labels' => array_values($labels_with_values),
                    'colors' => ['#1E90FF', '#FF6347', '#FFD700', '#32CD32'],
                    'legend' => [
                        'show' => true,
                        'floating' => true,
                        'position' => 'left',
                        'offsetX' => 10,
                        'offsetY' => 10,
                    ],
                ],
            ];


            $invoice_category = [
                1 => 'Academic',
                2 => 'Canteen',
                3 => 'Dormitory',
                4 => 'Transport',
                5 => 'Canteen Cash',
            ];
            $current_month =$current_date->format('Y-m');
            $payment = Payment::whereRaw("DATE_FORMAT(`date`, '%Y-%m') = ?",$current_month)->get()
                ->map(function ($item) use ($invoice_category) {
                $item->invoice_type = $invoice_category[$item->invoice_category] ?? 'Unknown';
                    return $item;
            })
            ->groupBy('invoice_type')
            ->map(function ($group) {
                return $group->sum('total_payed');
            });
            $data['invoice_category_Chart'] = [
                'series' => array_values($payment->toArray()),
                'chartOptions' => [
                    'chart' => [
                        'type' => 'donut',
                        'height' => 200,
                        'foreColor' => '#fff',
                    ],
                    'labels' => array_keys($payment->toArray()),
                    'responsive' => [
                        [
                            'breakpoint' => 480,
                            'options' => [
                                'chart' => ['width' => 200],
                                'legend' => ['position' => 'bottom'],
                            ],
                        ],
                    ],
                ],
            ];


        $student = Student::where('students.status',1)
            ->leftjoin('student_classes','students.class_id','=','student_classes.id')
            ->select('students.*','student_classes.id as class_id','student_classes.name as class_name')
            ->orderBy('student_classes.id','asc')
            ->get()
            ->groupBy('class_name')
            ->map(function ($group) {
                return $group->count();
            });

        $data['student_calss_wise_chart'] = [
            'series' => [
                [
                    'name' => 'Student ',
                    'data' => array_values($student->toArray()),
                ],
            ],
            'chartOptions' => [
                'chart' => [
                    'type' => 'bar',
                    'height' => 250,
                    'stacked' => true,
                    'foreColor' => '#fff',
                ],
                'plotOptions' => [
                    'bar' => [
                        'horizontal' => true,
                    ],
                ],
                'dataLabels' => [
                    'enabled' => false,
                ],
                'xaxis' => [
                    'categories' => array_keys($student->toArray()),
                ],
            ],
        ];

        $current_year = date('Y');
        $payment_data = Payment::select(
            DB::raw("MONTH(date) as month"),
            DB::raw("SUM(total_payed) as total_amount")
        )
            ->whereYear('date', $current_year)
            ->groupBy(DB::raw("MONTH(date)"))
            ->orderBy(DB::raw("MONTH(date)"))
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::create()->month($item['month'])->format('F') => $item['total_amount'],];
            });

        $data['payment_charts'] = [
            'series' => [
                [
                    'name' => 'Paid Amount ',
                    'data' => array_values($payment_data->toArray())
                ]
            ],
            'chartOptions' => [
                'chart' => [
                    'type' => 'bar',
                    'height' => 250,
                    'foreColor' => '#fff'
                ],
                'plotOptions' => [
                    'bar' => [
                        'horizontal' => false,
                        'columnWidth' => '50%',
                        'endingShape' => 'rounded'
                    ],
                ],
                'dataLabels' => [
                    'enabled' => false
                ],
                'xaxis' => [
                    'categories' =>array_keys($payment_data->toArray()) // ["South Korea", "Canada", ...]
                ],
            ],
        ];

        $attendance = StudentAttendance::whereDate("check_in",$current_date->format('Y-m-d'))
            ->leftjoin('student_classes','student_attendance.class_id','=','student_classes.id')
            ->select('student_attendance.*','student_classes.id as class_id','student_classes.name as class_name')
            ->orderBy('student_classes.id','asc')
            ->get()
            ->groupBy('class_name')
            ->map(function ($group) {
                return $group->count();
            });
        $allClasses = StudentClass::pluck('name');

        foreach ($student as $class => $total_count) {
            $present_count = isset($attendance[$class]) ? $attendance[$class] : 0;
            $absent[$class] = $total_count - $present_count;
        }
        $attendance = $allClasses->mapWithKeys(function ($className) use ($attendance) {
            return [$className => $attendance->get($className, 0)];
        });

        $data['attendance_charts'] = [
            'series' => [
                [
                    'name' => 'Present Student',
                    'data' => array_values($attendance->toArray())
                ] ,
                [
                    'name' => 'Absent Student',
                    'data' => array_values($absent)
                ]
            ],
            'chartOptions' => [
                'chart' => [
                    'type' => 'bar',
                    'height' => 250,
                    'foreColor' => '#fff',
                ],
                'plotOptions' => [
                    'bar' => [
                        'horizontal' => false,
                        'columnWidth' => '80%',
                        'endingShape' => 'rounded',
                    ],
                ],
                'dataLabels' => [
                    'enabled' => false
                ],
                'xaxis' => [
                    'categories' => array_keys($absent)
                ],
                'colors' => ["#28a745", "#dc3545"],
                'fill' => [
                    'opacity' => 0.85,
                ],
            ],
        ];

        $data['user'] = User::leftJoin('students', 'users.id', '=', 'students.user_id')
            ->where(function ($query) {
                $query->where('students.class_id', '!=', 29)
                    ->orWhereNull('students.class_id');
            })
            ->where('users.status', 1)
            ->count();
        $data['invoices'] = Invoice::selectRaw('SUM(total_amount) as total_amount, SUM(total_waiver) as total_waiver, SUM(total_payable) as total_payable')->get();

        $data['payments'] = Payment::selectRaw('SUM(total_payed) as total_payed')->get();
//        $data['staff'] = Employee::where('status', 1)->Where('employee_type',2)->count();
//        $data['notice_boards'] = NoticeBoard::where('status', 1)->count();
        $data['book_authors'] = BookAuthor::where('status', 1)->count();
        $data['book_publishers'] = BookPublisher::where('status', 1)->count();
        $data['stock_books'] = StockBooks::where('status', 1)->count();
        $data['assign_dormitories'] = AssignDormitory::where('status', 1)->count();

        $data['school'] = School::where('status', 1)->count();

        return returnData(2000, $data);
    }
}
