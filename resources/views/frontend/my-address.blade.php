@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$myAddressLabels['MYADDRESSSEODESC']}}">
<meta name="keywords" content="{{$myAddressLabels['MYADDRESSSEOKEYWORD']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
	var firstNameReq = <?php echo json_encode($myAddressLabels['FULLNAMEREQ']); ?>;
	var address1Req = <?php echo json_encode($myAddressLabels['ADDRESS1REQ']); ?>;
	var address2Req = <?php echo json_encode($myAddressLabels['ADDRESS2REQ']); ?>;
	var mobileReq = <?php echo json_encode($myAddressLabels['MOBILEREQ']); ?>;
	var mobileMustBe = <?php echo json_encode($myAddressLabels['MOBILEMUSTBE8DIGIT']); ?>;
	var mobileNum = <?php echo json_encode($myAddressLabels['MOBILENUM']); ?>;
	var countryReq = <?php echo json_encode($myAddressLabels['COUNTRYREQ']); ?>;
	var stateReq = <?php echo json_encode($myAddressLabels['STATEREQ']); ?>;
	var cityReq = <?php echo json_encode($myAddressLabels['CITYREQ']); ?>;
	var pinCodeReq = <?php echo json_encode($myAddressLabels['PINCODEREQ']); ?>;	
	var addressTypeReq = <?php echo json_encode($myAddressLabels['ADDRESSTYPEREQ']); ?>;	
