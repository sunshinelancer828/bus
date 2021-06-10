@extends('layouts.front')

@section('content')

<section class="login-signup">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <nav class="comment-log-reg-tabmenu">
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link login active" id="nav-log-tab" data-toggle="tab" href="#nav-log" role="tab"
              aria-controls="nav-log" aria-selected="true">
             User {{ $langg->lang198 }}
            </a>
            <a class="nav-item nav-link" id="nav-reg-tab" data-toggle="tab" href="#nav-reg" role="tab"
              aria-controls="nav-reg" aria-selected="false">
               {{ $langg->lang235 }}
            </a>
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-log" role="tabpanel" aria-labelledby="nav-log-tab">
            <div class="login-area">
              <div class="header-area">
                <h4 class="title">User {{ $langg->lang181 }}</h4>
              </div>
              <div class="login-form signin-form">
                @include('includes.admin.form-login')
              
                 <form class="mregisterform" action="{{route('user-register-submit')}}" method="POST">
                  {{ csrf_field() }}

                  <div class="form-input">
                    <input type="text" class="User Name" name="name" placeholder="{{ $langg->lang182 }}" required="">
                    <i class="icofont-user-alt-5"></i>
                  </div>

                  <div class="form-input">
                    <input type="email" class="User Name" name="email" placeholder="{{ $langg->lang183 }}" required="">
                    <i class="icofont-email"></i>
                  </div>

                  <div class="form-input">
                    <input type="tel" class="User Name" id="phone_valid" name="phone" placeholder="{{ $langg->lang184 }}" required="">
                    <i class="icofont-phone"></i>
                  </div>

                  <!--div class="form-input">
                    <input type="text" class="User Name" name="address" placeholder="{{ $langg->lang185 }}" required="">
                    <i class="icofont-location-pin"></i>
                  </div-->

                  <div class="form-input">
                    <input type="password" class="Password" name="password" placeholder="{{ $langg->lang186 }}"
                      required="">
                    <i class="icofont-ui-password"></i>
                  </div>

                  <div class="form-input">
                    <input type="password" class="Password" name="password_confirmation"
                      placeholder="{{ $langg->lang187 }}" required="">
                    <i class="icofont-ui-password"></i>
                  </div>

                  @if($gs->is_capcha == 1)

                  <ul class="captcha-area">
                    <li>
                      <p><img class="codeimg1" src="{{asset("assets/images/capcha_code.png")}}" alt="">
                  <i class="fas fa-sync-alt pointer refresh_code"></i></p>
                    </li>
                  </ul>

                  <div class="form-input">
                    <input type="text" class="Password" name="codes" placeholder="{{ $langg->lang51 }}" required="">
                    <i class="icofont-refresh"></i>
                  </div>

                  @endif
                  
                  <div class="form-input">
                      <input type="checkbox" name="terms_and_condition" id="terms_and_condition" class="form-check-input">
                      <label class="form-check-label" for="terms_and_condition">I agree to the <a href="{{ url('terms') }}" target="_blank">Terms & Condition</a></label>
                  </div>

                  <input class="mprocessdata" type="hidden" value="{{ $langg->lang188 }}">
                  <button type="submit" class="submit-btn">{{ $langg->lang189 }}</button>

                </form>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="nav-reg" role="tabpanel" aria-labelledby="nav-reg-tab">
            <div class="login-area signup-area">
              <div class="header-area">
                <h4 class="title">Vendor {{ $langg->lang181 }}</h4>
              </div>
              <div class="login-form signup-form">
                @include('includes.admin.form-login')
                 <form class="mregisterform" action="{{route('user-register-submit')}}" method="POST">
                          {{ csrf_field() }}
                          <div class="row">
                          <div class="col-lg-6">
                            <div class="form-input">
                                <input type="text" class="User Name" name="name" placeholder="{{ $langg->lang182 }}" required="">
                                <i class="icofont-user-alt-5"></i>
                              </div>
                           </div>

                           <div class="col-lg-6">
                              <div class="form-input">
                                <input type="email" class="User Name" name="email" placeholder="{{ $langg->lang183 }}" required="">
                                <i class="icofont-email"></i>
                            </div>

                            </div>
                           <div class="col-lg-6">
                                 <div class="form-input">
                                <input type="tel" class="User Name valid_phone" name="phone" placeholder="{{ $langg->lang184 }}" required="">
                                <i class="icofont-phone"></i>
                            </div>

                            </div>
                           <div class="col-lg-6">
                              <div class="form-input">
                                <input type="text" class="User Name" name="address" placeholder="{{ $langg->lang185 }}" required="">
                                <i class="icofont-location-pin"></i>
                            </div>
                            </div>

                           <div class="col-lg-6">
                              <div class="form-input">
                                <input type="text" class="User Name" name="city" placeholder="City" required="">
                                <i class="icofont-cart-alt"></i>
                            </div>

                            </div>
                           <div class="col-lg-6">
                            <div class="form-input">
                                <input type="text" class="User Name" name="country" placeholder="Country" required="">
                                <i class="icofont-cart"></i>
                            </div>
                            </div>
              <div class="col-lg-6">
                            <div class="form-input">
                                <input type="text" class="User Name" name="shop_name" placeholder="Store Name" required="">
                                <i class="icofont-cart-alt"></i>
                            </div>

                            </div>
              <div class="col-lg-6">

                            <div class="form-input">
                                <input type="text" class="User Name" name="owner_name" placeholder="Store Owner" required="">
                                <i class="icofont-cart"></i>
                            </div>
                            </div>
                           {{--<div class="col-lg-6">
                            <div class="form-input">
                                <input type="text" class="User Name" name="shop_number" placeholder="{{ $langg->lang240 }}" required="">
                                <i class="icofont-shopping-cart"></i>
                            </div>
                            </div>
                           <div class="col-lg-6">
                             <div class="form-input">
                                <input type="text" class="User Name" name="shop_address" placeholder="{{ $langg->lang241 }}" required="">
                                <i class="icofont-opencart"></i>
                            </div>
                            </div>
                           <div class="col-lg-6">
                            <div class="form-input">
                                <input type="text" class="User Name" name="reg_number" placeholder="{{ $langg->lang242 }}" required="">
                                <i class="icofont-ui-cart"></i>
                            </div>
                            </div>
                           <div class="col-lg-6">
                                <div class="form-input">
                                <input type="text" class="User Name" name="shop_message" placeholder="{{ $langg->lang243 }}" required="">
                                <i class="icofont-envelope"></i>
                            </div>
                            </div>--}}

                           <div class="col-lg-6">
                            <div class="form-input">
                                <input type="password" class="Password" name="password" placeholder="{{ $langg->lang186 }}" required="">
                                <i class="icofont-ui-password"></i>
                            </div>

                            </div>
                           <div class="col-lg-6">
                <div class="form-input">
                                <input type="password" class="Password" name="password_confirmation" placeholder="{{ $langg->lang187 }}" required="">
                                <i class="icofont-ui-password"></i>
                              </div>
                            </div>

                            @if($gs->is_capcha == 1)
                             <div class="col-lg-6">
                                <ul class="captcha-area">
                                    <li>
                                      <p>
                                        <img class="codeimg1" src="{{asset("assets/images/capcha_code.png")}}" alt="capcha"> <i class="fas fa-sync-alt pointer refresh_code "></i>
                                      </p>
    
                                    </li>
                                </ul>
                            </div>
                             <div class="col-lg-6">
                                 <div class="form-input">
                                    <input type="text" class="Password" name="codes" placeholder="{{ $langg->lang51 }}" required="">
                                    <i class="icofont-refresh"></i>
                                </div>
                          </div>
                          @endif
                          
                             <div class="col-lg-12">
                <div class="form-input">
                                  <input type="checkbox" name="terms_and_condition" id="terms_and_condition_2" class="form-check-input">
                                  <label class="form-check-label" for="terms_and_condition_2">I agree to the <a href="{{ url('terms') }}" target="_blank">Terms & Condition</a> and <a href="{{ url('Vendor Agreement') }}" target="_blank">Vendor Agreement</a></label>
                                </div>
                            </div>

                    <input type="hidden" name="vendor"  value="1">
                            <input class="mprocessdata" type="hidden"  value="{{ $langg->lang188 }}">
                            <button type="submit" class="submit-btn">{{ $langg->lang189 }}</button>
                            </div>
                        </form>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</section>
@endsection 

@section('scripts')
<script>
var phone_number = window.intlTelInput(document.querySelector("#phone_valid"), {
  separateDialCode: true,
  preferredCountries:["ng"],
  hiddenInput: "full",
  utilsScript: "//cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/utils.js"
});
// $('.mregisterform').on('submit',function(e){
//     var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
//     $("input[name='phone']").val(full_number);
// });
</script>
@endsection

