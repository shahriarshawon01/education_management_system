<?php


namespace App\SmsLibrary;

use App\Models\SMS\smsHistory;
use Illuminate\Support\Facades\DB;

class GPSMSLibrary
{
    // public function SendSMS($mobile, $sms, $request_type = FALSE, $message_type = FALSE, $campaign_title = FALSE)
    // {
    //     $setting = DB::table('sms_setting_gp')->first();
    //     if ($setting->api_status == 0) {
    //         $server_message = [
    //             'code' => 405,
    //             'message' => 'Please Active Your API First'
    //         ];
    //         return $server_message;
    //     }

    //     if (!$mobile) {
    //         $server_message = [
    //             'code' => 405,
    //             'message' => 'Please Check Your Mobile Number'
    //         ];
    //         return $server_message;
    //     }

    //     $dataUpdated = [
    //         'username' => $setting->username,
    //         'password' => $setting->password,
    //         'apicode' => $setting->apicode,
    //         'msisdn' => $mobile,
    //         'countrycode' => $setting->countrycode,
    //         'cli' => $setting->cli,
    //         'messagetype' => $setting->messagetype,
    //         'message' => $sms,
    //         'messageid' => $setting->messageid,
    //     ];

    //     $ch = curl_init('https://gpcmp.grameenphone.com/ecmapigw/webresources/ecmapigw.v2');

    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataUpdated));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         'Content-Type: application/json',
    //     ]);

    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //     $response = curl_exec($ch);
    //     $err = curl_error($ch);
    //     curl_close($ch);
    //     $ch = json_decode($response, true);
    //     // dd($ch);

    //     $sendMessage = '';
    //     if ($ch['statusCode'] == 200) {
    //         $message = $sendMessage;
    //         $code = 200;
    //         $data = $this->gp_response_message($message, $code);
    //         $server_response = 'Success';
    //         $server_message = [
    //             'code' => 200,
    //             'message' => 'Success'
    //         ];
    //     } else {
    //         $message = 'Error Code : ';
    //         $code = intval($ch['statusCode']);
    //         $data = $this->gp_response_message($code, $message);
    //         $message = $message . $code . '. ' . $data;
    //         $server_response = $message;
    //         $server_message = [
    //             'code' => $code,
    //             'message' => $server_response
    //         ];
    //     }
    //     $sms_history = new smsHistory;

    //     $sms_history->mobile = $mobile;
    //     $sms_history->sms = $sms;
    //     $sms_history->request_type = $request_type;
    //     $sms_history->message_type = 'TEXT';
    //     $sms_history->server_code = $code;
    //     $sms_history->server_response = $server_response;
    //     $sms_history->campaign_title = 'TPSC SMS';

    //     $sms_history->save();

    //     return $server_message;
    // }

    public function SendSMS($mobile, $sms, $request_type = false, $message_type = false, $campaign_title = false)
    {
        $setting = DB::table('sms_setting_gp')->first();
        if ($setting->api_status == 0) {
            return [
                'statusCode' => 405,
                'message' => 'Please activate your API first'
            ];
        }

        if (!$mobile) {
            return [
                'statusCode' => 405,
                'message' => 'Please check your mobile number'
            ];
        }

        $dataUpdated = [
            'username' => $setting->username,
            'password' => $setting->password,
            'apicode' => $setting->apicode,
            'msisdn' => $mobile,
            'countrycode' => $setting->countrycode,
            'cli' => $setting->cli,
            'messagetype' => $setting->messagetype,
            'message' => $sms,
            'messageid' => $setting->messageid,
            // 'request_type' => $request_type,
        ];

        $ch = curl_init('https://gpcmp.grameenphone.com/ecmapigw/webresources/ecmapigw.v2');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataUpdated));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        $ch = json_decode($response, true);

        if ($err || !$ch || !isset($ch['statusCode'])) {
            return [
                'statusCode' => 500,
                'message' => 'SMS API error or invalid response'
            ];
        }

        if ($ch['statusCode'] == 200) {
            $server_message = [
                'statusCode' => 200,
                'message' => 'Success'
            ];
        } else {
            $server_message = [
                'statusCode' => intval($ch['statusCode']),
                'message' => 'Error Code: ' . $ch['statusCode']
            ];
        }

        $sms_history = new smsHistory;
        $sms_history->mobile = $mobile;
        $sms_history->sms = $sms;
        $sms_history->request_type = $request_type;
        $sms_history->message_type = 'TEXT';
        $sms_history->server_code = $server_message['statusCode'];
        $sms_history->server_response = $server_message['message'];
        $sms_history->campaign_title = 'TPSC SMS';
        $sms_history->save();

        return $server_message;
    }

    public static function gp_response_message($code, $data)
    {
        $message = '';

        switch ($code) {
            case 201:
                $message .= 'IP BlackList';
                break;
            case 202:
                $message .= 'Duplicate Session';
                break;
            case 203:
                $message .= 'Invalid Username';
                break;
            case 204:
                $message .= 'Invalid Password';
                break;
            case 205:
                $message .= 'Invalid UserType';
                break;
            case 206:
                $message .= 'Invalid MSISDN';
                break;
            case 207:
                $message .= 'Invalid Country Code';
                break;
            case 208:
                $message .= 'Invalid Cli';
                break;
            case 209:
                $message .= 'Dnd User';
                break;
            case 210:
                $message .= 'Parameter Mismatch';
                break;
            case 216:
                $message .= 'Parameter Mismatch';
                break;
            case 217:
                $message .= 'Number Barred';
                break;
            case 220:
                $message .= 'Application Error';
                break;
            case 221:
                $message .= 'Message Sending Fail';
                break;
            case 223:
                $message .= 'MSISDN Must Be Start With Zero';
                break;
            case 226:
                $message .= 'Invalid API Code';
                break;
            default:
                $message .= 'Error Code Not Found';
        }
        return $message;
    }
}
