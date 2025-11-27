{{-- show table with column name --}}
{{-- <!DOCTYPE html>
<html>
<head>
    <title>Database Tables and Columns</title>
</head>
<body>
    <h1>Database Tables and Columns</h1>
    <ul>
        @foreach ($databaseSchema as $table => $columns)
            <li>
                <strong>{{ $table }}</strong>
                <ul>
                    @foreach ($columns as $column)
                        <li>{{ $column }}</li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</body>
</html> --}}

{{-- just table name --}}
<!DOCTYPE html>
<html>

<head>
    <title>Database Tables</title>
</head>

<body>
    <h1>Tables in Database</h1>

    <ul>
        @foreach ($tables as $table)
            <li>{{ $table }}</li>
        @endforeach
    </ul>
</body>

</html>
