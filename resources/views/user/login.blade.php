@extends('layouts.front')

@section('content')

<section class="login-signup">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <nav class="comment-log-reg-tabmenu">
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link login active  w-100" id="nav-log-tab" data-toggle="tab" href="#nav-log" role="tab"
              aria-controls="nav-log" aria-selected="true">
              {{ $langg->lang197 }}
            </a>
            {{-- <a class="nav-item nav-link" id="nav-reg-tab" data-toggle="tab" href="#nav-reg" role="tab"
              aria-controls="nav-reg" aria-selected="false">
              {{ $langg->lang198 }}
            </a> --}}
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-log" role="tabpanel" aria-labelledby="nav-log-tab">
            <div class="login-area">
              <div class="header-area">
                <h4 class="title">{{ $langg->lang172 }}</h4>
              </div>
              <div class="login-form signin-form">
                @include('includes.admin.form-login')
                  @if($errors->any())
                      <div id="socialError">
                        <div  class="alert alert-danger  validation">
                          <button type="button" class="close alert-close"><span>×</span></button>
                            <p class="text-left">{{$errors->first()}}</p> 
                        </div>
                      </div>
                  @endif
              <form class="motpform" id ="motpform" action="{{ route('user.loginotp.submit') }}" method="POST">
                {{ csrf_field() }}
                <div  id="otp_login">
                    <div class="form-input" id="phone_login">
                      <input type="text" name="phone" maxlength="9" id="phone_number" placeholder="{{ $langg->lang184 }}" required="">
                      <i class="icofont-phone"></i>
                    </div>
                    <button type="submit" class="submit-btn" onclick="sendLoginOtp();">{{ __('Отправить код') }}</button>
                </div>
              </form>
              <div  class="d-none" id="mloginform">
                @include('includes.admin.form-login')
                  <form class="mloginform " action="{{ route('user.login.submit') }}" method="POST">
                    {{ csrf_field() }}
                    {{-- <div  id="otp_login">
                        <div class="form-input" id="phone_login">
                          <input type="text" name="phone" maxlength="9" id="phone_id" placeholder="{{ $langg->lang184 }}" required="">
                          <i class="icofont-phone"></i>
                        </div>
                        <button type="button" class="submit-btn" onclick="sendLoginOtp();">{{ __('Отправить код') }}</button>
                    </div> --}}
                    {{-- <div class="form-input">
                      <input type="password" class="Password" name="password" placeholder="{{ $langg->lang174 }}"
                        required="">
                      <i class="icofont-ui-password"></i>
                    </div> --}}
                    <div class="form-input"  >
                      <input type="hidden" id="phone_id" name="phone">
                    </div>
                   
                    <div class="form-input" id="otp_login" >
                      <input type="password" class="Password" maxlength="10" name="password" placeholder="{{ __('Код отправленный на ваш номер')}}"
                        required="">
                      <i class="icofont-ui-password"></i>
                    </div>
                    <div class="" id="submit_login" >
                        {{-- <div class="form-forgot-pass">
                          <div class="left">
                            <input type="checkbox" name="remember" id="mrp" {{ old('remember') ? 'checked' : '' }}>
                            <label for="mrp">{{ $langg->lang175 }}</label>
                          </div>
                          <div class="right">
                            <a href="{{ route('user-forgot') }}">
                              {{ $langg->lang176 }}
                            </a>
                          </div>
                        </div> --}}
                        <input type="hidden" name="modal" value="1">
                        <input class="mauthdata" type="hidden" value="{{ $langg->lang177 }}">
                        {{-- <button type="submit" class="submit-btn">{{ $langg->lang178 }}</button> --}}
                        <button type="submit" class="submit-btn">{{ __('Продолжить') }}</button>
                    </div>
                    
               
                  </form>
                </div>
                @if(App\Models\Socialsetting::find(1)->f_check == 1 || App\Models\Socialsetting::find(1)->g_check ==
                1)
                <div class="social-area">
                  <h3 class="title">{{ $langg->lang179 }}</h3>
                  <p class="text">{{ $langg->lang180 }}</p>
                  <ul class="social-links">
                    @if(App\Models\Socialsetting::find(1)->f_check == 1)
                    <li>
                      <a href="{{ route('social-provider','facebook') }}">
                        <i class="fab fa-facebook-f"></i>
                      </a>
                    </li>
                    @endif
                    @if(App\Models\Socialsetting::find(1)->g_check == 1)
                    <li>
                      <a href="{{ route('social-provider','google') }}">
                        <i class="fab fa-google-plus-g"></i>
                      </a>
                    </li>
                    @endif
                  </ul>
                </div>
                @endif
              </div>
            </div>
          </div>


          {{--------------------- Reigistration form area ------- --}}
          
          {{-- <div class="tab-pane fade" id="nav-reg" role="tabpanel" aria-labelledby="nav-reg-tab">
            <div class="login-area signup-area">
              <div class="header-area">
                <h4 class="title">{{ $langg->lang181 }}</h4>
              </div>
              <div class="login-form signup-form">
                @include('includes.admin.form-login')

                <form class="mregisterform" action="{{route('user-register-submit')}}" method="POST">
               {{ csrf_field() }} 
                  <div class="form-input">
                    <input type="text" class="User Name" name="name" placeholder="{{ $langg->lang182 }}" required="">
                    <i class="icofont-user-alt-5"></i>
                  </div> --}}

                 <!--   {{-- <div class="form-input">
                    <input type="email" class="User Name" name="email" placeholder="{{ $langg->lang183 }}" required="">
                  <i class="icofont-email"></i>
                         </div> --}} -->
{{-- 


              <div class="form-input">
                <input type="text" class="User Name" name="address" placeholder="{{ $langg->lang185 }}" required="">
                <i class="icofont-location-pin"></i>
              </div>

              <div class="form-input">
                <input type="password" class="Password" name="password" placeholder="{{ $langg->lang186 }}" required="">
                <i class="icofont-ui-password"></i>
              </div>

              <div class="form-input">
                <input type="password" class="Password" name="password_confirmation" placeholder="{{ $langg->lang187 }}"
                  required="">
                <i class="icofont-ui-password"></i>
              </div>
            
              <div class="row  ">
                <div class=" col-md-4">
                  <div class="form-input">
                 
                    <input type="text" id="contry_code" class="User Name" name="contry_code"  placeholder="Код страны"
                     value="+992"  readonly required>
                    <i class="icofont-flag"></i>
                  </div>
                </div>
               
                <div class=" col-md-8">
                  <div class="form-input ">
                    <input type="text" id="mobile" class="User Name" name="phone"  placeholder="{{ $langg->lang184 }}"
                      required="" maxlength="9">
                    <i class="icofont-phone"></i>
                  </div>
                </div>
               
               
              </div>
                  --}}

              <!-- SMS Verification Start -->

