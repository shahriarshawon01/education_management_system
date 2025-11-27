<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;

class GeneralSettingController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new GeneralSetting();
    }
    public function index()
    {
     
        $data = $this->model->get();
        $schoolId = auth()->user()->school_id;

        if (count($data) > 0) {
            $settingGroup = $data->groupBy('setting_type');
            return returnData(2000, $settingGroup);
        }
        $data = [
           
            [
                'key' => 'system_name',
                'display_name' => 'System Name',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'phone',
                'display_name' => 'Phone',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'text'
            ],
            [
                'key' => 'address',
                'display_name' => 'Address',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'postal_code',
                'display_name' => 'Postal Code',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'text'
            ],
            [
                'key' => 'school_eiin',
                'display_name' => 'School EIIN',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'text'
            ],
            [
                'key' => 'logo',
                'display_name' => 'Application Logo',
                'value' => '',
                'type' => 'file'
            ],
            
        ];
        return returnData(2000, $data);
    }
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        
        if (!can('general_setting_add')) {
            return $this->notPermitted();
        }
        try {
           
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }
            $schoolId = auth()->user()->school_id;


            $existingSettings = GeneralSetting::get()->keyBy('key');
            foreach ($input as $groupKey => $settings) {
                if (is_array($settings)) {
                    foreach ($settings as $data) {
                        if (isset($existingSettings[$data['key']])) {
                            $existing = $existingSettings[$data['key']];
                            $existing->value = $data['value'];
                            $existing->type = $data['type'];
                            $existing->display_name = $data['display_name'];
                            $existing->school_id = $schoolId;
                            $existing->update();
                        } else {
                            GeneralSetting::create([
                                "key" => $data['key'],
                                "type" => $data['type'],
                                "display_name" => $data['display_name'],
                                "value" => $data['value'],
                                "school_id" => $schoolId,
                                "setting_type" => $data['setting_type'] ?? $groupKey,
                            ]);
                        }
                    }
                }
            }
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
        //
    }

    public function destroy($id)
    {
        //
    }
}
