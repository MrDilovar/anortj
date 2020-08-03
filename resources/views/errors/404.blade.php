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
            <a href="javascript:;">
              {{ $langg->lang427 }}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- Breadcrumb Area End -->

<section class="fourzerofour">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="content">
            <img src="{{ $gs->error_banner ? asset('assets/images/'.$gs->error_banner):asset('assets/images/noimage.png') }}" alt="">
            <h6 class="heading" style="font-size:22px">
              {{ $langg->lang428 }}
            </h6>
            {{-- <p class="text">
              {{ $langg->lang429 }}
            </p> --}}



            <a class="mybtn1" href="{{ route('front.index') }}">{{ $langg->lang430 }}</a>
          </div>
        </div>
      </div>
    </div>
  </section>


@endsection