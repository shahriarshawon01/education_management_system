<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookIssueReportController extends Controller
{
    public function showReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $bookId = $request->book_id;
        $publisherId = $request->publisher_id;
        $authorId = $request->author_id;
        $editionId = $request->edition_id;
        $bookType = $request->book_type;
        $statusId = $request->status;

        $data = DB::table('stock_books')
            ->leftJoin('schools', 'stock_books.school_id', '=', 'schools.id')
            ->leftJoin('book_accessions', 'stock_books.book_accession_id', '=', 'book_accessions.id')
            ->leftJoin('book_authors', 'book_accessions.author', '=', 'book_authors.id')
            ->leftJoin('book_publishers', 'book_accessions.publisher', '=', 'book_publishers.id')
            ->leftJoin('book_languages', 'book_accessions.language', '=', 'book_languages.id')
            ->leftJoin('book_editions', 'book_accessions.edition', '=', 'book_editions.id')

            ->selectRaw(
                "stock_books.*,
                schools.title as school_name,
                schools.steb_number as school_steb,
                schools.reg_number as registration_no,
                schools.institute_code as institute_code,
                schools.address as school_address,

                book_accessions.book_title as book_name,
                book_accessions.book_type as book_type,

                book_authors.name as author_name,
                book_publishers.name as publisher_name,
                book_languages.name as book_language,
                book_editions.name as book_edition",
            )
            ->where('stock_books.school_id', $schoolId)
            ->when($bookId, function ($query) use ($bookId) {
                $query->where('stock_books.book_accession_id', $bookId);
            })
            ->when($publisherId, function ($query) use ($publisherId) {
                $query->where('book_accessions.publisher', $publisherId);
            })
            ->when($authorId, function ($query) use ($authorId) {
                $query->where('book_accessions.author', $authorId);
            })
            ->when($editionId, function ($query) use ($editionId) {
                $query->where('book_accessions.edition', $editionId);
            })
            ->when($bookType, function ($query) use ($bookType) {
                $query->where('book_accessions.book_type', $bookType);
            })
            ->when(isset($statusId), function ($query) use ($statusId) {
                $query->where('stock_books.status', $statusId);
            })
            ->get();

        // if ($data->isEmpty()) {
        //     return returnData(4004, null, 'No records found for the specified criteria.');
        // }
        $single = $data->first();

        $responseData = [
            'data' => $data,
            'school_name' => $single ? $single->school_name : '',
            'registration_no' => $single ? $single->registration_no : '',
            'school_steb' => $single ? $single->school_steb : '',
            'school_address' => $single ? $single->school_address : '',
        ];

        return returnData(2000, $responseData,);
    }
}
