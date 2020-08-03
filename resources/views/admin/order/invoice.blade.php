@extends('layouts.admin')

@section('content')
<div class="content-area">
    <div class="mr-breadcrumb">
        <div class="row">
            <div class="col-lg-12">
                <h4 class="heading">{{ __('Order Invoice') }} <a class="add-btn" href="javascript:history.back();"><i
                            class="fas fa-arrow-left"></i> {{ __('Back') }}</a></h4>
                <ul class="links">
                    <li>
                        <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                    </li>
                    <li>
                        <a href="javascript:;">{{ __('Orders') }}</a>
                    </li>
                    <li>
                        <a href="javascript:;">{{ __('Invoice') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="order-table-wrap">
        <div class="invoice-wrap">
            <div class="invoice__title">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="invoice__logo text-left">
                           <img src="{{ asset('assets/images/'.$gs->invoice_logo) }}" alt="woo commerce logo">
                        </div>
                    </div>
                    <div class="col-lg-6 text-right">
                        <a class="btn  add-newProduct-btn print" href="{{route('admin-order-print',$order->id)}}"
                        target="_blank"><i class="fa fa-print"></i> {{ __('Print Invoice') }}</a>
                    </div>
                </div>
            </div>
            <br>
            <div class="row invoice__metaInfo mb-4">
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
            <div class="row invoice__metaInfo">
                    @if($order->dp == 0)
                    <div class="col-lg-6 " style="flex: 1 0 50%;max-width:100%">
                        <div class="invoice__orderDetails w-100" style="margin-top:5px;">
                            <p><strong>{{ __('Детали доставки') }}</strong></p>
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
                                <span><strong>{{ __('Время доставки') }}</strong>: {{__('В течении дня')}}</span>
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

            <div class="row">
                <div class="col-sm-12">
                    <div class="invoice_table">
                        <div class="mr-table">
                            <div class="table-responsive">
                                <table id="example2" class="table table-hover dt-responsive" cellspacing="0"
                                    width="100%" >
                                    <thead>
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
                                                <a target="_blank"
                                                    href="{{ route('front.product', $product['item']['slug']) }}">{{ $product['item']['name']}}</a>
                                                @else
                                                <a href="javascript:;">{{$product['item']['name']}}</a>
                                                @endif

                                                @else
                                                <a href="javascript:;">{{ $product['item']['name']}}</a>

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
                                                        <strong>{{ __('Цвет') }} :</strong> <span
                                                        style="width: 40px; height: 20px; display: block; background: #{{$product['color']}};"></span>
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




                                      
                                            <td>{{ $product['price']  }} {{$order->currency_sign}}
                                            </td>
                                            @php
                                            $subtotal += $product['price'];
                                            @endphp

                                        </tr>

                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="2">{{ __('Промежуточный итог:') }}</td>
                                            <td>{{ $subtotal }} {{$order->currency_sign}}</td>
                                        </tr>
                                        @if($order->shipping_cost != 0  && $order->pay_amount < 50)
                                        <tr>
                                            <td colspan="2">{{ __('Стоимость доставки:') }}</td>
                                            <td>{{ $order->shipping_cost }} {{$order->currency_sign}}</td>
                                        </tr>
                                        @endif

                                        @if($order->packing_cost != 0)
                                        <tr>
                                            <td colspan="2">{{ __('Packaging Cost') }}({{$order->currency_sign}})</td>
                                            <td>{{ round($order->packing_cost , 2) }}</td>
                                        </tr>
                                        @endif

                                        @if($order->tax != 0)
                                        <tr>
                                            <td colspan="2">{{ __('TAX') }}({{$order->currency_sign}})</td>
                                            @php
                                            $tax = ($subtotal / 100) * $order->tax;
                                            @endphp
                                            <td>{{round($tax, 2)}}</td>
                                        </tr>
                                        @endif
                                        @if($order->coupon_discount != null)
                                        <tr>
                                            <td colspan="2">{{ __('Coupon Discount') }}({{$order->currency_sign}})</td>
                                            <td>{{round($order->coupon_discount, 2)}}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td colspan="1"></td>
                                            <td>{{ __('Итого: ') }}</td>
                                            <td>{{$order->currency_sign}}{{$order->pay_amount }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Main Content Area End -->
</div>
</div>
</div>

@endsection