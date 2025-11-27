import baseLayouts from "../views/layouts/baseLayouts";
import userList from "../views/users/userList";
import pageNotFound from "../views/pageNotFound";
import profile from "../views/users/profile";
import dashboardPage from "../views/dashboardPage";
import moduleList from "../views/rbac/moduleList";
import roleList from "../views/rbac/roleList";
import roleModules from "../views/rbac/roleModules";
import rolePermissions from "../views/rbac/rolePermissions";
import configurationList from "../views/settings/configurationList";
import studentList from "../views/student/studentList";
import studentDetails from "../views/student/studentDetails";
import studentIdCard from "../views/student/studentIdCard";
import addStudent from "../views/student/addStudent";
import schoolList from "../views/school/schoolList";
import department from "../views/InstituteSetup/department";
import transportComponent from "../views/Transport/transportComponent";
import admissionRequestList from "../views/admission/AdmissionRequestList";
import admissionApprovedList from "../views/admission/AdmissionApprovedList";
import AdmissionExamList from "../views/admission/AdmissionExamList.vue";
import admission_mark_entry from "../views/admission/AdmissionMarkEntry.vue";
import AdmissionTestResult from "../views/admission/AdmissionTestResult";
import admissionSubjectList from "../views/admission/AdmissionSubjectList";

import preliminaryCandidates from "../views/admission/preliminaryCandidates";
import finalMeritList from "../views/admission/final_merit_list";
import finalWaitingList from "../views/admission/final_waiting_list";

import FeesTypeList from "../views/feeCollection/FeesTypeList";
import FeesMasterList from "../views/feeCollection/FeesMasterList";



import employeeDepartment from "../views/employee/employeeDepartment";
import employeeDesignation from "../views/employee/employeeDesignation";
import employeeList from "../views/employee/employeeList";
import employeeAddForm from "../views/employee/employeeAddForm";
import employeeDetails from "../views/employee/employeeDetails";

import ExamComponent from "../views/examination/ExamComponent";
import admitCardAndSeatPlan from "../views/examination/admitCardAndSeatPlan";
import numberSheet from "../views/examination/numberSheet";
import ExamNameList from "../views/examination/ExamNameList";
import ExaminationList from "../views/examination/ExaminationList";
import ExamGradeList from "../views/examination/ExamGradeList";
import ExamGradeNumber from "../views/examination/ExamGradeNumber";
import ManageMark from "../views/examination/ManageMark";
import TabulationSheet from "../views/examination/TabulationSheet";
import AcademicTranscript from "../views/examination/AcademicTranscript";
import DocumentList from "../views/examination/DocumentList";
import ClassRoomList from "../views/routine/ClassRoomList";
import BuildingList from "../views/routine/BuildingList";
import ClassRoutine from "../views/routine/ClassRoutine";
import NoticeBoardList from "../views/notice/NoticeBoardList";
import mealTime from "../views/canteen/mealTime";
import CanteenMenuItem from "../views/canteen/CanteenMenuItem";
import CanteenMenuRoster from "../views/canteen/CanteenMenuRoster";
import canteenWeeklyMenu from "../views/canteen/canteenWeeklyMenu";
import AssignCanteen from "../views/canteen/AssignCanteen";
import CanteenConfigure from "../views/canteen/CanteenConfigure";
import CanteenMemberMeal from "../views/canteen/CanteenMemberMeal";

import canteenDailyFree from "../views/canteen/canteenDailyFree";
import canteenMonthClosing from "../views/canteen/canteenMonthClosing";

import CanteenInvoice from "../views/canteen/CanteenInvoice";
import canteenDailySale from "../views/canteen/canteenDailySale";
import LiveClassList from "../views/liveClass/LiveClassList";
import ApprisalTemplate from "../views/apprisal/ApprisalTemplate";
import ApprisalList from "../views/apprisal/ApprisalList";
import sessionYear from "../views/InstituteSetup/sessionYear";
import studentWaiver from "../views/accounting/studentWaiver";
import studentWaiverDocument from "../views/accounting/studentWaiverDocument";
import componentGroup from "../views/accounting/componentGroup";
import componentCategory from "../views/accounting/componentCategory";
import bankList from "../views/accounting/bankList";
import componentList from "../views/accounting/componentList";
import studentGenerateInvoice from "../views/accounting/studentGenerateInvoice";
import addStudentGenerateInvoice from "../views/accounting/addStudentGenerateInvoice";
import studentPayment from "../views/accounting/studentPayment";
import StudentTransaction from "../views/accounting/StudentTransaction";

import studentClassList from "../views/InstituteSetup/studentClassList";
import studentPromotion from "../views/student/studentPromotion";
import studentDemotion from "../views/student/studentDemotion";
import sectionList from "../views/InstituteSetup/sectionList";
import bookAccession from "../views/Library/bookAccession.vue";
import stockBooks from "../views/Library/stockBooks.vue";
import memberShip from "../views/Library/memberShip.vue";
import bookIssue from "../views/Library/bookIssue.vue";

import transportRoute from "../views/Transport/transportRoute.vue";
import manageTransport from "../views/Transport/manageTransport.vue";
import assignTransport from "../views/Transport/assignTransport.vue";
import transportInvoice from "../views/Transport/transportInvoice.vue";
import transportMonthClosing from "../views/Transport/transportMonthClosing.vue";
import bookAuthor from "../views/Library/librarySetup/bookAuthor.vue";
import bookPublisher from "../views/Library/librarySetup/bookPublisher.vue";
import bookEdition from "../views/Library/librarySetup/bookEdition.vue";
import bookLanguage from "../views/Library/librarySetup/bookLanguage.vue";

