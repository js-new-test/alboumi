@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$reviewOrderLabels['PAYMENMETHOD']}}">
<meta name="keywords" content="{{$reviewOrderLabels['PAYMENMETHOD']}}">

@section('content')
<link rel="stylesheet" href="{{asset('public/assets/frontend/css/loader-style.css')}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>
</script>
<div id="ajax-loader">
  <div class="cv-spinner">
  	<img src="{{ asset('public/assets/frontend/img/Loader.svg') }}" class="spinner">
  </div>
</div>
<div class="thumb-nav tb-11">
	<div class="container">
		<a href="{{url('/')}}">{{$reviewOrderLabels['HOME']}}</a>
		<span>{{$reviewOrderLabels['CHECKOUT']}}</span>
	</div>
</div>
<section>
	<div class="container">
		<div class="process-section">
			<div class="row">
				<div class="col-6 col-sm-6 col-md-3">
					<div class="blur-checked-process">{{$reviewOrderLabels['LOGINEMAIL']}}</div>
				</div>
				<div class="col-6 col-sm-6 col-md-3">
					<div class="blur-checked-process">{{$reviewOrderLabels['SHIPPINGADDRESS']}}</div>
				</div>
				<div class="col-6 col-sm-6 col-md-3">
					<div class="blur-checked-process">{{$reviewOrderLabels['PAYMENMETHOD']}}</div>
				</div>
				<div class="col-6 col-sm-6 col-md-3">
					<div class="checked-process">{{$reviewOrderLabels['REVIEWORDER']}}</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="review-order">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-12 col-lg-8 review-place-order">
				<h4>{{$reviewOrderLabels['REVIEW/PLACEORDER']}}</h4>
				<div class="dividers"></div>
				<div class="two-address">
					<div class="row">
						@if(isset($checkouttype) && $checkouttype == 1)
						<div class="col-12 col-sm-12 col-md-6">
							<h6>{{$reviewOrderLabels['SHIPPINGADDRESS']}}</h6>
							<p class="s2">{{$shipping_address->fullname}}</p>
							<span>{{$shipping_address->address_1}}, {{$shipping_address->address_2}},</span>
							<span>{{$shipping_address->city}}, {{$shipping_address->state}},</span>
							@php $country = \App\Models\Country::where('id', $shipping_address->country)->first() @endphp
							<span>{{$country->name}}.</span>
							<span>{{$shipping_address->phone1}}</span>
							<a id="show_hide_change_button" href="{{url('/customer/shipping-address?shippingaddressid=').$shipping_address->id.'&checkouttype=1'}}">{{$reviewOrderLabels['CHANGE']}}</a>                            
						</div>
						@elseif(isset($checkouttype) && $checkouttype == 2)
						<div class="col-12 col-sm-12 col-md-6">
							<h6>{{$reviewOrderLabels['STOREPICKUPADDRESS']}}</h6>
							<p class="s2">{{$shipping_address->title}}</p>
							<span>{{$shipping_address->address_1}}, {{$shipping_address->address_2}}</span>
							<span>{{$shipping_address->phone}}</span>
							<a href="{{url('/customer/shipping-address?shippingaddressid=').$shipping_address->id.'&checkouttype=2'}}">{{$reviewOrderLabels['CHANGE']}}</a>                            
						</div>
						@endif
                        {{-- @if($shipping_address)
						<div class="col-12 col-sm-12 col-md-6">
							<h6>{{$reviewOrderLabels['SHIPPINGADDRESS']}}</h6>
							<p class="s2">{{$shipping_address->fullname}}</p>
							<span>{{$shipping_address->address_1}}, {{$shipping_address->address_2}},<br>{{$shipping_address->city}}, {{$shipping_address->state}}</span>
							<span>{{$shipping_address->phone1}}</span>
							<a href="{{url('/customer/shipping-address?shippingaddressid=').$shipping_address->id}}">{{$reviewOrderLabels['CHANGE']}}</a>                            
						</div>
                        @endif --}}
						@if($billing_address)					
						<div class="col-12 col-sm-12 col-md-6" id="dynamic_bill_address_list">
							<input type="hidden" name="billing_add_id" id="billing_add_id" value="{{$billing_address->id}}">
							<h6>{{$reviewOrderLabels['BILLINGADDRESS']}}</h6>
							<p class="s2">{{$billing_address->fullname}}</p>
							<span>{{$billing_address->address_1}} , {{$billing_address->address_2}},</span>
							<span>{{$billing_address->city}}, {{$billing_address->state}},</span>
							@php $country = \App\Models\Country::where('id', $billing_address->country)->first() @endphp
							<span>{{$country->name}}.</span>
							<span>{{$billing_address->phone1}}</span>
							@if($cartMaster->address_id > 0)
								<a href="javascript:void(0)" style="position: absolute;" data-bill_address_id="{{$billing_address->id}}" id="change_billing_address">{{$reviewOrderLabels['CHANGE']}}</a>
							@else
							<a href="javascript:void(0)" data-bill_address_id="{{$billing_address->id}}" id="change_billing_address">{{$reviewOrderLabels['CHANGE']}}</a>
							@endif
							@if(isset($checkouttype) && $checkouttype == 1)
								@if($cartMaster->same_as_ship_addr == 1)
								<span style="margin-left: 70px;font-weight: bold;color: #212121;" for="same_as_ship_addr">Same as Shipping Address <input style="margin-left: 8px;" type="checkbox" checked name="same_as_ship_addr" id="same_as_ship_addr" class="form-check-input"></span>
								@else
								<span style="margin-left: 70px;font-weight: bold;color: #212121;" for="same_as_ship_addr">Same as Shipping Address <input style="margin-left: 8px;" type="checkbox" name="same_as_ship_addr" id="same_as_ship_addr" class="form-check-input"></span>
								@endif							
							@endif
						</div>						
						@else
						<div class="col-12 col-sm-12 col-md-6">	
							<input type="hidden" name="billing_add_id" id="billing_add_id">
							<input type="hidden" name="billing_add_not_found" id="billing_add_not_found" value="{{$reviewOrderLabels['BILLINGADDRESSNOTFOUND']}}">
							<h6>{{$reviewOrderLabels['BILLINGADDRESS']}}</h6>						
							<a id="add_billing_address" style="text-decoration: none !important;" href="javascript:void(0)">{{$reviewOrderLabels['ADDNEWBILLADDRESS']}}</a>
							@if(isset($checkouttype) && $checkouttype == 1)
								@if($cartMaster->set_bill_addr_as_ship_addr == 1)
								<span style="margin-left: 0px;margin-top: 5px;font-weight: bold;color: #212121;" for="same_as_deli_addr">Same as Shipping Address <input style="margin-left: 8px;" type="checkbox" checked name="same_as_deli_addr" id="same_as_deli_addr" class="form-check-input"></span>
								<!-- <input type="hidden" name="same_as_ship_id" id="same_as_ship_id" value="{{$shipping_address->id}}"> -->
								@else
								<span style="margin-left: 0px;margin-top: 5px;font-weight: bold;color: #212121;" for="same_as_deli_addr">Same as Shipping Address <input style="margin-left: 8px;" type="checkbox" name="same_as_deli_addr" id="same_as_deli_addr" class="form-check-input"></span>
								<!-- <input type="hidden" name="same_as_ship_id" id="same_as_ship_id" value="{{$shipping_address->id}}"> -->
								@endif
							@endif
						</div>
						@endif
					</div>
				</div>

				<div class="payment-method-box">
					<p class="s1">{{$reviewOrderLabels['PAYMENMETHOD']}}:</p>
					<span>{{($cartMaster->payment_method == 1) ? $reviewOrderLabels['CREDITCARD'] : $reviewOrderLabels['DEBITCARD']}}<a href="{{url('/customer/payment-method')}}">{{$reviewOrderLabels['CHANGE']}}</a></span>
				</div>

				<div class="table-to-div">
					<div class="shopping-table order-3">
					    <div class="table-heading">
					        <div class="table-column text-center w96">
					            <p class="s1">{{$reviewOrderLabels['ITEMS']}}</p>
					        </div>
					        <div class="table-column">
					            <p class="s1">{{$reviewOrderLabels['PRODUCTNAME']}}</p>
					        </div>
					        <div class="table-column table-price w100">
					            <p class="s1">{{$reviewOrderLabels['UNITPRICE']}}</p>
					        </div>
					        <div class="table-column w110">
					            <p class="s1">{{$reviewOrderLabels['QUANTITY']}}</p>
					        </div>
					        <div class="table-column text-right w100">
					            <p class="s1">{{$reviewOrderLabels['SUBTOTAL']}}</p>
					        </div>
					    </div>
                        @foreach($cart_arr as $cart)
                        <div class="table-row add-pad-top dynamic_table_section_remove">

                            <div class="table-column w177 product-item">
								<a style="text-decoration: none !important;" href="{{url('/product').'/'.$cart['slug']}}"><img src="{{$cart['image']}}"></a>
                            </div>
                            
                            <div class="table-column product-name">
								<a style="text-decoration: none !important;" href="{{url('/product').'/'.$cart['slug']}}"><p class="s1">{{$cart['title']}}</p></a>
                                @if($cart['variant'])
                                    @foreach($cart['variant'] as $variant)
                                        <p>{{$variant['title']}}: {{$variant['value']}}</p>                                        
                                    @endforeach
                                @endif
                                <!-- <div class="remove-add">
                                    <a href="javascript:void(0)" class="remove_product" data-cart-id="{{$cart['id']}}">{{$reviewOrderLabels['REMOVE']}}</a>
                                    <a href="#">Add to Wishlist</a>
                                </div> -->
                            </div>
                           
                            <div class="table-column table-price w100">
					        	<!-- <p class="s1 d-show-767">Price:&nbsp;&nbsp; </p> -->
					            <p class="s1">{{$cart['unitPrice']}}</p>
					        </div>
                            <div class="table-column quantity w110">
                                <p class="s1 d-show-767">{{$reviewOrderLabels['QUANTITY']}}</p>					            
                                <div class="plusminus horiz">                                    
                                    <button></button>
                                    <input type="number" disabled class="productQty" name="productQty" value="{{$cart['qty']}}" min="1" max="10">
                                    <button></button> 
                                </div>		
                                <input type="hidden" class="cart_id" value="{{$cart['id']}}">						
                            </div>
                            <div class="table-column sub-total text-right w130">
                                <p class="s1 d-show-767">{{$reviewOrderLabels['SUBTOTAL']}}</p>
                                <p class="s1">{{$cart['subTotal']}}</p>
                            </div>
                        </div>
						@endforeach					    
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-12 col-lg-4 order-summary">
				<div class="pl-24">
					<h4>{{$reviewOrderLabels['ORDERSUMMERY']}}</h4>
					<div class="dividers"></div>
					<div class="order-summary-box">
						<div class="place-order">
							<table>
								<tr>
									<td>{{$reviewOrderLabels['SUBTOTAL']}}</td>
									<td>{{$priceDetails['sub_total']}}</td>	
								</tr>
								<tr>
									<td>{{$reviewOrderLabels['DISCOUNT']}}</td>
									<td>{{$priceDetails['discount']}}</td>
								</tr>
								<tr>
									<td>{{$reviewOrderLabels['NET']}}</td>
									<td>{{$priceDetails['net']}}</td>
								</tr>
								<tr>
									<td>{{$reviewOrderLabels['VAT']}}</td>
									<td>{{$priceDetails['VAT']}}</td>
								</tr>								
								<tr>
									<td class="pm19">{{$reviewOrderLabels['SHIPPINGCOST']}}</td>
									<td class="pm19">{{$priceDetails['shipping_cost']}}</td>
								</tr>
								

								<tr>
									<th>{{$reviewOrderLabels['GRANDTOTAL']}}</th>
									<th>{{$priceDetails['grand_total']}}</th>
								</tr>
								<tr><td colspan="2"></td></tr>
								<tr>
									<td colspan="2">
										<!-- <label style="float:left;" for="cart_master_message">Message</label> -->
										<textarea class="form-control" name="cart_master_message" 
										id="cart_master_message" cols="80" rows="3" placeholder="{{$reviewOrderLabels['MESSAGE']}}">{{(!empty($cartMaster) ? $cartMaster->message : '')}}</textarea>
										<input type="hidden" id="cart_master_id" value="{{Session::get('cart_master_id')}}">
									</td>
								</tr>
							</table>
						</div>
					</div>
					<!-- <div class="coupon-text-box">
						<input type="text" class="input" placeholder="{{$reviewOrderLabels['ENTER_COUPON_CODE']}}" name="">
						<button class="fill-btn">{{$reviewOrderLabels['APPLY']}}</button>
					</div> -->
					
					<div class="dividers clear-both tb-24"></div>
					<input type="hidden" id="wait_for_while_msg" value="{{$reviewOrderLabels['PLSWAITFORWHILE']}}">
					<a class="fill-btn p-order" id="place_order_btn" href="javascript:void(0)">{{$reviewOrderLabels['PLACEORDER']}}</a>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Modal Start -->
