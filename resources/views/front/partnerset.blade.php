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
                        <a href="{{ route('front.partnerset') }}">
                            Как начать продавать
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb Area End -->


<div class="container">
    <div class="container mt-5">
        <div class="row justify-content-md-center">
            <div class="col-sm-6">
                <h3>Продавайте больше — вместе с Анор</h3>
                <p>Получите новый канал онлайн-продаж — начните работать c маркетплейсом ЗАО «Шабакаи Аср».</p>

                <a href="javascript:;" data-toggle="modal" data-target="#vendor-login"
                    class="sell-btn btn-primery prodat">Продавать</a>

            </div>
            <div class="col-sm-6 d-flex justify-content-center" id="box2_anor">
                <div id="stage" class="box_anor">
                    <div class="spinner">
                        <div class="face1"><img src="assets/images/anor_logo.png" alt="image"></div>
                        <div class="face2"><img src="assets/images/anor_logo.png" alt="image"></div>
                        <div class="face3"><img src="assets/images/anor_logo.png" alt="image"></div>
                        <div class="face4"><img src="assets/images/anor_logo.png" alt="image"></div>
                        <div class="face5"><img src="assets/images/anor_logo.png" alt="image"></div>
                        <div class="face6"><img src="assets/images/anor_logo.png" alt="image"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="col-12">
        <h3>Что такое маркетплейс Анор</h3>
        <p>Анор — это площадка, на которой представлены товары разных продавцов, производителей и дистрибьюторов. За
            счёт широкого ассортимента люди могут купить здесь разные товары: например, корм для кошки, стиральный
            порошок, наушники или всё это сразу. Присоединяйтесь к числу партнёров, и Анор будет работать над тем,
            чтобы ваши товары оказались у покупателя.</p>
    </div>
</div>
<div class="container" style="margin-top: 30px; margin-bottom: 50px">
    <div class="row ">
        <div class="col-sm-4 justify-content-center">
            <div class="d-flex align-items-center justify-content-center">
                <img style="width: 80px; height: 80px" src="assets/images/speaker_1.png" alt="anor">
            </div>
            <div>
                <h3 class="text-center">Привлечёт и удержит</h3></br></br>
                <p>Маркетплейс берёт на себя привлечение аудитории с помощью маркетинга и рекламы.
                    После заказа люди получают Анор Бонусы — скидки или бесплатную доставку для будущих покупок.
                    Расплатиться можно наличными, банковской картой (НПС “Корти милли” всех банков Республики
                    Таджикистан) онлайн или при получении, или в рассрочку.</p>
            </div>
        </div>
        <div class="col-sm-4 justify-content-center">
            <div class="d-flex align-items-center justify-content-center">
                <img style="width: 80px; height: 80px" src="assets/images/card_1.png" alt="anor">
            </div>
            <div>
                <h3 class="text-center"> Примет заказы</h3></br></br>
                <p>Общение с покупателями ведёт маркетплейс. Например, подтверждает заказы, назначает время доставки
                    и помогает, если что-то пошло не так.</p>
            </div>
        </div>
        <div class="col-sm-4 justify-content-center">
            <div class="d-flex align-items-center justify-content-center">
                <img style="width: 80px; height: 80px" src="assets/images/delivery_1.png" alt="anor">
            </div>
            <div>
                <h3 class="text-center"> Доставит покупки</h3></br></br>
                <p>Доставка до покупателя — это задача Анор. Доставка всех заказов независимо от габарита
                    осуществляется бесплатно если ее сумма составляет больше 50 сомони.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection