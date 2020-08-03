@extends('layouts.front')

@section('content')

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
                        <a href="{{ route('user-forgot') }}">
                            {{ $langg->lang190 }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<section class="login-signup forgot-password">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="login-area">
                    <div class="header-area forgot-passwor-area">
                        <h4 class="title">{{ $langg->lang191 }} </h4>
                        <p class="text">{{ $langg->lang192 }} </p>
                    </div>
                    <div class="login-form">
                        @include('includes.admin.form-login')
                        <form id="forgotform" action="{{route('user-forgot-submit')}}" method="POST">
                            {{ csrf_field() }}
                            <div class="form-input">
                                <input type="text" name="phone" id="mobile" class="User Name" placeholder="{{ $langg->lang184 }}"
                                    required="">
                                <i class="icofont-phone"></i>
                            </div>
                            <div class="to-login-page">
                                <a href="{{ route('user.login') }}">
                                    {{ $langg->lang194 }}
                                </a>
                            </div>
							
							
							
							 <!-- SMS Verification Start -->

                                <div class="container ">
                                    <div class="container_hide">
                                    <div class="error1  text-danger text-center"></div>

                                    <input type="button" class="submit-btn" id="button_2" value="Код подтверждения"
                                        onclick="sendOTP();">
                                    </div>
                                </div>
                                <div class="success">
                                    <input type="hidden" id="ver_success" name="custId" value="0">
                                </div>
                                
                                <div class="container2" style="display: none;">
                            
                                    <div class="form-row">
                                    <label>Код подтверждения отправлен на ваш мобильный номер</label>
                                    </div>

                                    <div class="form-row" id="sucDIV">
                                    <input type="text" class="input_validator MustField" id="mobileOtp" name="message_sms" maxlength="6"
                                        class="form-input" placeholder="Введите код" required style="width: 100%;
                                                                                                        height: 50px;
                                                                                                        background: #f3f8fc;
                                                                                                        padding: 0px 30px 0px 45px;
                                                                                                        border: 1px solid rgba(0, 0, 0, 0.1);
                                                                                                        font-size: 14px;">
                                    <input type="hidden" id="ver_error" name="custId" value="0">
                                    </div>
                                    <div class="error2 text-danger text-center"></div>
                                    <div class="form-row d-flex justify-content-center">
                                    <input id="verify" type="button" class="submit-btn w-50" value="Подтвердить" onclick="verifyOTP();">
                                    </div>
                                    <div class="form-row d-flex justify-content-center">
                                    <label class="configure text-center">Если вы не получили СМС с кодом : </label>

                                    <button id="resend" type="button" class="submit-btn w-50" onclick="resendOTP();">Отправить код
                                        повторно</button>

                                    <p id="timer"></p>

                                    </div>

                                </div>


                                <!-- SMS Verification End -->
							
                            <input class="authdata" type="hidden" value="{{ $langg->lang195 }}">
                            <button type="submit" class="submit-btn" id="register_id">{{ $langg->lang196 }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection