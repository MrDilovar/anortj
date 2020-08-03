<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Classes\SmsSender;
use App\Classes\SmppAnor;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Generalsetting;
use App\Classes\GeniusMailer;
use Illuminate\Support\Facades\Input;
use Validator;

class ForgotController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showForgotForm()
    {
      return view('user.forgot');
    }

    public function mForgot(Request $request)
    {
       
      $gs = Generalsetting::findOrFail(1);
      $input =  $request->all();
      if (User::where('phone', '=', $request->phone)->count() > 0) {

      $admin = User::where('phone', '=', $request->phone)->firstOrFail();
      $autopass = str_random(8);
 
      $input['password'] = bcrypt($autopass);
      $admin->update($input);
      $subject = "Запрос на сброс пароля";
      $msg = "Ваш новый пароль: ".$autopass;
     
      try {
        // $sms_api = new SmsSender();
        // $sms_send = $sms_api->send('992'.$request->phone, $msg);
        $sms_send = SmppAnor::sendSMS($request->phone, $msg);   
      } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
      }


      //   if($gs->is_smtp == 1)
      //   {
      //       $data = [
      //               'to' => $request->email,
      //               'subject' => $subject,
      //               'body' => $msg,
      //       ];
  
      //       $mailer = new GeniusMailer();
      //       $mailer->sendCustomMail($data);                
      //   }
      //   else
      //   {
      //       $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
      //       mail($request->email,$subject,$msg,$headers);            
      //   }
   
      return  response()->json('Новый пароль отправлен на ваш мобильный номер.');

      }
      else{
      // user not found
      return response()->json(array('errors' => [ 0 => 'Этот номер телефона не зарегистрирован.' ]));    
      }  
    }

}
 