<div class="modal fade bd-example-modal-lg" id="addBillingAddressModel" tabindex="-1" role="dialog" aria-labelledby="addBillingAddressModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addBillingAddressModelLabel">{{$reviewOrderLabels['ADDNEWBILLADDRESS']}}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="edit_shipping_address" method="POST">
					@csrf
					<input type="hidden" name="customer_id" id="customer_id" value="{{Session::get('customer_id')}}">															
					<input type="hidden" id="baseUrl" value="{{$baseUrl}}">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-6">
							<input style="margin-bottom: 15px;width:100%;" type="text" name="full_name" id="full_name" placeholder="{{$reviewOrderLabels['FULL_NAME']}}" class="input">							
							<input type="hidden" name="full_name_label" id="full_name_label" value="{{$reviewOrderLabels['FULLNAMEREQ']}}">
							<div class="error_full_name" style="margin: -15px 0px 10px 15px;color: red;"></div>							
							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="address_1" id="address_1" placeholder="{{$reviewOrderLabels['ADDRESS_LINE1_HINT']}}" class="input">
							<input type="hidden" name="address_1_label" id="address_1_label" value="{{$reviewOrderLabels['ADDRESS1REQ']}}">
							<div class="error_address_1" style="margin: -15px 0px 10px 15px;color: red;"></div>							
							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="address_2" id="address_2" placeholder="{{$reviewOrderLabels['MYADDRESSES3']}}" class="input">
							<input type="hidden" name="address_2_label" id="address_2_label" value="{{$reviewOrderLabels['ADDRESS2REQ']}}">
							<div class="error_address_2" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="number" name="mobile" id="mobile" placeholder="{{$reviewOrderLabels['MYACCOUNTLABEL3']}}" class="input">							
							<input type="hidden" name="mobile_label" id="mobile_label" value="{{$reviewOrderLabels['MOBILEREQ']}}">
							<input type="hidden" name="mobile_not_num_label" id="mobile_not_num_label" value="{{$reviewOrderLabels['MOBILENUM']}}">
							<input type="hidden" name="mobile_num_must_8_degit" id="mobile_num_must_8_degit" value="{{$reviewOrderLabels['MOBILEMUSTBE8DIGIT']}}">
							<div class="error_mobile" style="margin: -15px 0px 10px 15px;color: red;"></div>			

							<select style="margin-bottom: 15px;width:100%;" class="select" name="address_type" id="address_type">
								<option value="1">{{$reviewOrderLabels['addressType1']}}</option>
								<option value="2">{{$reviewOrderLabels['addressType2']}}</option>
							</select>
						</div>
						<div class="col-12 col-sm-12 col-md-6">
							<select style="margin-bottom: 15px;width:100%;" class="select" name="country" id="country">
								<!-- <option value="">--- {{$reviewOrderLabels['SELECTCOUNTRY']}} ---</option> -->
								@foreach($countries as $country)
								<option value="{{$country->id}}">{{$country->name}}</option>
								@endforeach
							</select>							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="state" id="state" placeholder="{{$reviewOrderLabels['MYADDRESSES11']}}" class="input">
							<input type="hidden" name="state_label" id="state_label" value="{{$reviewOrderLabels['STATEREQ']}}">							
							<div class="error_state" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="text" name="city" id="city" placeholder="{{$reviewOrderLabels['MYADDRESSES12']}}" class="input">
							<input type="hidden" name="city_label" id="city_label" value="{{$reviewOrderLabels['CITYREQ']}}">							
							<div class="error_city" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="text" name="pincode" id="pincode" placeholder="{{$reviewOrderLabels['MYADDRESSES10']}}" class="input">							
							<input type="hidden" name="pincode_label" id="pincode_label" value="{{$reviewOrderLabels['PINCODEREQ']}}">
							<div class="error_pincode" style="margin: -15px 0px 10px 15px;color: red;"></div>
							@if($errors->has('pincode'))
								<div class="error" style="margin: -15px 0px 10px 15px;color: red;">{{ $errors->first('pincode') }}</div>
							@endif
						</div>
					</div>
					<div class="row">
						<div class="col-12 normal-ck">
							<label class="ck">{{$reviewOrderLabels['SET_AS_DEFAULT_ADDRESS']}}
							<input type="checkbox" name="is_default" id="is_default" checked disabled>
							<span class="checkmark"></span>
							</label>
						</div>
					</div>					
					<div class="row">
						<div class="col-12 text-center">
							<input type="button" class="btn btn-primary" id="save_billing_address" value="Save" name="save_edit_customer_address">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$reviewOrderLabels['CANCEL']}}</button>
						</div>
					</div>
				</form>				
			</div>			
		</div>
	</div>
