<?php

namespace App\Http\Controllers\Website;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\Address;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Website\WebsiteCourse;

class WebsiteCourseController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new WebsiteCourse();
    }

    public function index()
{
    if (!can('website_course_view')) {
        return $this->notPermitted();
    }

    try {
        $keyword = request()->input('keyword');
        $category = request()->input('category_id'); // Seems unused, remove if not needed
        $perPage = request()->input('perPage', 15); // Set a default value for pagination

        $data = $this->model
            ->with('website_course:id,name')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('website_course', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                })
                ->orWhere('student_name', 'LIKE', "%$keyword%");
            })->latest()->paginate($perPage);

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
        if (!can('website_course_add')) {
            return $this->notPermitted();
        }

        try {
            
            $input = $request->all();
            // dd($input);
            $addresses = $request->input('address');
            $createdBy = auth()->user()->id;
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
            }
            $this->model->fill($input);
            $this->model->save();

            $user = new User();
            $exist = $user->where('username', $request->user_name)->first();
            if ($exist) {
                return returnData(5000, null, 'Username Exist');
            }
            $schoolId = auth()->user()->school_id;
            $user->username = $request->user_name;
            $user->email = $request->email;
            $user->date_of_birth = $request->date_of_birth;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->role_id = 6;
            $user->type = 6;
            $user->school_id = $schoolId;
            $user->created_by = $createdBy;
            $user->save();

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }


    public function show($id)
    {
        if (!can('website_course_view')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)
        ->with(
            'website_course:id,name'
        )->first();
        return returnData(2000, $data);
    }

    public function edit($id)
    {
        try {
            

            $data = $this->model->where('id', $id)->first();
            
            $addres = Address::where('parent_id', $id)->get();

            $resultData = $data->toArray();

            $resultData['address'] = $addres->toArray();

            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
       
        if (!can('website_course_update')) {
            return $this->notPermitted();
        }

        try {
            $validate = $this->model->validate($request->all());
            $schoolId = auth()->user()->school_id;
            if ($validate->fails()) {
                return returnData(3000, $validate->errors(), 'Validation Failed');
            }

            DB::beginTransaction();

            $data = $this->model->where('id', $request->input('id'))->first();
            // $address = Address::where('parent_id', $id)->get();
            $studentId = $id;

            $addData = $request->input('address');

            if ($data) {
                $data->fill($request->all());
                $data->school_id = $schoolId;
                $data->save();

                // if ($address) {
                //     foreach ($addData as $addr) {
                //         $existingAddress = Address::find($addr['id']);
                //         if ($existingAddress) {
                //             $existingAddress->fill($addr);
                //             $existingAddress->save();
                //         } else {
                //             $addressData = new Address();
                //             $addressData->fill($addr);
                //             $addressData->parent_id = $studentId;
                //             $addressData->save();
                //         }
                //     }
                // }
                DB::commit();

                return returnData(2000, null, 'Successfully Updated');
            }

            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }


    public function destroy($id)
    {
        if (!can('website_course_delete')) {
            return $this->notPermitted();
        }

        $data = $this->model->where('id', $id)->first();
       
        if ($data) {
            $data->delete();
            // Address::where('parent_id', $id)->delete();
            $user = User::where('role_id', 6)->delete();
            Profile::where('user_type', 6)->delete();
            return returnData(2000, null, 'Successfully Deleted');
        }
        
        return returnData(2000, [], 'Company Menu not found');
    }
}
