@push('index')
  <meta name="keywords" content="{{ $seo->meta_keys }}">
	<meta name="description" content="{{ $seo->meta_description }}">
	<meta name="author" content="Anor"> 
@endpush  
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
            <h4 class="title text-center">
              {{ $page->title }}
            </h4>
            @if($page->id==3)
              <div class="container " >
                {!! $page->details !!}
              </div>
            @else
              <div>
                {!! $page->details !!}
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection