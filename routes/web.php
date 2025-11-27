<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SMS\SmsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ApprisalController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CommonAdmissionApi;
use App\Http\Controllers\DemotionController;
use App\Http\Controllers\ExamNameController;
use App\Http\Controllers\FeesTypeController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamGradeController;
use App\Http\Controllers\LiveClassController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\RBAC\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FeesMasterController;
use App\Http\Controllers\ManageMarkController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\GradeNumberController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\PreliminaryController;
use App\Http\Controllers\RBAC\ModuleController;
use App\Http\Controllers\SessionYearController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ApproveLeaveController;
use App\Http\Controllers\ExamDocumentController;
use App\Http\Controllers\ShowDatabaseController;
use App\Http\Controllers\SubjectGroupController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\AdmissionExamController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ExamComponentController;
use App\Http\Controllers\LeaveCategorycontroller;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\StockInController;
use App\Http\Controllers\StudentIdCardController;
use App\Http\Controllers\Website\EventController;
use App\Http\Controllers\Website\VideoController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\Store\StockOutController;
use App\Http\Controllers\Website\SliderController;
use App\Http\Controllers\Accounting\BankController;
use App\Http\Controllers\ManageDormitoryController;
use App\Http\Controllers\RBAC\RoleModuleController;
use App\Http\Controllers\Website\ServiceController;
use App\Http\Controllers\AcademicSyllabusController;
use App\Http\Controllers\ApplicantRequestController;
use App\Http\Controllers\ApprisalTemplateController;
use App\Http\Controllers\Canteen\MealTimeController;
use App\Http\Controllers\Website\FacultieController;
use App\Http\Controllers\Canteen\DailyMealController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\ExcelReportExportController;
use App\Http\Controllers\Library\BookIssueController;
use App\Http\Controllers\Library\StockBookController;
use App\Http\Controllers\ApplicantDashboardController;
use App\Http\Controllers\Library\BookAuthorController;
use App\Http\Controllers\Library\MembershipController;
use App\Http\Controllers\Reports\BookReportController;
use App\Http\Controllers\Website\FacilitiesController;
use App\Http\Controllers\Website\SchoolLifeController;
use App\Http\Controllers\Library\BookEditionController;
use App\Http\Controllers\RBAC\RolePermissionController;
use App\Http\Controllers\Reports\StockReportController;
use App\Http\Controllers\Store\StoreCategoryController;
use App\Http\Controllers\Accounting\ComponentController;
use App\Http\Controllers\Library\BookLanguageController;
use App\Http\Controllers\Reports\ParentReportController;
use App\Http\Controllers\Website\WebsiteSetupController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Canteen\AssignCanteenController;
use App\Http\Controllers\Library\BookAccessionController;
use App\Http\Controllers\Library\BookPublisherController;
use App\Http\Controllers\Website\OurManagementController;
use App\Http\Controllers\Website\WebsiteCourseController;
use App\Http\Controllers\Canteen\CanteenInvoiceController;
use App\Http\Controllers\Reports\SMS\SmsHistoryController;
use App\Http\Controllers\student\StudentGetDataController;
use App\Http\Controllers\Website\CourseCategoryController;
use App\Http\Controllers\Website\WebsiteContactController;
use App\Http\Controllers\Canteen\CanteenMenuItemController;
use App\Http\Controllers\Examination\NumberSheetController;
use App\Http\Controllers\Reports\BookIssueReportController;
use App\Http\Controllers\Accounting\StudentWaiverController;
use App\Http\Controllers\Canteen\CanteenConfigureController;
use App\Http\Controllers\Canteen\CanteenDailySaleController;
use App\Http\Controllers\Transport\TransportRouteController;
use App\Http\Controllers\Accounting\ComponentGroupController;
use App\Http\Controllers\Accounting\StudentPaymentController;
use App\Http\Controllers\Canteen\CanteenMenuRosterController;
use App\Http\Controllers\Canteen\CanteenWeeklyMenuController;
use App\Http\Controllers\Dormitory\AssignDormitoryController;
use App\Http\Controllers\Transport\AssignTransportController;
use App\Http\Controllers\Transport\TransportManageController;
use App\Http\Controllers\Accounting\InvoiceGenerateController;
use App\Http\Controllers\Admission\AdmissionRequestController;
use App\Http\Controllers\Admission\AdmissionSubjectController;
use App\Http\Controllers\Dormitory\DormitoryInvoiceController;
use App\Http\Controllers\Reports\Student\SchoolInfoController;
use App\Http\Controllers\Transport\TransportInvoiceController;
use App\Http\Controllers\Attendance\ManualAttendanceController;
use App\Http\Controllers\Canteen\CanteenMonthClosingController;
use App\Http\Controllers\Employee\EmployeeDepartmentController;
use App\Http\Controllers\Examination\TabulationSheetController;
use App\Http\Controllers\Accounting\ComponentCategoryController;
use App\Http\Controllers\Dormitory\DormitoryComponentController;
use App\Http\Controllers\Employee\EmployeeDesignationController;
use App\Http\Controllers\Reports\Accounting\DueReportController;
use App\Http\Controllers\Transport\TransportComponentController;
use App\Http\Controllers\Website\WebsiteFeesStructureController;
use App\Http\Controllers\Accounting\StudentTransactionController;
use App\Http\Controllers\Examination\AdmitCardSeatPlanController;
use App\Http\Controllers\Reports\Student\StudentDetailsReportController;
use App\Http\Controllers\Examination\AcademicTranscriptController;
use App\Http\Controllers\Reports\Accounting\AgingReportController;
use App\Http\Controllers\Dormitory\DormitoryMonthClosingController;
use App\Http\Controllers\Reports\Accounting\LedgerReportController;
use App\Http\Controllers\Reports\Accounting\WaiverReportController;
use App\Http\Controllers\Transport\TransportMonthClosingController;
use App\Http\Controllers\Accounting\StudentWaiverDocumentController;
use App\Http\Controllers\Reports\Accounting\InvoiceReportController;
use App\Http\Controllers\Reports\Employers\EmployeeReportController;
use App\Http\Controllers\Reports\Dormitory\DormitoryReportController;
use App\Http\Controllers\Reports\Transport\TransportReportController;
use App\Http\Controllers\Reports\Canteen\CanteenLedgerReportController;
use App\Http\Controllers\Reports\Employers\ClassTeacherReportController;
use App\Http\Controllers\Reports\Student\ActivityStatusReport;
use App\Http\Controllers\Reports\Accounting\FeesCollectionReportController;
use App\Http\Controllers\Reports\Accounting\DailyCollectionReportController;
use App\Http\Controllers\Reports\Attendance\StaffAttendanceReportController;
use App\Http\Controllers\Reports\Attendance\StudentAttendanceReportController;
use App\Http\Controllers\Reports\Attendance\TeacherAttendanceReportController;
use App\Http\Controllers\Reports\Employers\ResponsibleTeacherReportController;
use App\Http\Controllers\Reports\Canteen\CanteenMonthlyInvoiceReportController;
use App\Http\Controllers\Reports\Accounting\ClassWiseCollectionReportController;
use App\Http\Controllers\Reports\Dormitory\DormitoryMonthlyInvoiceReportController;
use App\Http\Controllers\Reports\Transport\TransportMonthlyInvoiceReportController;
use App\Http\Controllers\Reports\Accounting\FessCollectionInformationReportController;

Route::get('/database-tables', [ShowDatabaseController::class, 'showTables']);

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/noticeboard', [HomeController::class, 'noticeboard'])->name('noticeboard');
Route::get('/notice_details/{slug}', [HomeController::class, 'notice_details'])->name('notice_details');
Route::get('/event_details/{slug}', [HomeController::class, 'event_details'])->name('event_details');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact_store', [HomeController::class, 'contact_store'])->name('contact_store');
Route::get('/about-institute', [HomeController::class, 'about_institute'])->name('about_institute');
Route::get('/history', [HomeController::class, 'institute_history'])->name('institute_history');
Route::get('/mission_vision', [HomeController::class, 'mission_vision'])->name('mission_vision');
Route::get('/ed-message', [HomeController::class, 'ed_message'])->name('ed_message');
Route::get('/chairman-message', [HomeController::class, 'chairman_message'])->name('chairman_message');
Route::get('/principal-message', [HomeController::class, 'principal_message'])->name('principal_message');
Route::get('/vice-principal-message', [HomeController::class, 'vice_principal_message'])->name('vice_principal_message');
Route::get('/md-message', [HomeController::class, 'md_message'])->name('md_message');
Route::get('/assistant-professor-message', [HomeController::class, 'assistant_professor_message'])->name('assistant_professor_message');
Route::get('/live-class-schedule', [HomeController::class, 'live_class_schedule'])->name('live_class_schedule');

