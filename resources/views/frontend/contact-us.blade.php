@extends('frontend.layouts.master')
<title>@if(!empty($cmsPageDetails->seo_title)) {{ $cmsPageDetails->seo_title }} @else {{ $pageName }} @endif| {{ $projectName}}</title>
<meta name="description" content="@if(!empty($cmsPageDetails->seo_description)) <?php echo $cmsPageDetails->seo_description ?> @endif">
<meta name="keywords" content="@if(!empty($cmsPageDetails->seo_keyword)) <?php echo $cmsPageDetails->seo_keyword ?> @endif">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
	var emailReq = <?php echo json_encode($contactUsLabels['EMAILREQ']); ?>;
	var emailInvalid = <?php echo json_encode($contactUsLabels['NOTVALIDEMAIL']); ?>;
	var fullNameReq = <?php echo json_encode($contactUsLabels['FULLNAMEREQ']); ?>;
	var messageReq = <?php echo json_encode($contactUsLabels['MESSAGEREQ']); ?>;
</script>
@section('content')
    <div class="thumb-nav tb-11">
		<div class="container">
			<a href="{{url('/')}}">{{$contactUsLabels['CONTACTUSPAGELABEL3']}}</a>
			<span>{{ucwords($cmsPageDetails->title)}}</span>
		</div>
	</div>

	<section class="contact-us">
		<div class="container">
			<div class="row">
				<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 contact">
					<h4>{{ucwords($cmsPageDetails->title)}}</h4>

					<div class="dividers"></div>
					{!! $cmsPageDetails->description !!}
					<!-- <a href="" class="email-address" style="margin-top: 0px;"><img src="{{asset('public/assets/frontend/img/email.png')}}">info@alboumi.com</a><br>
					<a href="" class="phone-number"><img src="{{asset('public/assets/frontend/img/phone.png')}}">Contact Tel. : +973 17520238</a><br>
					<a href="" class="phone-number"><img src="{{asset('public/assets/frontend/img/Whatsapp.png')}}">WhatsApp : +973 34126630</a> -->
					<div>
						<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3578.812258444221!2d50.58627131503065!3d26.23528908342401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e49af8291ddfd39%3A0xe689d1626ccdb8b3!2sGCL%20-Gulf%20Color%20Lab%20(%20Ashrafs)!5e0!3m2!1sen!2sin!4v1629874172418!5m2!1sen!2sin" width="" height="" class="desktop-google-map mobile-google-map" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
					</div>
				</div>
				<div class="col-12 col-sm-12 col-md-6 col-lg-6 offset-xl-1 col-xl-5 write-us">
					<form id="frontContactUsForm" method="POST" action="{{ (isset(request()->lang_code)) ? url('/contact-us-with-lang-code') : url('/contact-us')}}">
						@csrf
						<h4>{{$contactUsLabels['CONTACTUSPAGELABEL']}}</h4>
						<div class="dividers"></div>
						<input type="text" class="input" placeholder="{{$contactUsLabels['MYADDRESSES9']}}" name="fullname">
						@if($errors->has('fullname'))
							<div class="error" style="margin: -10px 0px 10px 15px;color: red;">{{ $errors->first('fullname') }}</div>
						@endif
						<input type="text" class="input" placeholder="{{$contactUsLabels['FORGOTPASSLABEL2']}}" name="email">
						@if($errors->has('email'))
							<div class="error" style="margin: -10px 0px 10px 15px;color: red;">{{ $errors->first('email') }}</div>
						@endif
						<textarea name="text_message" class="textarea" placeholder="{{$contactUsLabels['CONTACTUSPAGELABEL1']}}"></textarea>
						@if($errors->has('text_message'))
							<div class="error" style="margin: -24px 0px 10px 15px;color: red;">{{ $errors->first('text_message') }}</div>
						@endif
						@if(config('services.recaptcha.key'))
							<div class="g-recaptcha"
								data-sitekey="{{config('services.recaptcha.key')}}">
							</div>
						@endif
						@if($errors->has('g-recaptcha-response'))
							<div class="error" style="margin: 0px 0px 0px 20px;color: red;">{{ $errors->first('g-recaptcha-response') }}</div>
						@endif
						<button style="margin-top: 15px;" class="fill-btn" type="submit">{{$contactUsLabels['CONTACTUSPAGELABEL2']}}</button>
					</form>
				</div>
			</div>
		</div>
	</section>
@endsection
@push('scripts')
  <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
  <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
  <script src="{{asset('public/assets/frontend/js/contact-us/contact-us.js')}}"></script>
@endpush
