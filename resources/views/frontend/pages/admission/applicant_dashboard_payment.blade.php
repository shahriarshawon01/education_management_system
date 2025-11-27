@extends('frontend.layouts.master')

@section('title','Applicant Payment')

@push('css')
   
    <style> body { margin: 0; padding: 0; z-index: 1; } ::-webkit-scrollbar { width: 10px; background-color: #fff; border-radius: 5px; } ::-webkit-scrollbar-thumb { background: linear-gradient(transparent, #4A8BDF); border-radius: 5px; } ::-webkit-scrollbar-thumb:hover { background: linear-gradient(transparent, #4A8BDF); } .useful-link li { width: 100% !important; } .toolbar-share-icon-active { border-bottom: 2px solid #FFFFFF; } </style>
    <style> .page-header { background-image: url(front/images/bg/page-header-bg.jpg); background-size: cover; background-position: center; margin: 0px; padding: 0px; border: none; } .section-notch:before { content: ""; position: absolute; background-repeat: repeat-x; display: block; top: 0; width: 100%; height: 7px; z-index: 2; } .page-header .overlay { padding: 25px 0px; background-color: rgba(125, 172, 230, 0.8); text-align: center; } .notice-section { background-image: url(https://diu.ac/front/images/bg/dot-grid.png) !important; background-attachment: fixed; background-position: center top; padding: 70px 0 50px !important}
        .pagination .page-item > * {
            width: 35px !important;
            height: 35px !important;
            line-height: 35px !important;
            font-size: 16px !important;
            color: #4A8BDF !important;
        }
    
        .active{
            color: #1a22ac !important;
        }
        p{
            font-size: 14px !important;
            margin: 0 0 7px 0 !important;
        }
        p::before {
            content: "• ";
            margin-right: 3px;
        }
        .previous-education tr th, td{
            line-height: 22px !important;
            font-size: 15px !important;
        } 
        h2, h3, h4{
            margin: 0 0 10px 0 !important;
        }

    </style>
@endpush

@section('content')
    <section class="page-header section-notch">
        <div class="overlay">
            <div class="container">
                <div class="col-md-12 text-center">
                    <h1 class="page-title">Welcome to Dashboard</h1>
                    <ul>
                        <li><a class="active" href="{{url('/')}}">Home</a> / <span style="color: #FFFFFF!important;"> Dashboard</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <div id="latest_news" class="latest_news notice-section">
        <div class="container">
            <div class="row">
                @include('frontend.layouts.applicant_dashboard_nav')
                <div class="col-sm-9">
                    <div id="all_application">
                        <div class="container" id="applicantDashboardPayment">

                            <div class="header_part" style="display: flex; justify-content: space-between; margin-bottom: 10px ">
                                <div class="header_left">
                                
                                </div>
                        
                                <div class="header_middle">
                        
                                    <h2>{{$data['school']['title']}}</h2>
                                    <h4>{{$data['school']['address']}}</h4>
                                    <h4>Emis Code : {{$data['school']['emis_code']}}</h4>
                                </div>
                                <div class="header_right d-block mt-5">
                                    @if ($admission_requests && $admission_requests->profile_photo )
                                       <img src="{{asset('storage/admission/'.$admission_requests->profile_photo)}}" style="width: 100px; height: 120px; object-fit:cover;border-radius: 4px " alt="Profile Picture">
                        
                                    @endif
                                    
                                </div>
                            </div>
                            <hr>
                            <div class="general_info" style="margin: 5px 0 15px !important";>
                                <h3 class="h3-font-size" style="margin: 0 0 10px 0 !important">Applicant Information:</h3>
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>Applicant Id</strong> : {{@$admission_requests['applicant_id']}}</p>
                                    </div>
                                    <div class="col-6">
                                        <p><strong>Full Name</strong> : {{@$admission_requests['applicant_name_en']}}</p>
                                    </div>
                                    @if($admission_requests && $admission_requests->applicant_phone)
                                        <div class="col-6">
                                            <p><strong>Phone</strong> : {{@$admission_requests['applicant_phone']}}</p>
                                        </div>
                                    @endif
                                    @if($admission_requests && $admission_requests->applicant_email)
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
                                    @if($admission_requests && $admission_requests->section)
                                        <div class="col-6">
                                            <p> <strong>Section</strong> : {{@$admission_requests['section']['name'] ?? ''}}</p>
                                        </div>
                                    @endif
                                    @if($admission_requests && $admission_requests->department)
                                        <div class="col-6">
                                            <p> <strong>Department</strong> : {{@$admission_requests['department']['department_name'] ?? ''}}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        
                            <div>
                                <h3 class="h3-font-size">Payment Details:</h3>
                                <table style="width:100%" class="previous-education table-bordered">
                                    <tr style="background-color: #ebebeb">
                                        <th>S.L</th>
                                        <th>Fees Type</th>
                                        <th>Fees Amount</th>
                                    </tr>
                                
                                    @php
                                        $totalAmount = 0;
                                    @endphp
                                
                                    @if($admission_requests && $admission_requests->payments->isNotEmpty())
                                        @foreach($admission_requests->payments as $key => $result)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $result->fees_type->name }}</td>
                                                <td>{{ $result->total_amount }}</td>
                                            </tr>
                                            @php
                                                $totalAmount += $result->total_amount;
                                            @endphp
                                        @endforeach
                                
                                        <tr style="background-color: rgb(255, 255, 255)">
                                            <td colspan="2" style="text-align: center"><strong>Total Amount</strong></td>
                                            <td><strong>{{ $totalAmount }}</strong></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="3" style="text-align: center">No payment records found</td>
                                        </tr>
                                    @endif
                                </table>
                                
                            </div>
                            <div class="print" id="printPageButton" style="padding-top: 15px">
                                <button onclick="printDiv('applicantDashboardPayment')">Print</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    function printDiv(divId) {
        var divContents = document.getElementById(divId).innerHTML;
        var a = window.open('', '', 'height=auto, width=600');
        a.document.write('<html>');
        a.document.write('<head><title>Print</title>');

        a.document.write('<link rel="stylesheet" type="text/css" href="{{asset('website_assets/applicant/style.css')}}">'); // Change 'app.css' to your CSS file if needed

        a.document.write('</head>');
        a.document.write('<body>');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();
        
        a.onload = function() {
            a.focus(); 
            a.print();
            a.close();
        };
    }
</script>

@endpush