</div>
<!-- Modal Over -->
<!-- Modal Start -->
<div class="modal fade bd-example-modal-lg" id="editBillAddressModel" tabindex="-1" role="dialog" aria-labelledby="editBillAddressModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editBillAddressModelLabel">{{$reviewOrderLabels['EDIT_ADDRESS_TITLE']}}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="edit_shipping_address" method="POST">
					@csrf
					<input type="hidden" name="customer_id" id="customer_id" value="{{Session::get('customer_id')}}">					
					<input type="hidden" name="bill_address_id" id="bill_address_id">					
					<input type="hidden" id="baseUrl" value="{{$baseUrl}}">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-6">
							<input style="margin-bottom: 15px;width:100%;" type="text" name="e_full_name" id="e_full_name" placeholder="{{$reviewOrderLabels['FULL_NAME']}}" class="input" value="{{($billing_address) ? $billing_address->fullname : ''}}">							
							<input type="hidden" name="e_full_name_label" id="e_full_name_label" value="{{$reviewOrderLabels['FULLNAMEREQ']}}">
							<div class="e_error_full_name" style="margin: -15px 0px 10px 15px;color: red;"></div>							
							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="e_address_1" id="e_address_1" placeholder="{{$reviewOrderLabels['ADDRESS_LINE1_HINT']}}" class="input">
							<input type="hidden" name="e_address_1_label" id="e_address_1_label" value="{{$reviewOrderLabels['ADDRESS1REQ']}}">
							<div class="e_error_address_1" style="margin: -15px 0px 10px 15px;color: red;"></div>							
							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="e_address_2" id="e_address_2" placeholder="{{$reviewOrderLabels['MYADDRESSES3']}}" class="input">
							<input type="hidden" name="e_address_2_label" id="e_address_2_label" value="{{$reviewOrderLabels['ADDRESS2REQ']}}">
							<div class="e_error_address_2" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="number" name="e_mobile" id="e_mobile" placeholder="{{$reviewOrderLabels['MYACCOUNTLABEL3']}}" class="input" value="{{($billing_address) ? $billing_address->phone1 : ''}}">							
							<input type="hidden" name="e_mobile_label" id="e_mobile_label" value="{{$reviewOrderLabels['MOBILEREQ']}}">
							<input type="hidden" name="e_mobile_not_num_label" id="e_mobile_not_num_label" value="{{$reviewOrderLabels['MOBILENUM']}}">
							<input type="hidden" name="e_mobile_num_must_8_degit" id="e_mobile_num_must_8_degit" value="{{$reviewOrderLabels['MOBILEMUSTBE8DIGIT']}}">
							<div class="e_error_mobile" style="margin: -15px 0px 10px 15px;color: red;"></div>			

							<select style="margin-bottom: 15px;width:100%;" class="select" name="e_address_type" id="e_address_type">
								<option value="1">{{$reviewOrderLabels['addressType1']}}</option>
								<option value="2">{{$reviewOrderLabels['addressType2']}}</option>
							</select>
						</div>
						<div class="col-12 col-sm-12 col-md-6">
							<select style="margin-bottom: 15px;width:100%;" class="select" name="e_country" id="e_country">
								<!-- <option value="">--- {{$reviewOrderLabels['SELECTCOUNTRY']}} ---</option> -->
								@foreach($countries as $country)
								<option value="{{$country->id}}">{{$country->name}}</option>
								@endforeach
							</select>							
							<input style="margin-bottom: 15px;width:100%;" type="text" name="e_state" id="e_state" placeholder="{{$reviewOrderLabels['MYADDRESSES11']}}" class="input">
							<input type="hidden" name="e_state_label" id="e_state_label" value="{{$reviewOrderLabels['STATEREQ']}}">							
							<div class="e_error_state" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="text" name="e_city" id="e_city" placeholder="{{$reviewOrderLabels['MYADDRESSES12']}}" class="input">							
							<input type="hidden" name="e_city_label" id="e_city_label" value="{{$reviewOrderLabels['CITYREQ']}}">							
							<div class="e_error_city" style="margin: -15px 0px 10px 15px;color: red;"></div>

							<input style="margin-bottom: 15px;width:100%;" type="text" name="e_pincode" id="e_pincode" placeholder="{{$reviewOrderLabels['MYADDRESSES10']}}" class="input">							
							<input type="hidden" name="e_pincode_label" id="e_pincode_label" value="{{$reviewOrderLabels['PINCODEREQ']}}">
							<div class="e_error_pincode" style="margin: -15px 0px 10px 15px;color: red;"></div>							
						</div>
					</div>
					<div class="row">
						<div class="col-12 normal-ck">
							<label class="ck">{{$reviewOrderLabels['SET_AS_DEFAULT_ADDRESS']}}
							<input type="checkbox" name="e_is_default" id="e_is_default">
							<span class="checkmark"></span>
							</label>
						</div>
					</div>					
					<div class="row">
						<div class="col-12 text-center">
							<input type="button" class="btn btn-primary" id="save_edit_bill_customer_address" value="Save" name="save_edit_customer_address">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$reviewOrderLabels['CANCEL']}}</button>
						</div>
					</div>
				</form>
				<!-- <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				<input type="hidden" name="cart_id" id="cart_id">                    
				<p class="mb-0">{{$reviewOrderLabels['AREYOUSURE']}}</p> -->
			</div>
			<!-- <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$reviewOrderLabels['NO']}}</button>
				<button type="button" class="btn btn-primary" id="dlt_prod_from_cart">{{$reviewOrderLabels['YES']}}</button>
			</div> -->
		</div>
	</div>
</div>
<!-- Modal Over -->
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<!-- <script src="{{asset('public/assets/frontend/js/payment-method/payment-method.js')}}"></script> -->
<script src="{{asset('public/assets/frontend/js/review-order/review-order.js')}}"></script>
@endpush
