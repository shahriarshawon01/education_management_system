<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<table>
    <tr>
        <th>Sl</th>
        <th>Applicant id</th>
        <th>Applicant Name</th>
        <th>Class</th>
        <th>section</th>
        <th>Department</th>
        <th>Session</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Religion</th>
        <th>Date of Birth</th>
        <th>Father Name</th>
        <th>Mother Name</th>
        <th>Guardian Number</th>
        <th>Application Date</th>
        <th>Quota</th>
        <th>Division</th>
        <th>District</th>
        <th>Upazila</th>
        <th>Union</th>
        <th>Post Office Code</th>
        <th>Village</th>
    </tr>
    @php
        $serialNumber = 1;
    @endphp
    @foreach($data as $d)
        <tr>
            <td>{{ $serialNumber++ }}</td>
            <td>{{ $d->applicant_id ?? '' }}</td>
            <td>{{ $d->applicant_name_en ?? '' }}</td>
            <td>{{ $d->class_info->name ?? '' }}</td>
            <td>{{ $d->section->name ?? '' }}</td>
            <td>{{ $d->department->department_name ?? '' }}</td>
            <td>{{ $d->session->title ?? '' }}</td>
            <td>{{ $d->applicant_phone ?? '' }}</td>
            <td>{{ $d->applicant_email ?? '' }}</td>
            <td>{{ $d->gender ?? '' }}</td>
            <td>{{ $d->religion ?? '' }}</td>
            <td>{{ $d->date_of_birth ?? '' }}</td>
            
            <td>{{ $d->father_name_en ?? '' }}</td>
            <td>{{ $d->mother_name_en ?? '' }}</td>
            <td>{{ $d->guardian->guardian_phone ?? '' }}</td>
            <td>{{ isset($d->created_at) ? \Carbon\Carbon::parse($d->created_at)->format('j D, Y') : '' }}</td>
            <td>{{ $d->quota ?? '' }}</td>
            <td>{{ $d->present_address->division_name->name ?? '' }}</td>
            <td>{{ $d->present_address->distict_name->name ?? '' }}</td>
            <td>{{ $d->present_address->upazila_name->name ?? '' }}</td>
            <td>{{ $d->present_address->union_name->name ?? '' }}</td>
            <td>{{ $d->present_address->post_office ?? '' }}</td>
            <td>{{ $d->present_address->village ?? '' }}</td>
        </tr>
    @endforeach
</table>
<br/>
<br/>

{{-- <table>
    <tr>
        <th>Sl</th>
        <th>Applicant id</th>
        <th>Applicant Name</th>
        <th>Class</th>
        <th>group</th>
        <th>Session</th>
        <th>Board</th>
        <th>Exam Type</th>
        <th>institute Name</th>
        <th>GPA/Number</th>
    </tr>
    @php
        $serialNumber = 1;
    @endphp
    @foreach($data as $d)
        <tr>
            <td>{{ $serialNumber++ }}</td>
            <td>{{ $d->applicant_id ?? '' }}</td>
            <td>{{ $d->name ?? '' }}</td>
            <td>{{ $d->class_info->name ?? '' }}</td>
            <td>{{ $d->group->name ?? '' }}</td>
            <td>{{ $d->session->session_title ?? '' }}</td>
            @foreach ( $d->admission_requests_previous_results as $prev_result)
                <td>{{ $prev_result->board ?? '' }}</td>
                <td>{{ $prev_result->exam_type ?? '' }}</td>
                <td>{{ $prev_result->institute_name ?? '' }}</td>
                <td>{{ $prev_result->gpa ?? '' }}</td>
            @endforeach
        </tr>
    @endforeach
</table> --}}


</body>
</html>