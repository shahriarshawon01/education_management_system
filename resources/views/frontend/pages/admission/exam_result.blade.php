@extends('frontend.layouts.master')

@section('title','Contact')

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
                        <div class="blog-left-area pt-60 pb-30">
                            <div class="card">
                                <div class="card-body">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-12" id="all_application">
                                                <div class="single-blog mb-30">
                                                    <div class="blog-text">
                                                        <h4 class="text-center text-dark mb-3">Admission Test Results</h4>
                                                        <table class="table table-responsive table-striped table-bordered">
                                                            <tbody>
                                                            @forelse($data_info as $key=>$data)
                                                                <tr>
                                                                <td><b>Applicant ID</b></td><td>{{$data->applicant_id}}</td>
                                                                <td><b>Applicant Name</b></td><td>{{$data->name}}</td>
                                                                </tr>
                                                                <td><b>Class</b></td><td>{{$data->class_info->name}}</td>
                                                                <td><b>Session</b></td><td>{{$data->session->session_title}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Group</b></td><td>{{$data->group->name}}</td>
                                                                    <td><b>Phone No.</b></td><td>{{$data->phone_no}}</td>
                                                                </tr>
                                                                <tr>
                                                                <td><b>Date of Birth</b></td><td>{{$data->birthday}}</td>
                                                                <td><b>Gender</b></td><td>{{$data->gender}}</td>
                                                                </tr>
                                                                <tr>
                            
                                                                <tr>
                                                                <td><b>Religion</b></td><td>{{$data->religion}}</td>
                                                                <td><b>Blood.</b></td><td>{{$data->blood_group}}</td>
                                                                </tr>
                                                                <tr>
                                                                <td><b>Total Marks</b></td><td><span class="badge text-bg-secondary" style="font-size: 12.5px; font-weight: 500">{{ $data->mark }}</span></td>
                                                                <td><b>Remarks</b></td>
                                                                <td
                                                                    @if ($data->mark > 33)
                                                                        <span class="badge text-bg-success" style="font-size: 11.5px !important; text-transform: uppercase; vertical-align: -webkit-baseline-middle; !important;margin-top: 5px;
                                                                        margin-left: 3px; box-shadow:0 5px 14px rgba(0,0,0,0.1)">Pass</span>
                                                                    @elseif ($data->mark === 33)
                                                                    <span class="badge text-bg-success" style="font-size: 11.5px !important; text-transform: uppercase;vertical-align: -webkit-baseline-middle; !important;margin-top: 5px;
                                                                    margin-left: 3px">Pass</span>
                                                                    @else
                                                                    <span class="badge text-bg-danger" style="font-size: 11.5px !important; text-transform: uppercase;vertical-align: -webkit-baseline-middle; !important;margin-top: 5px;
                                                                    margin-left: 3px">Fail</span>
                                                                    @endif
                                                                </td>
                                                                </tr>
                                                             @empty
                                                                <h5>Result not found !!</h5>
                                                            @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="">
                                                    <h4 class="text-center text-dark mb-3">Subjectwise Marks</h4>
                                                    <table class="table table-striped table-bordered" width="100%">
                                                        <thead>
                                                        <tr>
                                                        <th class="text-center">SL</th>
                                                        <th class="text-center">Subject Name</th>
                                                        <th class="text-center">Subject Marks</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                            $totalMarks = 0;
                                                            @endphp
                                                            @forelse ($data_info as $key => $subjectMark)
                                                                @php
                                                                $totalMarks += $subjectMark->subjectwise->sum('subject_marks'); 
                                                                @endphp
                                                                @foreach ($subjectMark->subjectwise as $ikey => $subject)
                                                                    <tr>
                                                                        <td align="center">{{$ikey + 1}}</td>
                                                                        <td align="center">{{$subject->subjectname->subject_name ?? ''}}</td>
                                                                        <td align="center">{{$subject->subject_marks ?? '0'}}</td>
                                                                    </tr>
                                                                @endforeach
                                                                @empty
                                                                <h4>Mark Not Found</h4>
                                                            @endforelse
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-center"><span style="font-weight: 500">Total Marks : <span class="badge text-bg-light" style="font-size: small">{{ $totalMarks }}</span> </span></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush





