@push('index')
  <meta name="keywords" content="{{ $seo->meta_keys }}">
	<meta name="description" content="{{ $seo->meta_description }}">
	<meta name="author" content="Anor"> 
@endpush  
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
                        <a href="{{ route('front.oplatainfo') }}">
                            Оплата и доставка
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb Area End -->
   
    
    <div class="container mt-5 mb-5">
        <h3 class="col-12 text-center mb-4">Платите сразу или потом</h3>
            <div class="row col-12 justify-content-center">
                <div class="col-sm-4 justify-content-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <img style="width: 80px; height: 80px" src="assets/images/online_1.png" alt="Оплата">
                    </div>
                        <div>
                            <h4 class="text-center">Картой или наличными</h4> 
                            <p class="text-center"> Расплатиться можно наличными, банковской картой (НПС “Корти милли” всех 
                            банков Республики Таджикистан) онлайн или при получении.</p>
                        </div>
                </div>
                <div class="col-sm-4 justify-content-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <img style="width: 80px; height: 80px" src="assets/images/credit_1.png" alt="Рассрочка">
                    </div>
                    <div>
                        <h4 class="text-center">В кредит</h4> 
                        <p class="text-center">Покупайте товары на сумму от 400 сомони сейчас, а платите в любое удобное время.</p>
                    </div>
                <div>           
            </div>
        </div>   
    </div> 
        <h3 class="col-12 text-center mb-4">Удобная доставка</h3>
            <div class="row col-12 justify-content-center">
                   <div class="col-sm-4 justify-content-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <img style="width: 80px; height: 80px" src="assets/images/door_delivery.png" alt="Заказ">
                    </div>
                        <div>
                            <h4 class="text-center">До двери</h4> 
                            <p class="text-center"> При оформлении заказа выберите дату и время доставки. 
                            Курьер позвонит и привезет заказ, куда нужно.</p>
                        </div>
                </div>
                <div class="col-sm-4 justify-content-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <img  style="width: 80px; height: 80px" src="assets/images/delivery_2.png" alt="Доставка">
                    </div>
                    <div>
                        <h4 class="text-center">Крупногабаритные заказы</h4> 
                        <p class="text-center">Их мы тоже доставим. Главное, чтобы автомобиль мог подъехать
                         к месту доставки, а товар проходил в двери.</p>
                    </div>
                <div>
            </div>    
        </div>
    </div>
</div>

@endsection
