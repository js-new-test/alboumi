@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$shippingAddressLabels['SHIPPINGADDRESS']}}">
<meta name="keywords" content="{{$shippingAddressLabels['SHIPPINGADDRESS']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
	var firstNameReq = <?php echo json_encode($shippingAddressLabels['FULLNAMEREQ']); ?>;
	var address1Req = <?php echo json_encode($shippingAddressLabels['ADDRESS1REQ']); ?>;
	var address2Req = <?php echo json_encode($shippingAddressLabels['ADDRESS2REQ']); ?>;
	var mobileReq = <?php echo json_encode($shippingAddressLabels['MOBILEREQ']); ?>;
	var mobileMustBe = <?php echo json_encode($shippingAddressLabels['MOBILEMUSTBE8DIGIT']); ?>;
	var mobileNum = <?php echo json_encode($shippingAddressLabels['MOBILENUM']); ?>;
	var countryReq = <?php echo json_encode($shippingAddressLabels['COUNTRYREQ']); ?>;
	var stateReq = <?php echo json_encode($shippingAddressLabels['STATEREQ']); ?>;
	var cityReq = <?php echo json_encode($shippingAddressLabels['CITYREQ']); ?>;
	var pinCodeReq = <?php echo json_encode($shippingAddressLabels['PINCODEREQ']); ?>;	
	var addressTypeReq = <?php echo json_encode($shippingAddressLabels['ADDRESSTYPEREQ']); ?>;
</script>
@section('content')
<div class="thumb-nav tb-11">
	<div class="container">
		<a href="{{url('/')}}">{{$shippingAddressLabels['HOME']}}</a>
		<span>{{$shippingAddressLabels['CHECKOUT']}}</span>
	</div>
</div>
<section>
	<div class="container">
		<div class="process-section">
			<div class="row">
				<div class="col-6 col-sm-6 col-md-3">
					<div class="blur-checked-process">{{$shippingAddressLabels['LOGINEMAIL']}}</div>
				</div>
				<div class="col-6 col-sm-6 col-md-3">
					<div class="checked-process">{{$shippingAddressLabels['SHIPPINGADDRESS']}}</div>
				</div>
				<div class="col-6 col-sm-6 col-md-3">
					<div class="unchecked-process">{{$shippingAddressLabels['PAYMENMETHOD']}}</div>
				</div>
				<div class="col-6 col-sm-6 col-md-3">
					<div class="unchecked-process">{{$shippingAddressLabels['REVIEWORDER']}}</div>
				</div>
			</div>
		</div>
	</div>
