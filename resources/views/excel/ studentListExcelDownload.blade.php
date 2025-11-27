<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<table>
    <tr>
        <th>Sl</th>
        <th>Student id</th>
        <th>Student Name</th>
        <th>Class</th>
        <th>group</th>
        <th>Session</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Blood Group</th>
        <th>Religion</th>
        <th>Date of Birth</th>
        <th>Address</th>
        <th>Father Name</th>
        <th>Mother Name</th>
        <th>Emergency Number</th>
        <th>Admission Date</th>
    </tr>
    @php
        $serialNumber = 1;
    @endphp
    @foreach($data as $d)
        <tr>
            <td>{{ $serialNumber++ }}</td>
            <td>{{ $d->student_id ?? '' }}</td>
            <td>{{ $d->user->name ?? '' }}</td>
            <td>{{ $d->class->name ?? '' }}</td>
            <td>{{ $d->group->name ?? '' }}</td>
            <td>{{ $d->session->session_title ?? '' }}</td>
            <td>{{ $d->student->phone_no ?? '' }}</td>
            <td>{{ $d->user->email ?? '' }}</td>
            <td>{{ $d->student->gender ?? '' }}</td>
            <td>{{ $d->student->blood_group ?? '' }}</td>
            <td>{{ $d->student->religion ?? '' }}</td>
            <td>{{ $d->student->birthday ?? '' }}</td>
            <td>{{ $d->student->address ?? '' }}</td>
            <td>{{ $d->student->father_name ?? '' }}</td>
            <td>{{ $d->student->mother_name ?? '' }}</td>
            <td>{{ $d->student->emergency_phone ?? '' }}</td>
            <td>{{ isset($d->student->admission_date) ? \Carbon\Carbon::parse($d->student->admission_date)->format('j D, Y') : '' }}</td>
        </tr>
    @endforeach
</table>


</body>
</html>