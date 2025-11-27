<?php

namespace App\Http\Controllers\Library;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Library\BookAccession;
use App\Models\Library\StockBooks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookAccessionController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new BookAccession();
    }

    public function index()
    {
        if (!can('book_accession_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model
                ->leftJoin('book_authors', 'book_authors.id', '=', 'book_accessions.author')
                ->leftJoin('book_editions', 'book_editions.id', '=', 'book_accessions.edition')
                ->leftJoin('book_languages', 'book_languages.id', '=', 'book_accessions.language')
                ->leftJoin('book_publishers', 'book_publishers.id', '=', 'book_accessions.publisher')
                ->select('book_accessions.*',
                    'book_authors.name as author_name',
                    'book_editions.name as books_edition',
                    'book_languages.name as books_language',
                    'book_publishers.name as books_publishers')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('book_title', 'Like', "%$keyword%");
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
        if (!can('book_accession_add')) {
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
            $this->model->fill($input);
            $this->model->save();
            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        // ddA($request);
        if (!can('book_accession_update')) {
            return $this->notPermitted();
        }
        try {
            $user = Auth::user();
            $input = $request->all();
            $schoolId = $user->school_id;

            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 3000, 'errors' => $validation->errors()], 200);
            }
            $input['school_id'] = $user->school_id;
            $data = $this->model->find($id);

            if ($data) {

            $this->model->book_title = $request->input('book_title');
            $this->model->author  = $request->input('author');
            $this->model->publisher = $request->input('publisher');
            $this->model->edition = $request->input('edition');
            $this->model->language = $request->input('language');
            $this->model->cell_number = $request->input('cell_number');
            $this->model->phone = $request->input('phone');
            $this->model->isbn = $request->input('isbn');
            $this->model->soft_copy = $request->input('soft_copy');

            $this->model->school_id = $schoolId;

                 $this->model->save();
                return returnData(2000, null, 'Successfully Updated');
            }
            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('book_accession_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            $relatedStock = StockBooks::where('book_accession_id', $data->id)->first();
            if ($relatedStock) {
                return returnData(5000, null, 'Cannot delete: Related records found in Stock');
            }
            if ($data) {
                $data->delete();
                return returnData(2000, $data, 'Successfully Deleted');
            }
            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}