</section>
<input type="hidden" name="cart_master_id" id="cart_master_id" value="{{$cart_master_id}}">
@if(count($addresses) > 0)
<section class="shopping-address addresses">
	<div class="container">

		<h4>{{$shippingAddressLabels['MYADDRESSES5']}}</h4>
		<div class="dividers"></div>
		<div class="all-address">

			<label class="rd">{{$shippingAddressLabels['DELIVERY']}}
				<input type="radio" checked="checked" name="Delivery" class="Delivery">
				<span class="rd-checkmark"></span>
				@php
					$langId = Session::get('language_id');
					$visibility = App\Models\GlobalLanguage::checkVisibility($langId);
					if($visibility->visibility == 0)
					{
						$float = 'right';
					}
					elseif($visibility->visibility == 1)
					{
						$float = 'left';
					}
				@endphp
				<span style="float:{{$float}};margin-top: -10px;">
					<div class="text-center">
						<a class="fill-btn" style="width:200px;" id="add_new_shi_address_btn" href="javascript:void(0)">Add New Address</a>
					</div>
				</span>
			</label>
			<input type="hidden" id="Delivery_msg" data-delivary-error-msg="{{$shippingAddressLabels['SELECTDELADDR']}}">
			<div id="delivery_section">
				@foreach($addresses_arr as $key => $address)										
					<div class="row">
						@foreach($address as $key => $addr)
							@if($key % 2 == 0)
							<div class="col-12 col-sm-12 col-md-12 col-lg-6">
								<div class="address-box">
									<input type="radio" name="address-ck" class="address-ck" data-address-type="1" {{($addr['id'] == request()->get('shippingaddressid')) ? 'checked' : ''}}>
									<div class="select-border">
										<div class="dynamic_address_list">
											<div class="row">
												<div class="col-12 col-sm-8 col-md-7">
													<p class="s2">{{$addr['fullname']}}</p>
													<span>{{$addr['address_1']}}, {{$addr['address_2']}}</span>
													<span>{{$addr['city']}}, {{$addr['state']}}</span>
													@php $country = \App\Models\Country::where('id', $addr['country'])->first() @endphp
													<span>{{$country->name}}</span>
													<span>{{$addr['phone1']}}</span>

													<a class="edit-address" data-address_id="{{$addr['id']}}"><img src="{{asset('/public/assets/frontend/img/Edit.png')}}">Edit Address</a>
												</div>
												<div class="col-12 col-sm-4 col-md-5 right-576">
													<button class="border-btn D-here" data-address_id="{{$addr['id']}}">{{$shippingAddressLabels['DELIVERYHERE']}}</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							@else
							<div class="col-12 col-sm-12 col-md-12 col-lg-6">
								<div class="address-box">
									<input type="radio" name="address-ck" class="address-ck" data-address-type="1" {{($addr['id'] == request()->get('shippingaddressid')) ? 'checked' : ''}}>
									<div class="select-border">
										<div class="dynamic_address_list">
											<div class="row">
												<div class="col-12 col-sm-8 col-md-7">
													<p class="s2">{{$addr['fullname']}}</p>
													<span>{{$addr['address_1']}}, {{$addr['address_2']}}</span>
													<span>{{$addr['city']}}, {{$addr['state']}}</span>
													@php $country = \App\Models\Country::where('id', $addr['country'])->first() @endphp
													<span>{{$country->name}}</span>
													<span>{{$addr['phone1']}}</span>

													<a class="edit-address" data-address_id="{{$addr['id']}}"><img src="{{asset('/public/assets/frontend/img/Edit.png')}}">Edit Address</a>
												</div>
												<div class="col-12 col-sm-4 col-md-5 right-576">
													<button class="border-btn D-here" data-address_id="{{$addr['id']}}">{{$shippingAddressLabels['DELIVERYHERE']}}</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							@endif								
						@endforeach
					</div>								
				@endforeach
			</div>
			<div class="dividers"></div>
				<label class="rd m24">{{$shippingAddressLabels['STOREPICKUPFREE']}}
				  <input type="radio" name="Delivery" class="StorePickup">
				  <span class="rd-checkmark"></span>
				</label>
				<input type="hidden" id="StorePickup_msg" data-store-error-msg="{{$shippingAddressLabels['SELECTSTOREADDR']}}">
				<div id="store_pickup_section">				
					@foreach($store_locations_arr as $key => $s_location)
						<div class="row">	
							@foreach($s_location as $key => $addr)						
								@if($key % 2 == 0)			
								<div class="col-12 col-sm-12 col-md-12 col-lg-6">
									<div class="address-box">
										<input type="radio" name="address-ck" class="address-ck" {{($addr['id'] == request()->get('shippingaddressid')) ? 'checked' : ''}} data-address-type="2">
										<div class="select-border">
											<div>
												<div class="row">
													<div class="col-12 col-sm-8 col-md-7">
														<p class="s2">{{$addr['title']}}</p>
														<span>{{$addr['address_1']}}, {{$addr['address_2']}}</span>
														<span>{{$addr['phone']}}</span>

														<!-- <a class="edit-address"><img src="{{asset('/public/assets/frontend/img/Edit.png')}}">Edit Address</a> -->
													</div>
													<div class="col-12 col-sm-4 col-md-5 right-576">
														<button class="border-btn D-here" data-s_location_id="{{$addr['id']}}">{{$shippingAddressLabels['PICKFROMHERE']}}</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>	
								@else
								<div class="col-12 col-sm-12 col-md-12 col-lg-6">
									<div class="address-box">
										<input type="radio" name="address-ck" class="address-ck" {{($addr['id'] == request()->get('shippingaddressid')) ? 'checked' : ''}} data-address-type="2">
										<div class="select-border">
											<div>
												<div class="row">
													<div class="col-12 col-sm-8 col-md-7">
														<p class="s2">{{$addr['title']}}</p>
														<span>{{$addr['address_1']}}, {{$addr['address_2']}}</span>
														<span>{{$addr['phone']}}</span>

														<!-- <a class="edit-address"><img src="{{asset('/public/assets/frontend/img/Edit.png')}}">Edit Address</a> -->
													</div>
													<div class="col-12 col-sm-4 col-md-5 right-576">
														<button class="border-btn D-here" data-s_location_id="{{$addr['id']}}">{{$shippingAddressLabels['PICKFROMHERE']}}</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								@endif
							@endforeach									
						</div>
					@endforeach	
				</div>
			<div class="dividers"></div>

			<div class="text-center">
				<a class="fill-btn address-continue" style="cursor:pointer;">{{$shippingAddressLabels['CONTINUE']}}</a>
			</div>
		</div>
	</div>
