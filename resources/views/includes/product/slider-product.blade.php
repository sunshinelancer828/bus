<a href="{{ route('front.product', $p->slug) }}" class="item">
		<div class="item-img">
			@if(!empty($p->features))
			<div class="sell-area">
				@foreach($p->features as $key => $data1)
				<span class="sale"
					style="background-color:{{ $p->colors[$key] }}">{{ $p->features[$key] }}</span>
				@endforeach
			</div>
			@endif
			<img class="img-fluid"
				src="{{ $p->photo ? asset('assets/images/thumbnails/'.$p->thumbnail):asset('assets/images/noimage.png') }}"
				alt="Trending Item">
		</div>
		<div class="info">
			<h5 class="name">{{ $p->showName() }}</h5>
			<h4 class="price">{{ $p->showPrice() }}
				<del><small>{{ $p->showPreviousPrice() }}</small></del></h4>
			<div class="stars">
				<div class="ratings">
					<div class="empty-stars"></div>
					<div class="full-stars"
						style="width:{{App\Models\Rating::ratings($p->id)}}%"></div>
				</div>
			</div>
			<div class="item-cart-area">
				
				<ul class="item-cart-options">
					<li>
							@if(Auth::guard('web')->check())

							<span href="javascript:;" class="add-to-wish"
								data-href="{{ route('user-wishlist-add',$p->id) }}" data-toggle="tooltip"
								data-placement="top" title="{{ $langg->lang54 }}"><i
									class="icofont-heart-alt"></i>
							</span>

							@else

							<span href="javascript:;" rel-toggle="tooltip" title="{{ $langg->lang54 }}"
								data-toggle="modal" id="wish-btn" data-target="#comment-log-reg"
								data-placement="top">
								<i class="icofont-heart-alt"></i>
							</span>

							@endif
					</li>
					<li>
						<span  class="quick-view" rel-toggle="tooltip" title="{{ $langg->lang55 }}" href="javascript:;" data-href="{{ route('product.quick',$p->id) }}" data-toggle="modal" data-target="#quickview" data-placement="top">
								<i class="fas fa-shopping-basket"></i>
						</span>
					</li>
					<li>
							<span href="javascript:;" class="add-to-compare"
							data-href="{{ route('product.compare.add',$p->id) }}" data-toggle="tooltip"
							data-placement="top" title="{{ $langg->lang57 }}" >
							<i class="icofont-exchange"></i>
						</span>
					</li>
				</ul>
			</div>
		</div>
	</a>
