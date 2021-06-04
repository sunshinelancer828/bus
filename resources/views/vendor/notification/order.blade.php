		<a class="clear">{{ $langg->lang436 }}</a>
		@if(count($datas) > 0)
		<a id="order-notf-clear" data-href="{{ route('vendor-order-notf-clear',Auth::guard('web')->user()->id) }}" class="clear" href="javascript:;">
			{{ $langg->lang437 }}
		</a>
		<ul>
		@foreach($datas as $data)
		@if($data->product_id == '' )
			<li>
				<a href="{{ route('vendor-order-show',$data->order_number) }}"> <i class="fas fa-newspaper"></i> {{ $langg->lang438 }}</a>
			</li>
			@else 
			
			<li>
				<a href="{{ route('vendor-prod-edit',$data->product_id) }}"> <i class="fas fa-newspaper"></i> Product rejected by admin!</a>
			</li>	
			@endif
		@endforeach
	
	
		</ul>

		@else 

		<a class="clear" href="javascript:;">
			{{ $langg->lang439 }}
		</a>

		@endif