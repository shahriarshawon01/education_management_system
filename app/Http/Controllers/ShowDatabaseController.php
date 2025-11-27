<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowDatabaseController extends Controller
{
    // public function showTables()
    // {
    //     $databaseName = DB::getDatabaseName();

    //     $tables = DB::select('SHOW TABLES');
    //     $tables = array_map('current', $tables);

    //     $databaseSchema = [];

    //     foreach ($tables as $table) {
    //         $columns = DB::select("SHOW COLUMNS FROM {$table}");
    //         $columns = array_map(fn($column) => $column->Field, $columns);

    //         $databaseSchema[$table] = $columns;
    //     }

    //     return view('database.tables', compact('databaseSchema'));
    // }

    public function showTables()
    {
        // Fetch all table names
        $tables = DB::select('SHOW TABLES');
        $tables = array_map('current', $tables);

        // Return a view with the table names only
        return view('database.tables', compact('tables'));
    }
}