import groupSubject from "../views/InstituteSetup/groupSubject";
import subjectList from "../views/InstituteSetup/subjectList";
import parentList from "../views/parent/parentList";
import parentForm from "../views/parent/parentForm";
import ParentDetails from "../views/parent/ParentDetails";
import websiteForm from "../views/website/websiteForm";
import generalSettingForm from "../views/componentConfigure/generalSettingForm";
import ourManagement from "../views/website/ourManagement";
import SchoolFacilities from "../views/website/SchoolFacilities";
import SchoolLife from "../views/website/SchoolLife";
import FacultieList from "../views/website/FacultieList";

import sliderList from "../views/website/sliderList";
import eventList from "../views/website/eventList";
import videoList from "../views/website/videoList";
import courseCategory from "../views/website/courseCategory";
import courseList from "../views/website/course/courseList";
import addCourse from "../views/website/course/addCourse";
import feeStructure from "../views/website/feeStructure";
import websiteContact from "../views/website/websiteContact";
import Service from "../views/website/ServiceList";
import LeaveCategoryList from "../views/leave/LeaveCategoryList";
import LeaveList from "../views/leave/LeaveList";
import ApproveLeave from "../views/leave/ApproveLeave";
import ViewStatus from "../views/leave/ViewStatus";
import teacherAttendance from "../views/attendance/teacherAttendance";
import staffAttendance from "../views/attendance/staffAttendance";
import studentAttendance from "../views/attendance/studentAttendance";
import smsAllStudent from "../views/attendance/smsAllStudent";
import manualStudentAttendance from "../views/attendance/manualStudentAttendance";
import manualEmployeeAttendance from "../views/attendance/manualEmployeeAttendance";
import dormitoryComponent from "../views/Dormitory/dormitoryComponent";
import manageDormitory from "../views/Dormitory/manageDormitory";
import assignDormitory from "../views/Dormitory/assignDormitory";
import dormitoryMonthClosing from "../views/Dormitory/dormitoryMonthClosing";
import dormitoryInvoice from "../views/Dormitory/dormitoryInvoice";
import productCategory from "../views/store/productCategory";
import productList from "../views/store/productList";
import stockIn from "../views/store/stockIn";
import stockOut from "../views/store/stockOut";
import AcademicSyllabus from '../views/academic/AcademicSyllabus';

import employeeReport from '../views/report/employee/employeeReport';
import classTeacherReport from '../views/report/employee/classTeacherReport';
import responsibleTeacherReport from '../views/report/employee/responsibleTeacherReport';
import dormitoryReport from '../views/report/dormitories/dormitoryReport';
import dormitoryMonthlyInvoiceReport from '../views/report/dormitories/dormitoryMonthlyInvoiceReport';
import transportReport from '../views/report/transports/transportReport';
import transportMonthlyInvoiceReport from '../views/report/transports/transportMonthlyInvoiceReport';
import canteenMonthlyInvoiceReport from "../views/report/canteen/canteenMonthlyInvoiceReport";
import canteenLedgerReport from "../views/report/canteen/canteenLedgerReport";
import bookReport from '../views/report/library/bookReport';
import stockReport from '../views/report/product/stockReport';

import StudentReport from '../views/report/student/studentReport';
import invoiceReport from '../views/report/accounting/invoiceReport';
import feesCollectionReport from '../views/report/accounting/feesCollectionReport';
import dueReport from '../views/report/accounting/dueReport';
import monthlyDueReport from '../views/report/accounting/monthlyDueReport';
import ledgerReport from '../views/report/accounting/ledgerReport';
import agingReport from '../views/report/accounting/agingReport';
import waiverReport from '../views/report/accounting/waiverReport';

import ClasswiseCollectionReport from '../views/report/ClasswiseCollectionReport';
import feesCollectionInformationReport from '../views/report/feesCollectionInformationReport';
import activityStatusReport from '../views/report/student/activityStatusReport';
import classWiseStudentReport from '../views/report/student/classWiseStudentReport';
import parentsReport from '../views/report/student/parentsReport';
import BookIssueReport from '../views/report/library/BookIssueReport';
import DailyCollectionReport from '../views/report/accounting/DailyCollectionReport';
import teacherAttendanceReport from '../views/report/attendance/teacherAttendanceReport';
import staffAttendanceReport from '../views/report/attendance/staffAttendanceReport';
import studentAttendanceReport from '../views/report/attendance/studentAttendanceReport';
import smsHistoryReport from '../views/report/sms/smsHistoryReport';

