@extends('layouts.front')
@section('content')

<!-- Breadcrumb Area Start -->
<div class="breadcrumb-area">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <ul class="pages">
          <li>
            <a href="{{ route('front.index') }}">
              {{ $langg->lang17 }}
            </a>
          </li>
          <li>
            <a href="{{ route('payment.return') }}">
              {{ $langg->lang169 }}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- Breadcrumb Area End -->







<section class="tempcart">

@if(!empty($tempcart))

        <div class="container">
            <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <!-- Starting of Dashboard data-table area -->
                        <div class="content-box section-padding add-product-1">
                            <div class="top-area">
                                    <div class="content">
                                        <h4 class="heading">
                                            {{ $langg->order_title }}
                                        </h4>
                                        @if($order->method=="Installments")
                                            <p class="text">
                                                {{ __('Ваш заказ оформлен, для уточнения деталей рассрочки наши сотрудники свяжутся с Вами по указанному в профиле номером телефона.') }}
                                            </p>
                                            @else
                                            <p class="text">
                                                {{ $langg->order_text }}
                                            </p>
                                        @endif        
                                        <a href="{{ route('front.index') }}" class="link">{{ $langg->lang170 }}</a>
                                    </div>
                            </div>
                            <div class="invoice-wrap">
                                <div class="invoice__title">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="invoice__logo text-left">
                                               <img src="{{ asset('assets/images/'.$gs->invoice_logo) }}" alt="woo commerce logo">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-right">
                                            <a class="btn  add-newProduct-btn print bg-primary text-white" href="{{route('user-order-print',$order->id)}}"
                                            target="_blank" ><i class="fa fa-print"></i> {{ __('Распечатать Чек') }}</a>
                                        </div>
                                    </div>
                                </div><br>

                            <div class="row">
                                <div class="col-lg-12">

                                        <div class="product__header">
                                            <div class="row reorder-xs">
                                                <div class="col-lg-12">
                                                    <div class="product-header-title">
                                                        <h2>{{ $langg->lang285 }} {{$order->order_number}}</h2>
                                            </div>   
                                        </div>
                                            @include('includes.form-success')
                                                <div class="col-md-12" id="tempview">
                                                    <div class="dashboard-content">
                                                        <div class="view-order-page" id="print">
                                                            {{-- <p class="order-date">{{ $langg->lang301 }} {{date('d-M-Y h:m',strtotime($order->created_at))}}</p> --}}


                                                    @if($order->dp == 1)

                                                            <div class="billing-add-area">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <h5>{{ $langg->lang287 }}</h5>
                                                                        <address>
                                                                            {{ $langg->lang288 }} {{$order->customer_name}}<br>
                                                                            {{ $langg->lang289 }} {{$order->customer_email}}<br>
                                                                            {{ $langg->lang290 }} {{$order->customer_phone}}<br>
                                                                            {{ $langg->lang291 }} {{$order->customer_address}}<br>
                                                                            {{$order->customer_city}}-{{$order->customer_zip}}
                                                                        </address>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h5>{{ $langg->lang292 }}</h5>
                                                                        <p>{{ $langg->lang293 }} {{$order->currency_sign}}{{ round($order->pay_amount * $order->currency_value , 2) }}</p>
                                                                        <p>{{ $langg->lang294 }} {{$order->method}}</p>

                                                                        @if($order->method != "Cash On Delivery")
                                                                            @if($order->method=="Stripe")
                                                                                {{$order->method}} {{ $langg->lang295 }} <p>{{$order->charge_id}}</p>
                                                                            @endif
                                                                            {{$order->method}} {{ $langg->lang296 }} <p id="ttn">{{$order->txnid}}</p>

                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                    @else
                                                            {{-- <div class="shipping-add-area">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        @if($order->shipping == "shipto") --}}
                                                                            {{-- <h5>{{ $langg->lang302 }}</h5> --}}
                                                                            {{-- <h5>{{ __('Детали заказа ') }}</h5>
                                                                            <address>
                                                                {{ $langg->lang288 }} {{$order->shipping_name == null ? $order->customer_name : $order->shipping_name}}<br>
                                                                {{ $langg->lang289 }} {{$order->shipping_email == null ? $order->customer_email : $order->shipping_email}}<br>
                                                                {{ $langg->lang290 }} {{$order->shipping_phone == null ? $order->customer_phone : $order->shipping_phone}}<br>
                                                                {{ $langg->lang291 }} {{$order->shipping_address == null ? $order->customer_address : $order->shipping_address}}<br>
                                                                {{$order->shipping_city == null ? $order->customer_city : $order->shipping_city}}-{{$order->shipping_zip == null ? $order->customer_zip : $order->shipping_zip}}
                                                                            </address>
                                                                        @else
                                                                            <h5>{{ $langg->lang303 }}</h5>
                                                                            <address>
                                                                                {{ $langg->lang304 }} {{$order->pickup_location}}<br>
                                                                            </address>
                                                                        @endif

                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h5>{{ $langg->lang305 }}</h5>
                                                                        @if($order->shipping == "shipto")
                                                                            <p>{{ $langg->lang306 }}</p>
                                                                        @else
                                                                            <p>{{ $langg->lang307 }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div> --}}

                                                            <div class="row invoice__metaInfo mb-4">
                                                                <div class="col-lg-6">
                                                                    <div class="invoice__orderDetails ">
                                                                        <h5>{{ __('Детали заказа ') }}</h5>
                                                                        {{-- <p><strong> </strong></p> --}}
                                                                
                                                                        <span><strong>{{ __('Номер инвойса') }} :</strong> {{ sprintf("%'.08d", $order->id) }}</span><br>
                                                                        <span><strong>{{ __('Дата заказа') }} :</strong> {{ $order->created_at }}</span><br>
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
                                                                    {{-- <p><strong>{{ __('Заказчик') }}</strong></p> --}}
                                                                    <h5>{{ __('Заказчик') }}</h5>
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

                                                            <div class="row invoice__metaInfo">
                                                                @if($order->dp == 0)
                                                                <div class="col-lg-6 " style="flex: 1 0 50%;max-width:100%">
                                                                    <div class="invoice__orderDetails w-100" style="margin-top:5px;">
                                                                        {{-- <p><strong>{{ __('Детали доставки') }}</strong></p> --}}
                                                                        <h5>{{ __('Детали доставки') }}</h5>
                                                                        <div class="row " >
                                                                            <div class="col-lg-6">
                                                                                <span><strong>{{ __('Получатель') }}</strong>: {{ $order->shipping_name == null ? $order->customer_name : $order->shipping_name}}</span>
                                                                            </div>
                                                                            <div class="col-lg-6">
                                                                                <span><strong>{{ __('Город') }}</strong>: {{ $order->shipping_city == null ? $order->customer_city : $order->shipping_city}}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-lg-6">
                                                                                <span ><strong>{{ __('Контакты') }}</strong>: {{ $order->shipping_phone == null ? $order->customer_phone : $order->shipping_phone }}</span><br>
                                                                            </div>
                                                                            <div class="col-lg-6">
                                                                                <span><strong>{{ __('Адрес') }}</strong>: {{ $order->shipping_address == null ? $order->customer_address : $order->shipping_address }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-lg-6 ">
                                                                                @php 
                                                                                $d_time = explode('-', $order->shipping_time)
                                                                                @endphp
                                                                              <span><strong>{{ __('Дата доставки') }}</strong>: {{ $order->shipping_date }}</span>
                                                                            </div>
                                                                            <div class="col-lg-6 ">
                                                                                <span><strong>{{ __('Представитель') }}</strong>: {{__(' Anor.tj') }}</span><br>
                                                                            </div>
                                                                        </div>  
                                                                         
                                                                        @if($d_time[0] =='during')
                                                                            <span><strong>{{ __('Время доставки') }}</strong>: {{__('В течение дня')}}</span>
                                                                            @else
                                                                            <span><strong>{{ __('Время доставки') }}</strong>: c {{$d_time[0]}} до {{$d_time[1]}}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                            
                                                                    @endif
                                            
                                                                {{-- <div class="col-lg-6">
                                                                        <div class="buyer">
                                                                            <p><strong>{{ __('Billing Details') }}</strong></p>
                                                                            <span><strong>{{ __('Customer Name') }}</strong>: {{ $order->customer_name}}</span><br>
                                                                            <span><strong>{{ __('Address') }}</strong>: {{ $order->customer_address }}</span><br>
                                                                            <span><strong>{{ __('City') }}</strong>: {{ $order->customer_city }}</span><br>
                                                                            <span><strong>{{ __('Country') }}</strong>: {{ $order->customer_country }}</span>
                                                                        </div>
                                                                </div> --}}
                                                            </div>

                                                            {{-- <div class="billing-add-area">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <h5>{{ $langg->lang287 }}</h5>
                                                                        <address>
                                                                            {{ $langg->lang288 }} {{$order->customer_name}}<br>
                                                                            {{ $langg->lang289 }} {{$order->customer_email}}<br>
                                                                            {{ $langg->lang290 }} {{$order->customer_phone}}<br>
                                                                            {{ $langg->lang291 }} {{$order->customer_address}}<br>
                                                                            {{$order->customer_city}}-{{$order->customer_zip}}
                                                                        </address>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h5>{{ $langg->lang292 }}</h5>
                                                                        @if($order->method=="Installments")
                                                                            <p>{{ $langg->lang294 }} {{$order->method =="Installments" ? "В рассрочку":$order->method}}</p>
                                                                            <p>Предоплата : (10%) {{$order->currency_sign}} {{$order->installments_prepay}} </p>
                                                                            <p>Ежемесячный платеж ({{$order->installments_for}} мес) : {{$order->currency_sign}} {{$order->installments_month_pay}}</p>
                                                                        @else
                                                                            @if($order->method == "Cash On Delivery")
                                                                                <p>{{ __('К оплате') }} {{$order->currency_sign}} {{ $order->pay_amount  }}</p> --}}
                                                                                {{-- <p>{{ $langg->lang293 }} {{$order->currency_sign}} {{ $order->pay_amount}}</p> --}}
                                                                                {{-- <p>{{ $langg->lang294 }} {{$order->method}}</p>
                                                                                @else   
                                                                                <p>{{ $langg->lang293 }} {{$order->currency_sign}} {{ $order->pay_amount }}</p> --}}
                                                                                {{-- <p>{{ $langg->lang293 }} {{$order->currency_sign}} {{ $order->pay_amount}}</p> --}}
                                                                                {{-- <p>{{ $langg->lang294 }} {{$order->method}}</p> --}}
                                                                            {{-- @endif
                                                                            @if($order->method != "Cash On Delivery")
                                                                                @if($order->method=="Stripe")
                                                                                    {{$order->method}} {{ $langg->lang295 }} <p>{{$order->charge_id}}</p>
                                                                                @endif
                                                                                @if($order->method=="Paypal")
                                                                                {{$order->method}} {{ $langg->lang296 }} <p id="ttn">{{ isset($_GET['tx']) ? $_GET['tx'] : '' }}</p>
                                                                                @else
                                                                                {{$order->method}} {{ $langg->lang296 }} <p id="ttn">{{$order->txnid}}</p>
                                                                                @endif
                                                                            @endif
                                                                        @endif    
                                                                    </div>
                                                                </div>
                                                            </div> --}}
                                    @endif
                                                            <br>
                                                            <div class="table-responsive">
                                                                <table  class="table">
                                                                    {{-- <h4 class="text-center">{{ $langg->lang308 }}</h4> --}}
                                                                    <thead>
                                                                    <tr>

                                                                        <th width="40%">{{ $langg->lang310 }}</th>
                                                                        <th width="20%">{{ $langg->lang539 }}</th>
                                                                        <th width="20%">{{ $langg->lang314 }}</th>
                                                                        <th width="20%">{{ $langg->lang315 }}</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                    @foreach($tempcart->items as $product)
                                                                        <tr>

                                                                                <td>{{ $product['item']['name'] }}</td>
                                                                                <td>
                                                                                    <b>{{ $langg->lang311 }}</b>: {{$product['qty']}} <br>
                                                                                    @if(!empty($product['size']))
                                                                                    <b>{{ $langg->lang312 }}</b>: {{ $product['item']['measure'] }}{{$product['size']}} <br>
                                                                                    @endif
                                                                                    @if(!empty($product['color']))
                                                                                    <div class="d-flex mt-2">
                                                                                    <b>{{ $langg->lang313 }}</b>:  <span id="color-bar" style="border: 10px solid #{{$product['color'] == "" ? "white" : $product['color']}};"></span>
                                                                                    </div>
                                                                                    @endif

                                                                                        @if(!empty($product['keys']))

                                                                                        @foreach( array_combine(explode(',', $product['keys']), explode(',', $product['values']))  as $key => $value)

                                                                                            <b>{{ ucwords(str_replace('_', ' ', $key))  }} : </b> {{ $value }} <br>
                                                                                        @endforeach

                                                                                        @endif

                                                                                    </td>
                                                                                <td>{{$product['item']['price']}} {{$order->currency_sign}}</td>
                                                                                <td>{{$product['price']}} {{$order->currency_sign}}</td>

                                                                        </tr>

                                                                        
                                                                    @endforeach
                                                                    @if($order->shipping_cost != 0  && $order->pay_amount < 50)
                                                                    <tr>
                                                                        <td colspan="2"></td>
                                                                        <td class="font-weight-bold">{{ __('Доставка:') }}</td>
                                                                        <td>{{ $order->shipping_cost }} {{$order->currency_sign}}</td>
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                    <tr>
                                                                        <td colspan="2"></td>
                                                                        <td class="font-weight-bold">{{ __('Итого:') }}</td>
                                                                        <td>{{$order->pay_amount }} {{$order->currency_sign}}
                                                                        </td>
                                                                    </tr>


                                                                    </tbody>
                                                                </table>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>

                               </div>
                            <div>
                        </div>
                    </div>
                <!-- Ending of Dashboard data-table area -->
            </div>

@endif

  </section>

@endsection