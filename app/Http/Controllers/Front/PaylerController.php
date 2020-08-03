<?php

namespace App\Http\Controllers\Front;

use App\Classes\GeniusMailer;
use App\Classes\Payler;
use App\Classes\SmsSender;
use App\Classes\SmppAnor;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\Product;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\VendorOrder;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PaylerController extends Controller
{

   

    public function store(Request $request){

        if($request->pass_check) {
            $users = User::where('phone','=',$request->personal_phone)->get();
            if(count($users) == 0) {
                if ($request->personal_pass == $request->personal_confirm){
                    $user = new User;
                    $user->name = $request->personal_name; 
                    $user->phone = $request->personal_phone;   
                    // $user->password = bcrypt($request->personal_pass);
                    $user->password = bcrypt('as21d21d212');
                    $token = md5(time().$request->personal_name.$request->personal_phone);
                    $user->verification_link = $token;
                    $user->affilate_code = md5($request->name.$request->phone);
                    $user->email_verified = 'Yes';
                    $user->save();
                    Auth::guard('web')->login($user);                     
                }else{
                    return redirect()->back()->with('unsuccess',"Подтверждающий пароль не совпадает.");     
                }
            }
            else {
                return redirect()->back()->with('unsuccess',"По этом номеру учетная запись уже зарегистрирована.");  
            }
        }
       
     if (!Session::has('cart')) {
        return redirect()->route('front.cart')->with('success',"You don't have any product to checkout.");
     }
    
     $oldCart = Session::get('cart');
     $cart = new Cart($oldCart);
            if (Session::has('currency')) 
            {
              $curr = Currency::find(Session::get('currency'));
            }
            else
            {
                $curr = Currency::where('is_default','=',1)->first();
            }

            // if($curr->name != "INR")
            // {
            //     return redirect()->back()->with('unsuccess','Please Select INR Currency For Instamojo.');
            // }

        foreach($cart->items as $key => $prod)
        {
        if(!empty($prod['item']['license']) && !empty($prod['item']['license_qty']))
        {
                foreach($prod['item']['license_qty']as $ttl => $dtl)
                {
                    if($dtl != 0)
                    {
                        $dtl--;
                        $produc = Product::findOrFail($prod['item']['id']);
                        $temp = $produc->license_qty;
                        $temp[$ttl] = $dtl;
                        $final = implode(',', $temp);
                        $produc->license_qty = $final;
                        $produc->update();
                        $temp =  $produc->license;
                        $license = $temp[$ttl];
                         $oldCart = Session::has('cart') ? Session::get('cart') : null;
                         $cart = new Cart($oldCart);
                         $cart->updateLicense($prod['item']['id'],$license);  
                         Session::put('cart',$cart);
                        break;
                    }                    
                }
        }
        }

        
        foreach($cart->items as $key => $prod)
        {
            if(!empty($prod['item']['id']))
            {
                
                $produc = Product::findOrFail($prod['item']['id']);      
    
                $produc->ordered_count = $produc->ordered_count +1;
                $produc->update();      
                   
            }
        }
         

     $settings = Generalsetting::findOrFail(1);
     $order = new Order;
     $item_name = $settings->title." Order";
    
     //------------generate uniq order id start
     $date_format = date("d-m-y_H:i");
    
     $last_order= Order::latest()->first(); 
     $id = 1;
     $exportId = str_pad((int) $id, 1,STR_PAD_LEFT);
     $new_order_frmt= 'anor'.$date_format.'.'.$exportId;
     if( $last_order->order_number!=null){

         $pos = strpos($last_order->order_number , '.');
         $last_num= substr($last_order->order_number, $pos + 1 );
         // $fiveDigitNumber = str_pad((int)  $last_num + 1, 5, "0", STR_PAD_LEFT);
         $fiveDigitNumber = str_pad((int)  $last_num + 1, 1,STR_PAD_LEFT);
 
         $new_order_frmt = 'anor'.$date_format.'.'.$fiveDigitNumber;
     }
    
    //------------generate uniq order id end
     $item_number = $new_order_frmt;
     $item_amount = $request->total;
     $api_send =null;
    // $success_url = action('Front\PaymentController@payreturn');
     //$cancel_url = action('Front\PaymentController@paycancle');
    // $notify_url = action('Front\KortiMilliController@notify');          
    
    if($settings->is_payler == 1){
    
        $payler = new Payler('sandbox');
        
    }
   
    try {
      
      
                        $order['user_id'] = $request->user_id;
                        $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9));
                        $order['totalQty'] = $request->totalQty;
                        // $order['pay_amount'] = round($item_amount / $curr->value, 2);
                        $order['pay_amount'] = $item_amount;
                        $order['method'] = 'VISA';
                        $order['customer_email'] = $request->email;
                        $order['customer_name'] = $request->name;
                        $order['customer_phone'] = $request->phone;
                        $order['order_number'] = $item_number;
                        $order['shipping'] = $request->shipping;
                        $order['pickup_location'] = $request->pickup_location;
                        $order['customer_address'] = $request->address;
                        $order['customer_country'] = $request->customer_country;
                        $order['customer_city'] = $request->city;
                        $order['customer_zip'] = $request->zip;
                        $order['shipping_email'] = $request->shipping_email;
                        $order['shipping_name'] = $request->shipping_name;
                        $order['shipping_phone'] = $request->shipping_phone;
                        $order['shipping_address'] = $request->shipping_address;
                        $order['shipping_country'] = $request->shipping_country;
                        $order['shipping_city'] = $request->shipping_city;
                        $order['shipping_zip'] = $request->shipping_zip;
                        $order['shipping_date'] = $request->shipping_date;
                        $order['shipping_time'] = $request->shipping_time;
                        $order['order_note'] = $request->order_notes;
                       // $order['pay_id'] = $item_number;
                        $order['coupon_code'] = $request->coupon_code;
                        $order['coupon_discount'] = $request->coupon_discount;
                        $order['payment_status'] = "Unpaid";
                        $order['currency_sign'] = $curr->sign;
                        $order['currency_value'] = $curr->value;
                        $order['shipping_cost'] = $request->shipping_cost;
                        $order['packing_cost'] = $request->packing_cost;
                        $order['tax'] = $request->tax;
                        $order['dp'] = $request->dp;
                        $order['vendor_shipping_id'] = $request->vendor_shipping_id;
                        $order['vendor_packing_id'] = $request->vendor_packing_id;
                        
                        if($order['dp'] == 1)
                        {
                            
                            $order['status'] = 'completed';
                        }
                        
                if (Session::has('affilate')) 
                {
                    $val = $request->total / 100;
                    $sub = $val * $settings->affilate_charge;
                    $user = User::findOrFail(Session::get('affilate'));
                    $user->affilate_income += $sub;
                    $user->update();
                    $order['affilate_user'] = $user->name;
                    $order['affilate_charge'] = $sub;
                }
                        $order->save();
                        
            if($order->dp == 1){
                $track = new OrderTrack;
                $track->title = 'Completed';
                $track->text = 'Your order has completed successfully.';
                $track->order_id = $order->id;
                $track->save();
            }
            else {
                $track = new OrderTrack;
                $track->title = 'Pending';
                $track->text = 'You have successfully placed your order.';
                $track->order_id = $order->id;
                $track->save();
            }
                        
                        if($request->coupon_id != "")
                        {
                        $coupon = Coupon::findOrFail($request->coupon_id);
                        $coupon->used++;
                        if($coupon->times != null)
                        {
                                $i = (int)$coupon->times;
                                $i--;
                                $coupon->times = (string)$i;
                        }
                        $coupon->update();

                        }
                        foreach($cart->items as $prod)
                        {
                            $x = (string)$prod['stock'];
                            if($x != null)
                            {
                                $product = Product::findOrFail($prod['item']['id']);
                                $product->stock =  $prod['stock'];
                                $product->update();                
                            }
                        }
            foreach($cart->items as $prod)
            {
                $x = (string)$prod['size_qty'];
                if(!empty($x))
                {
                    $product = Product::findOrFail($prod['item']['id']);
                    $x = (int)$x;
                    $x = $x - $prod['qty'];
                    $temp = $product->size_qty;
                    $temp[$prod['size_key']] = $x;
                    $temp1 = implode(',', $temp);
                    $product->size_qty =  $temp1;
                    $product->update();               
                }
            }

            foreach($cart->items as $prod)
            {
                $x = (string)$prod['stock'];
                if($x != null)
                {

                    $product = Product::findOrFail($prod['item']['id']);
                    $product->stock =  $prod['stock'];
                    $product->update();  
                    if($product->stock <= 5)
                    {
                        $notification = new Notification;
                        $notification->product_id = $product->id;
                        $notification->save();                    
                    }              
                }
            }
           
            $new_array=array();
            $i=0;
            $notf = null;

            foreach($cart->items as $prod)
            {
                if($prod['item']['user_id'] != 0)
                {
                    $vorder =  new VendorOrder;
                    $vorder->order_id = $order->id;
                    $vorder->user_id = $prod['item']['user_id'];
                    $notf[] = $prod['item']['user_id'];
                    $vorder->qty = $prod['qty'];
                    $vorder->price = $prod['price'];
                    $new_array[$i]['ID'] = $prod['item']['user_id'];
                    $new_array[$i]['Amount'] = $prod['price'];
                    $new_array[$i]['Quantity'] = $prod['qty'];
                    $vorder->order_number = $order->order_number;             
                    $vorder->save();
                    $i++;
                }
                
            }
            
           
            $part = '';
            $size = count($new_array);
           
            for ($i=1; $i<=$size; $i ++)
            {   
                    $part = $part.'<rows name = "'.$i.'">';
                    $part = $part.'<id>'.$new_array[$i-1]["ID"].'</id>';	
                    $part = $part.'<amount>'.$new_array[$i-1]["Amount"].'</amount>';	
                    $part = $part.'<quantity>'.$new_array[$i-1]["Quantity"].'</quantity>';	
                    $part = $part.'</rows>';	
            }


            if(!empty($notf))
            {
                $users = array_unique($notf);
                foreach ($users as $user) {
                    $notification = new UserNotification;
                    $notification->user_id = $user;
                    $notification->order_number = $order->order_number;
                    $notification->save();   
                   
                }
               
            }
            
            $gs = Generalsetting::find(1);

            //Sending Email To Buyer
            /*
            if($gs->is_smtp == 1)
            {
            $data = [
                'to' => $request->email,
                'type' => "new_order",
                'cname' => $request->name,
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
                'wtitle' => "",
                'onumber' => $order->order_number,
            ];

            $mailer = new GeniusMailer();
            $mailer->sendAutoOrderMail($data,$order->id);            
            }
            else
            {
            $to = $request->email;
            $subject = "Your Order Placed!!";
            $msg = "Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number.".Please wait for your delivery. \nThank you.";
                $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            mail($to,$subject,$msg,$headers);            
            }
            //Sending Email To Admin
            if($gs->is_smtp == 1)
            {
                $data = [
                    'to' => $gs->email,
                    'subject' => "New Order Recieved!!",
                    'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order->order_number.".Please login to your panel to check. <br>Thank you.",
                ];

                $mailer = new GeniusMailer();
                $mailer->sendCustomMail($data);            
            }
            else
            {
            $to = $gs->email;
            $subject = "New Order Recieved!!";
            $msg = "Hello Admin!\nYour store has recieved a new order.\nOrder Number is ".$order->order_number.".Please login to your panel to check. \nThank you.";
                $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            mail($to,$subject,$msg,$headers);
            }

            */

        Session::put('tempcart',$cart);
        Session::put('cart',$cart);
       // Session::forget('cart');

        Session::forget('already');
        Session::forget('coupon');
        Session::forget('coupon_total');
        Session::forget('coupon_total1');
        Session::forget('coupon_percentage');


            //----------Reponse From Payler Api --------
            
            $amoun_toInt =  intval(strval($request->total*100));
          
            $data = array (                // Параметры, отмеченные в описании звездочкой (*), обязательны для заполнения

                'key' => $settings->payler_key,             // *Идентификатор продавца, выдается продавцу при регистрации вместе с параметрами доступа
                'type' => '1',           // *Тип сессии, определяет количество стадий платежа (одностадийный или двухстадийный)
                                        //     допустимые значения - "OneStep"|"TwoStep"|1|2
                'order_id' => $item_number,   // *Идентификатор заказа (платежа). Для каждой сессии должен быть уникальным 
                                        //     (строка, максимум 100 символов, только печатные символы ASCII)
                'currency' => 'TJS',       //  Валюта платежа. По умолчанию - рубли
                                        //     допустимые значения - "RUB"|"USD"|"EUR"
                'amount' => $amoun_toInt,       // *Сумма платежа 
            /*                            //     в зависимости от валюты - в копейках|центах|евроцентах
                'product' => $product,     //  Описание товара или заказа
                                        //     (строка, максимум 256 символов)
                'total' => $total,         //  Количество товаров в заказе
                                        //     (вещественное число)
                'template' => $template,   //  Используемый шаблон платежной формы. Если не задан, используется шаблон по умолчанию
                                        //     (строка, максимум 100 символов)
                'lang' => 'ru'             //  Предпочитаемый язык платежной формы и ответов сервера. По умолчанию - русский.
                                        //     допустимые значения - "ru"|"en"
                'userdata' => 'dt'         //  Пользовательские данные, которые необходимо сохранить вместе с платежом
                                        //     (строка, максимум - 10 KiB). Для получения - см. в API GetAdvancedStatus
                'recurrent' => 'TRUE',     //  Флаг, показывает, требуется ли создать шаблон для рекурентных платежей на основании текущего
                                        //     допустимые значения - 'TRUE'|'FALSE'|1|0
                'pay_page_param_<имя парамметра>' => 'text'
                                        //  Параметр для отображения на странице платежной формы. Для использования см. template
                                        //     (строка, максимум - 100 символов)
            */
                );                         // Параметры, отмеченные в описании звездочкой (*), обязательны для заполнения

            //Создаем платежную сессию 
            $session_data = $payler->POSTtoGateAPI($data, "StartSession");

            //Если заполнен session_id, сессию удалось создать 
            if(isset($session_data['session_id'])) {
        
                $session_id = $session_data['session_id'];
                /*
                Оплачиваем заказ, параметр - session_id.
                Для оплаты заказа пользователь перенаправляется на сайт Payler, после успешной или неуспешной оплаты возвращается 
                на сайт по ссылке, указанной в настройках учетной записи Payler. Для получения результата оплаты на странице 
                возврата необходимо вызвать GetStatus с параметрами - идентификатор заказа ($order_id) и идентификатор продавца ($key)
                */   
                $pay = $payler->Pay($session_id);
                $redirect_url = $pay;
                return redirect($redirect_url);

            }else {
                echo 'Не удалось создать сессию и провести оплату. Возможные причины: <br/>
                        При создании сессии используется неуникальный номер заказа ('.$item_number.') <br/>
                        Некорректно указаны прочие параметры <br/>
                        IP сервера не включен в белый список в настройках учетной записи Payler ('.$_SERVER['SERVER_ADDR'].') <br/>
                        На сервере не установлена библиотека cURL или JSON <br/>
                        На сервере отключены функции curl_init, curl_setopt_array, curl_exec, json_decode, сurl_close';
            }

        
         
    }
    catch (Exception $e) {
        print('Error: ' . $e->getMessage());
    }

    }


    public function payPaylerCallback( Request $request ) {
       
        
        $order_id = $request->order_id;
        $settings = Generalsetting::findOrFail(1);
        $payler= null;
       
       if($settings->is_payler == 1){
       
             $payler = new Payler('sandbox');
           
       }

       $data = array (
        "key" => $settings->payler_key,
        "order_id" => $order_id
       );

        $pay_status = (array)$payler->POSTtoGateAPI($data, "GetStatus");

		if ( $pay_status['status'] == 'Charged' ) {
                      
            $oldCart = Session::get('cart');
            $vendor_data=[];
            $i=0;
           
            foreach($oldCart->items as $prod){    
                
                if($prod['item']['user_id']!=0  ){
                    $vendor_data[$i]['vendor_id']= $prod['item']['user_id'] ;
                    $vendor_data[$i]['vendor_phone'] = User::all()->where('id',"=",$prod['item']['user_id'])->pluck('phone')->first();
                   // $vendor_data[$i]['vendor_phone'] = '550000874';
                    $sms_send = SmppAnor::sendSMS($vendor_data[$i]['vendor_phone'],"С вашего магазина оформлен заказ. Номер заказа:$request->id . Метод оплаты: VISA"); 
                }  
                $i++; 
            }

            $sms_send = SmppAnor::sendSMS('550000874',"Оформлен заказ: $request->id  . Метод оплаты: VISA"); 

		//	$transaction_id = (string) $pay_status->guid;
            $order = Order::where( 'order_number', $order_id )->first();
            
            if (isset($order)) {
               
               // $data['txnid'] = $transaction_id;
                $data['payment_status'] = 'Paid';
            
                if($order->dp == 1)
                {
                  
                    $data['status'] = 'completed';
                }
                $order->update($data);
                $notification = new Notification;
                $notification->order_id = $order->id;
                $notification->save();
                
                Session::put('temporder',$order);
                Session::forget('cart');
                
            }
            return redirect()->route('payment.return');
            
		} else {
            //return view( 'payment-failed' );

            return redirect(route('payment.cancle'));
		}
    }


    // Capcha Code Image
    private function  code_image()
    {
        $actual_path = str_replace('project','',base_path());
        $image = imagecreatetruecolor(200, 50);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image,0,0,200,50,$background_color);

        $pixel = imagecolorallocate($image, 0,0,255);
        for($i=0;$i<500;$i++)
        {
            imagesetpixel($image,rand()%200,rand()%50,$pixel);
        }

        $font = $actual_path.'assets/front/fonts/NotoSans-Bold.ttf';
        $allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $length = strlen($allowed_letters);
        $letter = $allowed_letters[rand(0, $length-1)];
        $word='';
        //$text_color = imagecolorallocate($image, 8, 186, 239);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $cap_length=6;// No. of character in image
        for ($i = 0; $i< $cap_length;$i++)
        {
            $letter = $allowed_letters[rand(0, $length-1)];
            imagettftext($image, 25, 1, 35+($i*25), 35, $text_color, $font, $letter);
            $word.=$letter;
        }
        $pixels = imagecolorallocate($image, 8, 186, 239);
        for($i=0;$i<500;$i++)
        {
            imagesetpixel($image,rand()%200,rand()%50,$pixels);
        }
        session(['captcha_string' => $word]);
        imagepng($image, $actual_path."assets/images/capcha_code.png");
    }



}
