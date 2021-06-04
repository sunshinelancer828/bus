
@if($ps->hot_sale == 1)

<!-- Clothing and Apparel Area Start -->
<section class="product-tab">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 remove-padding">
				<div class="section-top">
					<h2 class="section-title">
						{{ $langg->lang832 }}
					</h2>
					<ul class="nav">
						<li class="nav-item">
							<a class="nav-link active" id="pills-tab1-tab" data-toggle="pill" href="#pills-tab1" role="tab" aria-controls="pills-tab1" aria-selected="false">{{ $langg->lang30 }}</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pills-tab2-tab" data-toggle="pill" href="#pills-tab2" role="tab" aria-controls="pills-tab2" aria-selected="true">{{ $langg->lang31 }}</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pills-tab3-tab" data-toggle="pill" href="#pills-tab3" role="tab" aria-controls="pills-tab3" aria-selected="false">{{ $langg->lang32 }}</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pills-tab4-tab" data-toggle="pill" href="#pills-tab4" role="tab" aria-controls="pills-tab4" aria-selected="false">{{ $langg->lang33 }}</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="tab-content">
					<div class="tab-pane fade active show" id="pills-tab1" role="tabpanel" aria-labelledby="pills-tab1-tab">
						<div class="row">
								@foreach($hot_products as $prod)
									@include('includes.product.list-product')
								@endforeach
						</div>
					</div>
					<div class="tab-pane fade" id="pills-tab2" role="tabpanel" aria-labelledby="pills-tab2-tab">
						<div class="row">
								@foreach($latest_products as $prod)
									@include('includes.product.list-product')
								@endforeach
						</div>
					</div>
					<div class="tab-pane fade" id="pills-tab3" role="tabpanel" aria-labelledby="pills-tab3-tab">
						<div class="row">
								@foreach($trending_products as $prod)
									@include('includes.product.list-product')
								@endforeach
						</div>
					</div>
					<div class="tab-pane fade" id="pills-tab4" role="tabpanel" aria-labelledby="pills-tab4-tab">
							<div class="row">
									@foreach($sale_products as $prod)
									@include('includes.product.list-product')
								@endforeach
							</div>
						</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Clothing and Apparel Area start -->

@endif

@if($ps->best == 1)
<!-- Phone and Accessories Area Start -->
<section class="phone-and-accessories categori-item">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 remove-padding">
				<div class="section-top">
					<h2 class="section-title">
						{{ $langg->lang27 }}
					</h2>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					@foreach($best_products as $prod)
					@include('includes.product.home-product')
					@endforeach
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Phone and Accessories Area start-->
@endif

	@if($ps->flash_deal == 1)
		<!-- Electronics Area Start -->
		<section class="categori-item electronics-section">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 remove-padding">
						<div class="section-top">
							<h2 class="section-title">
							Flash Deals
							</h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="flash-deals">
							<div class="flas-deal-slider">

								@foreach($discount_products as $prod)
									@include('includes.product.flash-product')
								@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- Electronics Area start-->
	@endif

	@if($ps->large_banner == 1)
		<!-- Banner Area One Start -->
		<section class="banner-section">
			<div class="container">
				@foreach($large_banners->chunk(1) as $chunk)
					<div class="row">
						@foreach($chunk as $img)
							<div class="col-lg-12 remove-padding">
								<div class="img">
									<a class="banner-effect" href="{{ $img->link }}">
										<img src="{{asset('assets/images/banners/'.$img->photo)}}" alt="">
									</a>
								</div>
							</div>
						@endforeach
					</div>
				@endforeach
			</div>
		</section>
		<!-- Banner Area One Start -->
	@endif

	@if($ps->top_rated == 1)
		<!-- Electronics Area Start -->
		<section class="categori-item electronics-section">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 remove-padding">
						<div class="section-top">
							<h2 class="section-title">
								{{ $langg->lang28 }}
							</h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="row">

							@foreach($top_products as $prod)
								@include('includes.product.top-product')
							@endforeach

						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- Electronics Area start-->
	@endif

	@if($ps->bottom_small == 1)
		<!-- Banner Area One Start -->
		<section class="banner-section">
			<div class="container">
				@foreach($bottom_small_banners->chunk(3) as $chunk)
					<div class="row">
						@foreach($chunk as $img)
							<div class="col-lg-4 col-md-6 remove-padding">
								<div class="left">
								       @if($img->id == 5)
								        	<a class="banner-effect" href="javascript:;" data-toggle="modal" data-target="#vendor-login" target="_blank">
								        	    	<img src="{{asset('assets/images/banners/'.$img->photo)}}" alt="">
								        	    	</a>
								    @else
									    <a class="banner-effect" href="{{ $img->link }}" target="_blank">
									        	<img src="{{asset('assets/images/banners/'.$img->photo)}}" alt="">
									   </a>
									@endif    
									    
									<!--<a class="banner-effect" href="{{ $img->link }}" target="_blank">-->
									<!--	<img src="{{asset('assets/images/banners/'.$img->photo)}}" alt="">-->
									<!--</a>-->
								</div>
							</div>
						@endforeach
					</div>
				@endforeach
			</div>
		</section>
		<!-- Banner Area One Start -->
	@endif

	@if($ps->big == 1)
	<!-- Clothing and Apparel Area Start -->
	<section class="categori-item clothing-and-Apparel-Area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 remove-padding">
					<div class="section-top">
						<h2 class="section-title">
							{{ $langg->lang29 }}
						</h2>

					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="row">
						@foreach($big_products as $prod)
						@include('includes.product.home-product')
						@endforeach
					</div>
				</div>
			</div>
		</div>
		</div>
	</section>
	<!-- Clothing and Apparel Area start-->
