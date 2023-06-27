@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$changePasswordLabels['CHANGEPASSSEODESC']}}">
<meta name="keywords" content="{{$changePasswordLabels['CHANGEPASSSEOKEYWORD']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
	var passwordReq = <?php echo json_encode($changePasswordLabels['PASSWORDREQ']); ?>;
    var confirmPassReq = <?php echo json_encode($changePasswordLabels['PASSCONFREQ']); ?>;
    var confirmPassMissMatch = <?php echo json_encode($changePasswordLabels['error516']); ?>;
	var currentPassReq = <?php echo json_encode($changePasswordLabels['CURRENTPASSREQ']); ?>;
</script>
@section('content')
    <div class="thumb-nav tb-11">
		<div class="container">
			<a href="{{url('/')}}">{{$changePasswordLabels['HOME']}}</a>
			<span>{{$changePasswordLabels['CHANGE_PASSWORD']}}</span>
		</div>
	</div>
	
	<section class="profile-pages">
		<div class="container">
			<div class="row">
				@include('frontend.include.sidebar')
				<div class="col-12 col-sm-12 col-md-8 col-lg-9">
					<div class="pl-24">
						<div class="right-side-items">
							<div class="change-psw">
								<h4 class="profile-header">{{$changePasswordLabels['CHANGEPASSWORDLABEL']}}</h4>
								
								@if(Session::has('msg'))                     
									<div class="row" style="margin-top:10px;margin-bottom: -15px;">
										<div class="col-12">
											<div class="alert {{(Session::get('alert_type') == true) ? 'alert-success' : 'alert-danger'}} alert-dismissible fade show" role="alert">
												{{ Session::get('msg') }}
												<button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
										</div>										
									</div>
								@endif
								
								<div class="row">
									<div class="col-sm-12 col-md-10 col-lg-6 col-xl-6">										
										<form id="frontChangePassForm" class="row" method="POST" action="{{url('/customer/change-password')}}">
											@csrf
											<div class="col-sm-12">
												<input type="password" class="input" placeholder="{{$changePasswordLabels['CHANGEPASSWORDLABEL1']}}" name="currentpassword">
												@if($errors->has('currentpassword'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('currentpassword') }}</div>
												@endif
											</div>
											<div class="col-sm-12">
												<input type="password" class="input" placeholder="{{$changePasswordLabels['CHANGEPASSWORDLABEL2']}}" id="password" name="password">
												@if($errors->has('password'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('password') }}</div>
												@endif
											</div>
											<div class="col-sm-12">
												<input type="password" class="input" placeholder="{{$changePasswordLabels['REGISTERLABEL8']}}" name="confirm_password">
												@if($errors->has('confirm_password'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('confirm_password') }}</div>
												@endif
											</div>
											<div class="col-sm-12 col-md-12 col-lg-6">
												<button type="submit" class="fill-btn">{{$changePasswordLabels['CHANGEPASSWORDLABEL3']}}</button>
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
	</section>
@endsection
@push('scripts')
  <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
  <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
  <script src="{{asset('public/assets/frontend/js/change-password/change-password.js')}}"></script>
@endpush
