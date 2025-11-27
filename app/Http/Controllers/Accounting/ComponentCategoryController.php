<?php


namespace App\Http\Controllers\Accounting;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounting\ComponentCategory;

class ComponentCategoryController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new ComponentCategory();
    }

    public function index()
    {
        if (!can('component_category_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
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
        if (!can('canteen_configure_add')) {
            return $this->notPermitted();
        }

        try {
            $schoolId = auth()->user()->school_id ?? null;
            $name = trim($request->name);
            $exists = $this->model
                ->where('name', $name)
                ->exists();

            if ($exists) {
                return returnData(5000, null, 'This component name already exists.');
            }
            $this->model->fill($request->all());
            $this->model->school_id = $schoolId;
            $this->model->created_by = auth()->id();
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
        if (!can('component_category_update')) {
            return $this->notPermitted();
        }

        try {
            $schoolId = auth()->user()->school_id;
            $authUserId = auth()->user()->id;
            $name = trim($request->name);

            $exists = $this->model
                ->where('name', $name)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return returnData(5000, null, 'This component name already exists.');
            }

            $data = $this->model->find($id);
            if (!$data) {
                return returnData(5000, null, 'Data not found');
            }

            $data->fill($request->all());
            $data->school_id = $schoolId;
            $data->updated_by = $authUserId;
            $data->save();

            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('component_category_delete')) {
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
