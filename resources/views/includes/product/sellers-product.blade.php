                                {{-- If This product belongs to vendor then apply this --}}
                                @if($p->user_id != 0)

                                {{-- check  If This vendor status is active --}}
                                @if($p->user->is_vendor == 2)

															<div class="single-box">
																<div class="left-area">
																	<img src="{{ $p->photo ? asset('assets/images/thumbnails/'.$p->thumbnail):asset('assets/images/noimage.png') }}" alt="">
																</div>
																<div class="right-area">
																		<h4 class="price">{{ $p->showPrice() }} <del>{{ $p->showPreviousPrice() }}</del> </h4>
																	<div class="stars">
																		<div class="ratings">
																			<div class="empty-stars"></div>
																			<div class="full-stars" style="width:{{App\Models\Rating::ratings($p->id)}}%"></div>
																		</div>
																	</div>
																	<p class="text"><a href="{{ route('front.product',$p->slug) }}">{{ strlen($p->name) > 65 ? substr($p->name,0,65).'...' : $p->name }}</a></p>

																	<ul class="action-meta">
																			<li>
																					@if(Auth::guard('web')->check())
														
																					<span href="javascript:;" class="wish add-to-wish"
																						data-href="{{ route('user-wishlist-add',$p->id) }}" data-toggle="tooltip"
																						data-placement="top" title="{{ $langg->lang54 }}"><i class="far fa-heart"></i>
																					</span>
														
																					@else
														
																					<span href="javascript:;" class="wish" rel-toggle="tooltip" title="{{ $langg->lang54 }}"
																						data-toggle="modal" id="wish-btn" data-target="#comment-log-reg"
																						data-placement="top">
																						<i class="far fa-heart"></i>
																					</span>
														
																					@endif
																			</li>
																			<li>
																				<span  class="cart-btn quick-view" rel-toggle="tooltip" title="{{ $langg->lang55 }}" href="javascript:;" data-href="{{ route('product.quick',$p->id) }}" data-toggle="modal" data-target="#quickview" data-placement="top">
																						<i class="fas fa-shopping-basket"></i>
																				</span>
																			</li>
																			<li>
																					<span href="javascript:;" class="compear add-to-compare"
																					data-href="{{ route('product.compare.add',$p->id) }}" data-toggle="tooltip"
																					data-placement="top" title="{{ $langg->lang57 }}" >
																					<i class="fas fa-random"></i>
																				</span>
																			</li>
																		</ul>																	
																</div>
															</div>


								@endif

                                {{-- If This product belongs admin and apply this --}}

								@else 

										<div class="single-box">
											<div class="left-area">
												<img src="{{ $p->photo ? asset('assets/images/thumbnails/'.$p->thumbnail):asset('assets/images/noimage.png') }}" alt="">
											</div>
											<div class="right-area">
													<h4 class="price">{{ $p->showPrice() }} <del>{{ $p->showPreviousPrice() }}</del> </h4>
												<div class="stars">
													<div class="ratings">
														<div class="empty-stars"></div>
														<div class="full-stars" style="width:{{App\Models\Rating::ratings($p->id)}}%"></div>
													</div>
												</div>
												<p class="text"><a href="{{ route('front.product',$p->slug) }}">{{ strlen($p->name) > 65 ? substr($p->name,0,65).'...' : $p->name }}</a></p>

												<ul class="action-meta">
														<li>
																@if(Auth::guard('web')->check())
									
																<span href="javascript:;" class="wish add-to-wish"
																	data-href="{{ route('user-wishlist-add',$p->id) }}" data-toggle="tooltip"
																	data-placement="top" title="{{ $langg->lang54 }}"><i class="far fa-heart"></i>
																</span>
									
																@else
									
																<span href="javascript:;" class="wish" rel-toggle="tooltip" title="{{ $langg->lang54 }}"
																	data-toggle="modal" id="wish-btn" data-target="#comment-log-reg"
																	data-placement="top">
																	<i class="far fa-heart"></i>
																</span>
									
																@endif
														</li>
														<li>
															<span  class="cart-btn quick-view" rel-toggle="tooltip" title="{{ $langg->lang55 }}" href="javascript:;" data-href="{{ route('product.quick',$p->id) }}" data-toggle="modal" data-target="#quickview" data-placement="top">
																	<i class="fas fa-shopping-basket"></i>
															</span>
														</li>
														<li>
																<span href="javascript:;" class="compear add-to-compare"
																data-href="{{ route('product.compare.add',$p->id) }}" data-toggle="tooltip"
																data-placement="top" title="{{ $langg->lang57 }}" >
																<i class="fas fa-random"></i>
															</span>
														</li>
													</ul>																	
											</div>
										</div>

								@endif