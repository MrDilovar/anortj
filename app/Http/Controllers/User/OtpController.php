<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Classes\SmsSender;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function mVerify(Request $request)
    {
        switch ($_POST["input"]["action"]) {
            case "send_otp":
                $mobile_number = $_POST['input']['mobile_number'];

                $otp = rand(100000, 999999);
                // echo json_encode($otp);
                //Session::put('session_otp', $otp);
                session_start();
                $_SESSION['otp'] = $otp;
                $message = "Ваш код для подтверждения номер телефона " . $otp;
                try {
                    $msg_body = "Код подтверждения: $otp";
                    $sms_api = new SmsSender();
                    $sms_response = $sms_api->send('992' . $mobile_number, $msg_body);
                    echo json_encode($sms_response);
                    exit();
                } catch (Exception $e) {
                    die('Error: ' . $e->getMessage());
                }
             
                break;

            case "verify_otp":
                 
                $otp = $_POST['input']['otp'];
                session_start();
                $session_otp = $_SESSION['otp'];
                 //dd($_SESSION['otp']);

                if ($otp == $session_otp) {

                    unset($session_otp);
                    echo json_encode(array("type" => "success", "message" => "Ваш номер телефона подтвержден!"));
                } else {
                    echo json_encode(array("type" => "error", "message" => "Неверный код подтверждения!"));
                }
                break;

            case "resend_otp":
                // $_SESSION['session_otp'] = $otp;                
                $phone = $_POST['input']['mobile_number'];
                
                $msg = rand(100000, 999999);
                
                // $message = "Ваш код для поддтверждения номера телефона " . $otp;
               // $_SESSION['session_otp'] = $msg;
                session_start();
                $_SESSION['otp'] = $msg;
               
                $message = "Ваш код для подтверждения номер телефона " . $msg;
                // dd($message);
                try {
                    $msg_body = "Повторный код : $msg";
                    $sms_api = new SmsSender();
                    $sms_response = $sms_api->send('992' . $phone, $msg_body);
                    echo json_encode($sms_response);
                   // if ($response == '1') {

                   //     echo json_encode(array("type" => "success"));
                 //   } else {
                 //       echo json_encode(array("type" => "error"));
                  //  }
                    exit();
                } catch (Exception $e) {
                    die('Error: ' . $e->getMessage());
                }

                break;

        }

    }

}
