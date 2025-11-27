<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RoleModules;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        Module::truncate();
        Permission::truncate();
        RolePermission::truncate();
        RoleModules::truncate();

        $modules = [
            [
                'display_name' => 'Dashboard',
                'name' => 'dashboard',
                'link' => '/admin/dashboard',
                'permissions' =>
                    [
                        'view',
                        'report',
                        'print',
                        'user_view',
                        'student_view',
                        'teacher_view',
                        'staff_view',
                        'invoice_view',
                        'waiver_view',
                        'payable_amount_view',
                        'paid_amount_view',
                        'notice_view',
                        'dormitory_view',
                        'library_view'
                    ],
                'icon' => 'fa-dashboard'
            ],
            [
                'display_name' => 'RBAC Accesses',
                'name' => 'accesses',
                'link' => '#',
                'permissions' => ['show'],
                'icon' => 'fa-shield',
                'submenus' => [
                    [
                        'display_name' => 'Modules',
                        'name' => 'module',
                        'link' => '/admin/module',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Roles',
                        'name' => 'role',
                        'link' => '/admin/role',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Module Permissions',
                        'name' => 'module_permission',
                        'link' => '/admin/module_permission',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Role Permissions',
                        'name' => 'role_permission',
                        'link' => '/admin/role_permissions',
                        'permissions' => ['add', 'delete'],
                    ],
                    [
                        'display_name' => 'Users',
                        'name' => 'user',
                        'link' => '/admin/users',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                        'submenus' => []
                    ],
                ]
            ],

            // [
            //     'display_name' => 'Application',
            //     'name' => 'application',
            //     'link' => '#',
            //     'permissions' => ['show'],
            //     'icon' => 'fa-users',
            //     'submenus' => [
            //         [
            //             'display_name' => 'Applicant List',
            //             'name' => 'applicant_list',
            //             'link' => '/admin/applicant_list',
            //             'permissions' => ['add', 'update', 'delete', 'view', 'others'],
            //         ],
            //         [
            //             'display_name' => 'Short Listed Candidates',
            //             'name' => 'applicant_request_selection',
            //             'link' => '/admin/applicant_request_selection',
            //             'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others'],
            //         ],
            //         [
            //             'display_name' => 'Schedule Exam',
            //             'name' => 'admission_exam',
            //             'link' => '/admin/admission_exam',
            //             'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others']
            //         ],
            //         [
            //             'display_name' => 'Admission Mark & Result',
            //             'name' => 'applicant_test_mark_entry',
            //             'link' => '#',
            //             'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others'],
            //             'submenus' => [
            //                 [
            //                     'display_name' => 'Admission Test Mark Entry',
            //                     'name' => 'applicant_test_mark_entry',
            //                     'link' => '/admin/applicant_test_mark_entry',
            //                     'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others']
            //                 ],
            //                 [
            //                     'display_name' => 'Admission Test Result',
            //                     'name' => 'admission_test_result',
            //                     'link' => '/admin/admission_test_result',
            //                     'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others']
            //                 ],
            //             ]
            //         ],
            //         [
            //             'display_name' => 'Final Applicant Selection',
            //             'name' => 'final_applicant_selection_list',
            //             'link' => '/admin/final_applicant_selection_list',
            //             'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others']
            //         ],
            //         [
            //             'display_name' => 'Waiting List',
            //             'name' => 'waiting_list',
            //             'link' => '/admin/waiting_list',
            //             'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others']
            //         ],
            //         [
            //             'display_name' => 'Merit List',
            //             'name' => 'merit_list',
            //             'link' => '/admin/merit_list',
            //             'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others']
            //         ],
            //         [
            //             'display_name' => 'Admission Subject',
            //             'name' => 'admission_subject',
            //             'link' => '/admin/admission_subject',
            //             'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others'],
            //         ],

            //     ]
            // ],
            // [
            //     'display_name' => 'Fees Collection',
            //     'name' => 'fees_collection',
            //     'link' => '#',
            //     'permissions' => ['show'],
            //     'icon' => 'fa-file-invoice',
            //     'submenus' => [
            //         [
            //             'display_name' => 'Fees Type',
            //             'name' => 'fees_type',
            //             'link' => '/admin/fees_type',
            //             'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others'],
            //         ],
            //         [
            //             'display_name' => 'Assign Fees',
            //             'name' => 'fees_master',
            //             'link' => '/admin/fees_master',
            //             'permissions' => ['add', 'update', 'delete', 'view', 'school', 'others'],
            //         ],
            //     ],
            // ],

            [
                'display_name' => 'Configurations',
                'name' => 'setting',
                'link' => '#',
                'permissions' => ['show'],
                'icon' => 'fa fa-tools',
                'submenus' => [
                    [
                        'display_name' => 'General Settings',
                        'name' => 'general_setting',
                        'link' => '/admin/general_setting',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Settings',
                        'name' => 'configuration',
                        'link' => '/admin/configurations',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ]
                ]
            ],
            [
                'display_name' => 'Academic',
                'name' => 'institute_setup',
                'link' => '#',
                'permissions' => ['show'],
                'icon' => 'fa-cog',
                'submenus' => [
                    [
                        'display_name' => 'Institutes',
                        'name' => 'school',
                        'link' => '/admin/school',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                        'icon' => 'fa-university',
                    ],
                    [
                        'display_name' => 'Session',
                        'name' => 'session',
                        'link' => '/admin/session',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Class',
                        'name' => 'class',
                        'link' => '/admin/class',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Department',
                        'name' => 'department',
                        'link' => '/admin/department',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                        'icon' => 'fa-university',
                    ],
                    [
                        'display_name' => 'Section',
                        'name' => 'section',
                        'link' => '/admin/section',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Group Subject',
                        'name' => 'group_subject',
                        'link' => '/admin/group_subject',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Subject',
                        'name' => 'subjects',
                        'link' => '/admin/subjects',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                ]
            ],
            [
                'display_name' => 'Student',
                'name' => 'student',
                'link' => '#',
                'permissions' => ['show'],
                'icon' => 'fa-user-graduate',
                'submenus' => [
                    [
                        'display_name' => 'Admit Student',
                        'name' => 'student',
                        'link' => '/admin/student',
                        'permissions' => ['add', 'update', 'delete', 'view', 'id_print'],
                    ],
                    [
                        'display_name' => 'Student Promotion',
                        'name' => 'promotion',
                        'link' => '/admin/promotion',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Student Demotion',
                        'name' => 'student_demotion',
                        'link' => '/admin/student_demotion',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    // [
                    //     'display_name' => 'Assign Subject',
                    //     'name' => 'assign_subject',
                    //     'link' => '/admin/assign_subject',
                    //     'permissions' => ['add', 'update', 'delete', 'view'],
                    // ],
                ]
            ],
            // [
            //     'display_name' => 'Appraisal',
            //     'name' => 'appraisal',
            //     'link' => '#',
            //     'permissions' => ['show'],
            //     'icon' => 'fa-user-graduate',
            //     'submenus' => [
            //         [
            //             'display_name' => 'Appraisal Template',
            //             'name' => 'appraisal_template',
            //             'link' => '/admin/appraisal_template',
            //             'permissions' => ['add', 'update', 'delete', 'view'],
            //         ],
            //         [
            //             'display_name' => 'Appraisal',
            //             'name' => 'appraisal',
            //             'link' => '/admin/appraisal',
            //             'permissions' => ['add', 'update', 'delete', 'view'],
            //         ]
            //     ]
            // ],
            [
                'display_name' => 'Employee',
                'name' => 'employee',
                'link' => '#',
                'permissions' => ['show'],
                'icon' => 'fa fa-user-tie',
                'submenus' => [
                    [
                        'display_name' => 'Department',
                        'name' => 'employee_department',
                        'link' => '/admin/employee_department',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Designation',
                        'name' => 'employee_designation',
                        'link' => '/admin/employee_designation',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Employees',
                        'name' => 'employee',
                        'link' => '/admin/employee',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],

                    // [
                    //     'display_name' => 'Teacher',
                    //     'name' => 'teacher',
                    //     'link' => '/admin/teacher',
                    //     'permissions' => ['add', 'update', 'delete', 'view'],
                    // ],
                    // [
                    //     'display_name' => 'Staff',
                    //     'name' => 'staff',
                    //     'link' => '/admin/staff',
                    //     'permissions' => ['add', 'update', 'delete', 'view'],
                    // ],
                ]
            ],

            [
                'display_name' => 'User Document',
                'name' => 'user_document',
                'link' => '#',
                'permissions' => ['show'],
                'icon' => 'fa fa-file-alt',
                'submenus' => [
                    [
                        'display_name' => 'User Document',
                        'name' => 'user_document',
                        'link' => '/admin/user_document',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ]
                ]
            ],

            // [
            //     'display_name' => 'Parents',
            //     'name' => 'parents',
            //     'link' => '/admin/parents',
            //     'permissions' => ['add', 'update', 'delete', 'view'],
            //     'icon' => 'fa-file-invoice',
            //     'submenus' => []
            // ],

            [
                'display_name' => 'Academic Syllabus',
                'name' => 'academic_syllabus',
                'link' => '/admin/academic_syllabus',
                'icon' => 'fa-file-invoice',
                'permissions' => ['add', 'update', 'delete', 'view'],
            ],
            [
                'display_name' => 'Routine',
                'name' => 'routine',
                'link' => '#',
                'permissions' => ['show'],
                'icon' => 'fa fa-clipboard-list',
                'submenus' => [
                    [
                        'display_name' => 'Building',
                        'name' => 'building',
                        'link' => '/admin/building',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Room',
                        'name' => 'room',
                        'link' => '/admin/room',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Class Routine',
                        'name' => 'class_routines',
                        'link' => '/admin/class_routines',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                ]
            ],

            [
                'display_name' => 'Attendance',
                'name' => 'attendance',
                'link' => '#',
                'permissions' => ['show'],
                'icon' => 'fa fa-check-circle',
                'submenus' => [
                    [
                        'display_name' => 'Student Attendance',
                        'name' => 'student_attendance',
                        'link' => '/admin/student_attendance',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Teacher Attendance',
                        'name' => 'teacher_attendance',
                        'link' => '/admin/teacher_attendance',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Staff Attendance',
                        'name' => 'staff_attendance',
                        'link' => '/admin/staff_attendance',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'SMS To All Student',
                        'name' => 'sms_to_all_student',
                        'link' => '/admin/sms_to_all_student',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Manual Student Attendance',
                        'name' => 'manual_student_attendance',
                        'link' => '/admin/manual_student_attendance',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Manual Employee Attendance',
                        'name' => 'manual_employee_attendance',
                        'link' => '/admin/manual_employee_attendance',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                ]
            ],

            [
                'display_name' => 'Examination',
                'name' => 'examination',
                'link' => '#',
                'permissions' => ['show'],
                'icon' => 'fa-solid fa-graduation-cap',
                'submenus' => [
                    [
                        'display_name' => 'Exam Component',
                        'name' => 'exam_component',
                        'link' => '/admin/exam_component',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Examination Name',
                        'name' => 'exam_name',
                        'link' => '/admin/exam_name',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Examination Type',
                        'name' => 'examination',
                        'link' => '/admin/examination',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Grade Number',
                        'name' => 'grade_number',
                        'link' => '/admin/grade_number',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    // [
                    //     'display_name' => 'Examination Grade',
                    //     'name' => 'examination_grade',
                    //     'link' => '/admin/examination_grade',
                    //     'permissions' => ['add', 'update', 'delete', 'view'],
                    // ],
                    [
                        'display_name' => 'Manage Marks',
                        'name' => 'manage_marks',
                        'link' => '/admin/manage_marks',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Academic Transcript',
                        'name' => 'academic_transcript',
                        'link' => '/admin/academic_transcript',
                        'permissions' => ['view'],
                    ],

                    [
                        'display_name' => 'Tabulation Sheet',
                        'name' => 'tabulation_sheet',
                        'link' => '/admin/tabulation_sheet',
                        'permissions' => ['view'],
                    ],
                    // [
                    //     'display_name' => 'Exam Documents',
                    //     'name' => 'exam_documents',
                    //     'link' => '/admin/exam_documents',
                    //     'permissions' => ['add', 'update', 'delete', 'view'],
                    // ],
                    [
                        'display_name' => 'Admit Card & Seat Plan',
                        'name' => 'admit_card_seat_plan',
                        'link' => '/admin/admit_card_seat_plan',
                        'permissions' => ['view'],
                    ],

                    [
                        'display_name' => 'Number Sheet',
                        'name' => 'number_sheet',
                        'link' => '/admin/number_sheet',
                        'permissions' => ['view'],
                    ],
                ]
            ],
            [
                'display_name' => 'Accounting',
                'name' => 'accounting',
                'link' => '#',
                'permissions' => ['add', 'update', 'delete', 'view'],
                'icon' => 'fa fa-money-bill-wave',
                'submenus' => [
                    [
                        'display_name' => 'Component Category',
                        'name' => 'component_category',
                        'link' => '/admin/component_category',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                        'submenus' => []
                    ],
                    [
                        'display_name' => 'Bank',
                        'name' => 'bank',
                        'link' => '/admin/bank',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                        'submenus' => []
                    ],
                    [
                        'display_name' => 'Component',
                        'name' => 'component',
                        'link' => '/admin/component',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                        'submenus' => []
                    ],
                    [
                        'display_name' => 'Waiver Document',
                        'name' => 'student_waiver_document',
                        'link' => '/admin/student_waiver_document',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                        'submenus' => []
                    ],
                    [
                        'display_name' => 'Waiver',
                        'name' => 'student_waiver',
                        'link' => '/admin/student_waiver',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                        'submenus' => []
                    ],
                    [
                        'display_name' => 'Invoice',
                        'name' => 'generate_invoice',
                        'link' => '/admin/generate_invoice',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],

                    [
                        'display_name' => 'Payment',
                        'name' => 'student_payment',
                        'link' => '/admin/student_payment',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    // [
                    //     'display_name' => 'Student Transaction Status',
                    //     'name' => 'student_transaction_status',
                    //     'link' => '/admin/student_transaction_status',
                    //     'permissions' => ['add', 'update', 'delete', 'view'],
                    // ],
                ]
            ],
            [
                'display_name' => 'Library',
                'name' => 'library',
                'link' => '#',
                'permissions' => ['add', 'update', 'delete', 'view'],
                'icon' => 'fa fa-book-open',
                'submenus' => [
                    [
                        'display_name' => 'Setup',
                        'name' => 'setup',
                        'link' => '/admin/book_accession',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'Books Author',
                                'name' => 'book_author',
                                'link' => '/admin/library/book_author',
                                'permissions' => ['add', 'update', 'delete', 'view'],
                            ],
                            [
                                'display_name' => 'Books Publisher',
                                'name' => 'publisher',
                                'link' => '/admin/library/publisher',
                                'permissions' => ['add', 'update', 'delete', 'view'],
                            ],
                            [
                                'display_name' => 'Books Edition',
                                'name' => 'edition',
                                'link' => '/admin/library/edition',
                                'permissions' => ['add', 'update', 'delete', 'view'],
                            ],
                            [
                                'display_name' => 'Books Language',
                                'name' => 'language',
                                'link' => '/admin/library/language',
                                'permissions' => ['add', 'update', 'delete', 'view'],
                            ],
                        ]
                    ],
                    [
                        'display_name' => 'Book Accession',
                        'name' => 'book_accession',
                        'link' => '/admin/book_accession',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Stock Books',
                        'name' => 'stock_books',
                        'link' => '/admin/stock_books',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Membership',
                        'name' => 'membership',
                        'link' => '/admin/membership',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Book Issue & Receive',
                        'name' => 'book_issue',
                        'link' => '/admin/book_issue',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ]
                ]
            ],

            [
                'display_name' => 'Stationery Stock',
                'name' => 'stock',
                'link' => '#',
                'permissions' => ['add', 'update', 'delete', 'view'],
                'icon' => 'fa fa-chart-line',
                'submenus' => [
                    [
                        'display_name' => 'Category',
                        'name' => 'category',
                        'link' => '/admin/category',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Product',
                        'name' => 'product',
                        'link' => '/admin/product',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Stock In',
                        'name' => 'stock_in',
                        'link' => '/admin/stock_in',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Stock Out',
                        'name' => 'stock_out',
                        'link' => '/admin/stock_out',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                ]
            ],

            [
                'display_name' => 'Transport',
                'name' => 'transport',
                'link' => '#',
                'permissions' => ['add', 'update', 'delete', 'view'],
                'icon' => 'fa fa-bus',
                'submenus' => [
                    [
                        'display_name' => 'Component',
                        'name' => 'transport_component',
                        'link' => '/admin/transport_component',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Route',
                        'name' => 'route',
                        'link' => '/admin/route',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Manage Transport',
                        'name' => 'manage_transport',
                        'link' => '/admin/manage_transport',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Assign Transport',
                        'name' => 'assign_transport',
                        'link' => '/admin/assign_transport',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Month Closing',
                        'name' => 'transport_month_closing',
                        'link' => '/admin/transport_month_closing',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Invoice',
                        'name' => 'transport_invoice',
                        'link' => '/admin/transport_invoice',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ]
                ]
            ],

            [
                'display_name' => 'Dormitory',
                'name' => 'dormitory',
                'link' => '#',
                'permissions' => ['add', 'update', 'delete', 'view'],
                'icon' => 'fa fa-home',
                'submenus' => [
                    [
                        'display_name' => 'Component',
                        'name' => 'dormitory_component',
                        'link' => '/admin/dormitory_component',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Manage Dormitory',
                        'name' => 'manage_dormitory',
                        'link' => '/admin/manage_dormitory',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Assign Dormitory',
                        'name' => 'assign_dormitory',
                        'link' => '/admin/assign_dormitory',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Month Closing',
                        'name' => 'dormitory_month_closing',
                        'link' => '/admin/dormitory_month_closing',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Invoice',
                        'name' => 'dormitory_invoice',
                        'link' => '/admin/dormitory_invoice',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                ]
            ],

            [
                'display_name' => 'Canteen',
                'name' => 'canteen',
                'link' => '#',
                'permissions' => ['add', 'update', 'delete', 'view'],
                'icon' => 'fa fa-utensils',
                'submenus' => [
                    // [
                    //     'display_name' => 'Daily Meal',
                    //     'name' => 'daily_meal_members',
                    //     'link' => '/admin/daily_meal_members',
                    //     'permissions' => ['add', 'update', 'delete', 'view'],
                    // ],
                    [
                        'display_name' => 'Component',
                        'name' => 'canteen_configure',
                        'link' => '/admin/canteen_configure',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Meal Times',
                        'name' => 'meal_time',
                        'link' => '/admin/meal_time',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Menu',
                        'name' => 'canteen_menu_item',
                        'link' => '/admin/canteen_menu_item',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Menu Rostering',
                        'name' => 'canteen_menu_rostering',
                        'link' => '/admin/canteen_menu_rostering',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Weekly Menu',
                        'name' => 'canteen_weekly_menu',
                        'link' => '/admin/canteen_weekly_menu',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Assign Canteen',
                        'name' => 'assign_canteen',
                        'link' => '/admin/assign_canteen',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],

                    // [
                    //     'display_name' => 'Daily Fee',
                    //     'name' => 'canteen_daily_fee',
                    //     'link' => '/admin/canteen_daily_fee',
                    //     'permissions' => ['add', 'update', 'delete', 'view'],
                    // ],

                    [
                        'display_name' => 'Daily Sale',
                        'name' => 'canteen_daily_sale',
                        'link' => '/admin/canteen_daily_sale',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Month Closing',
                        'name' => 'canteen_month_closing',
                        'link' => '/admin/canteen_month_closing',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Invoice',
                        'name' => 'canteen_invoice',
                        'link' => '/admin/canteen_invoice',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                ]
            ],

            // [
            //     'display_name' => 'Leaves',
            //     'name' => 'leave',
            //     'link' => '#',
            //     'permissions' => ['add', 'update', 'delete', 'view'],
            //     'icon' => 'fa fa-clipboard',
            //     'submenus' => [
            //         [
            //             'display_name' => 'Leave Category',
            //             'name' => 'leave_category',
            //             'link' => '/admin/leave_category',
            //             'permissions' => ['add', 'update', 'delete', 'view'],
            //         ],
            //         [
            //             'display_name' => 'Apply Leave',
            //             'name' => 'apply_leave',
            //             'link' => '/admin/apply_leave',
            //             'permissions' => ['add', 'update', 'delete', 'view'],
            //         ],
            //         [
            //             'display_name' => 'Approved Leave',
            //             'name' => 'approve_leave',
            //             'link' => '/admin/approve_leave',
            //             'permissions' => ['add', 'update', 'delete', 'view'],
            //         ],
            //     ]
            // ],

            [
                'display_name' => 'Noticeboard',
                'name' => 'noticeboard',
                'link' => '/admin/noticeboard',
                'permissions' => ['add', 'update', 'delete', 'view'],
                'icon' => 'fa fa-bookmark',
                'submenus' => []
            ],

            // [
            //     'display_name' => 'Live Class',
            //     'name' => 'live_class',
            //     'link' => '/admin/live_class',
            //     'permissions' => ['add', 'update', 'delete', 'view'],
            //     'icon' => 'fa fa-video',
            //     'submenus' => []
            // ],

            [
                'display_name' => 'Website',
                'name' => 'website',
                'link' => '#',
                'permissions' => ['add', 'update', 'delete', 'view'],
                'icon' => 'fa fa-globe',
                'submenus' => [
                    [
                        'display_name' => 'Website Setup',
                        'name' => 'website_setup',
                        'link' => '/admin/website',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Our Management',
                        'name' => 'our_management',
                        'link' => '/admin/our_management',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Faculties',
                        'name' => 'faculties',
                        'link' => '/admin/faculties',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Slider',
                        'name' => 'slider',
                        'link' => '/admin/slider',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Event',
                        'name' => 'event',
                        'link' => '/admin/event',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Video',
                        'name' => 'video',
                        'link' => '/admin/video',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Course Category',
                        'name' => 'course_category',
                        'link' => '/admin/course_category',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Course',
                        'name' => 'website_course',
                        'link' => '/admin/website_course',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Department Wise Fees Structure',
                        'name' => 'department_wise_fees_structure',
                        'link' => '/admin/department_wise_fees_structure',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Contact',
                        'name' => 'contact',
                        'link' => '/admin/contact',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Service',
                        'name' => 'Service',
                        'link' => '/admin/service',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'Facilities',
                        'name' => 'Facilities',
                        'link' => '/admin/facilities',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                    [
                        'display_name' => 'School Life',
                        'name' => 'school_life',
                        'link' => '/admin/school_life',
                        'permissions' => ['add', 'update', 'delete', 'view'],
                    ],
                ]
            ],
            [
                'display_name' => 'Report',
                'name' => 'reports',
                'link' => '#',
                'permissions' => ['show'],
                'icon' => 'fa fa-chart-bar',
                'submenus' => [
                    [
                        'display_name' => 'Attendance',
                        'name' => 'attendance',
                        'link' => '/admin/reports/attendance',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'Teachers Attendance Report',
                                'name' => 'teacher_attendance',
                                'link' => '/admin/reports/teacher_attendance',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Staffs Attendance Report',
                                'name' => 'staff_attendance',
                                'link' => '/admin/reports/staff_attendance',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Students Attendance Report',
                                'name' => 'student_attendance',
                                'link' => '/admin/reports/student_attendance',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                        ]
                    ],

                    [
                        'display_name' => 'Student',
                        'name' => 'student',
                        'link' => '/admin/reports/student',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'Details Report',
                                'name' => 'students',
                                'link' => '/admin/reports/students',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Parents Report',
                                'name' => 'parents',
                                'link' => '/admin/reports/parents',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Activity Status Report',
                                'name' => 'activity_status',
                                'link' => '/admin/reports/activity_status',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                             [
                                'display_name' => 'Class Wise Student Report',
                                'name' => 'class_wise_students',
                                'link' => '/admin/reports/class_wise_students',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                        ]
                    ],

                    [
                        'display_name' => 'Employee',
                        'name' => 'employee',
                        'link' => '/admin/reports/employee',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'Employee Report',
                                'name' => 'employee',
                                'link' => '/admin/reports/employee',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Class Wise Teacher Report',
                                'name' => 'class-teachers',
                                'link' => '/admin/reports/class-teachers',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Responsible Teacher Report',
                                'name' => 'responsible-teachers',
                                'link' => '/admin/reports/responsible-teachers',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                        ]
                    ],
                    [
                        'display_name' => 'Accounting',
                        'name' => 'accounting',
                        'link' => '/admin/reports/accounting',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'Invoice Report',
                                'name' => 'invoices',
                                'link' => '/admin/reports/invoices',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Daily Collection Report',
                                'name' => 'daily_collection',
                                'link' => '/admin/reports/daily_collection',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            // [
                            //     'display_name' => 'Fees Collection Report',
                            //     'name' => 'fees_collections',
                            //     'link' => '/admin/reports/fees_collections',
                            //     'permissions' => ['view'],
                            //     'submenus' => []
                            // ],
                            [
                                'display_name' => 'Due Report',
                                'name' => 'due_report',
                                'link' => '/admin/reports/due_report',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Monthly Due Report',
                                'name' => 'due_report',
                                'link' => '/admin/reports/monthly_due_report',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Ledger Report',
                                'name' => 'ledgers',
                                'link' => '/admin/reports/ledgers',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Aging Report',
                                'name' => 'aging_report',
                                'link' => '/admin/reports/aging_report',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Waiver Report',
                                'name' => 'waiver_report',
                                'link' => '/admin/reports/waiver_report',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            // [
                            //     'display_name' => 'Class Wise Component Collection Report',
                            //     'name' => 'class_wise_component_collection',
                            //     'link' => '/admin/reports/class_wise_component_collection',
                            //     'permissions' => ['view'],
                            //     'submenus' => []
                            // ],
                            // [
                            //     'display_name' => 'Collection Information  Report',
                            //     'name' => 'fees_collection_information',
                            //     'link' => '/admin/reports/fees_collection_information',
                            //     'permissions' => ['view'],
                            //     'submenus' => []
                            // ],
                        ]
                    ],
                    [
                        'display_name' => 'Dormitories',
                        'name' => 'dormitories',
                        'link' => '/admin/reports/dormitories',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'Dormitories Report',
                                'name' => 'dormitories',
                                'link' => '/admin/reports/dormitories',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Monthly Invoice Report',
                                'name' => 'dormitories_monthly_invoice_report',
                                'link' => '/admin/reports/dormitories_monthly_invoice_report',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                        ]
                    ],
                    [
                        'display_name' => 'Transports',
                        'name' => 'transports',
                        'link' => '/admin/reports/transports',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'Transports Report',
                                'name' => 'transports',
                                'link' => '/admin/reports/transports',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Monthly Invoice Report',
                                'name' => 'transports_monthly_invoice_report',
                                'link' => '/admin/reports/transports_monthly_invoice_report',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                        ]
                    ],

                    [
                        'display_name' => 'Canteen',
                        'name' => 'canteen_menu',
                        'link' => '/admin/reports/canteen_menu',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'Production Report',
                                'name' => 'canteen_productions',
                                'link' => '/admin/reports/canteen_productions',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Sales Report',
                                'name' => 'canteen_sales',
                                'link' => '/admin/reports/canteen_sales',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Monthly Invoice Report',
                                'name' => 'canteen_monthly_invoice_report',
                                'link' => '/admin/reports/canteen_monthly_invoice_report',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'ledger Report',
                                'name' => 'canteen_ledger_report',
                                'link' => '/admin/reports/canteen_ledger_report',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                        ]
                    ],
                    [
                        'display_name' => 'Product',
                        'name' => 'product',
                        'link' => '/admin/reports/product',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'Product Stocks Report',
                                'name' => 'stocks',
                                'link' => '/admin/reports/stocks',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                        ]
                    ],

                    [
                        'display_name' => 'Library',
                        'name' => 'library',
                        'link' => '/admin/reports/library',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'Stock Books Report',
                                'name' => 'stock_books',
                                'link' => '/admin/reports/stock_books',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                            [
                                'display_name' => 'Book Issue report',
                                'name' => 'book_issue',
                                'link' => '/admin/reports/book_issue',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                        ]
                    ],

                    [
                        'display_name' => 'SMS',
                        'name' => 'sms_history',
                        'link' => '/admin/reports/sms_history',
                        'permissions' => ['view'],
                        'submenus' => [
                            [
                                'display_name' => 'SMS History',
                                'name' => 'sms_history',
                                'link' => '/admin/reports/sms_history',
                                'permissions' => ['view'],
                                'submenus' => []
                            ],
                        ]
                    ],
                ]
            ],
        ];

        foreach ($modules as $data) {
            $submenus = isset($data['submenus']) && count($data['submenus']) > 0 ? $data['submenus'] : [];
            $permissions = $data['permissions'];
            unset($data['submenus']);
            unset($data['permissions']);

            $parentModule = $this->insertModule($data, 0);
            $this->insertRoleModule($parentModule->id, 1);


            foreach ($permissions as $permission) {
                $new_permission = $this->insertPermission($parentModule->name, $permission, $parentModule->display_name, $parentModule->id);
                $this->insertRolePermission($new_permission->id, 1);
            }

            if (count($submenus) > 0) {
                foreach ($submenus as $submenu) {
                    $subSubMenus = isset($submenu['submenus']) && count($submenu['submenus']) > 0 ? $submenu['submenus'] : [];

                    $permissions = $submenu['permissions'];
                    unset($submenu['permissions']);
                    unset($submenu['submenus']);

                    $module = $this->insertModule($submenu, $parentModule->id);
                    $subParent = $module;

                    $this->insertRoleModule($module->id, 1);

                    foreach ($permissions as $permission) {
                        $new_permission = $this->insertPermission($module->name, $permission, $module->display_name, $module->id);
                        $this->insertRolePermission($new_permission->id, 1);
                    }

                    if (count($subSubMenus) > 0) {
                        foreach ($subSubMenus as $subSubMenu) {
                            $permissions = $subSubMenu['permissions'];
                            unset($subSubMenu['permissions']);
                            unset($subSubMenu['submenus']);

                            $module = $this->insertModule($subSubMenu, $subParent->id);
                            $this->insertRoleModule($module->id, 1);


                            foreach ($permissions as $permission) {
                                $new_permission = $this->insertPermission($module->name, $permission, $module->display_name, $module->id);
                                $this->insertRolePermission($new_permission->id, 1);
                            }
                        }
                    }
                }
            }
        }
    }

    public function insertModule($data, $parent_id = 0)
    {
        $module = new Module();
        $module->fill($data);
        $module->parent_id = $parent_id;
        $module->save();

        return $module;
    }

    public function insertRoleModule($module_id, $role_id = 1)
    {
        $role_module = new RoleModules();
        $role_module->role_id = $role_id;
        $role_module->module_id = $module_id;
        $role_module->save();

        return $role_module;
    }

    public function insertPermission($name, $permission, $display_name, $module_id)
    {
        $new_name = $name . '_' . $permission;
        $new_permission = new Permission();
        $new_permission->module_id = $module_id;
        $new_permission->name = $new_name;
        $new_permission->display_name = $display_name . ' ' . str_replace('_', ' ', $permission);
        $new_permission->save();

        return $new_permission;
    }

    public function insertRolePermission($permission_id, $role_id = 1)
    {
        $role_module = new RolePermission();
        $role_module->role_id = $role_id;
        $role_module->permission_id = $permission_id;
        $role_module->save();

        return $role_module;
    }
}
