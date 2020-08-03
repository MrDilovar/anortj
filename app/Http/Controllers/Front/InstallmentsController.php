<?php

namespace App\Http\Controllers\Front;

use App\Classes\GeniusMailer;
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

class InstallmentsController extends Controller
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
            if (Session::has('currency')) 
            {
              $curr = Currency::find(Session::get('currency'));
            }
            else
            {
                $curr = Currency::where('is_default','=',1)->first();
            }
        $gs = Generalsetting::findOrFail(1);
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
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
         
        
        $date_format = date("d-m-y_H:i");
        $order = new Order;
        //generate uniq order id
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


        $success_url = action('Front\PaymentController@payreturn');
        $item_name = $gs->title." Order";
        $item_number = $new_order_frmt;
        $order['user_id'] = $request->user_id;
        $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9)); 
        $order['totalQty'] = $request->totalQty;
        $order['pay_amount'] = $request->total;
        $order['method'] = 'Installments';
        $order['installments_for'] = $request->optionsCredit;
        $order['installments_prepay'] = $request->credit_prepayment;
        $order['installments_month_pay'] = $request->credit_month_pay;
        $order['shipping'] = $request->shipping;
        $order['pickup_location'] = $request->pickup_location;
        $order['customer_email'] = $request->email;
        $order['customer_name'] = $request->name;
        $order['shipping_cost'] = $request->shipping_cost;
        $order['packing_cost'] = $request->packing_cost;
        $order['tax'] = $request->tax;
        $order['customer_phone'] = $request->phone;
        $order['order_number'] =   $item_number;
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
        $order['coupon_code'] = $request->coupon_code;
        $order['coupon_discount'] = $request->coupon_discount;
        $order['dp'] = $request->dp;
        $order['payment_status'] = "Unpaid";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;
        $order['vendor_shipping_id'] = $request->vendor_shipping_id;
        $order['vendor_packing_id'] = $request->vendor_packing_id;
        
            if (Session::has('affilate')) 
            {
                $val = $request->total / 100;
                $sub = $val * $gs->affilate_charge;
                $user = User::findOrFail(Session::get('affilate'));
                $user->affilate_income += $sub;
                $user->update();
                $order['affilate_user'] = $user->name;
                $order['affilate_charge'] = $sub;
            }
        $order->save();
        $track = new OrderTrack;
        $track->title = 'Pending';
        $track->text = 'You have successfully placed your order.';
        $track->order_id = $order->id;
        $track->save();

        $notification = new Notification;
        $notification->order_id = $order->id;
        $notification->save();
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
                $vorder->order_number = $order->order_number;             
                $vorder->save();
            }

        }

        $oldCart = Session::get('cart');
        $vendor_data=[];
        $i=0;
        
        foreach($oldCart->items as $prod){    
           
            if($prod['item']['user_id']!=0  ){
                $vendor_data[$i]['vendor_id']= $prod['item']['user_id'] ;
                $vendor_data[$i]['vendor_phone'] = User::all()->where('id',"=",$prod['item']['user_id'])->pluck('phone')->first();
                //$vendor_data[$i]['vendor_phone'] = '550000874';
                $sms_send = SmppAnor::sendSMS($vendor_data[$i]['vendor_phone'],"С вашего магазина оформлен заказ. Номер заказа:$order->order_number . Метод оплаты: В рассрочку"); 
            }  
            $i++;  
        }

        $sms_send = SmppAnor::sendSMS('550000874',"Anor.TJ. Оформлен заказ: $order->order_number . Метод оплаты: В рассрочку");

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

        Session::put('temporder',$order);
        Session::put('tempcart',$cart);

        Session::forget('cart');

            Session::forget('already');
            Session::forget('coupon');
            Session::forget('coupon_total');
            Session::forget('coupon_total1');
            Session::forget('coupon_percentage');
    
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
			
		
        return redirect($success_url);

    }


    // public function notify( Request $request ) {
       
    //     $order_id = $request->id;
    //     $settings = Generalsetting::findOrFail(1);
    //     $api_check= null;
          
    //    if($settings->is_kmilli == 1){
       
    //        $api_check = new KortiMilliCheck($settings->kmilli_key, $settings->kmilli_user_name);
          
    //    }
    //    $pay_status = $api_check->check($order_id);
       
	// 	if ( $pay_status->result == '0' ) {
            
    //         $oldCart = Session::get('cart');
    //         $vendor_data=[];
    //         $i=0;
    //         foreach($oldCart->items as $prod){    
               
    //             if($prod['item']['user_id']!=0  ){
    //                 $vendor_data[$i]['vendor_id']= $prod['item']['user_id'] ;
    //                 //$vendor_data[$i]['vendor_phone'] = User::all()->where('id',"=",$prod['item']['user_id'])->pluck('phone')->first();
    //                 $vendor_data[$i]['vendor_phone'] = '931400160';
    //                 $sms_api = new SmsSender();
    //                 $sms_send = $sms_api->send('992'.$vendor_data[$i]['vendor_phone'],"С вашего магазина оформлен заказ. Номер заказа:$request->id . Метод оплаты: Корти Милли"); 
    //                 $i++;
    //             }    
    //         }

	// 		$transaction_id = (string) $pay_status->guid;
    //         $order = Order::where( 'order_number', $order_id )->first();
            
    //         if (isset($order)) {
               
    //             $data['txnid'] = $transaction_id;
    //             $data['payment_status'] = 'Completed';
            
    //             if($order->dp == 1)
    //             {
                  
    //                 $data['status'] = 'completed';
    //             }
    //             $order->update($data);
    //             $notification = new Notification;
    //             $notification->order_id = $order->id;
    //             $notification->save();
                
    //             Session::put('temporder',$order);
    //             Session::forget('cart');
                
    //         }
    //         return redirect()->route('payment.return');

	// 	} else {
    //         //return view( 'payment-failed' );

    //         return redirect(route('payment.cancle'));
	// 	}
    // }


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
