<?php


use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\School;

use App\Models\Session;
use App\Models\Student;
use App\Models\Permission;
use App\Models\Configuration;
use App\Models\GeneralSetting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

if (!function_exists('input')) {
    function input($inputName)
    {
        if (request()->input($inputName)) {
            return request()->input($inputName);
        }
        return null;
    }
}

if (!function_exists('assets')) {
    function assets($path)
    {
        if (env('PUBLIC_PATH')) {
            return env('PUBLIC_PATH') . '/' . $path;
        }
        return asset($path);
    }
}

if (!function_exists('can')) {
    function can($permission)
    {
        $permissions = Permission::whereHas('role_permissions', function ($query) {
            $query->where('role_id', auth()->user()->role_id);
        })->get()->pluck('name')->toArray();

        if (is_array($permission)) {
            foreach ($permission as $each_per) {
                if (in_array($each_per, $permissions)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            if (in_array($permission, $permissions)) {
                return true;
            } else {
                return false;
            }
        }
    }
}

if (!function_exists('userRole')) {
    function userRole($field = 'name')
    {
        $role = Role::where('id', auth()->user()->role_id)->first();
        if ($role) {
            return $role->{$field};
        }
        return '';
    }
}

if (!function_exists('getConfig')) {
    function getConfig($name)
    {
        $config = Configuration::where('key', $name)->first();
        if ($config) {
            return $config->value;
        }
        return '';
    }
}
if (!function_exists('getGeneralConfig')) {
    function getGeneralConfig($name)
    {
        $config = GeneralSetting::where('key', $name)->first();
        if ($config) {
            return $config->value;
        }
        return '';
    }
}

if (!function_exists('getData')) {
    function getData($id, $column, $table = 'users', $whereColumn = 'id')
    {
        $user = DB::table($table)->where($whereColumn, $id)->first();
        if ($user) {
            return $user->{$column};
        }
        return '';
    }
}

if (!function_exists('storageImage')) {
    function storageImage($path)
    {
        if (env('UPLOAD_PATH')) {
            return env('UPLOAD_PATH') . '/' . $path;
        }

        return env('UPLOAD_PATH') . '/' . $path;
    }
}

if (!function_exists('publicImage')) {
    function publicImage($path)
    {
        return env('PUBLIC_PATH') . '/' . $path;
    }
}

if (!function_exists('returnData')) {
    function returnData($status_code = 2000, $result = null, $message = null, $type = false)
    {
        $data = [];
        if ($status_code) {
            $data['status'] = $status_code;
        }
        if ($result) {
            $data['result'] = $result;
        }
        if ($message) {
            $data['message'] = $message;
        }
        if ($message) {
            $data['message'] = $message;
        }
        if ($type) {
            $data['type'] = $type;
        }
        return response()->json($data);
    }
}

if (!function_exists('permissions')) {
    function permissions()
    {
        $user_permissons = @unserialize(Session::get(''));
        if (is_array($user_permissons)) {
            return $user_permissons;
        }
        return [];
    }
}

if (!function_exists('val')) {
    function val($data, $index, $retArrayOrHyfen = '-')
    {
        $array = (array)$data;

        if (isset($array[$index]) && (!is_null($array[$index]) || $array[$index] > 0)) {
            return $array[$index];
        }
        return $retArrayOrHyfen;
    }
}

if (!function_exists('randomString')) {
    function randomString($length = 25, $type = 'n')
    {
        $characters = $type == 'n' ? '123456789' : '123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('folder')) {
    function folder($path, $permission = 0777)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            return $path;
        } else {
            return $path;
        }
    }
}

if (!function_exists('appFile')) {
    function appFile($path)
    {
        if (file_exists(public_path() . $path)) {
            return $path;
        } else {
            return '/img/no-image.png';
        }
    }
}

if (!function_exists('uploadFile')) {
    function uploadFile($requestFile, $fileName = null, $folder = null)
    {
        try {
            if ($requestFile) {
                $filePath = $folder ? $folder : 'img/';
                $image = $requestFile;
                $format = explode('/', mime_content_type($requestFile))[1];
                $data['image'] = $fileName ? $fileName . ".$format" : time() . ".$format";
                $img = Image::make($image);
                $upload_path = folder(public_path($filePath));
                $image_url = $upload_path . $data['image'];
                $img->save($image_url);

                if ($img) {
                    return $filePath . $data['image'];
                }
                return null;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('ddA')) {
    function ddA($arrayOrObject)
    {
        dd(collect($arrayOrObject)->toArray());
    }
}

if (!function_exists('exact_permission')) {
    function exact_permission($permission_name)
    {
        $explode = explode('_', $permission_name);
        return end($explode);
    }
}

if (!function_exists('configs')) {
    function configs($keys)
    {

        $configs = Configuration::where(function ($query) use ($keys) {
            if (is_array($keys)) {
                $query->whereIn('key', $keys);
            } else {
                $query->where('key', $keys);
            }
        })->get();

        $conData = [];

        foreach ($configs as $config) {
            $conData[$config->key] = $config->value;

            if ($config->type == 'file') {
                $conData[$config->key] = storageImage($config->value);
            }
            if ($config->type == 'encoded') {
                $conData[$config->key] = json_decode($config->value);
            }
            if ($config->type == 'youtube') {
                $conData[$config->key] = deviceWiseUrl($config->value);
            }
        }
        return $conData;
    }
}

if (!function_exists('strLimit')) {
    function strLimit($string, $limit)
    {
        return mb_strimwidth(strip_tags($string), 0, $limit, '...');
    }
}

if (!function_exists('dbValue')) {
    function dbValue($table, $columnOrConditionArray, $value = null, $targetColumn = null)
    {
        if (is_array($columnOrConditionArray)) {
            $data = DB::table($table)->where($columnOrConditionArray)->first();
        } else {
            $data = DB::table($table)->where($columnOrConditionArray, $value)->first();
        }
        if ($data && $targetColumn) {
            return $data->{$targetColumn};
        }

        return $data;
    }
}

if (!function_exists('themeLayout')) {
    function themeLayout()
    {
        if (auth()->check()) {
            return auth()->user()->layout;
        }
        return 'vertical';
    }
}

if (!function_exists('themeLayout')) {
    function themeLayout()
    {
        if (auth()->check()) {
            return auth()->user()->layout;
        }
        return 'vertical';
    }
}

if (!function_exists('isSuperUser')) {
    function isSuperUser()
    {
        if (can('user_add') && can('user_update')) {
            return true;
        }

        return false;
    }
}

if (!function_exists('user')) {
    function user()
    {
        return auth()->user();
    }
}


if (!function_exists('defaultSessionId')) {
    function defaultSessionId($school_id, $onlyId = true)
    {
        try {

            $session = Session::where('school_id', $school_id)->first();

            return $session ? ($onlyId ? $session->id : $session) : '';
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('school_info')) {
    function school_info()
    {
        $school = School::where('status', 1)->first();
        return $school;
    }
}
if (!function_exists('school_logo')) {
    function school_logo()
    {
        $logo = Configuration::select('value', 'display_name', 'key')->where('key', 'logo')->where('status', 1)->where('is_visible', 1)->first();
        return $logo;
    }
}

if (!function_exists('global_school_name')) {
    function global_school_name()
    {
        $school_name = Configuration::select('value', 'display_name', 'key')->where('key', 'name')->where('status', 1)->where('is_visible', 1)->first();
        return $school_name;
    }
}

if (!function_exists('auth_user_name')) {
    function auth_user_name()
    {
        if (Auth::check()) {
            return Auth::user()->username;
        }
        return null; 
    }
}

// if (!function_exists('defaultSessionId')) {
//     function defaultSessionId($school_id, $onlyId = true)
//     {
//         try {
//             $session = Session::where('school_id', $school_id)->first();
//             return $session ? ($onlyId ? $session->id : $session) : '';
//         } catch (Exception $exception) {
//             return null;
//         }
//     }
// }

if (!function_exists('students')) {
    function students($column = false, $user_id = false)
    {
        $userId = $user_id ? $user_id : auth()->user()->id;

        $student = Student::where('user_id', $userId)->where('status', 1)->first();

        if ($student) {
            if ($column) {
                return $student->{$column};
            }

            return $student;
        }

        return null;
    }
}
