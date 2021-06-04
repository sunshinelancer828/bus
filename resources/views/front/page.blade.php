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
            <a href="{{ route('front.page',$page->slug) }}">
              {{ $page->title }}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- Breadcrumb Area End -->



<section class="about">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="about-info">
            <h4 class="title">
              {{ $page->title }}
            </h4>
            <p>
              {!! $page->details !!}
            </p>

          </div>
        </div>
      </div>
    </div>
  </section>

@endsection

@if (request()->is('how-it-works') || request()->is('privacy') || request()->is('terms') || request()->is('Vendor Agreement') || request()->is('Refund Policy') || request()->is('about'))
  @section('scripts')
    <script>
      document.addEventListener('contextmenu', event => event.preventDefault());
      document.onkeydown = function(e) {
            if (e.ctrlKey && 
                (e.keyCode === 67 || 
                 e.keyCode === 86 || 
                 e.keyCode === 85 || 
                 e.keyCode === 117)) {
                return false;
            } else {
                return true;
            }
    };
    $(document).keypress("u",function(e) {
        if(e.ctrlKey){
            return false;
        } else{
            return true;
        }
    });
    </script>
    <style>
    html body {
         -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        -o-user-select: none;
        user-select: none;
    }
    </style>
  @endsection
@endif