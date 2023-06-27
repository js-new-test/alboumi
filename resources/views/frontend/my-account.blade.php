@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$myAccountLabels['MYACCOUNTSEODESC']}}">
<meta name="keywords" content="{{$myAccountLabels['MYACCOUNTSEOKEYWORD']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
	var emailReq = <?php echo json_encode($myAccountLabels['EMAILREQ']); ?>;
    var emailInvalid = <?php echo json_encode($myAccountLabels['NOTVALIDEMAIL']); ?>;    
    var firstNameReq = <?php echo json_encode($myAccountLabels['FIRSTNAMEREQ']); ?>;
	var lastNameReq = <?php echo json_encode($myAccountLabels['LASTNAMEREQ']); ?>;
    var mobileMustBe = <?php echo json_encode($myAccountLabels['MOBILEMUSTBE8DIGIT']); ?>;
    var mobileNum = <?php echo json_encode($myAccountLabels['MOBILENUM']); ?>;
	var dateOfBirthReq = <?php echo json_encode($myAccountLabels['error505']); ?>;
	var mobileReq = <?php echo json_encode($myAccountLabels['MOBILEREQ']); ?>;
</script>
@section('content')
    <div class="thumb-nav tb-11">
		<div class="container">
			<a href="{{url('/')}}">{{$myAccountLabels["HOME"]}}</a>
			<span>{{$myAccountLabels["MYACCOUNTLABEL"]}}</span>
		</div>
	</div>

	<section class="profile-pages">
		<div class="container">
			<div class="row">
				@include('frontend.include.sidebar')
				<div class="col-12 col-sm-12 col-md-8 col-lg-9">
					<div class="pl-24">
						<div class="right-side-items">
							<div class="my-account">
								<h4 class="profile-header">{{$myAccountLabels["MYACCOUNTLABEL"]}}</h4>
								@if(Session::has('msg'))
									<div class="alert {{(Session::has('alert_type') == true) ? 'alert-success' : 'alert-danger'}} alert-dismissible fade show" role="alert">
										{{ Session::get('msg') }}
										<button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
								@endif
								<div class="row">
									<div class="col-sm-12 col-md-10 col-lg-12 col-xl-11">
										<form id="frontMyAccountForm" class="row" method="POST" action="{{url('/customer/save-my-account')}}">
											@csrf
											<div class="col-sm-12 col-md-12 col-lg-6">
												<input type="text" class="input" placeholder="{{$myAccountLabels['MYACCOUNTLABEL1']}}" name="firstName" value="{{$customer->first_name}}">
												@if($errors->has('firstName'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('firstName') }}</div>
												@endif
											</div>
											<div class="col-sm-12 col-md-12 col-lg-6">
												<input type="text" class="input" placeholder="{{$myAccountLabels['MYACCOUNTLABEL2']}}" name="lastName" value="{{$customer->last_name}}">
												@if($errors->has('lastName'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('lastName') }}</div>
												@endif
											</div>
											<div class="col-sm-12 col-md-12 col-lg-6">
												<input type="text" class="input" placeholder="{{$myAccountLabels['LOGINLABEL2']}}" name="email" value="{{$customer->email}}">
												@if($errors->has('email'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('email') }}</div>
												@endif
											</div>
											<div class="col-sm-12 col-md-12 col-lg-6">
												<input type="number" class="input" placeholder="{{$myAccountLabels['MYACCOUNTLABEL3']}}" name="mobile" value="{{$customer->mobile}}">
												@if($errors->has('mobile'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('mobile') }}</div>
												@endif
											</div>
											<div class="col-sm-12 col-md-12 col-lg-6">
												<input type="date" class="input" placeholder="{{$myAccountLabels['MYACCOUNTLABEL5']}}" id="dateOfBirth" name="dateOfBirth" value="{{$customer->date_of_birth}}">
												@if($errors->has('dateOfBirth'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('dateOfBirth') }}</div>
												@endif
											</div>
											<div class="col-sm-12 col-md-12 col-lg-6">
												<select name="gender" id="gender" class="input">
													<option value="Male" {{($customer->gender == 'Male') ? 'selected' : ''}}>Male</option>
													<option value="Female" {{($customer->gender == 'Female') ? 'selected' : ''}}>Female</option>
												</select>
											</div>
											<div class="col-sm-12 col-md-12 col-lg-6">
												<input type="text" class="input" placeholder="{{$myAccountLabels['REGISTERLABEL11']}}" name="loyalty_number" value="{{$customer->loyalty_number}}">												
											</div>
											<div class="col-sm-12 col-md-12 col-lg-6">
												
											</div>
											<div class="col-sm-12 col-md-12 col-lg-6">
												<button type="submit" class="fill-btn">{{$myAccountLabels['MYACCOUNTLABEL4']}}</button>
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
<script src="{{asset('public/assets/frontend/js/my-account/my-account.js')}}"></script>
@endpush
