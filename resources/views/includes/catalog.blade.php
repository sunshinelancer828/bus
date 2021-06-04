        <div class="col-lg-3 col-md-6">
          <div class="left-area">
            <div class="filter-result-area">
            <div class="header-area">
              <h4 class="title">
                {{$langg->lang61}}
              </h4>
            </div>
            <div class="body-area">
              <form id="catalogForm" action="{{ route('front.category', [Request::route('category'), Request::route('subcategory'), Request::route('childcategory')]) }}" method="GET">
                @if (!empty(request()->input('search')))
                  <input type="hidden" name="search" value="{{ request()->input('search') }}">
                @endif
                @if (!empty(request()->input('sort')))
                  <input type="hidden" name="sort" value="{{ request()->input('sort') }}">
                @endif
                <ul class="filter-list">
                  @foreach ($categories as $element)
                  <li>
                    <div class="content">
                        <a href="{{route('front.category', $element->slug)}}{{!empty(request()->input('search')) ? '?search='.request()->input('search') : ''}}" class="category-link"> <i class="fas fa-angle-double-right"></i> {{$element->name}} </a>
                        @if(!empty($cat) && $cat->id == $element->id && !empty($cat->subs))
                            @foreach ($cat->subs->sortBy('name') as $key => $subelement)
                            <div class="sub-content open">
                              <a href="{{route('front.category', [$cat->slug, $subelement->slug])}}{{!empty(request()->input('search')) ? '?search='.request()->input('search') : ''}}" class="subcategory-link"><i class="fas fa-angle-right"></i>{{$subelement->name}} </a>
                              @if(!empty($subcat) && $subcat->id == $subelement->id && !empty($subcat->childs))
                                @foreach ($subcat->childs->sortBy('name') as $key => $childcat)
                                <div class="child-content open">
                                  <a href="{{route('front.category', [$cat->slug, $subcat->slug, $childcat->slug])}}{{!empty(request()->input('search')) ? '?search='.request()->input('search') : ''}}" class="subcategory-link"><i class="fas fa-caret-right"></i> {{$childcat->name}}  </a>
                                </div>
                                @endforeach
                              @endif
                            </div>
                            @endforeach

                          </div>
                        @endif


                  </li>
                  @endforeach

                </ul>


                <div class="price-range-block">
                    <div id="slider-range" class="price-filter-range" name="rangeInput"></div>
                    <div class="livecount">
                      <input type="number" min=0  name="min"  id="min_price" class="price-range-field" />
                      <span>{{$langg->lang62}}</span>
                      <input type="number" min=0  name="max" id="max_price" class="price-range-field" />
                    </div>
                  </div>

                  <button class="filter-btn" type="submit">{{$langg->lang58}}</button>
              </form>
            </div>
            </div>


            
            @if(!isset($vendor))

            {{-- <div class="tags-area">
                <div class="header-area">
                    <h4 class="title">
                        {{$langg->lang63}}
                    </h4>
                  </div>
                  <div class="body-area">
                    <ul class="taglist">
                      @foreach(App\Models\Product::showTags() as $tag)
                      @if(!empty($tag))
                      <li>
                        <a class="{{ isset($tags) ? ($tag == $tags ? 'active' : '') : ''}}" href="{{ route('front.tag',$tag) }}">
                            {{ $tag }}
                        </a>
                      </li>
                      @endif
                      @endforeach
                    </ul>
                  </div>
            </div> --}}


            @else

            <div class="service-center">
              <div class="header-area">
                <h4 class="title">
                    {{ $langg->lang227 }}
                </h4>
              </div>
              <div class="body-area">
                <ul class="list">
                  <li>
                      <a href="javascript:;" data-toggle="modal" data-target="{{ Auth::guard('web')->check() ? '#vendorform1' : '#comment-log-reg' }}">
                          <i class="icofont-email"></i> <span class="service-text">{{ $langg->lang228 }}</span>
                      </a>
                  </li>
                  <li>
                        <a href="tel:+{{$vendor->shop_number}}">
                          <i class="icofont-phone"></i> <span class="service-text">{{$vendor->shop_number}}</span>
                        </a>
                  </li>
                </ul>
              <!-- Modal -->
              </div>

              <div class="footer-area">
                <p class="title">
                  {{ $langg->lang229 }}
                </p>
                <ul class="list">


              @if($vendor->f_check != 0)
              <li><a href="{{$vendor->f_url}}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
              @endif
              @if($vendor->g_check != 0)
              <li><a href="{{$vendor->g_url}}" target="_blank"><i class="fab fa-google"></i></a></li>
              @endif
              @if($vendor->t_check != 0)
              <li><a href="{{$vendor->t_url}}" target="_blank"><i class="fab fa-twitter"></i></a></li>
              @endif
              @if($vendor->l_check != 0)
              <li><a href="{{$vendor->l_url}}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
              @endif


                </ul>
              </div>
            </div>


            @endif


          </div>
        </div>
