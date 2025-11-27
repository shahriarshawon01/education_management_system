import pageNotFound from "../views/pageNotFound";
import dashboardPage from "../student_views/dashboardComponent";
import teacherList from "../student_views/teacherList";
import noticeList from "../student_views/noticeList";
import subjectList from "../student_views/subjectList";
import routinList from "../student_views/routinList";
import resultList from "../student_views/resultList";
import examRoutine from "../student_views/studentExamRoutine";
import paymentsList from "../student_views/paymentsList";
import canteenMeal from "../student_views/canteenMeal";
import libraryList from "../student_views/libraryList";
import studentProfile from "../student_views/studentProfile";
import studentBaseLayout from "../student_views/layouts/studentBaseLayout";

const student_routes = [{
        path: '/student/',
        component: studentBaseLayout,
        children: [
            { path: '404', name: '404', component: pageNotFound },
            {
                path: 'dashboard',
                name: 'dashboard',
                component: dashboardPage,
                meta: { dataUrl: 'dashboard', pageTitle: 'Student Dashboard' }
            },
            {
                path: 'teachers',
                name: 'teachers',
                component: teacherList,
                meta: { dataUrl: 'teachers', pageTitle: 'Teachers' }
            },
            {
                path: 'notice',
                name: 'notice',
                component: noticeList,
                meta: { dataUrl: 'notice', pageTitle: 'Notice' }
            },
            {
                path: 'subjects',
                name: 'subjects',
                component: subjectList,
                meta: { dataUrl: 'subjects', pageTitle: 'Subjects' }
            },
            {
                path: 'library',
                name: 'library',
                component: libraryList,
                meta: { dataUrl: 'library', pageTitle: 'Library' }
            },
            {
                path: 'routine',
                name: 'routine',
                component: routinList,
                meta: { dataUrl: 'routine', pageTitle: 'Routine' }
            },
            {
                path: 'exam_routine',
                name: 'exam_routine',
                component: examRoutine,
                meta: { dataUrl: 'exam_routine', pageTitle: 'Exam Routine' }
            },
            {
                path: 'result',
                name: 'result',
                component: resultList,
                meta: { dataUrl: 'result', pageTitle: 'Exam Result' }
            },
            {
                path: 'payments',
                name: 'payments',
                component: paymentsList,
                meta: { dataUrl: 'payments', pageTitle: 'Payments' }
            },
            {
                path: 'canteen_meal',
                name: 'canteen_meal',
                component: canteenMeal,
                meta: { dataUrl: 'canteen_meal', pageTitle: 'Canteen Meal' }
            },
            {
                path: 'profile',
                name: 'profile',
                component: studentProfile,
                meta: { dataUrl: 'profile', pageTitle: 'Profile' }
            },
        ]
    },
    { path: '*', redirect: '/admin/404' },

];

export default student_routes;