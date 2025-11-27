<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<table>
    <tr>
        <th>Sl</th>
        <th>Teacher Name</th>
        <th>Teacher ID</th>
        <th>Designation</th>
        <th>Department</th>
        <th>Mobile</th>
        <th>E-mail	</th>
        <th>Gender/th>
        <th>Job Status	</th>
        <th>Comments</th>
    </tr>
    @php
        $serialNumber = 1;
    @endphp
    @foreach($data as $d)
        <tr>
            <td>{{ $serialNumber++ }}</td>
            <td>{{ $d->name }}</td>
            <td>{{ $d->student_name_en}}</td>
            <td>{{ $d->designation_name }}</td>
            <td>{{ $d->department_name }}</td>
            <td>{{ $d->phone }}</td>
            <td>{{ $d->email }}</td>
            <td>{{ $d->gender }}</td>
            <td>{{ $d->job_comments }}</td>
        </tr>
    @endforeach
</table>

</body>
</html>