</section>
@else
<section class="payment-method">
	<div class="container">
		<h4>{{$shippingAddressLabels['MYADDRESSES1']}}</h4>
		<div class="dividers"></div>
		<div class="row">
			<div class="col-12">
				@if(Session::has('msg'))                     
					<div class="alert {{(Session::get('alert_type') == true) ? 'alert-success' : 'alert-danger'}} alert-dismissible fade show" role="alert">
						{{ Session::get('msg') }}
						<button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				@endif
				<form id="add_new_address" class="add-new-address" method="POST" action="{{url('/customer/save-shipping-address')}}">
					@csrf
					<input type="hidden" name="customer_id" id="customer_id" value="{{$customer_id}}">
					<input type="hidden" name="cart_master_id" id="cart_master_id" value="{{$cart_master_id}}">
					<label class="rd">{{$shippingAddressLabels['DELIVERY']}}
					  <input type="radio" checked="checked" name="Delivery" class="dynamic_delivery_radio" value="1">
					  <span class="rd-checkmark"></span>
					</label>
					<div id="dynamic_delivery_section">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-6">
							<input type="text" name="full_name" value="{{($c_first_name != '' && $c_last_name != '') ? $c_first_name.' '.$c_last_name : ''}}" placeholder="{{$shippingAddressLabels['FULL_NAME']}}" class="input">
							@if($errors->has('full_name'))
								<div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('full_name') }}</div>
							@endif
							<input type="text" name="address_1" placeholder="{{$shippingAddressLabels['ADDRESS_LINE1_HINT']}}" class="input">
							@if($errors->has('address_1'))
								<div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('address_1') }}</div>
							@endif
							<input type="text" name="address_2" placeholder="{{$shippingAddressLabels['MYADDRESSES3']}}" class="input">
							@if($errors->has('address_2'))
								<div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('address_2') }}</div>
							@endif
							<input type="number" name="mobile" value="{{ ($c_mobile != '') ? $c_mobile : '' }}" placeholder="{{$shippingAddressLabels['MYACCOUNTLABEL3']}}" class="input">							
							@if($errors->has('mobile'))
								<div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('mobile') }}</div>
							@endif
							<select class="select" name="address_type" id="address_type">
								<option value="1">{{$shippingAddressLabels['addressType1']}}</option>
								<option value="2">{{$shippingAddressLabels['addressType2']}}</option>
							</select>
						</div>
						<div class="col-12 col-sm-12 col-md-6">
							<select class="select" name="country" id="country">
								<!-- <option value="">--- {{$shippingAddressLabels['SELECTCOUNTRY']}} ---</option> -->
								@foreach($countries as $country)
								<option value="{{$country->id}}">{{$country->name}}</option>
								@endforeach
							</select>							
							<input type="text" name="state" placeholder="{{$shippingAddressLabels['MYADDRESSES11']}}" class="input">
							@if($errors->has('state'))
								<div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('state') }}</div>
							@endif
							<input type="text" name="city" placeholder="{{$shippingAddressLabels['MYADDRESSES12']}}" class="input">
							@if($errors->has('city'))
								<div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('city') }}</div>
							@endif
							<input type="text" name="pincode" placeholder="{{$shippingAddressLabels['MYADDRESSES10']}}" class="input">							
							@if($errors->has('pincode'))
								<div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('pincode') }}</div>
							@endif
						</div>
					</div>
					<div class="row">
						<div class="col-12 normal-ck">
							<label class="ck">{{$shippingAddressLabels['SET_AS_DEFAULT_ADDRESS']}}
							  <input type="checkbox" name="is_default" id="is_default" checked value="1" onclick="return false;">
							  <span class="checkmark"></span>
							</label>
						</div>
					</div>
					<div class="dividers"></div>
					</div>
					
					<label class="rd m24">{{$shippingAddressLabels['STOREPICKUPFREE']}}
						<input type="radio" name="Delivery" class="dynamic_store_pickup_radio">
						<span class="rd-checkmark"></span>
					</label>
					<input type="hidden" id="StorePickup_msg" data-store-error-msg="{{$shippingAddressLabels['SELECTSTOREADDR']}}">
					<div id="dynamic_store_pickup_section">
					@foreach($store_locations_arr as $key => $s_location)
						<div class="row">	
							@foreach($s_location as $key => $addr)						
								@if($key % 2 == 0)			
								<div class="col-12 col-sm-12 col-md-12 col-lg-6">
									<div class="address-box">
										<input type="radio" name="address-ck" class="address-ck" {{($addr['id'] == request()->get('shippingaddressid')) ? 'checked' : ''}}>
										<div class="select-border">
											<div>
												<div class="row">
													<div class="col-12 col-sm-8 col-md-7">
														<p class="s2">{{$addr['title']}}</p>
														<span>{{$addr['address_1']}}, {{$addr['address_2']}}</span>
														<span>{{$addr['phone']}}</span>

														<a class="edit-address"><img src="{{asset('/public/assets/frontend/img/Edit.png')}}">Edit Address</a>
													</div>
													<div class="col-12 col-sm-4 col-md-5 right-576">
														<button class="border-btn D-here" data-s_location_id="{{$addr['id']}}" data-delivery_type="2">Pick From Here</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>	
								@else
								<div class="col-12 col-sm-12 col-md-12 col-lg-6">
									<div class="address-box">
										<input type="radio" name="address-ck" class="address-ck" {{($addr['id'] == request()->get('shippingaddressid')) ? 'checked' : ''}}>
										<div class="select-border">
											<div>
												<div class="row">
													<div class="col-12 col-sm-8 col-md-7">
														<p class="s2">{{$addr['title']}}</p>
														<span>{{$addr['address_1']}}, {{$addr['address_2']}}</span>
														<span>{{$addr['phone']}}</span>

														<a class="edit-address"><img src="{{asset('/public/assets/frontend/img/Edit.png')}}">Edit Address</a>
													</div>
													<div class="col-12 col-sm-4 col-md-5 right-576">
														<button class="border-btn D-here" data-s_location_id="{{$addr['id']}}" data-delivery_type="2">Pick From Here</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								@endif
							@endforeach									
						</div>
					@endforeach	
					<div class="dividers"></div>
					</div>
					
					

					<div class="row">
						<div class="col-12 text-center">
							<input type="submit" class="fill-btn save-continue" value="Save & Continue" name="">
						</div>
					</div>
				</form>
			</div>
		</div>				
	</div>
</section>
@endif
<!-- Modal Start -->
<div class="modal fade bd-example-modal-lg" id="editAddressModel" tabindex="-1" role="dialog" aria-labelledby="editAddressModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editAddressModelLabel">{{$shippingAddressLabels['EDIT_ADDRESS_TITLE']}}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="edit_shipping_address" method="POST">
					@csrf
					<input type="hidden" name="customer_id" id="customer_id" value="{{Session::get('customer_id')}}">					
					<input type="hidden" name="address_id" id="address_id">					
					<input type="hidden" id="baseUrl" value="{{$baseUrl}}">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-6">
							<input style="margin-bottom: 15px;width:100%;" type="text" name="full_name" id="full_name" placeholder="{{$shippingAddressLabels['FULL_NAME']}}" class="input">							
							<input type="hidden" name="full_name_label" id="full_name_label" value="{{$shippingAddressLabels['FULLNAMEREQ']}}">
							<div class="error_full_name" style="margin: -15px 0px 10px 15px;color: red;"></div>							
							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="address_1" id="address_1" placeholder="{{$shippingAddressLabels['ADDRESS_LINE1_HINT']}}" class="input">
							<input type="hidden" name="address_1_label" id="address_1_label" value="{{$shippingAddressLabels['ADDRESS1REQ']}}">
							<div class="error_address_1" style="margin: -15px 0px 10px 15px;color: red;"></div>							
							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="address_2" id="address_2" placeholder="{{$shippingAddressLabels['MYADDRESSES3']}}" class="input">
							<input type="hidden" name="address_2_label" id="address_2_label" value="{{$shippingAddressLabels['ADDRESS2REQ']}}">
							<div class="error_address_2" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="number" name="mobile" id="mobile" placeholder="{{$shippingAddressLabels['MYACCOUNTLABEL3']}}" class="input">							
							<input type="hidden" name="mobile_label" id="mobile_label" value="{{$shippingAddressLabels['MOBILEREQ']}}">
							<input type="hidden" name="mobile_not_num_label" id="mobile_not_num_label" value="{{$shippingAddressLabels['MOBILENUM']}}">
							<input type="hidden" name="mobile_num_must_8_degit" id="mobile_num_must_8_degit" value="{{$shippingAddressLabels['MOBILEMUSTBE8DIGIT']}}">
							<div class="error_mobile" style="margin: -15px 0px 10px 15px;color: red;"></div>			

							<select style="margin-bottom: 15px;width:100%;" class="select" name="address_type" id="address_type">
								<option value="1">{{$shippingAddressLabels['addressType1']}}</option>
								<option value="2">{{$shippingAddressLabels['addressType2']}}</option>
							</select>
						</div>
						<div class="col-12 col-sm-12 col-md-6">
							<select style="margin-bottom: 15px;width:100%;" class="select" name="country" id="country">
								<option value="">--- {{$shippingAddressLabels['SELECTCOUNTRY']}} ---</option>
								@foreach($countries as $country)
								<option value="{{$country->id}}">{{$country->name}}</option>
								@endforeach
							</select>	
							<input type="hidden" name="country_label" id="country_label" value="{{$shippingAddressLabels['COUNTRYREQ']}}">
							<div class="error_country" style="margin: -15px 0px 10px 15px;color: red;"></div>	
							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="state" id="state" placeholder="{{$shippingAddressLabels['MYADDRESSES11']}}" class="input">
							<input type="hidden" name="state_label" id="state_label" value="{{$shippingAddressLabels['STATEREQ']}}">							
							<div class="error_state" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="text" name="city" id="city" placeholder="{{$shippingAddressLabels['MYADDRESSES12']}}" class="input">							
							<input type="hidden" name="city_label" id="city_label" value="{{$shippingAddressLabels['CITYREQ']}}">							
							<div class="error_city" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="text" name="pincode" id="pincode" placeholder="{{$shippingAddressLabels['MYADDRESSES10']}}" class="input">							
							<input type="hidden" name="pincode_label" id="pincode_label" value="{{$shippingAddressLabels['PINCODEREQ']}}">
							<div class="error_pincode" style="margin: -15px 0px 10px 15px;color: red;"></div>							
						</div>
					</div>
					<div class="row">
						<div class="col-12 normal-ck">
							<label class="ck">{{$shippingAddressLabels['SET_AS_DEFAULT_ADDRESS']}}
							<input type="checkbox" name="is_default" id="is_default">
							<span class="checkmark"></span>
							</label>
						</div>
					</div>					
					<div class="row">
						<div class="col-12 text-center">
							<input type="button" class="btn btn-primary" id="save_edit_customer_address" value="Save" name="save_edit_customer_address">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$shippingAddressLabels['CANCEL']}}</button>
						</div>
					</div>
				</form>
				<!-- <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				<input type="hidden" name="cart_id" id="cart_id">                    
				<p class="mb-0">{{$shippingAddressLabels['AREYOUSURE']}}</p> -->
			</div>
			<!-- <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$shippingAddressLabels['NO']}}</button>
				<button type="button" class="btn btn-primary" id="dlt_prod_from_cart">{{$shippingAddressLabels['YES']}}</button>
			</div> -->
		</div>
	</div>
