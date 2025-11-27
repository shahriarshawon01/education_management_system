<?php

namespace App\Helpers;

use App\Models\Configuration;
use App\Models\RoleModules;
use App\Models\SchoolModule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\DB;

trait Helper
{
    public $permission = [];
    public $model = '';
    public $childModel = '';
    public $perPage = 20;
    public $permissionMessage = 'Sorry, You do not have permission to perform this action..!!';
    public $exceptionMessage = 'Whoops, looks like something went wrong.';
    public $permissionMessageType = 'error';
    public $statusCode = array('-100', '-110', '-111', '-112', '-113', '-114', '-115', '-116', '-120');

    public function __construct()
    {
        $perPage = request()->input('perPage');
        if ($perPage && $perPage > 0) {
            $this->perPage = $perPage;
        }
    }

    public function status()
    {
        try {
            $data = $this->model->where('id', request()->input('id'))->first();

            if (!$data) {
                return returnData(2000, null, 'Data Not found');
            }

            if ($data->status == 1) {
                $data->status = 0;
                $data->save();

                return returnData(2000, 'warning', "Successfully InActivated");
            } else {
                $data->status = 1;
                $data->save();

                return returnData(2000, 'success', "Successfully Activated");
            }

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Not Updated');
        }
    }

    public function notPermitted()
    {
        $data = [];
        $data['status'] = 5001;
        $data['message'] = $this->permissionMessage;
        $data['type'] = $this->permissionMessageType;
        return response()->json($data);
    }

}
