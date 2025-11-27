<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\Address;
use App\Models\Parents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new Parents();
    }

    public function index()
    {
        if (!can('parents_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $status = request()->input('status');


            $data = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('name', 'Like', "%$keyword%");
                })
                ->when(isset($status), function ($query) use ($status) {
                    $query->where('status', $status);
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
        if (!can('parents_add')) {
            return $this->notPermitted();
        }

        try {
            DB::beginTransaction();
            $input = $request->all();
            $addresses = $request->input('address');
            $schoolId = auth()->user()->school_id;
            $createdById = auth()->user()->id;

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
            }

            $userId = null;

            if ($request->as_login) {
                $user = new User();
                $exist = $user->where('email', $request->email)->first();
                if ($exist) {
                    return returnData(5000, null, 'Email already exists..');
                }
                $user->username = strtolower($request->name);
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->role_id = 4;
                $user->type = 4;
                $user->school_id = $schoolId;
                $user->created_by = $createdById;
                $user->save();

                $userId = $user->id;
            }
            $picturePath = $request->image ?? null;

            $this->model->user_id = $userId;
            $this->model->name = $request->input('name');
            $this->model->gender = $request->input('gender');
            $this->model->phone = $request->input('phone');
            $this->model->profession = $request->input('profession');
            $this->model->income = $request->input('income');
            $this->model->image = $picturePath;
            $this->model->school_id = $schoolId;
            $this->model->status = 1;

            $this->model->save();

            // address entry
            $parentId = $this->model->id;

            foreach ($addresses as $address) {
                $addressData = new Address();
                $addressData->fill($address);
                $addressData->school_id = $schoolId;
                $addressData->parent_id = $parentId;
                $addressData->save();
            }

            DB::commit();
            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        $data = $this->model->where('id', $id)->first();
        $address = Address::selectRaw("*, divisions.name as division, districts.name as district, upazilas.name as upazilla,unions.name as union_name")
            ->leftJoin('divisions', 'addresses.division', '=', 'divisions.id')
            ->leftJoin('districts', 'addresses.district', '=', 'districts.id')
            ->leftJoin('upazilas', 'addresses.upazila', '=', 'upazilas.id')
            ->leftJoin('unions', 'addresses.union', '=', 'unions.id')
            ->where('parent_id', $id)->get();

        $data->{'address'} = $address;

        return returnData(2000, $data);
    }

    public function edit($id)
    {
        try {

            $data = $this->model->with('user:id,email,username')->where('id', $id)->first();
          
            $address = Address::where('parent_id', $id)->get();

            $resultData = $data->toArray();
            $resultData['address'] = $address->toArray();

            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('parents_update')) {
            return $this->notPermitted();
        }

        try {
            DB::beginTransaction();
            $input = $request->all();
            $addresses = $request->input('address');
            $schoolId = auth()->user()->school_id;
            $createdById = auth()->user()->id;

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
            }

            $parent = $this->model->findOrFail($id);
            $userId = $parent->user_id;

            if ($request->as_login) {
                $user = User::find($userId);
                if (!$user) {
                    $user = new User();
                    $exist = $user->where('email', $request->email)->first();
                    if ($exist) {
                        return returnData(5000, null, 'Email already exists..');
                    }
                } else {
                    $exist = $user->where('email', $request->email)->where('id', '!=', $userId)->first();
                    if ($exist) {
                        return returnData(5000, null, 'Email already exists..');
                    }
                }

                $user->username = strtolower($request->name);
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->role_id = 4;
                $user->type = 4;
                $user->school_id = $schoolId;
                $user->created_by = $createdById;

                $user->save();

                $userId = $user->id;
            }

            $picturePath = $request->image ?? $parent->image;

            $parent->user_id = $userId;
            $parent->name = $request->input('name');
            $parent->gender = $request->input('gender');
            $parent->phone = $request->input('phone');
            $parent->profession = $request->input('profession');
            $parent->income = $request->input('income');
            $parent->image = $picturePath;
            $parent->school_id = $schoolId;
            $parent->status = 1;

            $parent->save();

            // Update address entries
            foreach ($addresses as $address) {
                if (isset($address['id'])) {
                    $addressData = Address::find($address['id']);
                    if ($addressData) {
                        $addressData->fill($address);
                        $addressData->school_id = $schoolId;
                        $addressData->parent_id = $parent->id;
                        $addressData->save();
                    }
                } else {
                    $addressData = new Address();
                    $addressData->fill($address);
                    $addressData->school_id = $schoolId;
                    $addressData->parent_id = $parent->id;
                    $addressData->save();
                }
            }
            DB::commit();
            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('parents_delete')) {
            return $this->notPermitted();
        }

        $data = $this->model->where('id', $id)->first();

        if ($data) {
            $data->delete();
            Address::where('parent_id', $id)->delete();
            User::where('role_id', 4)->delete();
            return returnData(2000, null, 'Successfully Deleted');
        }

        return returnData(2000, [], 'Parent not found');
    }
}