</script>
@section('content')    
    <div class="thumb-nav tb-11">
		<div class="container">
			<a href="{{url('/')}}">{{$myAddressLabels['HOME']}}</a>
			<span>{{$myAddressLabels['MYADDRESSES']}}</span>
		</div>
	</div>
	
	<section class="profile-pages">
		<div class="container">
			<div class="row">
				@include('frontend.include.sidebar')
				<div class="col-12 col-sm-12 col-md-8 col-lg-9">
					<div class="pl-24">
						<div class="right-side-items">
							<div class="my-addresses">
								<h4 class="profile-header">{{$myAddressLabels['MYADDRESSES']}}</h4>
								<h6 class="semi-header">{{$myAddressLabels['MYADDRESSES1']}}</h6>
								@if(Session::has('msg'))                     
									<div class="alert {{(Session::get('alert_type') == true) ? 'alert-success' : 'alert-danger'}} alert-dismissible fade show" role="alert">
										{{ Session::get('msg') }}
										<button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
								@endif
								<form id="frontMyAddressForm" class="row" method="POST" action="{{url('/customer/my-address')}}">
									<input type="hidden" name="baseUrl" id="baseUrl" value="{{$baseUrl}}">
									<input type="hidden" name="customer_id" id="customer_id" value="{{Session::get('customer_id')}}">
									<input type="hidden" name="address_id" id="edit_address_id">
									@csrf
									<div class="col-sm-12 col-md-10 col-lg-6 col-xl-5">
										<div class="row">
											<div class="col-sm-12">
												<input type="text" class="input" placeholder="{{$myAddressLabels['MYADDRESSES9']}}" name="full_name" id="full_name" value="{{($cust_default_address) ? $cust_default_address->fullname : ''}}">
												@if($errors->has('full_name'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('full_name') }}</div>
												@endif
											</div>
											<div class="col-sm-12">
												<input type="text" class="input" placeholder="{{$myAddressLabels['MYADDRESSES2']}}" name="address_1" id="address_1">
												@if($errors->has('address_1'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('address_1') }}</div>
												@endif
											</div>
											<div class="col-sm-12">
												<input type="text" class="input" placeholder="{{$myAddressLabels['MYADDRESSES3']}}" name="address_2" id="address_2">
												@if($errors->has('address_2'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('address_2') }}</div>
												@endif
											</div>
											<div class="col-sm-12">
												<input type="number" class="input" placeholder="{{$myAddressLabels['MYACCOUNTLABEL3']}}" name="mobile" id="mobile" value="{{($cust_default_address) ? $cust_default_address->phone1 : ''}}">
												@if($errors->has('mobile'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('mobile') }}</div>
												@endif
											</div>
											<!-- <div class="col-sm-12">
												<input type="" class="input" placeholder="Phone 2" name="">
											</div> -->
											<div class="col-sm-12">
												<select name="address_type" id="address_type" class="select">
													<option value="1">{{$myAddressLabels['addressType1']}}</option>
													<option value="2">{{$myAddressLabels['addressType2']}}</option>
												</select>
											</div>
										</div>
									</div>
									<div class="col-sm-12 col-md-10 col-lg-6 col-xl-5">
										<div class="row">
											<div class="col-sm-12">
												<select name="country" id="country" class="select">
													<!-- <option value="">--- Select Country ---</option> -->
													@foreach($countries as $country)                                                                
														<option value="{{$country->id}}">{{$country->name}}</option>
													@endforeach												
												</select>
												<!-- <div id="edit_country_dropdown"></div> -->
												@if($errors->has('country'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('country') }}</div>
												@endif
											</div>
											<div class="col-sm-12">												
												<input type="text" name="state" id="state" class="input" placeholder="{{$myAddressLabels['MYADDRESSES11']}}">
												<!-- <div id="edit_states_textbox"></div> -->
												@if($errors->has('state'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('state') }}</div>
												@endif
											</div>
											<div class="col-sm-12">												
												<input type="text" name="city" id="city" class="input" placeholder="{{$myAddressLabels['MYADDRESSES12']}}">
												<!-- <div id="edit_cities_textbox"></div>			 -->
												@if($errors->has('city'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('city') }}</div>
												@endif							
											</div>
											<div class="col-sm-12">
												<input id="pincode" name="pincode" type="text" class="input" placeholder="{{$myAddressLabels['MYADDRESSES10']}}">
												@if($errors->has('pincode'))
													<div class="error" style="margin: 0px 0px 10px 15px;color: red;">{{ $errors->first('pincode') }}</div>
												@endif
											</div>
											
										</div>
									</div>
									<input type="hidden" name="is_default" id="is_default">
									<div class="col-12">
										<button type="submit" class="fill-btn">{{$myAddressLabels['MYADDRESSES4']}}</button>
									</div>
								</form>

								<div class="dividers"></div>

								<h6 class="semi-header">{{$myAddressLabels['MYADDRESSES5']}}</h6>
								<div class="row">
									@foreach($cust_addresses as $cust_address)
									<div class="col-sm-12 col-md-10 col-lg-6">
										<div class="address-box">
											<div class="select-border">
												<p class="s2">{{$cust_address->fullname}}</p>
												<span>{{$cust_address->address_1}}<br>{{$cust_address->address_2}}</span>
												<span>{{$cust_address->city}}, {{$cust_address->state}}</span>
												@php $country = \App\Models\Country::where('id', $cust_address->country)->first() @endphp
												<span>{{$country->name}}</span>
												<span>{{$cust_address->phone1}}</span>

												<a class="edit-address" data-id="{{$cust_address->id}}"><img src="{{asset('/public/assets/frontend/img/Edit.png')}}">{{$myAddressLabels['MYADDRESSES7']}}</a>
												@if($cust_address->is_default != 1)
												<a class="delete-address" data-id="{{$cust_address->id}}"><img src="{{asset('/public/assets/frontend/img/Delete-1.png')}}">{{$myAddressLabels['MYADDRESSES8']}}</a>
												@endif
												<div class="dividers"></div>

												<label class="rd">{{$myAddressLabels['MYADDRESSES6']}}
												<input type="radio" {{($cust_address->is_default == 1) ? 'checked' : ''}} data-id="{{$cust_address->id}}" name="change_is_default" class="change_is_default">
												<span class="rd-checkmark"></span>
												</label>
											</div>
										</div>
									</div>
									@endforeach								
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Modal Start -->
		<div class="modal fade bd-example-modal-sm" id="changeDefaultAddressModel" tabindex="-1" role="dialog" aria-labelledby="changeDefaultAddressModelLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="changeDefaultAddressLabel">{{$myAddressLabels['CONFIRMATION']}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
						<input type="hidden" name="address_id" id="address_id">                    
						<p class="mb-0">{{$myAddressLabels['AREYOUSURE']}}</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$myAddressLabels['NO']}}</button>
						<button type="button" class="btn btn-primary" id="change_defalut_address">{{$myAddressLabels['YES']}}</button>
					</div>
				</div>
			</div>
		</div>
		<!-- Modal Over -->
		<!-- Modal Start -->
		<div class="modal fade bd-example-modal-sm" id="deleteAddressModel" tabindex="-1" role="dialog" aria-labelledby="deleteAddressModelLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="deleteAddressLabel">{{$myAddressLabels['CONFIRMATION']}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
						<input type="hidden" name="address_id" id="address_id">                    
						<p class="mb-0">{{$myAddressLabels['AREYOUSURE']}}</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$myAddressLabels['NO']}}</button>
						<button type="button" class="btn btn-primary" id="delete_address">{{$myAddressLabels['YES']}}</button>
					</div>
				</div>
			</div>
		</div>
		<!-- Modal Over -->
	</section>    
@endsection
@push('scripts')
<script src="{{asset('/public/assets/frontend/js/my-address/my-address.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
@endpush