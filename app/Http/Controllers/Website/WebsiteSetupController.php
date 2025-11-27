<?php

namespace App\Http\Controllers\Website;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Website\WebsiteSetup;

class WebsiteSetupController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new WebsiteSetup();
    }
    public function index()
    {
        $data = $this->model->get();
        $schoolId = auth()->user()->school_id;

        if (!$data->isEmpty()) {
            return returnData(2000, $data);
        }
        $data = [
            [
                'key' => 'about_us',
                'display_name' => 'About Us',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'history',
                'display_name' => 'History',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'mission_vision',
                'display_name' => 'Mission Vision',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'principle_message',
                'display_name' => 'Principle Message',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'vice_principle_message',
                'display_name' => 'Vice Principle Message',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'md_message',
                'display_name' => 'MD Message',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'ed_message',
                'display_name' => 'ED Message',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'chairman_message',
                'display_name' => 'Chairman Message',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'a_p_message',
                'display_name' => 'Assistant Professor Message',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'textarea'
            ],
            [
                'key' => 'providan_google_drive_link',
                'display_name' => 'Providan Google Drive Link',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'text'
            ],
            [
                'key' => 'academic_calender_google_drive_link',
                'display_name' => 'Academic Calender Google Drive Link',
                'value' => '',
                'school_id' => $schoolId,
                'type' => 'text'
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
        if (!can('website_add')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }
            $schoolId = auth()->user()->school_id;

            $value = WebsiteSetup::get()->keyBy('key');
            foreach ($input as $index => $data) {
                if (isset($value[$data['key']])) {
                    $existData = $value[$data['key']];
                    $existData->value = $data['value'];
                    $existData->school_id = $schoolId;
                    $existData->update();
                } else {
                    WebsiteSetup::create([
                        "key" => $data['key'],
                        "type" => $data['type'],
                        "display_name" => $data['display_name'],
                        "value" => $data['value'],
                        "school_id" => $schoolId,
                    ]);
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
