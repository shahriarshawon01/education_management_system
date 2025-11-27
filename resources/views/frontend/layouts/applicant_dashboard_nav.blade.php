<div class="col-sm-3">
    <ul class="sidebar-link border px-3 pt-2 pb-2">
        <li class="pb-1"><a href="{{url('applicant_dashboard')}}" class="{{Request::is('applicant_dashboard') ? 'active' : ''}}">Applicant Details</a></li>
        <li class="pb-1"><a href="{{url('approved_application')}}" class="{{Request::is('approved_application') ? 'active' : ''}}">Admit Card</a></li>
        <li class="pb-1"><a href="{{url('exam_result')}}" class="{{Request::is('exam_result') ? 'active' : ''}}">Admission Result</a></li>
        <li class="pb-1"><a href="{{url('applicant_logout')}}">Logout</a></li>
    </ul>
</div>