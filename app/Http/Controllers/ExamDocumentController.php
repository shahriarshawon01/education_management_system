<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\ExamDocument;
use Illuminate\Http\Request;

class ExamDocumentController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new ExamDocument();
    }

    public function index()
    {
        if (!can('documents_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');

            $data = $this->model->with('examName:id,name', 'class:id,name', 'session:id,title')
                ->when($keyword, callback: function ($query) use ($keyword) {
                    $query->where(function($query) use ($keyword){
                        $query->orWhereHas('examName', function($q) use ($keyword){
                            $q->where('name','like',"%$keyword%");
                        })
                        ->orWhereHas('class', function($q) use ($keyword){
                            $q->where('name','like',"%$keyword%");
                        });
                    }); 
                })
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
        if (!can('documents_add')) {
            return $this->notPermitted();
        }
        $authID = auth()->user();
        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors(),'Please select an image');
            }

            $this->model->fill($input);
            $this->model->session_id = $request->input('session_year_id');
            $this->model->school_id = $authID->school_id;
            $this->model->user_id = $authID->id;
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
        $data = $this->model::leftJoin('session_years','exam_documents.session_id','session_years.id')
            ->leftJoin('examinations','exam_documents.exam_id','examinations.id')
            ->select('exam_documents.*','session_years.title as session_name','examinations.name as exam_name')
        
        ->where('exam_documents.id', $id)->first();
    
        return returnData(2000, $data);
    }

    public function update(Request $request, $id)
    {
        if (!can('documents_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $authID = auth()->user();

            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 2000, 'errors' => $validation->errors()], 200);
            }
            $data = $this->model->find($id);

            if ($data) {
                $data->update($input);
                $data->session_id = $request->input('session_year_id');
                $data->school_id = $authID->school_id;
                $data->user_id = $authID->id;
                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('documents_delete')) {
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
}
