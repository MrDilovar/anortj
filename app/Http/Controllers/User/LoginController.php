<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Classes\SmppAnor;
use Illuminate\Http\Request;
use App\Classes\SmsSender;
use App\Models\User;
use Session;
use Validator;
use Illuminate\Support\Facades\Input;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout', 'userLogout']]);
    }

    public function showLoginForm()
    {
        $this->code_image();
        return view('user.login');
    }

    public function otp_login(Request $request)
    {
       

        $rules = [
            //'email'   => 'required|email',
            'phone' => 'required|min:9|max:9',
            // 'password' => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        // $autopass = str_random(8);
        $autopass =  rand(10000,99999);
       
        Session::put('otp_cd',  $autopass);
        $msg = "Идентификационный код : ".$autopass;

        try {
            // $sms_api = new SmsSender();
            // $sms_send = $sms_api->send('992'.$request->phone, $msg);
            $sms_send = SmppAnor::sendSMS($request->phone, $msg); 
            } catch (Exception $e) {
                die('Error: ' . $e->getMessage());
            }

            return response()->json(array('otp' =>  'send'));
            
    }


    public function login(Request $request)
    {
        $input =  $request->all();

       //  --- Validation Section
        $rules = [
            //'email'   => 'required|email',
            'password' => 'required',
            // 'password' => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        $otp_cod = Session::get('otp_cd');

        if( $otp_cod == $request->password ){

            if (User::where('phone', '=', $request->phone)->count() > 0) {
    
                $admin = User::where('phone', '=', $request->phone)->firstOrFail();
               
                if($admin->is_vendor == 0){
                    $input['password'] = bcrypt($otp_cod);
                    $admin->update($input);
                }
            }else{
                $user = new User;
                $input = $request->all();
                $input['password'] = bcrypt($otp_cod);
                $input['name'] = $request->phone;
                $user->fill($input)->save();
            }
           
            
        }

        // Attempt to log the user in
        if (Auth::attempt(['phone' => $request->phone, 'password' => $request->password])) {

            // if successful, then redirect to their intended location

            // Check If Email is verified or not
            //if (Auth::guard('web')->user()->email_verified == 'No') {
            //    Auth::guard('web')->logout();
            //    return response()->json(array('errors' => [0 => 'Your Email is not Verified!']));
            //}

            if (Auth::guard('web')->user()->ban == 1) {
                Auth::guard('web')->logout();
                return response()->json(array('errors' => [0 => 'Ваш аккаунт заблокирован.']));
            }

            // Login Via Modal
            if (!empty($request->modal)) {
                // Login as Vendor
                if (!empty($request->vendor)) {
                    if (Auth::guard('web')->user()->is_vendor == 2) {
                        return response()->json(route('vendor-dashboard'));
                    } else {
                        return response()->json(route('user-package'));
                    }
                }
                // Login as User
                return response()->json(1);
            }
            // Login as User
            return response()->json(route('user-dashboard'));
        }

        // if unsuccessful, then redirect back to the login with the form data
        return response()->json(array('errors' => [0 => 'Введен неправильный код !']));
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect(route('front.index'));
    }

    // Capcha Code Image
    private function code_image()
    {
        $actual_path = str_replace('project', '', base_path());
        $image = imagecreatetruecolor(200, 50);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, 200, 50, $background_color);

        $pixel = imagecolorallocate($image, 0, 0, 255);
        for ($i = 0; $i < 500; $i++) {
            imagesetpixel($image, rand() % 200, rand() % 50, $pixel);
        }

        $font = $actual_path . 'assets/front/fonts/NotoSans-Bold.ttf';
        $allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $length = strlen($allowed_letters);
        $letter = $allowed_letters[rand(0, $length - 1)];
        $word = '';
        //$text_color = imagecolorallocate($image, 8, 186, 239);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $cap_length = 6; // No. of character in image
        for ($i = 0; $i < $cap_length; $i++) {
            $letter = $allowed_letters[rand(0, $length - 1)];
            imagettftext($image, 25, 1, 35 + ($i * 25), 35, $text_color, $font, $letter);
            $word .= $letter;
        }
        $pixels = imagecolorallocate($image, 8, 186, 239);
        for ($i = 0; $i < 500; $i++) {
            imagesetpixel($image, rand() % 200, rand() % 50, $pixels);
        }
        session(['captcha_string' => $word]);
        imagepng($image, $actual_path . "assets/images/capcha_code.png");
    }

    // public function test()
    // {
    //     return "hello";
    // }

}