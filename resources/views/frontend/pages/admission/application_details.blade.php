<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Form</title>
    <link rel="stylesheet" href="{{asset('website_assets/applicant/style.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"/>
    <style>
        @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
    ul{
        list-style-type: none;
        padding-left: 0 !important;
    }
    p::before {
        content: "• ";
        margin-right: 3px;
    }
    </style>
</head>
<body>
<div class="container">

    <div class="header_part" >
        <div class="header_left">
            <ul>
                <li><small><strong>Name:</strong> {{@$admission_requests->applicant_name_bn}}</small></li>
                <li><small><strong>ID:</strong> {{@$admission_requests->applicant_id}}</small></li>
                @if ($admission_requests->applicant_email)
                    <li><small>{{@$admission_requests->applicant_email}}</small></li>
                @endif
                @if ($admission_requests->applicant_phone)
                    <li><small> {{@$admission_requests->applicant_phone}}</small></li>
                @endif
            </ul>
        </div>

        <div class="header_middle">
            <div>
                <h2>{{$data['school']['title']}}</h2>
                <h4>{{$data['school']['address']}}</h4>
                <h4>emis code : {{$data['school']['emis_code']}}</h4>
            </div>
           
        </div>
        <div class="header_right">
            <img src="{{asset('storage/admission/'.$admission_requests->profile_photo)}}" style="width: 100px; height: 120px; object-fit:cover;border-radius: 4px " alt="Profile Picture">
        </div>
    </div>

    <div class="general_info">
        <hr>
        <h3 class="h3-font-size" style="margin: 5px 0 !important">Applicant Information</h3>
        <div class="col-6">
            <p style="margin: 10px 0 3px !important">Applicant Name : {{@$admission_requests->applicant_name_en}}</p>

        </div>
        
        <div class="col-6">
            <p>Date Of Birth : {{$admission_requests->date_of_birth}}</p>

        </div>
       
        <div class="col-6">
            <p>Gender : {{$admission_requests->gender}}</p>
        </div>
        <div class="col-6">
            <p>Religion : {{$admission_requests->gender}}</p>
        </div>

        <div class="col-6">
            <p>1Blood Group : {{@$admission_requests->blood_group}}</p>

        </div>
        <div class="col-6">
            <p>1Nationality : {{@$admission_requests->nationality}}</p>

        </div>
    </div>
    <div class="academic_info">
        <h3 class="h3-font-size" style="margin: 5px 0 !important">Academic Information</h3>
        <div class="col-6">
            <p>Class : {{@$admission_requests->class_info->name ?? ''}}</p>
        </div>
        <div class="col-6">
            <p>Session : {{@$admission_requests->session->title}}</p>

        </div>
        @if($admission_requests->section !=null)
            <div class="col-6">
                <p>Section : {{@$admission_requests->section->name ?? ''}}</p>
            </div>
        @endif
        @if($admission_requests->department !=null)
            <div class="col-6">
                <p>Department : {{@$admission_requests->department->department_name ?? ''}}</p>

            </div>
        @endif
    </div>
    <div class="general_info">
        <h3 class="h3-font-size" style="margin: 5px 0 !important">Parents Information</h3>
        <div class="col-6">
            <p>Fathers Name : {{@$admission_requests->father_name_en}}</p>

        </div>
        <div class="col-6">
            <p>Father's Phone No : {{@$admission_requests->father_phone}}</p>

        </div>
        <div class="col-6">
            <p>Father's Profession : {{@$admission_requests->father_profession}}</p>

        </div>
        <div class="col-6">
            <p>Mother's Name : {{@$admission_requests->mother_name_en}}</p>

        </div>
        <div class="col-6">
            <p>Mother's Phone No : {{@$admission_requests->mother_phone}} </p>

        </div>
        <div class="col-6">
            <p>Mother's Profession : {{@$admission_requests->mother_profession}}</p>
        </div>
        <div class="col-6">
            <p>Yearly Income : {{@$admission_requests->yearly_income}}</p>
        </div>

    </div>
    <div class="general_info">
        <h3 class="h3-font-size" style="margin: 5px 0 !important">Guardian Information</h3>
        <div class="col-6">
            <p>Name : {{@$admission_requests->guardian->guardian_name}}</p>

        </div>
        <div class="col-6">
            <p>Relation : {{@$admission_requests->guardian->guardian_relation}}</p>

        </div>
        <div class="col-6">
            <p>Phone : {{@$admission_requests->guardian->guardian_phone}}</p>

        </div>
        <div class="col-6">
            <p>Address : {{@$admission_requests->guardian->guardian_address}}</p>

        </div>
    </div>

    <div class="general_info">
        <h3 class="h3-font-size" style="margin: 5px 0 !important">Present Address</h3>
        <div class="col-6">
            <p>Division : {{@$admission_requests->present_address->division_name->name}}</p>

        </div>
        <div class="col-6">
            <p>District : {{@$admission_requests->present_address->distict_name->name}}</p>

        </div>
        <div class="col-6">
            <p>Upazila : {{@$admission_requests->present_address->upazila_name->name}}</p>

        </div>
        <div class="col-6">
            <p>Union : {{@$admission_requests->present_address->union_name->name}}</p>

        </div>
        <div class="col-6">
            <p>Post Office Code : {{@$admission_requests->present_address->post_office}}</p>

        </div>
        <div class="col-6">
            <p>Village : {{@$admission_requests->present_address->village}}</p>
        </div>

    </div>

    <div class="general_info">
        <h3 class="h3-font-size" style="margin: 5px 0 !important">Permanent Address</h3>
        <div class="col-6">
            <p>Division : {{@$admission_requests->permanent_address->division_name->name}}</p>

        </div>
        <div class="col-6">
            <p>District : {{@$admission_requests->permanent_address->distict_name->name}}</p>

        </div>
        <div class="col-6">
            <p>Upazila : {{@$admission_requests->permanent_address->upazila_name->name}}</p>

        </div>
        <div class="col-6">
            <p>Union : {{@$admission_requests->permanent_address->union_name->name}}</p>

        </div>
        <div class="col-6">
            <p>Post Office Code : {{@$admission_requests->permanent_address->post_office}}</p>

        </div>
        <div class="col-6">
            <p>Village : {{@$admission_requests->permanent_address->village}}</p>

        </div>
    </div>

    @if ($admission_requests->quota)
        <div class="general_info" style="margin-bottom: 10px">
            <h3 class="h3-font-size" style="margin: 5px 0 !important">Quota</h3>
            <div class="col-6">
                <p>Quota : {{@$admission_requests->quota}}</p>
            </div>
        </div>
    @endif
   
    <div>
        <h3 class="h3-font-size">Previous Education Details:</h3>

        <table style="width:100%" class="previous-education">
            <tr>
                <th>S.L</th>
                <th>Board Name</th>
                <th>Exam Name</th>
                <th>Reg. No</th>
                <th>Roll No</th>
                <th>Group</th>
                <th>Passing Year</th>
                <th>GPA</th>
            </tr>
            @foreach($admission_requests->previous_result as $key=>$result)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$result->board_name}}</td>
                    <td>{{$result->exam_name}}</td>
                    <td>{{$result->reg_no}}</td>
                    <td>{{$result->roll_no}}</td>
                    <td>{{$result->group}}</td>
                    <td>{{$result->passing_year}}</td>
                    <td>{{$result->gpa}}</td>
                    {{-- <td class="no-print">
                        <a target="_blank" style="color:rgb(82, 82, 82)" href="{{url('api/applicant_previous_result_pdf'.'/'.$result->exam_type.'/'.$result->admission_request_id)}}"><i class="fas fa-file-download" style="font-size: 16px; cursor: pointer"></i></a>
                    </td> --}}
                </tr>
            @endforeach
        </table>
    </div>
    <div class="print" id="printPageButton">

        <button onclick="window.print()">Print</button>
    </div>
</div>


</body>
</html>