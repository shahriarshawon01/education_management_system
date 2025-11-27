<?php

namespace App\Http\Controllers\Website;

use App\Models\School;
use App\Models\Service;
use App\Models\Student;
use App\Models\Facility;
use App\Models\LiveClass;
use App\Models\SchoolLife;
use App\Models\NoticeBoard;
use Illuminate\Http\Request;
use App\Models\Website\Event;
use App\Models\Website\Video;
use App\Models\Website\Slider;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Website\WebsiteSetup;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\Website\WebsiteContact;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index(){
        $data['sliders'] = Slider::where('status',1)->orderBy('id','desc')->take(5)->get();
        $data['notices'] = NoticeBoard::where('status',1)->take(5)->orderBy('id','desc')->get();
        $data['events'] = Event::where('status',1)->take(5)->orderBy('id','desc')->get();
        $data['video'] = Video::select('video_link')->where('show_on',1)->where('status',1)->first();
        $data['total_student'] = Student::count();
        $data['current_student'] = Student::where('status',1)->count();
        $data['teachers'] = Employee::where('status',1)->where('employee_type',1)->count();
        $data['staffs'] = Employee::where('status',1)->where('employee_type',1)->count();
        $data['about_us'] = WebsiteSetup::where('key','about_us')->first();
        $data['mission_vission'] = WebsiteSetup::where('key','mission_vision')->first();
        $data['founder_info'] = School::select('founder_info')->first();
        $data['services'] = Service::where('status',1)->latest()->take(3)->get();
        $data['facilities'] = Facility::where('status',1)->latest()->take(6)->get();
        $data['school_lifes'] = SchoolLife::where('status',1)->latest()->get();
        return view('frontend.pages.home',$data);
    }
    public function noticeboard(){
        $data['notices'] = Noticeboard::where('status', '1')->latest()->paginate(20);
        $data['events'] = Event::where('status', '1')->latest()->paginate(20);
        return view( 'frontend.pages.notice_board', $data);
    }
    public function notice_details($id){
        $notice_details = Noticeboard::where('id', $id)->where('status',1)->first();
        if($notice_details){
            return view('frontend.pages.notice_details', ['notice_details' => $notice_details]);
        }else{
            Toastr::error('Info', 'Notice not found');
            return redirect()->back();
        }
        
    }
    public function event_details($slug)
    { 
        $eventdetails = Event::where('slug', $slug)->where('status',1)->first();
        if($eventdetails){
            return view('frontend.pages.event_details', ['eventdetails' => $eventdetails]);
        }else{
            Toastr::error('Info', 'Event not found');
            return redirect()->back();
        }
    
    }
    public function contact()
    {
        $contact = School::where('status', 1)->first();
        return view('frontend.pages.contact', ['contact' => $contact]);
    }
    public function contact_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|unique:website_contacts',
            'phone' => 'required|min:11|unique:website_contacts',
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $contact = new WebsiteContact();
        $contact->school_id = Auth::user()->school_id;
        $contact->first_name = $request->first_name;
        $contact->last_name = $request->last_name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
        $contact->save();
        Toastr::success('Success', 'Thank You for Contacting us. We will contact you soon.');
        return redirect()->back()->with('success', 'Contact info added Successfully!');
    }

    public function about_institute(){
        $data['about_us'] = WebsiteSetup::where('key','about_us')->first();
        $data['video'] = Video::select('video_link')->where('show_on',1)->first();
        return view('frontend.pages.institute.about_institute',$data);
    }
    public function institute_history(){
        $data['history'] = WebsiteSetup::where('key','history')->first();
        return view('frontend.pages.institute.history',$data);
    }
    public function mission_vision(){
        $data['mission_vision'] = WebsiteSetup::where('key','mission_vision')->first();
        return view('frontend.pages.institute.mission_vision',$data);
    }
    public function ed_message(){
        $data['ed_message'] = WebsiteSetup::where('key','ed_message')->first();
        return view('frontend.pages.institute.ed_message',$data);
    }
    public function chairman_message(){
        $data['chairman_message'] = WebsiteSetup::where('key','chairman_message')->first();
        return view('frontend.pages.institute.chairman_message',$data);
    }
    public function principal_message(){
        $data['principal_message'] = WebsiteSetup::where('key','principle_message')->first();
        return view('frontend.pages.institute.principal_message',$data);
    } 
    public function vice_principal_message(){
        $data['vice_principal_message'] = WebsiteSetup::where('key','vice_principle_message')->first();
        return view('frontend.pages.institute.vice_principal_message',$data);
    } 
    public function md_message(){
        $data['md_message'] = WebsiteSetup::where('key','md_message')->first();
        return view('frontend.pages.institute.md_message',$data);
    }
    public function assistant_professor_message(){
        $data['assistant_professor_message'] = WebsiteSetup::where('key','a_p_message')->first();
        return view('frontend.pages.institute.assistant_professor_message',$data);
    }
    public function live_class_schedule(){
        $data['live_class_schedules'] = LiveClass::leftJoin('student_classes','live_classes.class_id','student_classes.id')
        ->leftJoin('session_years','live_classes.session_year_id','session_years.id')
        ->leftJoin('departments','live_classes.department_id','departments.id')
        ->leftJoin('employees','live_classes.teacher_id','teachers.id')
        ->select('live_classes.*','student_classes.name as class_name','session_years.title as session_name','departments.department_name','employees.name as teacher_name')
        ->where('live_classes.status', 1)->get();
        return view('frontend.pages.live_class_schedule',$data);
    }
    
}


