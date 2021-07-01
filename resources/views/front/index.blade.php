@extends('layouts.front')

@section('content')

	@if($ps->slider == 1)
		<!-- Hero Area Start -->
		<section class="hero-area">
			{{--<div class="container">
				<div class="row">
					<div class="col-lg-3 decrease-padding">
						<div class="featured-link-box">
							<h4 class="title">
									{{ $langg->lang831 }}
							</h4>
							<ul class="link-list">
								@foreach(DB::table('featured_links')->get() as $data)
								<li>
									<a href="{{ $data->link }}" target="_blank"><img src="{{ $data->photo ? asset('assets/images/featuredlink/'.$data->photo) :  asset('assets/images/noimage.png') }}" alt="{{ $data->name }}">{{ $data->name }}</a>
								</li>
								@endforeach
							</ul>
						</div>
					</div>
					<div class="col-lg-9 decrease-padding">--}}
							@if($ps->slider == 1)
							@if(count($sliders))
								<div class="hero-area-slider">
									<div class="slide-progress"></div>
									<div class="intro-carousel">
										@foreach($sliders as $data)
											<div class="intro-content {{$data->position}}" style="background-image: url({{asset('assets/images/sliders/'.$data->photo)}})">
												<div class="container">
													<div class="row">
														<div class="col-lg-12">
															<div class="slider-content">
																<!-- layer 1 -->
																<div class="layer-1">
																	<h4 style="font-size: {{$data->subtitle_size}}px; color: {{$data->subtitle_color}}" class="subtitle subtitle{{$data->id}}" data-animation="animated {{$data->subtitle_anime}}">{{$data->subtitle_text}}</h4>
																	<h1 style="font-size: {{$data->title_size}}px; color: {{$data->title_color}}" class="title title{{$data->id}}" data-animation="animated {{$data->title_anime}}">{{$data->title_text}}</h1>
																</div>
																<!-- layer 2 -->
																<div class="layer-2">
																	<p style="font-size: {{$data->details_size}}px; color: {{$data->details_color}}"  class="text text{{$data->id}}" data-animation="animated {{$data->details_anime}}">{{$data->details_text}}</p>
																</div>
																<!-- layer 3 -->
																<div class="layer-3">
																	<a href="{{$data->link}}" target="_blank" class="mybtn1 test"><span>{{$data->btn_txt}}<i class="fas fa-chevron-right"></i></span></a>
																
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										@endforeach
									</div>
								</div>
							@endif
						@endif
					<!--/div>
				</div>	
			</div-->
		</section>
		<!-- Hero Area End -->
	@endif
		<!-- Slider bottom categories start -->
		<section class="slider-buttom-category d-none d-md-block">
		    <div class="container-fluid">
			   <div class="row">
				@foreach($homecategories as $category)
					@if($category->image != '')
						<div class="col-xl-2 col-lg-3 col-md-4 sc-common-padding">
							<a href="{{ route('front.category',$category->slug) }}" class="single-category"> 
							<div class="left">
								<h5 class="title">{{ $category->name }}</h5>
						      <span style="font-size: 11px;">{{$category->products()->count()}} item(s)</span>
							</div>
							<div class="right">
								<img src="{{ $category->image ? asset('assets/images/categories/'.$category->image) : asset('assets/images/noimage.png') }}" alt="{{ $category->name }}">
							</div>
							</a>
						</div>
                    @endif
				@endforeach
	     	</div>
		</div>
	</section>
    <!-- Slider bottom categories end -->
	@if($ps->featured_category == 1)
    	{{-- Slider Bottom Banner Start --}}
    	<section class="slider_bottom_banner">
    		<div class="container">
    		@foreach(DB::table('featured_banners')->get()->chunk(4) as $data1)
    			<div class="row">
    				@foreach($data1 as $data)
    				<div class="col-lg-3 col-6">
        				<a href="{{ $data->link }}" target="_blank" class="banner-effect">
        					<img src="{{ $data->photo ? asset('assets/images/featuredbanner/'.$data->photo) : asset('assets/images/noimage.png') }}" alt="projectshelve">
        				</a>
    				</div>
    				@endforeach
    			</div>
    			@if(!$loop->last)
    			     <br>
    			@endif
    		@endforeach
    		</div>
    	</section>
    	{{-- Slider Botom Banner End --}}
	@endif	

	@if($ps->featured == 1)
		<!-- Trending Item Area Start -->
		<section  class="trending">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 remove-padding">
						<div class="section-top">
							<h2 class="section-title">
								{{ $langg->lang26 }}
							</h2>
							{{-- <a href="#" class="link">View All</a> --}}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 remove-padding">
						<div class="trending-item-slider">
							@foreach($feature_products as $prod)
								@include('includes.product.slider-product')
							@endforeach
						</div>
					</div>

				</div>
			</div>
		</section>
		<!-- Tranding Item Area End -->
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
                                     		<img class="codeimg1" src="{{asset("assets/images/capcha_code.png")}}" alt="codeimg"> <i class="fas fa-sync-alt pointer refresh_code "></i>
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

	@if($ps->small_banner == 1)
		<!-- Banner Area One Start -->
		<section class="banner-section">
			<div class="container">
				@foreach($top_small_banners->chunk(2) as $chunk)
					<div class="row">
						@foreach($chunk as $img)
							<div class="col-lg-6 remove-padding">
								<div class="left">
								    @if($img->id == 2)
								        	<a class="banner-effect" href="javascript:;" data-toggle="modal" data-target="#vendor-login" target="_blank">
								    @else
									    <a class="banner-effect" href="{{ $img->link }}" target="_blank">
									@endif    
									    
										<img src="{{asset('assets/images/banners/'.$img->photo)}}" alt="projectshelve">
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

	<section id="extraData">
		<div class="text-center">
		<img class="{{ $gs->is_loader == 1 ? '' : 'd-none' }}" src="{{asset('assets/images/'.$gs->loader)}}" alt="loader">
		</div>
	</section>
	@if($ps->service == 1)

    {{-- Info Area Start --}}
    <section class="info-area">
		<div class="container">
	
			@foreach($services->chunk(4) as $chunk)
	
			<div class="row">
	
				<div class="col-lg-12 p-0">
					<div class="info-big-box">
						<div class="row">
							@foreach($chunk as $service)
							<div class="col-6 col-xl-3 p-0">
								<div class="info-box">
									<div class="icon">
										<img src="{{ asset('assets/images/services/'.$service->photo) }}" alt="Project Helves Benefits">
									</div>
									<div class="info">
										<div class="details">
											<h4 class="title">{{ $service->title }}</h4>
											<p class="text">
												{!! $service->details !!}
											</p>
										</div>
									</div>
								</div>
							</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</section>
	{{-- Info Area End  --}}
@endif

@endsection

@section('styles')
@if($ps->slider == 1)
   @if(count($sliders))
		@include('includes.slider-style')
	@endif
@endif
@endsection

@section('scripts')
<script>
    $(window).on('load',function() {

        setTimeout(function(){

            $('#extraData').load('{{route('front.extraIndex')}}');

        }, 100);
    });

</script>
@endsection