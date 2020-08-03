<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="{{$seo->meta_keys}}">
        <meta name="author" content="GeniusOcean">

        <title>{{$gs->title}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{asset('assets/print/bootstrap/dist/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('assets/print/font-awesome/css/font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{asset('assets/print/Ionicons/css/ionicons.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('assets/print/css/style.css')}}">
  <link href="{{asset('assets/print/css/print.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link rel="icon" type="image/png" href="{{asset('assets/images/'.$gs->favicon)}}"> 
  <style type="text/css">
@page { size: auto;  margin: 0mm; }
@page {
  size: A4;
  margin: 0;
}
@media print {
  html, body {
    width: 210mm;
    height: 287mm;
  }

  ::-webkit-scrollbar {
        width: 0px;  /* remove scrollbar space */
        background: transparent;  /* optional: just make scrollbar invisible */
    }
}
  </style>
</head>
<body onload="window.print();">
    <div class="invoice-wrap">
        
            <div class="invoice__title">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="invoice__logo text-left">
                           <img  class="img-fluid" src="{{ asset('assets/images/'.$gs->invoice_logo) }}" alt="woo commerce logo">
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="invoice__metaInfo ">
                
                <div class="col-lg-6">
                    <div class="invoice__orderDetails ">
                        
                        <p><strong>{{ __('Детали заказа ') }} </strong></p>
                  
                        <span><strong>{{ __('Номер инвойса') }} :</strong> {{ sprintf("%'.08d", $order->id) }}</span><br>
                        <span><strong>{{ __('Дата заказа') }} :</strong> {{ $order->created_at}}</span><br>
                        <span><strong>{{  __('Номер заказа')}} :</strong> {{ $order->order_number }}</span><br>
                        @if($order->dp == 0)
                            <span> <strong>{{ __('Метод доставки') }} :</strong>
                                @if($order->shipping == "pickup")
                                {{ __('Pick Up') }}
                                @else
                                {{ __('Стандартная') }}
                                @endif
                            </span><br>
                        @endif
                       
                    </div>
                </div>
                <div class="col-lg-6"  style="width:50%;">
                    <div class="invoice__orderDetails" >
                       <p><strong>{{ __('Заказчик') }}</strong></p>
                       <span><strong>{{ __('Клиент') }}</strong>: {{ $order->customer_name == null ? $order->customer_name : $order->customer_name}}</span><br>
                       <span><strong>{{ __('Контакты') }}</strong>: {{ $order->customer_phone == null ? $order->customer_phone : $order->customer_phone }}</span><br>
                        @if($order->method=="Installments")
                            <span> <strong>{{ $langg->lang605 }} :</strong> {{ __("В рассрочку")}}</span><br>
                            <span> <strong>{{ __("Предоплата") }} (10%) :</strong> {{$order->installments_prepay}}{{$order->currency_sign}}</span><br>
                            <span> <strong>{{ __("Ежемесячный платеж ") }}({{$order->installments_for}} мес) :</strong> {{$order->installments_month_pay}}{{$order->currency_sign}}</span><br>
                         @else
                            <span> <strong>{{ $langg->lang605 }} :</strong> {{$order->method}}</span>
                         @endif  
                       {{-- <span><strong>{{ __('Country') }}</strong>: {{ $order->shipping_country == null ? $order->customer_country : $order->shipping_country }}</span> --}}
                    </div>
                 </div>
            </div>
           
            <div class="" style="margin-top:0px;">
                @if($order->dp == 0)
                <div class="col-lg-6">
                        <div class="invoice__orderDetails w-100" style="margin-top:5px;">
                            <p><strong>{{ __('Детали доставки') }}</strong></p>
                            <div class="row " >
                                <div class="col-xs-6">
                                    <span><strong>{{ __('Получатель') }}</strong>: {{ $order->shipping_name == null ? $order->customer_name : $order->shipping_name}}</span>
                                </div>
                                <div class="col-xs-6">
                                    <span><strong>{{ __('Город') }}</strong>: {{ $order->shipping_city == null ? $order->customer_city : $order->shipping_city}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <span ><strong>{{ __('Контакты') }}</strong>: {{ $order->shipping_phone == null ? $order->customer_phone : $order->shipping_phone }}</span><br>
                                </div>
                                <div class="col-xs-6">
                                    <span><strong>{{ __('Адрес') }}</strong>: {{ $order->shipping_address == null ? $order->customer_address : $order->shipping_address }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 ">
                                    @php 
                                    $d_time = explode('-', $order->shipping_time)
                                    @endphp
                                  <span><strong>{{ __('Дата доставки') }}</strong>: {{ $order->shipping_date }}</span>
                                </div>
                                <div class="col-xs-6 ">
                                    <span><strong>{{ __('Представитель') }}</strong>: {{__(' Anor.tj') }}</span><br>
                                </div>
                            </div>    
                            @if($d_time[0] =='during')
                                <span><strong>{{ __('Время доставки') }}</strong>: {{__('В течении дня')}}</span>
                                @else
                                <span><strong>{{ __('Время доставки') }}</strong>: c {{$d_time[0]}} до {{$d_time[1]}}</span>
                             @endif  
                               
                        </div>
                </div>
                @endif
                {{-- <div class="col-lg-6" style="width:50%;">
                        <div class="invoice__orderDetails" style="margin-top:5px;">
                            <p><strong>{{ __('Billing Details') }}</strong></p>
                            <span><strong>{{ __('Customer Name') }}</strong>: {{ $order->customer_name}}</span><br>
                            <span><strong>{{ __('Address') }}</strong>: {{ $order->customer_address }}</span><br>
                            <span><strong>{{ __('City') }}</strong>: {{ $order->customer_city }}</span><br>
                            <span><strong>{{ __('Country') }}</strong>: {{ $order->customer_country }}</span>
                        </div>
                </div> --}}
            </div>

                <div class="col-lg-12">
                    <div class="invoice_table">
                        <div class="mr-table">
                            <div class="table-responsive">
                                <table id="example2" class="table table-hover dt-responsive" cellspacing="0"
                                    width="100%">
                                    <thead style="border-top:1px solid rgba(0, 0, 0, 0.1) !important;">
                                        <tr>
                                            <th>{{ __('Наименование продукта/товара') }}</th>
                                            <th>{{ __('Детали') }}</th>
                                            <th>{{ __('Итого') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $subtotal = 0;
                                        $tax = 0;
                                        @endphp
                                        @foreach($cart->items as $product)
                                        <tr>
                                            <td width="50%">
                                                @if($product['item']['user_id'] != 0)
                                                @php
                                                $user = App\Models\User::find($product['item']['user_id']);
                                                @endphp
                                                @if(isset($user))
                                                {{ $product['item']['name']}}
                                                @else
                                                {{$product['item']['name']}}
                                                @endif

                                                @else
                                                {{ $product['item']['name']}}
                                                @endif
                                            </td>

                                            <td>
                                                @if($product['size'])
                                               <p>
                                                    <strong>{{ __('Размер') }} :</strong> {{$product['size']}}
                                               </p>
                                               @endif
                                               @if($product['color'])
                                                <p>
                                                        <strong>{{ __('Цвет') }} :</strong> <span style="width: 20px; height: 5px; display: block; border: 10px solid {{$product['color'] == "" ? "white" : '#'.$product['color']}};"></span>
                                                </p>
                                                @endif
                                                <p>
                                                        <strong>{{ __('Цена') }} :</strong> {{$product['item']['price']  }} {{$order->currency_sign}}
                                                </p>
                                               <p>
                                                    <strong>{{ __('Количество') }} :</strong> {{$product['qty']}} {{ $product['item']['measure'] }}
                                               </p>


                                                    @if(!empty($product['keys']))

                                                    @foreach( array_combine(explode(',', $product['keys']), explode(',', $product['values']))  as $key => $value)
                                                    <p>

                                                        <b>{{ ucwords(str_replace('_', ' ', $key))  }} : </b> {{ $value }} 

                                                    </p>
                                                    @endforeach

                                                    @endif
                                               
                                            </td>

                                            <td>{{ $product['price'] }} {{$order->currency_sign}}
                                            </td>
                                            @php
                                            $subtotal += $product['price'] ;
                                            @endphp

                                        </tr>

                                        @endforeach

                                        {{-- <tr class="semi-border">
                                            <td colspan="1"></td>
                                            <td><strong>{{ __('Subtotal') }}</strong></td>
                                            <td>{{$order->currency_sign}}{{ round($subtotal, 2) }}</td>

                                        </tr> --}}
                                        @if($order->shipping_cost != 0 && $order->pay_amount < 50)
                                        <tr class="no-border">
                                            <td colspan="1"></td>
                                            <td><strong>{{ __('Стоимость доставки: ') }}</strong></td>
                                            <td>{{ $order->shipping_cost }} {{$order->currency_sign}}</td>
                                        </tr>
                                        @endif

                                        @if($order->packing_cost != 0)
                                        <tr class="no-border">
                                            <td colspan="1"></td>
                                            <td><strong>{{ __('Packaging Cost') }}({{$order->currency_sign}})</strong></td>
                                            <td>{{ round($order->packing_cost , 2) }}</td>
                                        </tr>
                                        @endif

                                        @if($order->tax != 0)
                                        <tr class="no-border">
                                            <td colspan="1"></td>
                                            <td><strong>{{ __('TAX') }}({{$order->currency_sign}})</strong></td>

                                            @php
                                            $tax = ($subtotal / 100) * $order->tax;
                                            @endphp

                                            <td>{{round($tax, 2)}}</td>
                                        </tr>

                                        @endif
                                        @if($order->coupon_discount != null)
                                        <tr class="no-border">
                                            <td colspan="1"></td>
                                            <td><strong>{{ __('Coupon Discount') }}({{$order->currency_sign}})</strong></td>
                                            <td>{{$order->coupon_discount}} {{$order->currency_sign}}</td>
                                        </tr>
                                        @endif
                                        <tr class="final-border">
                                            <td colspan="1"></td>
                                            <td><strong>{{ __('Итого:') }}</strong></td>
                                            <td>{{$order->pay_amount }} {{$order->currency_sign}}
                                            </td>
                                        </tr>

                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 w-100">
                    <div class="invoice__orderSign" style="margin-top:5px;">
                        <p><strong>{{ __('Заказ получен в надлежащем качествеs') }}&nbsp; &nbsp;&nbsp;____________</strong></p>
                     
                    </div>
                </div>
        </div>
<!-- ./wrapper -->

<script type="text/javascript">
setTimeout(function () {
        window.close();
      }, 500);
</script>

</body>
</html>
