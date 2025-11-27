@extends('frontend.layouts.master')

@section('title','Live Class Schedule')

@push('css')
<style> 
  body { margin: 0; padding: 0; z-index: 1; } ::-webkit-scrollbar { width: 10px; background-color: #fff; border-radius: 5px; } ::-webkit-scrollbar-thumb { background: linear-gradient(transparent, #4A8BDF); border-radius: 5px; } ::-webkit-scrollbar-thumb:hover { background: linear-gradient(transparent, #4A8BDF); } .useful-link li { width: 100% !important; } .toolbar-share-icon-active { border-bottom: 2px solid #FFFFFF; } 
  .live_class_schedule table tr th, td{
    padding: 8px !important;
    line-height: 1.42857143 !important;
    text-align: left !important;
    font-size: 14px !important;
  }
  </style>
<link rel="stylesheet" type=text/css href="front/css/timeline.css">
<style> .page-header { background-image: url(front/images/bg/page-header-bg.jpg); background-size: cover; background-position: center; margin: 0px; padding: 0px; border: none; } .section-notch:before { content: ""; position: absolute; background-repeat: repeat-x; display: block; top: 0; width: 100%; height: 7px; z-index: 2; } .page-header .overlay { padding: 25px 0px; background-color: rgba(125, 172, 230, 0.8); text-align: center; } .documents-title { font-size: 22px; text-decoration: underline; color: #4A8BDF; font-weight: 600; margin-bottom: 4px; } .contact-address p { font-size: 13px; } </style>
@endpush

@section('content')

<section class="page-header section-notch">
  <div class="container">
  <div class="overlay mt-2" style="padding: 15px 0px !important">
       <div class="container">
          <div class="col-md-12 text-center">
             <h1 class="page-title m-0">Live Class Schedule</h1>
             <ul>
                <li><a class="active" href="{{url('/')}}">Home</a> / <span style="color: #FFFFFF!important;">Live Class Schedule</span></li>
             </ul>
          </div>
       </div>
    </div>
  </div>

       <!-- <div class="container">
          <div class="col-md-12 text-center">
             <h1 class="page-title">Live Class Schedule</h1>
             <ul>
                <li><a class="active" href="{{url('/')}}">Home</a> / <span style="color: #FFFFFF!important;">Live Class Schedule</span></li>
             </ul>
          </div>
       </div> -->
    </div>
 </section>

 <div id="rs-about" class="live_class_schedule rs-about sec-spacer about-us" style="padding-top: 80px">
    <div class="container">
        <div class="row">
             <div class="col-12">
                <div class="about-desc">
                    <div class="card">
                        <div class="card-header" style="background-color: #4A8BDF; color: #fff">
                            Live Class Schedule
                          </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>SL</th>
                                    <th>Topic</th> 
                                    <th>Teacher</th>
                                    <th>Class Name</th>
                                    <th>Department</th>
                                    <th>Session</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                  </tr>
                                </thead>
                                <tbody>
                                    @forelse ($live_class_schedules as $key => $schedule)
                                      <tr>
                                        <td>{{$loop->iteration }}</td>
                                        <td>{{$schedule->topic ?? ''}}</td>
                                        <td>{{$schedule->teacher_name ?? ''}}</td>
                                        <td>{{$schedule->class_name ?? ''}}</td>
                                        <td>{{$schedule->department_name ?? ''}}</td>
                                        <td>{{$schedule->session_name ?? ''}}</td>
                                        <td>{{$schedule->start_time ?? ''}}</td>
                                        <td>{{$schedule->end_time ?? ''}}</td>
                                        <td>{{$schedule->duration ?? ''}}</td>
                                        <td>
                                            @if ($schedule->status == 1)
                                                <span>CLOSED</span>
                                            @else
                                               <span>PENDING</span>
                                            @endif
                                        </td>
                                      </tr>
                                    @empty
                                      <tr>
                                        <td>No Live Class Schedule Yet</td>
                                      </tr>
                                    @endforelse
                                  
                                </tbody>
                            </table>
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