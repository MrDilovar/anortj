<?php

namespace App\Http\Controllers\Admin;

use App\Classes\GeniusMailer;
use App\Models\Generalsetting;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Classes\SmsSender;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Input;
use Validator;


class LoginController extends Controller
{
    public function __construct()
    {
      $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
      return view('admin.login');
    }

    public function login(Request $request)
    {
        //--- Validation Section
        $rules = [
                  'phone'   => 'required',
                  'password' => 'required'
                ];

        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

      // Attempt to log the user in
      if (Auth::guard('admin')->attempt(['phone' => $request->phone, 'password' => $request->password], $request->remember)) {
        // if successful, then redirect to their intended location
        return response()->json(route('admin.dashboard'));
      }

      // if unsuccessful, then redirect back to the login with the form data
          return response()->json(array('errors' => [ 0 => 'Учетные данные не совпадают!' ]));     
    }

    public function showForgotForm()
    {
      return view('admin.forgot');
    }

    public function forgot(Request $request)
    {
      $gs = Generalsetting::findOrFail(1);
      $input =  $request->all();
      if (Admin::where('phone', '=', $request->phone)->count() > 0) {
          // user found
          $admin = Admin::where('phone', '=', $request->phone)->firstOrFail();
          $autopass = str_random(8);
          $input['password'] = bcrypt($autopass);
          $admin->update($input);
          $subject = "Запрос на сброс пароля";
          $msg = "Ваш новы пароль: ".$autopass;

          try {
            $sms_api = new SmsSender();
            $sms_send = $sms_api->send('992'.$request->phone, $msg);
                
          } catch (Exception $e) {
                die('Error: ' . $e->getMessage());
          }
          // if($gs->is_smtp == 1)
          // {
          //     $data = [
          //             'to' => $request->email,
          //             'subject' => $subject,
          //             'body' => $msg,
          //     ];

          //     $mailer = new GeniusMailer();
          //     $mailer->sendCustomMail($data);                
          // }
          // else
          // {
          //     $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
          //     mail($request->email,$subject,$msg,$headers);            
          // }


          return response()->json('Новый пароль отправлен на ваш мобильный номер.');
      }
      else{
      // user not found
      return response()->json(array('errors' => [ 0 => 'Этот номер телефона не зарегистрирован.' ]));    
      }  
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/');
    }
}