// Route::namespace('App\Http\Controllers\Website')->group(function () {
//         Route::get('/applicant_login', 'ApplicantController@loginForm')->name('applicant.login');
//         Route::post('/applicant_login', 'ApplicantController@applicant_login_store')->name('applicant.login_store');
//         Route::get('/applicant_register', 'ApplicantController@registerForm')->name('applicant.register');
//         Route::post('/applicant_register', 'ApplicantController@applicant_register_store')->name('applicant.register_store');
//         Route::get('/applicant_logout', 'ApplicantController@applicant_logout')->name('applicant_logout');
//     });

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'doLogin'])->name('admin_doLogin');
    Route::get('/student/login', [AuthController::class, 'studentLoginForm'])->name('student_login');
    Route::post('/student/login', [AuthController::class, 'doLogin'])->name('student_doLogin');
});

//applicant dashboard start
Route::middleware('auth')->group(function () {
    Route::get('/applicant_dashboard', [ApplicantDashboardController::class, 'index']);
    Route::get('/approved_application', [ApplicantDashboardController::class, 'approved_application']);
    Route::get('/exam_result', [ApplicantDashboardController::class, 'exam_result']);
    Route::get('/payment_details', [ApplicantDashboardController::class, 'payment_details']);
    Route::get('view_application_details/{id}', [ApplicantDashboardController::class, 'view_application_details']);
    Route::get('view_admit_card/{id}', [ApplicantDashboardController::class, 'view_admit_card']);
});
Route::prefix('api/applicant')->group(function () {
    Route::get('all_application', [ApplicantDashboardController::class, 'all_application_list']);
    Route::get('application_details/{id}', [ApplicantDashboardController::class, 'application_details']);
    Route::get('approved_application', [ApplicantDashboardController::class, 'approved_application_list']);
});

Route::get('/home', function () {
    if (auth()->check() && auth()->user()->type == 5) {
        return redirect('/student/dashboard');
    }
    if (auth()->check() && auth()->user()->type != 5) {
        return redirect('/admin/dashboard');
    }
});