@endif
<!-- Home Blog Area Start -->
<section class="blogpagearea">
    <div class="container">
<div class="row">
				<div class="col-lg-12">
					<div class="section-top">
						<h2 class="section-title">
						{{ $langg->lang24 }}
						</h2>
					</div>
				</div>
			</div>
      <div class="row mt-3">

	  @foreach (App\Models\Blog::orderBy('created_at', 'desc')->limit(3)->get() as $blogg)
        <div class="col-md-6 col-lg-4">
              <div class="blog-box">
                <div class="blog-images">
                    <div class="img">
                    <img src="{{ $blogg->photo ? asset('assets/images/blogs/'.$blogg->photo):asset('assets/images/noimage.png') }}" class="img-fluid" alt="">
                    <div class="date d-flex justify-content-center">
                      <div class="box align-self-center">
                        <p>{{date('d', strtotime($blogg->created_at))}}</p>
                        <p>{{date('M', strtotime($blogg->created_at))}}</p>
                      </div>
                    </div>
                    </div>
                </div>
                <div class="details">
                    <a href='{{route('front.blogshow',$blogg->id)}}'>
                      <h4 class="blog-title">
                        {{mb_strlen($blogg->title,'utf-8') > 50 ? mb_substr($blogg->title,0,50,'utf-8')."...":$blogg->title}}
                      </h4>
                    </a>
                  <p class="blog-text">
                    {{substr(strip_tags($blogg->details),0,120)}}
                  </p>
                  <a class="read-more-btn" href="{{route('front.blogshow',$blogg->id)}}">{{ $langg->lang38 }}</a>
                </div>
            </div>
        </div>


        @endforeach
</div>

    </div>
  </section>
<!--home blog section end-->

@if($ps->partners == 1)
<!-- Partners Area Start -->
<section class="brand-section partners">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="section-top">
						<h2 class="section-title">
							Partners
						</h2>
					</div>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-lg-12 padding-decrease">
					<div class="brand-slider">
						@foreach($partners->chunk(2) as $partner)
							<div class="slide-item">
								@foreach($partner as $data)
									<a href="{{ $data->link }}" target="_blank" class="brand">
										<img src="{{ asset('assets/images/partner/'.$data->photo) }}" alt="">
									</a>
								@endforeach		
							</div>						
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Partners Area End -->
@endif

	<!-- VENDOR LOGIN MODAL -->
	<div class="modal fade" id="vendor-login" tabindex="-1" role="dialog" aria-labelledby="vendor-login-Title" aria-hidden="true">
  <div class="modal-dialog  modal-dialog-centered" style="transition: .5s;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
				<nav class="comment-log-reg-tabmenu">
					<div class="nav nav-tabs" id="nav-tab1" role="tablist">
						<a class="nav-item nav-link login active" id="nav-log-tab11" data-toggle="tab" href="#nav-log11" role="tab" aria-controls="nav-log" aria-selected="true">
							{{ $langg->lang234 }}
						</a>
						<a class="nav-item nav-link" id="nav-reg-tab11" data-toggle="tab" href="#nav-reg11" role="tab" aria-controls="nav-reg" aria-selected="false">
							{{ $langg->lang235 }}
						</a>
					</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
					<div class="tab-pane fade show active" id="nav-log11" role="tabpanel" aria-labelledby="nav-log-tab">
				        <div class="login-area">
				          <div class="login-form signin-form">
				                @include('includes.admin.form-login')
				            <form class="mloginform" action="{{ route('user.login.submit') }}" method="POST">
				              {{ csrf_field() }}
				              <div class="form-input">
				                <input type="email" name="email" placeholder="{{ $langg->lang173 }}" required="">
				                <i class="icofont-user-alt-5"></i>
				              </div>
				              <div class="form-input">
				                <input type="password" class="Password" name="password" placeholder="{{ $langg->lang174 }}" required="">
				                <i class="icofont-ui-password"></i>
				              </div>
				              <div class="form-forgot-pass">
				                <div class="left">
				                  <input type="checkbox" name="remember"  id="mrp1" {{ old('remember') ? 'checked' : '' }}>
				                  <label for="mrp1">{{ $langg->lang175 }}</label>
				                </div>
				                <div class="right">
				                  <a href="javascript:;" id="show-forgot1">
				                    {{ $langg->lang176 }}
				                  </a>
				                </div>
				              </div>
				              <input type="hidden" name="modal"  value="1">
				               <input type="hidden" name="vendor"  value="1">
				              <input class="mauthdata" type="hidden"  value="{{ $langg->lang177 }}">
				              <button type="submit" class="submit-btn">{{ $langg->lang178 }}</button>
					              @if(App\Models\Socialsetting::find(1)->f_check == 1 || App\Models\Socialsetting::find(1)->g_check == 1)
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
				            </form>
				          </div>
				        </div>
					</div>
					<div class="tab-pane fade" id="nav-reg11" role="tabpanel" aria-labelledby="nav-reg-tab">
                <div class="login-area signup-area">
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
                           <!--div class="col-lg-6">

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
                           	</div-->

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
                                 		<img class="codeimg1" src="{{asset("assets/images/capcha_code.png")}}" alt=""> <i class="fas fa-sync-alt pointer refresh_code "></i>
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
                                  <input type="checkbox" name="terms_and_condition" id="terms_and_condition" class="form-check-input">
                                  <label class="form-check-label" for="terms_and_condition">I agree to the <a href="{{ url('terms') }}" target="_blank">Terms & Condition</a> and <a href="{{ url('Vendor Agreement') }}" target="_blank">Vendor Agreement</a></label>
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
</div>
<!-- VENDOR LOGIN MODAL ENDS -->

	<!-- main -->
	<script src="{{asset('assets/front/js/mainextra.js')}}"></script>