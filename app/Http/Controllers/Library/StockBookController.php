<?php

namespace App\Http\Controllers\Library;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Library\BookAccession;
use App\Models\Library\StockBooks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockBookController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new StockBooks();
    }

    public function index()
    {
        if (!can('stock_books_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model
                ->leftJoin('book_accessions', 'stock_books.book_accession_id', '=', 'book_accessions.id')
                ->leftJoin('book_authors', 'book_accessions.author', '=', 'book_authors.id')
                ->leftJoin('book_editions', 'book_accessions.edition', '=', 'book_editions.id')
                ->leftJoin('book_languages', 'book_accessions.language', '=', 'book_languages.id')
                ->leftJoin('book_publishers', 'book_accessions.publisher', '=', 'book_publishers.id')
                ->select('stock_books.*', 'book_accessions.book_title as book_name', 'book_accessions.isbn','book_authors.name as author_name','book_editions.name as books_edition','book_languages.name as books_language','book_publishers.name as books_publisher')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function($q) use ($keyword){
                        $q->where('book_accessions.book_title','like',"%$keyword%")
                            ->orWhere('book_authors.name','like',"%$keyword%")
                            ->orWhere('book_editions.name','like',"%$keyword%")
                            ->orWhere('book_languages.name','like',"%$keyword%")
                            ->orWhere('book_publishers.name','like',"%$keyword%");
                    });
                })
                ->orderBy('id','DESC')
                ->paginate(input('perPage'));
            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // dd($request->all());
        if (!can('stock_books_add')) {
            return $this->notPermitted();
        }
        try {
            $user = Auth::user();
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
            }
            $input['school_id'] = $user->school_id;
            $input['available_quantity'] = $request->quantity;
            $this->model->fill($input);
            $this->model->save();
            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        if (!can('stock_books_view')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('stock_books.id', $id)
            ->leftJoin('book_accessions', 'stock_books.book_accession_id', '=', 'book_accessions.id')
            ->leftJoin('book_authors', 'book_accessions.author', '=', 'book_authors.id')
            ->leftJoin('book_editions', 'book_accessions.edition', '=', 'book_editions.id')
            ->leftJoin('book_languages', 'book_accessions.language', '=', 'book_languages.id')
            ->leftJoin('book_publishers', 'book_accessions.publisher', '=', 'book_publishers.id')
            ->select('stock_books.*', 
                     'book_accessions.book_title as book_name', 
                     'book_accessions.isbn',
                     'book_authors.name as author_name',
                     'book_editions.name as books_edition',
                     'book_languages.name as books_language',
                     'book_publishers.name as books_publisher')
            ->first();
            // ddA($data);
        return returnData(2000, $data);
    }
    
    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        if (!can('stock_books_update')) {
            return $this->notPermitted();
        }
        try {
            $user = Auth::user();
            $input = $request->all();
            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 3000, 'errors' => $validation->errors()], 200);
            }
            $input['school_id'] = $user->school_id;
            $input['available_quantity'] = $request->quantity;
            $data = $this->model->find($id);
            if ($data) {
                $data->update($input);
                return returnData(2000, null, 'Successfully Updated');
            }
            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('stock_books_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if ($data) {
                $data->delete();
                return returnData(2000, $data, 'Successfully Deleted');
            }
            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }

    }
    public function getBookDetails(Request $request)
    {
        $data = BookAccession::where('book_accessions.id', $request->book_accession_id)
            ->leftJoin('book_authors', 'book_accessions.author', '=', 'book_authors.id')
            ->leftJoin('book_editions', 'book_accessions.edition', '=', 'book_editions.id')
            ->leftJoin('book_languages', 'book_accessions.language', '=', 'book_languages.id')
            ->leftJoin('book_publishers', 'book_accessions.publisher', '=', 'book_publishers.id')
            ->select('book_accessions.*', 'book_authors.name as author_name', 'book_editions.name as book_edition', 'book_languages.name as books_language', 'book_publishers.name as books_publisher')
            ->first();
        return returnData(2000, $data);
    }
}