// admission request
Route::get('admission_request', [AdmissionRequestController::class, 'admission_request'])->name('admission_request');
Route::post('student_admission', [AdmissionRequestController::class, 'student_admission'])->name('student_admission');
Route::get('admission_data', [AdmissionRequestController::class, 'admission_data'])->name('admission_data');
Route::get('/admission_get_location/{type}/{id}', [AdmissionRequestController::class, 'admission_get_location'])->name('admission_get_location');
Route::post('upload_prev_edu_certificate', [AdmissionRequestController::class, 'upload_prev_edu_certificate'])->name('upload_prev_edu_certificate');
Route::post('admission/upload_profile_photo', [AdmissionRequestController::class, 'admission_upload_profile_photo'])->name('admission.upload_profile_photo');
Route::post('admission/upload_birth_nid_certificate', [AdmissionRequestController::class, 'admission_upload_birth_nid_certificate'])->name('admission.upload_birth_nid_certificate');
Route::get('/class/{id}/sections-departments', [AdmissionRequestController::class, 'getSectionsAndDepartments']);

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::view('/admin/{any}', 'backend.index')->where('any', '.*');
    Route::view('/student/{any}', 'student.index')->where('any', '.*');

    Route::prefix('api')->group(function () {

        Route::get('/teacher_excel_report', [ExcelReportExportController::class, 'teacherExcel']);
        Route::post('/configurations', [ConfigurationController::class, 'getConfigurations']);
        Route::post('/file_upload', [UploadController::class, 'uploadFile']);
        Route::post('/image', [UploadController::class, 'imageUpload']);
        Route::resource('/profile', UserProfileController::class);
        Route::resource('/users', UserController::class);
        Route::get('/status_changes/{id}', [UserController::class, 'statusChanges']);
        Route::middleware('admin')->group(function () {
            Route::resource('/settings', ConfigurationController::class);

            // admission request
            Route::resource('/applicant_list', ApplicantRequestController::class);
            Route::get('/custom_short_list_status_change/{id}', [ApplicantRequestController::class, 'custom_short_list_status_change']);
            Route::get('/custom_preliminary_status/{id}', [ApplicantRequestController::class, 'custom_preliminary_status']);
            Route::get('/custom_reject_status/{id}', [ApplicantRequestController::class, 'custom_reject_status']);
            Route::get('/multiple_selected_status_changes/{event}/{id}', [ApplicantRequestController::class, 'multiple_selected_status_changes']);
            Route::get('view_application_details_backend/{id}', [ApplicantRequestController::class, 'view_application_details_backend']);
            Route::get('/applicant_list_export_excel', [ApplicantRequestController::class, 'applicantExportExcel']);
            Route::get('/admission_approved', [ApplicantRequestController::class, 'admission_approved']);
            Route::get('/remove_from_shortlist/{id}', [ApplicantRequestController::class, 'remove_from_shortlist']);
            Route::get('/multiple_selected_delete_status/{id}', [ApplicantRequestController::class, 'multiple_selected_delete_status']);
            Route::get('/applicant_sort_list_export_excel', [ApplicantRequestController::class, 'applicantSortListExportExcel']);
            Route::resource('/admission_exam', AdmissionExamController::class);
            Route::get('/applicant_final_export_excel', [ApplicantRequestController::class, 'applicantFinalExportExcel']);
            Route::get('/applicant_merit_export_excel', [ApplicantRequestController::class, 'applicantMeritExportExcel']);
            Route::get('/applicant_waiting_export_excel', [ApplicantRequestController::class, 'applicantWaitingExportExcel']);
            Route::get('/custom_merit_status/{id}', [ApplicantRequestController::class, 'custom_merit_status']);
            // add student
            Route::post('/student_store_from_admission', [ApplicantRequestController::class, 'studentStoreFromAdmission']);
            Route::get('view_application_payment_details/{id}', [ApplicantRequestController::class, 'view_application_payment_details']);
            //preliminary
            Route::get('/preliminary_approved', [PreliminaryController::class, 'preliminary_approved']);
            Route::get('/merit_list', [PreliminaryController::class, 'merit_index']);
            Route::get('/waiting_list', [PreliminaryController::class, 'waiting_index']);
            Route::get('/multiple_merit_waiting_changes/{event}/{id}', [PreliminaryController::class, 'multipleMeritWaitingChanges']);
            Route::get('/multiple_waiting_changes/{event}/{id}', [PreliminaryController::class, 'multipleWaitingChanges']);
            Route::get('/admission_test_result', [PreliminaryController::class, 'prilimaniry_result']);

            Route::any('/admission_mark_entry', [CommonAdmissionApi::class, 'index']);
            Route::post('/admission_mark_entry/store', [CommonAdmissionApi::class, 'store_admission_mark']);

            Route::resource('/fees_type', FeesTypeController::class);
            Route::resource('/fees_master', FeesMasterController::class);

            Route::resource('/admission_subject', AdmissionSubjectController::class);

            Route::post('/general', [ConfigurationController::class, 'getGeneralData']);
            Route::resource('/modules', ModuleController::class);
            Route::resource('/roles', RoleController::class);
            Route::resource('/module_permissions', RoleModuleController::class);
            Route::resource('/role_permissions', RolePermissionController::class);
            Route::resource('/book_accession', BookAccessionController::class);
            Route::resource('/stock_books', StockBookController::class);
            Route::get('/get_book_details', [StockBookController::class, 'getBookDetails']);

            Route::get('/get_student_details', [MembershipController::class, 'getStudentDetails']);
            Route::get('/get_teachers_details', [MembershipController::class, 'getTeacherDetails']);

            Route::get('/get_route_fare', [AssignTransportController::class, 'getRouteFare']);
            Route::resource('/membership', MembershipController::class);
            Route::resource('/book_author', BookAuthorController::class);
            Route::resource('/publisher', BookPublisherController::class);
            Route::resource('/edition', BookEditionController::class);
            Route::resource('/language', BookLanguageController::class);
            Route::resource('/book_issue', BookIssueController::class);
            Route::post('/return_single_book', [BookIssueController::class, 'returnSingleBook']);
            Route::post('/return_all_book', [BookIssueController::class, 'returnAllBook']);

            Route::resource('/category', StoreCategoryController::class);
            Route::resource('/product', ProductController::class);
            Route::resource('/stock_in', StockInController::class);

            Route::resource('/stock_out', StockOutController::class);
            Route::get('/get_product', [StockOutController::class, 'getProduct']);

            Route::resource('/transport_component', TransportComponentController::class);
            Route::resource('/route', TransportRouteController::class);
            Route::resource('/manage_transport', TransportManageController::class);
            Route::resource('/assign_transport', AssignTransportController::class);

            Route::get('/transport_month_closing', [TransportMonthClosingController::class, 'getTransportClosingData']);
            Route::post('/transport_month_closing', [TransportMonthClosingController::class, 'addTransportClosingData']);

            Route::resource('/transport_invoice', TransportInvoiceController::class);

            Route::resource('/dormitory_component', DormitoryComponentController::class);
            Route::resource('/manage_dormitory', ManageDormitoryController::class);
            Route::resource('/assign_dormitory', AssignDormitoryController::class);
            Route::get('/dormitory_month_closing', [DormitoryMonthClosingController::class, 'getDormitoryClosingData']);
            Route::post('/dormitory_month_closing', [DormitoryMonthClosingController::class, 'addDormitoryClosingData']);
            Route::resource('/dormitory_invoice', DormitoryInvoiceController::class);

            Route::get('/get_dormitory_student', [AssignDormitoryController::class, 'getDormitoryStudent']);

            Route::resource('/school', SchoolController::class);
            Route::resource('/department', DepartmentController::class);

            Route::get('/dashboard', [DashboardController::class, 'dashboardData']);
            Route::resource('/component_group', ComponentGroupController::class);
            Route::resource('/component_category', ComponentCategoryController::class);
            Route::resource('/bank', BankController::class);
            Route::resource('/component', ComponentController::class);
            Route::resource('/session', SessionYearController::class);

            Route::resource('/generate_invoice', InvoiceGenerateController::class);
            Route::resource('/student_payment', StudentPaymentController::class);

            Route::get('/classWiseStudent', [ConfigurationController::class, 'classWiseStudent']);
            Route::get('/get_waiver_student_invoice', [ConfigurationController::class, 'getWaiverStudentInvoice']);
            Route::get('/getComponentBySchoolId', [ConfigurationController::class, 'getComponentBySchoolId']);
            Route::get('/getComponentByGroupId', [ConfigurationController::class, 'getComponentByGroupId']);
            Route::get('/student_wise_invoice', [ConfigurationController::class, 'getStudentWiseInvoice']);
            // get student/teacher/staff
            Route::get('/get-canteen-member', [ConfigurationController::class, 'getCanteenMember']);
            Route::get('/get-student', [ConfigurationController::class, 'getStudent']);
            Route::get('/get-employee', [ConfigurationController::class, 'getEmployee']);
            Route::get('/user_wise_invoice', [ConfigurationController::class, 'getUserWiseInvoice']);
            Route::get('/get_invoices', [ConfigurationController::class, 'getInvoices']);
            Route::get('/get_cash_payment_invoice', [ConfigurationController::class, 'getCashInvoice']);

            Route::resource('/student', StudentController::class);
            Route::post('/student_status/{id}', [StudentController::class, 'studentStatus']);
            Route::post('/update_student_optional_subject/{id}', [StudentController::class, 'updateOptionalSubject']);
            Route::get('/get_student_optional_subjects', [StudentController::class, 'getOptionalSubjects']);

            Route::post('/generate-student-roll', [StudentController::class, 'generateStudentRoll']);

            // print status update
            Route::post('/update_print_status/{id}', [StudentController::class, 'updatePrintStatus']);

            // student waiver document
            Route::resource('/student_waiver_document', StudentWaiverDocumentController::class);
            Route::get('/get_child_merit_student', [StudentWaiverDocumentController::class, 'getChildMeritStudent']);
            // student waiver
            Route::resource('/student_waiver', StudentWaiverController::class);

            // student ID card
            Route::post('/student_id_card/{student_id}', [StudentIdCardController::class, 'getIdCard']);

            Route::resource('/getInstitute', StudentController::class);
            Route::resource('/class', ClassController::class);

            Route::resource('/employee_department', EmployeeDepartmentController::class);
            Route::resource('/employee_designation', EmployeeDesignationController::class);

            Route::resource('/employee', EmployeeController::class);
            Route::post('/employee_job_status/{id}', [EmployeeController::class, 'employeeJobStatus']);
            Route::post('/employees/reorder', [EmployeeController::class, 'reorder']);

            Route::resource('/parents', ParentController::class);
            Route::resource('/subjects', SubjectController::class);
            Route::resource('/group_subject', SubjectGroupController::class);
            Route::resource('/section', SectionController::class);

            // attendance
            Route::get('/student_attendance', [AttendanceController::class, 'studentAttendance']);
            Route::get('/teacher_attendance', [AttendanceController::class, 'teacherAttendance']);
            Route::get('/staff_attendance', [AttendanceController::class, 'staffAttendance']);

            // Manual  Attendance 
            Route::get('/get_manual_student_attendance', [ManualAttendanceController::class, 'getManualStudentAttendance']);
            Route::post('/submit_manual_student_attendance', [ManualAttendanceController::class, 'submitManualStudentAttendance']);
            Route::get('/manual_employee_attendance', [ManualAttendanceController::class, 'getEmployeeAttendance']);
            Route::post('/submit_manual_employee_attendance', [ManualAttendanceController::class, 'submitEmployeeAttendance']);

            // sms -> student
            Route::post('/sms_absent_student/{student_id}', [SmsController::class, 'smsToAbsentStudent']);
            Route::post('/sms_class_wise_absent_student', [SmsController::class, 'smsToClassWiseAbsentStudent']);
            Route::get('/sms_to_all_student', [SmsController::class, 'getAllStudents']);
            Route::post('/custom_sms_to_student', [SmsController::class, 'customSmsToStudent']);
            Route::post('/manual_student_attendance', [SmsController::class, 'customSmsToStudent']);

            // sms -> teacher & staff
            Route::post('/sms_single_absent_teacher/{teacher_id}', [SmsController::class, 'smsToSingleTeacher']);
            Route::post('/sms_multiple_absent_teacher', [SmsController::class, 'smsToMultipleTeacher']);

            Route::post('/sms_single_absent_staff/{staff_id}', [SmsController::class, 'smsToSingleStaff']);
            Route::post('/sms_multiple_absent_staff', [SmsController::class, 'smsToMultipleStaff']);

            Route::resource('/promotion', PromotionController::class);
            Route::post('/promotion', [PromotionController::class, 'addPromotion']);
            Route::get('/get_promotion_data', [PromotionController::class, 'getPromotionData']);
            Route::get('/get_new_class_wise_data', [PromotionController::class, 'getNewClassWiseData']);
            Route::get('/get_optional_subjects', [PromotionController::class, 'getOptionalSubjects']);
            Route::get('/get_class_wise_department', [PromotionController::class, 'getClassWiseDepartment']);
            Route::get('/transactionView/{id}', [StudentTransactionController::class, 'transactionDetails']);

            Route::resource('/student_demotion', DemotionController::class);
            Route::post('/student_demotion', [DemotionController::class, 'addDemotion']);
            Route::get('/get_demotion_data', [DemotionController::class, 'getDemotionData']);

            Route::resource('/appraisal_template', ApprisalTemplateController::class);
            Route::resource('/appraisal', ApprisalController::class);
            Route::get('/getDataByTemplateId', [ApprisalController::class, 'getTemplateData']);

            Route::resource('/website', WebsiteSetupController::class);
            Route::resource('/general_setting', GeneralSettingController::class);
            Route::resource('/our_management', OurManagementController::class);
            Route::resource('/faculties', FacultieController::class);
            Route::resource('/slider', SliderController::class);
            Route::resource('/service', ServiceController::class);
            Route::resource('/facilities', FacilitiesController::class);
            Route::resource('/school_life', SchoolLifeController::class);
            Route::resource('/event', EventController::class);
            Route::resource('/video', VideoController::class);
            Route::resource('/course_category', CourseCategoryController::class);
            Route::resource('/website_course', WebsiteCourseController::class);
            Route::resource('/department_wise_fees_structure', WebsiteFeesStructureController::class);
            Route::resource('/contact', WebsiteContactController::class);

            Route::get('/student_transaction_status', [StudentTransactionController::class, 'studentTransaction']);
            Route::get('/transactionView/{id}', [StudentTransactionController::class, 'transactionDetails']);

            Route::resource('/exam_name', ExamNameController::class);
            Route::resource('/exam_component', ExamComponentController::class);
            Route::resource('/examination', ExaminationController::class);
            Route::resource('/examination_grade', ExamGradeController::class);
            Route::resource('/grade_number', GradeNumberController::class);
            Route::get('/get_exam_type', [GradeNumberController::class, 'getExamType']);
            Route::get('/admit_card_seat_plan', [AdmitCardSeatPlanController::class, 'getAdmitCardSeatPlan']);
            Route::get('/number_sheet', [NumberSheetController::class, 'getStudentNumberSheet']);

            Route::resource('/documents', ExamDocumentController::class);

            Route::resource('/room', ClassRoomController::class);
            Route::resource('/building', BuildingController::class);
            Route::resource('/class_routines', RoutineController::class);

            Route::resource('/noticeboard', NoticeBoardController::class);
            Route::resource('/live_class', LiveClassController::class);

            // Canteen Route 
            Route::resource('/daily_meal_members', DailyMealController::class);
            Route::get('/meal-times', [DailyMealController::class, 'getMealTimes']);

            Route::resource('/canteen_configure', CanteenConfigureController::class);
            Route::resource('/meal_time', MealTimeController::class);
            Route::resource('/canteen_menu_item', CanteenMenuItemController::class);
            Route::resource('/canteen_menu_rostering', CanteenMenuRosterController::class);
            Route::get('/canteen_weekly_menu', [CanteenWeeklyMenuController::class, 'getWeeklyMenu']);
            Route::resource('/assign_canteen', AssignCanteenController::class);

            Route::get('/canteen_month_closing', [CanteenMonthClosingController::class, 'getCanteenClosingData']);
            Route::post('/canteen_month_closing', [CanteenMonthClosingController::class, 'addCanteenClosingData']);

            Route::resource('/canteen_invoice', CanteenInvoiceController::class);
            Route::resource('/canteen_daily_sale', CanteenDailySaleController::class);
            Route::post('/cash_sale_invoice_generate', [CanteenDailySaleController::class, 'addCashSaleInvoice']);

            Route::get('/canteen_menu_items_price', [ConfigurationController::class, 'getMenuItems']);


            Route::get('/canteen_weekly_menu/download/{month}/{week}', [CanteenWeeklyMenuController::class, 'downloadWeeklyMenu']);

            Route::post('/manage_marks', [ManageMarkController::class, 'store']);
            Route::get('/manage_marks', [ManageMarkController::class, 'getSubjectMark']);

            Route::get('/academic_transcript', [AcademicTranscriptController::class, 'getTranscript']);
            Route::get('/tabulation_sheet', [TabulationSheetController::class, 'getTabulationSheet']);

            Route::resource('/academic_syllabus', AcademicSyllabusController::class);

            Route::resource('/leave_category', LeaveCategorycontroller::class);
            Route::resource('/apply_leave', LeaveController::class);
            Route::resource('/approve_leave', ApproveLeaveController::class);
            Route::post('/approve_leave/{id}', [ApproveLeaveController::class, 'updateLeaveStatus']);
            Route::post('/due_leave', [LeaveController::class, 'DueLeaveData']);

            // Reports 
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/school_info', [SchoolInfoController::class, 'getSchoolInfo'])->name('school_info');

                // Attendance Reports
                Route::get('/teacher_attendance', [TeacherAttendanceReportController::class, 'showReport'])->name('teacher_attendance');
                Route::get('/staff_attendance', [StaffAttendanceReportController::class, 'showReport'])->name('staff_attendance');
                Route::get('/student_attendance', [StudentAttendanceReportController::class, 'showReport'])->name('student_attendance');

                // Excel Export
                Route::get('/teacher_attendance_excel', [TeacherAttendanceReportController::class, 'getTeacherAttendanceExcel'])->name('teacher_attendance_excel');
                Route::get('/staff_attendance_excel', [StaffAttendanceReportController::class, 'getStaffAttendanceExcel'])->name('staff_attendance_excel');
                Route::get('/student_attendance_excel', [StudentAttendanceReportController::class, 'getStudentAttendanceExcel'])->name('student_attendance_excel');

                // Student Reports
                Route::get('/students', [StudentDetailsReportController::class, 'showReport'])->name('students');
                Route::get('/activity_status', [ActivityStatusReport::class, 'showReport'])->name('dropout_active');
                Route::get('/class_wise_students', [StudentDetailsReportController::class, 'classWiseStudentReport'])->name('at_a_glance');

                Route::get('/employee', [EmployeeReportController::class, 'showReport'])->name('employees');
                Route::get('/class-teachers', [ClassTeacherReportController::class, 'showReport'])->name('class_teachers');
                Route::get('/responsible-teachers', [ResponsibleTeacherReportController::class, 'showReport'])->name('responsible_teachers');

                // Accounting Reports
                Route::get('/invoices', [InvoiceReportController::class, 'showReport'])->name('invoices');
                Route::get('/get-invoices', [InvoiceReportController::class, 'getInvoices']);
                Route::get('/get_invoice_by_student', [InvoiceReportController::class, 'getInvoiceByStudent'])->name('get_invoice_by_student');
                Route::get('/fees_collections', [FeesCollectionReportController::class, 'showReport'])->name('fees_collections');
                Route::get('/due_report', [DueReportController::class, 'showReport'])->name('due_report');
                Route::get('/monthly_due_report', [DueReportController::class, 'showMonthlyReport'])->name('monthly_due_report');
                Route::get('/ledgers', [LedgerReportController::class, 'showReport'])->name('ledgers');
                Route::get('/aging_report', [AgingReportController::class, 'showReport'])->name('aging_report');
                Route::get('/due_students', [AgingReportController::class, 'getDueStudents'])->name('due_students');
                Route::get('/waiver_report', [WaiverReportController::class, 'showReport'])->name('waiver_report');

                // Collection Reports
                Route::get('/class_wise_component_collection', [ClassWiseCollectionReportController::class, 'showReport'])->name('class_wise_component_collection');
                Route::get('/get_component_data', [ClassWiseCollectionReportController::class, 'getComponentData'])->name('get_component_data');
                Route::get('/fees_collection_information', [FessCollectionInformationReportController::class, 'showReport'])->name('fees_collection_information');
                Route::get('/get_component_data', [FessCollectionInformationReportController::class, 'getComponentData'])->name('fees_information_component_data');
                Route::get('/daily_collection', [DailyCollectionReportController::class, 'showReport'])->name('daily_collection');

                // Misc Reports
                Route::get('/dormitories', [DormitoryReportController::class, 'showReport'])->name('dormitories');
                Route::get('/dormitories_monthly_invoice_report', [DormitoryMonthlyInvoiceReportController::class, 'showReport'])->name('dormitories_monthly_report');
                Route::get('/transports', [TransportReportController::class, 'showReport'])->name('transports');
                Route::get('/transports_monthly_invoice_report', [TransportMonthlyInvoiceReportController::class, 'showReport'])->name('transports_monthly_report');
                Route::get('/canteen_monthly_invoice_report', [CanteenMonthlyInvoiceReportController::class, 'showReport'])->name('canteen_monthly_report');
                Route::get('/canteen_ledger_report', [CanteenLedgerReportController::class, 'showReport'])->name('canteen_ledger_report');
                Route::get('/book_issue', [BookIssueReportController::class, 'showReport'])->name('book_issue');
                Route::get('/stock_books', [BookReportController::class, 'showReport'])->name('stock_books');
                Route::get('/parents', [ParentReportController::class, 'showReport'])->name('parents');
                Route::get('/stocks', [StockReportController::class, 'showReport'])->name('stocks');
                Route::get('/sms_history', [SmsHistoryController::class, 'showReport'])->name('sms_history');
            });
        });
    });

    Route::middleware('student')->group(function () {
        Route::post('general', [ConfigurationController::class, 'getGeneralData']);

        Route::get('/dashboard', [ConfigurationController::class, 'getStudentDashboard']);
        Route::get('/teachers', [StudentGetDataController::class, 'getEmployees']);
        Route::get('/notice', [StudentGetDataController::class, 'getNotice']);
        Route::get('/subjects', [StudentGetDataController::class, 'getSubjects']);
        Route::get('/routine', [StudentGetDataController::class, 'getRoutine']);
        Route::get('/exam_routine', [StudentGetDataController::class, 'getExamRoutine']);
        Route::get('/result', [StudentGetDataController::class, 'getResult']);
        Route::get('/payments', [StudentGetDataController::class, 'getPayments']);
        Route::get('/canteen_meal', [StudentGetDataController::class, 'getCanteenMeal']);
        Route::get('/library', [StudentGetDataController::class, 'getLibrary']);
        Route::get('/profile', [StudentGetDataController::class, 'getProfileData']);
        Route::post('/updateProfile', [StudentGetDataController::class, 'getUpdateProfile']);
    });
});