{{-- 
              <div class="container ">
                <div class="container_hide">
                  <div class="error1 text-danger text-center"></div>

                  <input type="button" class="submit-btn" id="button_2" value="Отправить код подтверждения"
                    onclick="sendOTP();">
                </div>
              </div>
              <div class="success">
                <input type="hidden" id="ver_success" name="custId" value="0">
              </div>
              
              <div class="container2" style="display: none;">
        
                <div class="form-row p-2">
                  <label class="text-center">Код подтверждения отправлен на ваш мобильный номер</label>
                </div>

                <div class="form-row" id="sucDIV">
                  <input type="text" class="input_validator MustField Password" id="mobileOtp" name="message_sms" maxlength="6"
                    class="form-input" placeholder="Введите код" required style="width: 100%;
																					height: 50px;
																					background: #f3f8fc;
																					padding: 0px 30px 0px 45px;
																					border: 1px solid rgba(0, 0, 0, 0.1);
																					font-size: 14px;">
                  <input type="hidden" id="ver_error" name="custId" value="0">
                </div>
                <div class="error2 text-danger text-center"></div>
                <div class="row d-flex justify-content-center">
                  <input id="verify" type="button" class="submit-btn w-50" value="Проверить" onclick="verifyOTP();">
                </div>
                <div class="form-row d-flex justify-content-center">
                  <label class="configure">Если вы не получили СМС с кодом : </label>

                  <button id="resend" type="button" class="submit-btn w-50" onclick="resendOTP();">Отправить код
                    повторно</button>

                  <p id="timer"></p>

                </div>

              </div> --}}


              <!-- SMS Verification End -->


              {{-- @if($gs->is_capcha == 1)

              <ul class="captcha-area">
                <li>
                  <p><img class="codeimg1" src="{{asset("assets/images/capcha_code.png")}}" alt=""> <i
                class="fas fa-sync-alt pointer refresh_code "></i></p>
              </li>
              </ul>

              <div class="form-input">
                <input type="text" class="Password" name="codes" placeholder="{{ $langg->lang51 }}" required="">
                <i class="icofont-refresh"></i>
              </div>

              @endif --}}
          {{-- 
              <input class="mprocessdata" type="hidden" value="{{ $langg->lang188 }}">
              <button type="submit" class="submit-btn" id="register_id">{{ $langg->lang189 }}</button>

              </form>
             </div>
            </div>
         </div> --}}
      </div>

    </div>

  </div>
  </div>
</section>

@endsection