const routes = [{
        path: "/admin/",
        component: baseLayouts,
        children: [
            { path: "404", name: "404", component: pageNotFound },
            {
                path: "dashboard",
                name: "dashboard",
                component: dashboardPage,
                meta: { dataUrl: "api/dashboard", pageTitle: "Dashboard" },
            },

            {
                path: "profile",
                name: "profile",
                component: profile,
                meta: { dataUrl: "api/profile", pageTitle: "Profile" },
            },
            {
                path: "users",
                name: "users",
                component: userList,
                meta: { dataUrl: "api/users", pageTitle: "Users" },
            },
            {
                path: "role",
                name: "role",
                component: roleList,
                meta: { dataUrl: "api/roles", pageTitle: "Role" },
            },
            {
                path: "module",
                name: "module",
                component: moduleList,
                meta: { dataUrl: "api/modules", pageTitle: "Module" },
            },
            {
                path: "configurations",
                name: "configuration",
                component: configurationList,
                meta: { dataUrl: "api/settings", pageTitle: "Configuration" },
            },
            {
                path: "module_permission",
                name: "module_permission",
                component: roleModules,
                meta: {
                    dataUrl: "api/module_permissions",
                    pageTitle: "Module Permission",
                },
            },
            {
                path: "role_permissions",
                name: "role_permissions",
                component: rolePermissions,
                meta: {
                    dataUrl: "api/role_permissions",
                    pageTitle: "Role Permission",
                },
            },
            {
                path: "general_setting",
                name: "general_setting",
                component: generalSettingForm,
                meta: { dataUrl: "api/general_setting", pageTitle: "General Setting", },
            },
            //addmission
            {
                path: 'applicant_list',
                name: 'applicant_list',
                component: admissionRequestList,
                meta: { dataUrl: 'api/applicant_list' }
            },
            {
                path: 'applicant_request_selection',
                name: 'applicant_request_selection',
                component: admissionApprovedList,
                meta: { dataUrl: 'api/admission_approved' }
            },
            {
                path: 'admission_exam',
                name: 'admission_exam',
                component: AdmissionExamList,
                meta: { dataUrl: 'api/admission_exam' }
            },
            {
                path: 'applicant_test_mark_entry',
                name: 'applicant_test_mark_entry',
                component: admission_mark_entry,
                meta: { dataUrl: 'api/admission_mark_entry', pageTitle: 'Admission Mark Entry' }
            },
            {
                path: 'admission_test_result',
                name: 'admission_test_result',
                component: AdmissionTestResult,
                meta: { dataUrl: 'api/admission_test_result', pageTitle: 'Admission Test Result' }
            },

            {
                path: 'final_applicant_selection_list',
                name: 'final_applicant_selection_list',
                component: preliminaryCandidates,
                meta: { dataUrl: 'api/preliminary_approved' }
            },
            {
                path: 'merit_list',
                name: 'merit_list',
                component: finalMeritList,
                meta: { dataUrl: 'api/merit_list' }
            },

            {
                path: 'waiting_list',
                name: 'waiting_list',
                component: finalWaitingList,
                meta: { dataUrl: 'api/waiting_list' }
            },

            {
                path: 'admission_subject',
                name: 'admission_subject',
                component: admissionSubjectList,
                meta: { dataUrl: 'api/admission_subject' }
            },
            {
                path: 'fees_type',
                name: 'fees_type',
                component: FeesTypeList,
                meta: { dataUrl: 'api/fees_type' }
            },
            {
                path: 'fees_master',
                name: 'fees_master',
                component: FeesMasterList,
                meta: { dataUrl: 'api/fees_master' }
            },

            {
                path: "student",
                name: "student",
                component: studentList,
                meta: { dataUrl: "api/student", pageTitle: "Student List" },
            },
            {
                path: "student/add",
                name: "student_add",
                component: addStudent,
                meta: { dataUrl: "api/student", pageTitle: "Student Form" },
            },
            {
                path: "student/add/:id?",
                name: "student_edit",
                component: addStudent,
                meta: { dataUrl: "api/student", pageTitle: "Student Edit" },
            },
            {
                path: "student_view/:id",
                name: "student_view",
                component: studentDetails,
                meta: { dataUrl: "api/student", pageTitle: "Student Details" },
            },
            {
                path: 'promotion',
                name: 'promotion',
                component: studentPromotion,
                meta: { dataUrl: 'api/promotion', pageTitle: 'Promotion' }
            },
            {
                path: 'student_demotion',
                name: 'student_demotion',
                component: studentDemotion,
                meta: { dataUrl: 'api/student_demotion', pageTitle: 'Demotion' }
            },

            // Student ID card
            {
                path: "student_id_card/:id",
                name: "student_id_card",
                component: studentIdCard,
                meta: { dataUrl: "api/student_id_card", pageTitle: "Student ID Card" },
            },
            {
                path: "student_attendance",
                name: "student_attendance",
                component: studentAttendance,
                meta: { dataUrl: "api/student_attendance", pageTitle: "Student Attendance", isFormPage: true, },
            },
            {
                path: "teacher_attendance",
                name: "teacher_attendance",
                component: teacherAttendance,
                meta: { dataUrl: "api/teacher_attendance", pageTitle: "Teacher Attendance", isFormPage: true, },
            },
            {
                path: "staff_attendance",
                name: "staff_attendance",
                component: staffAttendance,
                meta: { dataUrl: "api/staff_attendance", pageTitle: "Staff Attendance", isFormPage: true, },
            },
            {
                path: "sms_to_all_student",
                name: "sms_to_all_student",
                component: smsAllStudent,
                meta: { dataUrl: "api/sms_to_all_student", pageTitle: "SMS To All Student", isFormPage: true, },
            },

            {
                path: "manual_student_attendance",
                name: "manual_student_attendance",
                component: manualStudentAttendance,
                meta: { dataUrl: "api/manual_student_attendance", pageTitle: "Manual Student Attendance", isFormPage: true, },
            },
            {
                path: "manual_employee_attendance",
                name: "manual_employee_attendance",
                component: manualEmployeeAttendance,
                meta: { dataUrl: "api/manual_employee_attendance", pageTitle: "Manual Employee Attendance", isFormPage: true, },
            },

            {
                path: "category",
                name: "category",
                component: productCategory,
                meta: { dataUrl: "api/category", pageTitle: "Product Category", },
            },
            {
                path: "product",
                name: "product",
                component: productList,
                meta: { dataUrl: "api/product", pageTitle: "Product List", },
            },
            {
                path: "stock_in",
                name: "stock_in",
                component: stockIn,
                meta: { dataUrl: "api/stock_in", pageTitle: "Stock In", },
            },
            {
                path: "stock_out",
                name: "stock_out",
                component: stockOut,
                meta: { dataUrl: "api/stock_out", pageTitle: "Stock Out", },
            },
            {
                path: "dormitory_component",
                name: "dormitory_component",
                component: dormitoryComponent,
                meta: { dataUrl: "api/dormitory_component", pageTitle: "Dormitory Component", },
            },
            {
                path: "manage_dormitory",
                name: "manage_dormitory",
                component: manageDormitory,
                meta: { dataUrl: "api/manage_dormitory", pageTitle: "Manage Dormitory", },
            },
            {
                path: "assign_dormitory",
                name: "assign_dormitory",
                component: assignDormitory,
                meta: { dataUrl: "api/assign_dormitory", pageTitle: "Assign Dormitory", },
            },
            {
                path: 'dormitory_month_closing',
                name: 'dormitory_month_closing',
                component: dormitoryMonthClosing,
                meta: { dataUrl: 'api/dormitory_month_closing', pageTitle: 'Dormitory Invoice Generate', isFormPage: true }
            },
            {
                path: 'dormitory_invoice',
                name: 'dormitory_invoice',
                component: dormitoryInvoice,
                meta: { dataUrl: 'api/dormitory_invoice', pageTitle: 'Dormitory Invoice' }
            },
            {
                path: "parents",
                name: "parents",
                component: parentList,
                meta: { dataUrl: "api/parents", pageTitle: "Parent List" },
            },
            {
                path: "parents/add",
                name: "parents_add",
                component: parentForm,
                meta: { dataUrl: "api/parents", pageTitle: "Parent Form" },
            },
            {
                path: "parents/add/:id?",
                name: "parents_edit",
                component: parentForm,
                meta: { dataUrl: "api/parents", pageTitle: "Parent Edit" },
            },
            {

                path: "parents_view/:id",
                name: "parents_view",
                component: ParentDetails,
                meta: { dataUrl: "api/parents", pageTitle: "Parent Details" },
            },

            {
                path: 'leave_category',
                name: 'leave_category',
                component: LeaveCategoryList,
                meta: { dataUrl: 'api/leave_category', pageTitle: 'Leave Category' }

            },
            {
                path: 'apply_leave',
                name: 'apply_leave',
                component: LeaveList,
                meta: { dataUrl: 'api/apply_leave', pageTitle: 'Leave List' }

            },
            {
                path: 'approve_leave',
                name: 'approve_leave',
                component: ApproveLeave,
                meta: { dataUrl: 'api/approve_leave', pageTitle: 'Leave Approve' }

            },
            {
                path: 'viewStatus/:id',
                name: 'viewStatus',
                component: ViewStatus,
                meta: { dataUrl: 'api/apply_leave', pageTitle: 'View Status', isFormPage: true }
            },
            {
                path: 'academic_syllabus',
                name: 'academic_syllabus',
                component: AcademicSyllabus,
                meta: { dataUrl: 'api/academic_syllabus', pageTitle: 'Academic Syllabus' }
            },


            // ./ employee as a teacher and staff
            {
                path: 'employee',
                name: 'employee',
                component: employeeList,
                meta: { dataUrl: 'api/employee', pageTitle: 'Employees List' }

            },
            {
                path: 'employee/add/',
                name: 'employee_add',
                component: employeeAddForm,
                meta: { dataUrl: 'api/employee', pageTitle: 'Employee Form' }
            },
            {
                path: 'employee/add/:id?',
                name: 'employee_edit',
                component: employeeAddForm,
                meta: { dataUrl: 'api/employee', pageTitle: 'Employee Edit' }
            },
            {
                path: 'employee_view/:id',
                name: 'employee_view',
                component: employeeDetails,
                meta: { dataUrl: 'api/employee', pageTitle: 'Employees Details' }
            },
            // ./staff


            {
                path: 'student_transaction_status',
                name: 'student_transaction_status',
                component: StudentTransaction,
                meta: { dataUrl: 'api/student_transaction_status', pageTitle: 'Student Transaction' }
            },
            {
                path: 'employee_department',
                name: 'employee_department',
                component: employeeDepartment,
                meta: { dataUrl: 'api/employee_department', pageTitle: 'Employee Department' }
            },
            {
                path: 'employee_designation',
                name: 'employee_designation',
                component: employeeDesignation,
                meta: { dataUrl: 'api/employee_designation', pageTitle: 'Employee Designation' }
            },
            {
                path: 'exam_name',
                name: 'exam_name',
                component: ExamNameList,
                meta: { dataUrl: 'api/exam_name', pageTitle: 'Exam Name' }
            },
            {
                path: 'exam_component',
                name: 'exam_component',
                component: ExamComponent,
                meta: { dataUrl: 'api/exam_component', pageTitle: 'Exam Component' }
            },
            {
                path: 'examination',
                name: 'examination',
                component: ExaminationList,
                meta: { dataUrl: 'api/examination', pageTitle: 'Examination' }

            },
            {
                path: 'examination_grade',
                name: 'examination_grade',
                component: ExamGradeList,
                meta: { dataUrl: 'api/examination_grade', pageTitle: 'Exam Grade' }
            },
            {
                path: 'manage_marks',
                name: 'manage_marks',
                component: ManageMark,
                meta: { dataUrl: 'api/manage_marks', pageTitle: 'Manage Marks', isFormPage: true }
            },
            {
                path: 'academic_transcript',
                name: 'academic_transcript',
                component: AcademicTranscript,
                meta: { dataUrl: 'api/academic_transcript', pageTitle: 'Academic Transcript', isFormPage: true }
            },

            {
                path: 'tabulation_sheet',
                name: 'tabulation_sheet',
                component: TabulationSheet,
                meta: { dataUrl: 'api/tabulation_sheet', pageTitle: 'Tabulation Sheet', isFormPage: true }
            },
            {
                path: 'documents',
                name: 'documents',
                component: DocumentList,
                meta: { dataUrl: 'api/documents', pageTitle: 'Documents' }
            },
            {
                path: 'admit_card_seat_plan',
                name: 'admit_card_seat_plan',
                component: admitCardAndSeatPlan,
                meta: { dataUrl: 'api/admit_card_seat_plan', pageTitle: 'Admit Card & Seat Plan' }
            },

            {
                path: 'number_sheet',
                name: 'number_sheet',
                component: numberSheet,
                meta: { dataUrl: 'api/number_sheet', pageTitle: 'Number Sheet' }
            },

            {
                path: 'room',
                name: 'room',
                component: ClassRoomList,
                meta: { dataUrl: 'api/room', pageTitle: 'Class Room' }
            },
            {
                path: 'building',
                name: 'building',
                component: BuildingList,
                meta: { dataUrl: 'api/building', pageTitle: 'Buildings' }
            },
            {
                path: 'class_routines',
                name: 'class_routines',
                component: ClassRoutine,
                meta: { dataUrl: 'api/class_routines', pageTitle: 'Class Routine' }
            },
            {
                path: 'noticeboard',
                name: 'noticeboard',
                component: NoticeBoardList,
                meta: { dataUrl: 'api/noticeboard', pageTitle: 'Notice Board' }
            },
            {
                path: 'live_class',
                name: 'live_class',
                component: LiveClassList,
                meta: { dataUrl: 'api/live_class', pageTitle: 'Live Class' }
            },
            // canteen 
            {
                path: 'daily_meal_members',
                name: 'daily_meal_members',
                component: CanteenMemberMeal,
                meta: { dataUrl: 'api/daily_meal_members', pageTitle: 'Canteen Members Meal' }
            },
            {
                path: 'canteen_configure',
                name: 'canteen_configure',
                component: CanteenConfigure,
                meta: { dataUrl: 'api/canteen_configure', pageTitle: 'Configure Canteen' }
            },
            {
                path: 'meal_time',
                name: 'meal_time',
                component: mealTime,
                meta: { dataUrl: 'api/meal_time', pageTitle: 'Canteen component' }
            },
            {
                path: 'canteen_menu_item',
                name: 'canteen_menu_item',
                component: CanteenMenuItem,
                meta: { dataUrl: 'api/canteen_menu_item', pageTitle: 'Canteen Menu Item' }
            },
            {
                path: 'canteen_menu_rostering',
                name: 'canteen_menu_rostering',
                component: CanteenMenuRoster,
                meta: { dataUrl: 'api/canteen_menu_rostering', pageTitle: 'Canteen Menu Rostering' }
            },
            {
                path: 'canteen_weekly_menu',
                name: 'canteen_weekly_menu',
                component: canteenWeeklyMenu,
                meta: { dataUrl: 'api/canteen_weekly_menu', pageTitle: 'Canteen Weekly Menu' }
            },
            {
                path: 'assign_canteen',
                name: 'assign_canteen',
                component: AssignCanteen,
                meta: { dataUrl: 'api/assign_canteen', pageTitle: 'Assign Canteen' }
            },
            // {
            //     path: 'canteen_daily_fee',
            //     name: 'canteen_daily_fee',
            //     component: canteenDailyFree,
            //     meta: { dataUrl: 'api/canteen_daily_fee', pageTitle: 'Canteen Daily Sale', isFormPage: true }
            // },
            {
                path: 'canteen_daily_sale',
                name: 'canteen_daily_sale',
                component: canteenDailySale,
                meta: { dataUrl: 'api/canteen_daily_sale', pageTitle: 'Canteen Daily Sale' }
            },
            {
                path: 'canteen_month_closing',
                name: 'canteen_month_closing',
                component: canteenMonthClosing,
                meta: { dataUrl: 'api/canteen_month_closing', pageTitle: 'Canteen Month Closing', isFormPage: true }
            },
            {
                path: 'canteen_invoice',
                name: 'canteen_invoice',
                component: CanteenInvoice,
                meta: { dataUrl: 'api/canteen_invoice', pageTitle: 'Canteen Invoice' }
            },

            // ./ canteen
            {
                path: 'grade_number',
                name: 'grade_number',
                component: ExamGradeNumber,
                meta: { dataUrl: 'api/grade_number', pageTitle: 'Grade Number' }
            },
            {
                path: 'department',
                name: 'department',
                component: department,
                meta: { dataUrl: 'api/department', pageTitle: 'Department List' }
            },
            {
                path: 'student_waiver',
                name: 'student_waiver',
                component: studentWaiver,
                meta: { dataUrl: 'api/student_waiver', pageTitle: 'Student Waiver' }
            },
            {
                path: 'student_waiver_document',
                name: 'student_waiver_document',
                component: studentWaiverDocument,
                meta: { dataUrl: 'api/student_waiver_document', pageTitle: 'Student Waiver Document' }
            },

            {
                path: 'appraisal_template',
                name: 'appraisal_template',
                component: ApprisalTemplate,
                meta: { dataUrl: 'api/appraisal_template', pageTitle: 'Appraisal Template' }
            },
            {
                path: 'appraisal',
                name: 'appraisal',
                component: ApprisalList,
                meta: { dataUrl: 'api/appraisal', pageTitle: 'Appraisal' }
            },
            {
                path: 'school',
                name: 'school',
                component: schoolList,
                meta: { dataUrl: 'api/school', pageTitle: 'School List' }
            },

            {
                path: 'component_group',
                name: 'component_group',
                component: componentGroup,
                meta: { dataUrl: 'api/component_group', pageTitle: 'Component Group' }
            },
            {
                path: 'component_category',
                name: 'component_category',
                component: componentCategory,
                meta: { dataUrl: 'api/component_category', pageTitle: 'Component Category' }
            },
            {
                path: 'bank',
                name: 'bank',
                component: bankList,
                meta: { dataUrl: 'api/bank', pageTitle: 'Bank' }
            },
            {
                path: 'component',
                name: 'component',
                component: componentList,
                meta: { dataUrl: 'api/component', pageTitle: 'Component' }
            },
            {
                path: 'generate_invoice',
                name: 'generate_invoice',
                component: studentGenerateInvoice,
                meta: { dataUrl: 'api/generate_invoice', pageTitle: 'Academic Invoice' }
            },

            {
                path: "generate_invoice/add",
                name: "generate_invoice_add",
                component: addStudentGenerateInvoice,
                meta: { dataUrl: "api/generate_invoice", pageTitle: "Academic Invoice Form" },
            },

            {
                path: "generate_invoice/edit/:id?",
                name: "generate_invoice_edit",
                component: addStudentGenerateInvoice,
                meta: { dataUrl: "api/generate_invoice", pageTitle: "Academic Invoice Edit" },
            },

            {
                path: 'student_payment',
                name: 'student_payment',
                component: studentPayment,
                meta: { dataUrl: 'api/student_payment', pageTitle: 'Payment Collection' }
            },
            {
                path: 'session',
                name: 'session',
                component: sessionYear,
                meta: { dataUrl: 'api/session', pageTitle: 'Session' }
            },
            {
                path: 'subjects',
                name: 'subjects',
                component: subjectList,
                meta: { dataUrl: 'api/subjects', pageTitle: 'Subject' }
            },
            {
                path: 'group_subject',
                name: 'group_subject',
                component: groupSubject,
                meta: { dataUrl: 'api/group_subject', pageTitle: 'Group Subject' }
            },
            {
                path: 'class',
                name: 'class',
                component: studentClassList,
                meta: { dataUrl: 'api/class', pageTitle: 'Class List' }
            },
            {
                path: 'section',
                name: 'section',
                component: sectionList,
                meta: { dataUrl: 'api/section', pageTitle: 'Section' }
            },
            {
                path: 'ledger_report',
                name: 'ledger_report',
                component: ledgerReport,
                meta: { dataUrl: 'api/ledger_report', pageTitle: 'Ledger Report', isFormPage: true }
            },
            {
                path: 'book_accession',
                name: 'book_accession',
                component: bookAccession,
                meta: { dataUrl: 'api/book_accession', pageTitle: 'Book Accession' }
            },
            {
                path: 'stock_books',
                name: 'stock_books',
                component: stockBooks,
                meta: { dataUrl: 'api/stock_books', pageTitle: 'Stock' }
            },
            {
                path: 'membership',
                name: 'membership',
                component: memberShip,
                meta: { dataUrl: 'api/membership', pageTitle: 'Membership' }
            },
            {
                path: 'book_issue',
                name: 'book_issue',
                component: bookIssue,
                meta: { dataUrl: 'api/book_issue', pageTitle: 'Issue Books' }
            },
            {
                path: 'route',
                name: 'route',
                component: transportRoute,
                meta: { dataUrl: 'api/route', pageTitle: 'Transport Route' }
            },
            {
                path: 'transport_component',
                name: 'transport_component',
                component: transportComponent,
                meta: { dataUrl: 'api/transport_component', pageTitle: 'Transport Component' }
            },
            {
                path: 'manage_transport',
                name: 'manage_transport',
                component: manageTransport,
                meta: { dataUrl: 'api/manage_transport', pageTitle: 'Manage Transport' }
            },
            {
                path: 'assign_transport',
                name: 'assign_transport',
                component: assignTransport,
                meta: { dataUrl: 'api/assign_transport', pageTitle: 'Assign Transport' }
            },
            {
                path: 'transport_month_closing',
                name: 'transport_month_closing',
                component: transportMonthClosing,
                meta: { dataUrl: 'api/transport_month_closing', pageTitle: 'Transport Invoice Generate', isFormPage: true }
            },
            {
                path: 'transport_invoice',
                name: 'transport_invoice',
                component: transportInvoice,
                meta: { dataUrl: 'api/transport_invoice', pageTitle: 'Transport Invoice' }
            },
            {
                path: 'library/book_author',
                name: 'book_author',
                component: bookAuthor,
                meta: { dataUrl: 'api/book_author', pageTitle: 'Book Author' }
            },
            {
                path: 'library/publisher',
                name: 'publisher',
                component: bookPublisher,
                meta: { dataUrl: 'api/publisher', pageTitle: 'Book Publisher' }
            },
            {
                path: 'library/edition',
                name: 'edition',
                component: bookEdition,
                meta: { dataUrl: 'api/edition', pageTitle: 'Book Edition' }
            },
            {
                path: 'library/language',
                name: 'language',
                component: bookLanguage,
                meta: { dataUrl: 'api/language', pageTitle: 'Language' }
            },

            // -------------- All reports ------------
            {
                path: 'reports/teacher_attendance',
                name: 'reports.teacher_attendance',
                component: teacherAttendanceReport,
                meta: { dataUrl: 'api/reports/teacher_attendance', pageTitle: 'Teacher Attendance Report', isFormPage: true }
            },
            {
                path: 'reports/staff_attendance',
                name: 'reports.staff_attendance',
                component: staffAttendanceReport,
                meta: { dataUrl: 'api/reports/staff_attendance', pageTitle: 'Staff Attendance Report', isFormPage: true }
            },
            {
                path: 'reports/student_attendance',
                name: 'reports.student_attendance',
                component: studentAttendanceReport,
                meta: { dataUrl: 'api/reports/student_attendance', pageTitle: 'Student Attendance Report', isFormPage: true }
            },
            {
                path: 'reports/sms_history',
                name: 'reports.sms_history',
                component: smsHistoryReport,
                meta: { dataUrl: 'api/reports/sms_history', pageTitle: 'SMS History Report', isFormPage: true }
            },
            {
                path: 'reports/employee',
                name: 'reports.employee',
                component: employeeReport,
                meta: { dataUrl: 'api/reports/employee', pageTitle: 'Employee Report', isFormPage: true }
            },
            {
                path: 'reports/class-teachers',
                name: 'reports.class_teachers',
                component: classTeacherReport,
                meta: { dataUrl: 'api/reports/class-teachers', pageTitle: 'Class Teacher Report', isFormPage: true }
            },
            {
                path: 'reports/responsible-teachers',
                name: 'reports.responsible_teachers',
                component: responsibleTeacherReport,
                meta: { dataUrl: 'api/reports/responsible-teachers', pageTitle: 'Responsible Teacher Report', isFormPage: true }
            },
            {
                path: 'reports/dormitories_monthly_invoice_report',
                name: 'reports.dormitories_monthly_invoice_report',
                component: dormitoryMonthlyInvoiceReport,
                meta: { dataUrl: 'api/reports/dormitories_monthly_invoice_report', pageTitle: 'Dormitories Monthly Invoice Report', isFormPage: true }
            },
            {
                path: 'reports/dormitories',
                name: 'reports.dormitories',
                component: dormitoryReport,
                meta: { dataUrl: 'api/reports/dormitories', pageTitle: 'Dormitories Report', isFormPage: true }
            },
            {
                path: 'reports/transports',
                name: 'reports.transports',
                component: transportReport,
                meta: { dataUrl: 'api/reports/transports', pageTitle: 'Transports Report', isFormPage: true }
            },
            {
                path: 'reports/transports_monthly_invoice_report',
                name: 'reports.transports_monthly_invoice_report',
                component: transportMonthlyInvoiceReport,
                meta: { dataUrl: 'api/reports/transports_monthly_invoice_report', pageTitle: 'Transport Monthly Report', isFormPage: true }
            },
            {
                path: 'reports/canteen_monthly_invoice_report',
                name: 'reports.canteen_monthly_invoice_report',
                component: canteenMonthlyInvoiceReport,
                meta: { dataUrl: 'api/reports/canteen_monthly_invoice_report', pageTitle: 'Canteen Monthly Invoice Report', isFormPage: true }
            },
            {
                path: 'reports/canteen_ledger_report',
                name: 'reports.canteen_ledger_report',
                component: canteenLedgerReport,
                meta: { dataUrl: 'api/reports/canteen_ledger_report', pageTitle: 'Canteen Ledger Report', isFormPage: true }
            },
            {
                path: 'reports/stock_books',
                name: 'reports.stock_books',
                component: bookReport,
                meta: { dataUrl: 'api/reports/stock_books', pageTitle: 'Books Report', isFormPage: true }
            },
            {
                path: 'reports/stocks',
                name: 'reports.stocks',
                component: stockReport,
                meta: { dataUrl: 'api/reports/stocks', pageTitle: 'Stocks Report', isFormPage: true }
            },
            {
                path: 'reports/students',
                name: 'reports.students',
                component: StudentReport,
                meta: { dataUrl: 'api/reports/students', pageTitle: 'Student Details Report', isFormPage: true }
            },
            {
                path: 'reports/invoices',
                name: 'reports.invoices',
                component: invoiceReport,
                meta: { dataUrl: 'api/reports/invoices', pageTitle: 'Invoice Report', isFormPage: true }
            },
            {
                path: 'reports/fees_collections',
                name: 'reports.fees_collections',
                component: feesCollectionReport,
                meta: { dataUrl: 'api/reports/fees_collections', pageTitle: 'Fees Collection Report', isFormPage: true }
            },
            {
                path: 'reports/due_report',
                name: 'reports.due_report',
                component: dueReport,
                meta: { dataUrl: 'api/reports/due_report', pageTitle: 'Due Report', isFormPage: true }
            },
            {
                path: 'reports/monthly_due_report',
                name: 'reports.monthly_due_report',
                component: monthlyDueReport,
                meta: { dataUrl: 'api/reports/monthly_due_report', pageTitle: 'Monthly Due Report', isFormPage: true }
            },
            {
                path: 'reports/ledgers',
                name: 'reports.ledgers',
                component: ledgerReport,
                meta: { dataUrl: 'api/reports/ledgers', pageTitle: 'Ledger Report', isFormPage: true }
            },
            {
                path: 'reports/aging_report',
                name: 'reports.aging_report',
                component: agingReport,
                meta: { dataUrl: 'api/reports/aging_report', pageTitle: 'Aging Report', isFormPage: true }
            },
            {
                path: 'reports/waiver_report',
                name: 'reports.waiver_report',
                component: waiverReport,
                meta: { dataUrl: 'api/reports/waiver_report', pageTitle: 'Waiver Report', isFormPage: true }
            },
            {
                path: 'reports/class_wise_component_collection',
                name: 'reports.class_wise_component_collection',
                component: ClasswiseCollectionReport,
                meta: { dataUrl: 'api/reports/class_wise_component_collection', pageTitle: 'Class Wise Component Collection Report', isFormPage: true }
            },
            {
                path: 'reports/fees_collection_information',
                name: 'reports.fees_collection_information',
                component: feesCollectionInformationReport,
                meta: { dataUrl: 'api/reports/fees_collection_information', pageTitle: 'Fees Collection Report', isFormPage: true }
            },
            {
                path: 'reports/daily_collection',
                name: 'reports.daily_collection',
                component: DailyCollectionReport,
                meta: { dataUrl: 'api/reports/daily_collection', pageTitle: 'Daily Collection Report', isFormPage: true }
            },
            {
                path: 'reports/activity_status',
                name: 'reports.activity_status',
                component: activityStatusReport,
                meta: { dataUrl: 'api/reports/activity_status', pageTitle: 'Activity Status Report', isFormPage: true }
            },
            {
                path: 'reports/class_wise_students',
                name: 'reports.class_wise_students',
                component: classWiseStudentReport,
                meta: { dataUrl: 'api/reports/class_wise_students', pageTitle: 'Class Wise Student Report', isFormPage: true }
            },
            {
                path: 'reports/parents',
                name: 'reports.parents',
                component: parentsReport,
                meta: { dataUrl: 'api/reports/parents', pageTitle: 'Parents Report', isFormPage: true }
            },
            {
                path: 'reports/book_issue',
                name: 'reports.book_issue',
                component: BookIssueReport,
                meta: { dataUrl: 'api/reports/book_issue', pageTitle: 'Book Issue Report', isFormPage: true }
            },

            // ------------website---------

            {
                path: "website",
                name: "website",
                component: websiteForm,
                meta: { dataUrl: "api/website", pageTitle: "Website Setup" },
            },
            {
                path: "our_management",
                name: "our_management",
                component: ourManagement,
                meta: { dataUrl: "api/our_management", pageTitle: "Our Management", },
            },
            {
                path: "faculties",
                name: "faculties",
                component: FacultieList,
                meta: { dataUrl: "api/faculties", pageTitle: "Facultie" },
            },
            {
                path: "slider",
                name: "slider",
                component: sliderList,
                meta: { dataUrl: "api/slider", pageTitle: "Slider" },
            },
            {
                path: "event",
                name: "event",
                component: eventList,
                meta: { dataUrl: "api/event", pageTitle: "Event" },
            },
            {
                path: "video",
                name: "video",
                component: videoList,
                meta: { dataUrl: "api/video", pageTitle: "Video" },
            },
            {
                path: "course_category",
                name: "course_category",
                component: courseCategory,
                meta: { dataUrl: "api/course_category", pageTitle: "Course Category List", },
            },
            {
                path: "website_course",
                name: "website_course",
                component: courseList,
                meta: { dataUrl: "api/website_course", pageTitle: "Course List" },
            },
            {
                path: "website_course/add",
                name: "website_course_add",
                component: addCourse,
                meta: { dataUrl: "api/website_course", pageTitle: "Course Form" },
            },
            {
                path: "website_course/add/:id?",
                name: "website_course_edit",
                component: addCourse,
                meta: { dataUrl: "api/website_course", pageTitle: "Course Edit" },
            },
            {
                path: "department_wise_fees_structure",
                name: "department_wise_fees_structure",
                component: feeStructure,
                meta: { dataUrl: "api/department_wise_fees_structure", pageTitle: "Fee Structure", },
            },
            {
                path: "contact",
                name: "website_contact",
                component: websiteContact,
                meta: { dataUrl: "api/contact", pageTitle: "Website Contact" },
            },
            {
                path: "service",
                name: "website_service",
                component: Service,
                meta: { dataUrl: "api/service", pageTitle: "Website Service" },
            },
            {
                path: "facilities",
                name: "website_facilities",
                component: SchoolFacilities,
                meta: { dataUrl: "api/facilities", pageTitle: "Website Facilities", },
            },
            {
                path: "school_life",
                name: "website_school_life",
                component: SchoolLife,
                meta: {
                    dataUrl: "api/school_life",
                    pageTitle: "Website School Life",
                },
            },
        ],
    },
    { path: "*", redirect: "/admin/404" },
];

export default routes;