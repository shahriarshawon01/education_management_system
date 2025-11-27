<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Payment Details</title>
    <link rel="stylesheet" href="{{asset('website_assets/applicant/style.css')}}">
</head>
<style>
    p{
        font-size: 14px !important;
    }
     p::before {
        content: "• ";
        margin-right: 3px;
    }
    .previous-education tr th, td{
        line-height: 22px;
        font-size: 15px !important;
    } 
</style>
<body>
<div class="container">

    <div class="header_part" style="display: flex; justify-content: space-between; margin-bottom: 10px ">
        <div class="header_left">
           
        </div>

        <div class="header_middle">

            <h2>{{$data['school']['title']}}</h2>
            <h4>{{$data['school']['address']}}</h4>
            <h4>Emis Code : {{$data['school']['emis_code']}}</h4>
        </div>
        <div class="header_right d-block mt-5">
            <img src="{{asset('storage/admission/'.$admission_requests->profile_photo)}}" style="width: 100px; height: 120px; object-fit:cover;border-radius: 4px " alt="Profile Picture">
           
        </div>
    </div>
    <hr>
    <div class="general_info" style="margin: 5px 0 15px !important";>
        
        <h3 class="h3-font-size" style="margin: 0 0 10px 0 !important">Applicant Information:</h3>
        <div class="col-6">
            <p><strong>Applicant Id</strong> : {{@$admission_requests['applicant_id']}}</p>
        </div>
        <div class="col-6">
            <p><strong>Full Name</strong> : {{@$admission_requests['applicant_name_en']}}</p>
        </div>
        @if($admission_requests->applicant_phone)
            <div class="col-6">
                <p><strong>Phone</strong> : {{@$admission_requests['applicant_phone']}}</p>
            </div>
        @endif
        @if($admission_requests->applicant_email)
            <div class="col-6">
                <p><strong>Email</strong> : {{@$admission_requests['applicant_email']}}</p>
            </div>
        @endif
        <div class="col-6">
            <p><strong>Class</strong> : {{@$admission_requests['class_info']['name'] ?? ''}}</p>
        </div>
        <div class="col-6">
            <p><strong>Session</strong> : {{@$admission_requests['session']['title'] ?? ''}}</p>
        </div>
        @if($admission_requests->section)
            <div class="col-6">
                <p> <strong>Section</strong> : {{@$admission_requests['section']['name'] ?? ''}}</p>
            </div>
        @endif
        @if($admission_requests->department)
            <div class="col-6">
                <p> <strong>Department</strong> : {{@$admission_requests['department']['department_name'] ?? ''}}</p>
            </div>
        @endif
        
    </div>

    <div>
        <h3 class="h3-font-size">Payment Details:</h3>
        <table style="width:100%" class="previous-education">
            <tr style="background-color: #ebebeb">
                <th>S.L</th>
                <th>Fees Type</th>
                <th>Fees Amount</th>
            </tr>
            @php
                $totalAmount = 0;
            @endphp

            @foreach($admission_requests->payments as $key => $result)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$result->fees_type->name}}</td>
                    <td>{{$result->total_amount}}</td>
                </tr>
                @php
                    $totalAmount += $result->total_amount;
                @endphp
            @endforeach

            <tr style="background-color: rgb(255, 255, 255)">
                <td colspan="2" style="text-align: center"><strong>Total Amount</strong></td>
                <td><strong>{{$totalAmount}}</strong></td>
            </tr>
        </table>
    </div>
    <div class="print" id="printPageButton" style="padding-top: 15px">
        <button onclick="window.print()">Print</button>
    </div>
</div>


</body>
</html>