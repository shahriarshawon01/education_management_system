<?php


namespace App\SmsLibrary;
use App\Helpers\SMSHelper;
use App\Models\SMS\smsHistory;
use App\sms_history_model;
use Illuminate\Support\Facades\DB;

class SMSLibrary
{
    public function SendSMS($mobile, $sms, $request_type = FALSE, $message_type = FALSE, $campaign_title = FALSE)
    {
        $setting = DB::table('sms_setting')->first();

        if ($setting) {
            $url = 'https://portal.adnsms.com/api/v1/secure/send-sms';
            $data = [
                'api_key' => $setting->api_key,
                'api_secret' => $setting->api_secret,
                'request_type' => ($request_type) ? $request_type : $setting->request_type,
                'message_type' => ($message_type) ? $message_type : $setting->message_type,
                'mobile' => $mobile,
                'message_body' => $sms
            ];

            if ($request_type == 'GENERAL_CAMPAIGN' && $campaign_title != '') {
                $data['campaign_title'] = $campaign_title;
                $data['isPromotional'] = 0;
            } else if ($request_type == 'GENERAL_CAMPAIGN') {
                return 'Campaign Title is required';
            }

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $message = "CURL Error #:" . $err;
            } else if ($response) {
                $response_data = json_decode($response);

                if (is_object($response_data)) {

                    if ($response_data->api_response_code == 200 && $response_data->api_response_message == 'SUCCESS') {
                        $message = 'OK';
                    } else {
                        $message = 'Response Code: ' . $response_data->api_response_code . ', ';

                        $message .= $this->response_message($response_data->error->error_code, $data);

                        $message .= ', Error Code: ' . $response_data->error->error_code . ', Error Message: ' . $response_data->error->error_message;
                    }
                } else {
                    $message = 'Unable to parse response data';
                    return $message;
                }
            } else {
                $message = 'Unable to get response';
                return $message;
            }
        } else {
            $message = 'SMS setting data not found';
            return $message;

        }

        $sms_history = new smsHistory;

        $sms_history->mobile = $data['mobile'];
        $sms_history->sms = $data['message_body'];
        $sms_history->request_type = 'SINGLE_SMS';
        $sms_history->message_type = 'TEXT';
        $sms_history->campaign_title = $message;
        $sms_history->server_code = $response_data->api_response_code;
        $sms_history->server_response = $response_data->api_response_message;
        $sms_history->save();
        return $message;
    }

    // new sms send function 

    // public function SendSMS($mobile, $sms, $request_type = FALSE, $message_type = FALSE, $campaign_title = FALSE)
    // {
    //     $setting = DB::table('sms_setting')->first();
    //     if (!$setting) {
    //         return ['status' => 'error', 'error_message' => 'SMS setting data not found'];
    //     }

    //     $url = 'https://portal.adnsms.com/api/v1/secure/send-sms';
    //     $data = [
    //         'api_key' => $setting->api_key,
    //         'api_secret' => $setting->api_secret,
    //         'request_type' => $request_type ?: $setting->request_type,
    //         'message_type' => $message_type ?: $setting->message_type,
    //         'mobile' => $mobile,
    //         'message_body' => $sms
    //     ];

    //     $curl = curl_init();
    //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    //     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    //     curl_setopt($curl, CURLOPT_URL, $url);
    //     curl_setopt($curl, CURLOPT_HEADER, 0);
    //     curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($curl, CURLOPT_POST, 1);
    //     curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    //     $response = curl_exec($curl);
    //     $err = curl_error($curl);
    //     curl_close($curl);

    //     if ($err) {
    //         return ['status' => 'error', 'error_message' => "CURL Error #: $err"];
    //     }

    //     $response_data = json_decode($response);
    //     if (!is_object($response_data)) {
    //         return ['status' => 'error', 'error_message' => 'Unable to parse response data'];
    //     }

    //     if ($response_data->api_response_code == 200 && $response_data->api_response_message == 'SUCCESS') {
    //         return ['status' => 'success', 'response_message' => 'SMS sent successfully'];
    //     } else {
    //         $error_message = 'Error Code: ' . $response_data->error->error_code . ', ' . $response_data->error->error_message;
    //         return ['status' => 'error', 'error_message' => $error_message];
    //     }
    // }

    public static function response_message($code, $data)
    {
        $message = '';

        switch ($code) {
            case 2001:
                $message .= 'INVALID NUMBER';
                break;
            case 2002:
                $message .= 'INVALID MESSAGE LENGTH';
                break;
            case 2003:
                $message .= 'INVALID PARAMETER';
                break;
            case 2004:
                $message .= 'EXCEED REQUEST LIMIT';
                break;
            case 2005:
                $message .= 'INVALID MESSAGE TYPE';
                break;
            case 3001:
                $message .= 'INVALID CREDENTIAL';
                break;
            case 4001:
                $message .= 'CLIENT DISABLED';
                break;
            case 4002:
                $message .= 'MASK DISABLED';
                break;
            case 4003:
                $message .= 'API DISABLED';
                break;
            case 4004:
                $message .= 'IP BLACKLISTED';
                break;
            case 4005:
                $message .= 'INSUFFICIENT BALANCE';
                break;
            case 4006:
                $message .= 'NOT CONFIGURED';
                break;
            case 5001:
                $message .= 'UNKNOWN ERROR';
                break;
            case 5004:
                $message .= 'RECORD NOT FOUND';
                break;
            default:
                $message .= 'Error Code Not Found';
        }
        return $message;
    }
}


