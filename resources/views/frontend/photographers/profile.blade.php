@extends('frontend.layouts.master')
<title>@if(!empty($photographerDetials->seo_title)) {{ $photographerDetials->seo_title }} @else {{ $pageName }} @endif| {{ $projectName}}</title>
<meta name="description" content="@if(!empty($photographerDetials->seo_description)) <?php echo $photographerDetials->seo_description ?> @endif">
<meta name="keywords" content="@if(!empty($photographerDetials->seo_keyword)) <?php echo $photographerDetials->seo_keyword ?> @endif">
<script>
    var baseUrl = <?php echo json_encode($baseUrl); ?>
</script>
@section('content')

  <img src="{{asset('public/assets/images/photographers').'/'.$photographerDetials->cover_photo}}" class="photographerBanner">

  <section class="up-profile">
  	<div class="container">
  		<div class="row">
  			<div class="col-sm-12 col-md-12 col-lg-2">
  				<div class="profile-img">
  					<img src="{{asset('public/assets/images/photographers').'/'.$photographerDetials->profile_pic}}">
  				</div>
  			</div>
  			<div class="col-sm-12 col-md-12 col-lg-10">
  				<div class="profile-details">
  					<h4>{{$photographerDetials->name}}</h4>
  					<span class="blurColor">{{$photographerDetials->about}}</span>
  					<ul>
  						<li>
  							<img src="{{asset('public/assets/frontend/img/photographer-profile/P-pin.png')}}">
  							<span>{{$photographerDetials->location}}</span>
  						</li>
  						<li>
  							<img src="{{asset('public/assets/frontend/img/photographer-profile/P-website.png')}}">
  							<span>{{$photographerDetials->web}}</span>
  						</li>
  						<li>
  							<img src="{{asset('public/assets/frontend/img/photographer-profile/P-user.png')}}">
  							<span>{{$photographerDetials->experience}}</span>
  						</li>
  					</ul>
  				</div>
  			</div>
  		</div>
  	</div>
  </section>

  <section class="portfolio">
  	<div class="container">
  		<h4>{{$profileLabels['PORTFOLIO']}} <span>({{$portfolioCount}})</span></h4>
  		<div class="portfolio-grid">
        @foreach($portfolioArr as $portfolio)
        <img src="{{asset('public/assets/images/photographers/portfolio/').'/'.$portfolio['image']}}" onclick="location.href = '{{$baseUrl}}/product/{{$portfolio['product_slug']}}'">
        @endforeach

  		</div>
  	</div>
  </section>
  @endsection
  @push('scripts')
  <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
  <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
  @endpush