</div>
<!-- Modal Over -->

<!-- Modal Start -->
<div class="modal fade bd-example-modal-lg" id="addNewAddressModel" tabindex="-1" role="dialog" aria-labelledby="addNewAddressModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addNewAddressModelLabel">{{$shippingAddressLabels['ADD_ADDRESS']}}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="edit_shipping_address" method="POST">
					@csrf
					<input type="hidden" name="customer_id" id="customer_id" value="{{Session::get('customer_id')}}">					
					<input type="hidden" name="address_id" id="address_id">					
					<input type="hidden" id="baseUrl" value="{{$baseUrl}}">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-6">
							<input style="margin-bottom: 15px;width:100%;" type="text" name="n_full_name" id="n_full_name" placeholder="{{$shippingAddressLabels['FULL_NAME']}}" class="input" value="{{($cust_default_address) ? $cust_default_address->fullname : ''}}">							
							<input type="hidden" name="full_name_label" id="full_name_label" value="{{$shippingAddressLabels['FULLNAMEREQ']}}">
							<div class="error_full_name" style="margin: -15px 0px 10px 15px;color: red;"></div>							
							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="n_address_1" id="n_address_1" placeholder="{{$shippingAddressLabels['ADDRESS_LINE1_HINT']}}" class="input">
							<input type="hidden" name="address_1_label" id="address_1_label" value="{{$shippingAddressLabels['ADDRESS1REQ']}}">
							<div class="error_address_1" style="margin: -15px 0px 10px 15px;color: red;"></div>							
							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="n_address_2" id="n_address_2" placeholder="{{$shippingAddressLabels['MYADDRESSES3']}}" class="input">
							<input type="hidden" name="address_2_label" id="address_2_label" value="{{$shippingAddressLabels['ADDRESS2REQ']}}">
							<div class="error_address_2" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="number" name="n_mobile" id="n_mobile" placeholder="{{$shippingAddressLabels['MYACCOUNTLABEL3']}}" class="input" value="{{($cust_default_address) ? $cust_default_address->phone1 : ''}}">							
							<input type="hidden" name="mobile_label" id="mobile_label" value="{{$shippingAddressLabels['MOBILEREQ']}}">
							<input type="hidden" name="mobile_not_num_label" id="mobile_not_num_label" value="{{$shippingAddressLabels['MOBILENUM']}}">
							<input type="hidden" name="mobile_num_must_8_degit" id="mobile_num_must_8_degit" value="{{$shippingAddressLabels['MOBILEMUSTBE8DIGIT']}}">
							<div class="error_mobile" style="margin: -15px 0px 10px 15px;color: red;"></div>			

							<select style="margin-bottom: 15px;width:100%;" class="select" name="n_address_type" id="n_address_type">
								<option value="1">{{$shippingAddressLabels['addressType1']}}</option>
								<option value="2">{{$shippingAddressLabels['addressType2']}}</option>
							</select>
						</div>
						<div class="col-12 col-sm-12 col-md-6">
							<select style="margin-bottom: 15px;width:100%;" class="select" name="n_country" id="n_country">
								<!-- <option value="">--- {{$shippingAddressLabels['SELECTCOUNTRY']}} ---</option> -->
								@foreach($countries as $country)
								<option value="{{$country->id}}">{{$country->name}}</option>
								@endforeach
							</select>							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="n_state" id="n_state" placeholder="{{$shippingAddressLabels['MYADDRESSES11']}}" class="input">
							<input type="hidden" name="state_label" id="state_label" value="{{$shippingAddressLabels['STATEREQ']}}">							
							<div class="error_state" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="text" name="n_city" id="n_city" placeholder="{{$shippingAddressLabels['MYADDRESSES12']}}" class="input">							
							<input type="hidden" name="city_label" id="city_label" value="{{$shippingAddressLabels['CITYREQ']}}">							
							<div class="error_city" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="text" name="n_pincode" id="n_pincode" placeholder="{{$shippingAddressLabels['MYADDRESSES10']}}" class="input">							
							<input type="hidden" name="pincode_label" id="pincode_label" value="{{$shippingAddressLabels['PINCODEREQ']}}">
							<div class="error_pincode" style="margin: -15px 0px 10px 15px;color: red;"></div>							
						</div>
					</div>
					<div class="row">
						<div class="col-12 normal-ck">
							<label class="ck">{{$shippingAddressLabels['SET_AS_DEFAULT_ADDRESS']}}
							<input type="checkbox" name="n_is_default" id="n_is_default">
							<span class="checkmark"></span>
							</label>
						</div>
					</div>					
					<div class="row">
						<div class="col-12 text-center">
							<input type="button" class="btn btn-primary" id="save_new_customer_address" value="Save" name="save_edit_customer_address">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$shippingAddressLabels['CANCEL']}}</button>
						</div>
					</div>
				</form>
				<!-- <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				<input type="hidden" name="cart_id" id="cart_id">                    
				<p class="mb-0">{{$shippingAddressLabels['AREYOUSURE']}}</p> -->
			</div>
			<!-- <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$shippingAddressLabels['NO']}}</button>
				<button type="button" class="btn btn-primary" id="dlt_prod_from_cart">{{$shippingAddressLabels['YES']}}</button>
			</div> -->
		</div>
	</div>
</div>
<!-- Modal Over -->

@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/shipping-address/shipping-address.js')}}"></script>
@endpush
