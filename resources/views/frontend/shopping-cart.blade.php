@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$cartLabels['SHOPPINGCARTDESC']}}">
<meta name="keywords" content="{{$cartLabels['SHOPPINGCARTKEYWORD']}}">

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
		<a href="{{url('/')}}">{{$cartLabels['HOME']}}</a>
		<span>{{$cartLabels['MYCART']}}</span>
	</div>
</div>

@if(count($cart_arr) > 0)
<section class="shopping-cart dynamic_section_remove">
	<div class="container">
			<h4>{{$cartLabels['MYCART']}}</h4>

			<div class="row">
				<div class="col-12 offset-sm-1 col-sm-10 offset-md-0 col-md-12">
					<div class="table-to-div">
						<div class="shopping-table order-3">
							<div class="table-heading">
								<div class="table-column text-center w177">
									<p class="s1">{{$cartLabels['ITEMS']}}</p>
								</div>
								<div class="table-column">
									<p class="s1">{{$cartLabels['PRODUCTNAME']}}</p>
								</div>
								{{--<div class="table-column">
									<p class="s1">{{$cartLabels['DELIVERYDETAILS']}}</p>
								</div>--}}
								<div class="table-column w110">
									<p class="s1">{{$cartLabels['QUANTITY']}}</p>
								</div>
								<div class="table-column w110">
									<p class="s1">{{$cartLabels['UNITPRICE']}}</p>
								</div>
								<div class="table-column text-right w130">
									<p class="s1">{{$cartLabels['SUBTOTAL']}}</p>
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
									<div class="remove-add">
										<a href="javascript:void(0)" class="remove_product" data-cart-id="{{$cart['id']}}">{{$cartLabels['REMOVE']}}</a>
										<!-- <a href="#">Add to Wishlist</a> -->
									</div>
								</div>

								{{-- <div class="table-column d-date">
									<p>Shipping Charges: BHD 5.000</p>
								</div> --}}
								<div class="table-column quantity w110">
									<p class="s1 d-show-767">{{$cartLabels['QUANTITY']}}</p>
									<div class="plusminus horiz">
										<button class="remove_qty" {{($cart['qty'] == '1') ? 'disabled' : ''}} data-id="{{$cart['qty']}}"><img src="{{asset('/public/assets/frontend/img/Minus1.png')}}"></button>
										<input type="number" class="productQty" name="productQty" value="{{$cart['qty']}}" min="1">
										<button class="add_qty"><img src="{{asset('/public/assets/frontend/img/Plus1.png')}}"></button>
									</div>
									<input type="hidden" class="cart_id" value="{{$cart['id']}}">
								</div>
								<div class="table-column w110">
									<!-- <p class="s1 d-show-767">{{$cartLabels['SUBTOTAL']}}</p> -->
									<p class="s1">{{$cart['unitPrice']}}</p>
								</div>
								<div class="table-column sub-total text-right w130">
									<p class="s1 d-show-767">{{$cartLabels['SUBTOTAL']}}</p>
									<p class="s1">{{$cart['subTotal']}}</p>
								</div>
							</div>
							@endforeach
						</div>

						<a class="fill-btn shopping-btn order-5" href="{{$baseUrl}}">{{$cartLabels['CONTINUESHOPPING']}}</a>
						<input type="hidden" name="enter_promo_code_label" id="enter_promo_code_label" value="{{$cartLabels['ENTERPROMOCODE']}}">
						<div class="coupon-text-box order-1">
							<input type="text" class="input promo_code_apply_input" placeholder="{{$cartLabels['ENTER_COUPON_CODE']}}" name="coupon_code" id="coupon_code"
							value="{{($priceDetails['promo_code'] != '') ? $priceDetails['promo_code'] : ''}}">
							<button class="fill-btn promo_code_apply_btn" data-cart-id="{{$cart_master_id}}" id="{{($priceDetails['promo_code'] != '') ? 'remove_coupon_code_btn' : 'apply_coupon_code_btn'}}">{{($priceDetails['promo_code'] != '') ? $cartLabels['REMOVE'] : $cartLabels['APPLY']}}</button>
							<!-- <div class="promo_code_input_error"></div> -->
						</div>

						<div class="dividers clear-both order-4"></div>

						<div class="place-order order-2">
							<table>
								<tr>
									<td>{{$cartLabels['SUBTOTAL']}}</td>
									<td id="sub_total">{{$priceDetails['sub_total']}}</td>
								</tr>
								<tr>
									<td>{{$cartLabels['DISCOUNT']}}</td>
									<td id="discount">{{$priceDetails['discount']}}</td>
								</tr>
								<tr>
									<td>{{$cartLabels['NET']}}</td>
									<td id="net">{{$priceDetails['net']}}</td>
								</tr>
								<tr>
									<td>{{$cartLabels['VAT']}}</td>
									<td id="vat">{{$priceDetails['VAT']}}</td>
								</tr>
								<tr>
									<td class="pm19">{{$cartLabels['SHIPPINGCOST']}}</td>
									<td class="pm19" id="shipping_cost">{{$priceDetails['shipping_cost']}}</td>
								</tr>


								<tr>
									<th>{{$cartLabels['GRANDTOTAL']}}</th>
									<th id="grand_total">{{$priceDetails['grand_total']}}</th>
								</tr>
							</table>

							<a class="fill-btn" href="{{Auth::guard('customer')->check() ? url('/customer/shipping-address') : url('/login').'?flagCheckout=1'}}">{{$cartLabels['PLACEORDER']}}</a>
						</div>

					</div>
				</div>
			</div>
	</div>
</section>
@else
<section class="shopping-cart">
	<div class="container">
		<div>
			<h4>{{$cartLabels['SHOPPINGCARTEMPTY']}}</h4>
		</div>
	</div>
</section>
@endif
<div id="shopping_cart_empty"></div>
<!-- Modal Start -->
<div class="modal fade bd-example-modal-sm" id="deleteCartProductModel" tabindex="-1" role="dialog" aria-labelledby="deleteCartProductModelLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="deleteCartProductModelLabel">{{$cartLabels['CONFIRMATION']}}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
					<input type="hidden" name="cart_id" id="cart_id">
					<p class="mb-0">{{$cartLabels['AREYOUSURE']}}</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$cartLabels['NO']}}</button>
					<button type="button" class="btn btn-primary" id="dlt_prod_from_cart">{{$cartLabels['YES']}}</button>
				</div>
			</div>
		</div>
	</div>
<!-- Modal Over -->
<!-- Modal Start -->
<div class="modal fade bd-example-modal-sm" id="removePromoCodeModel" tabindex="-1" role="dialog" aria-labelledby="removePromoCodeModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="removePromoCodeModelLabel">{{$cartLabels['CONFIRMATION']}}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				<input type="hidden" name="cart_master_id" id="cart_master_id">
				<p class="mb-0">{{$cartLabels['AREYOUSURE']}}</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$cartLabels['NO']}}</button>
				<button type="button" class="btn btn-primary" id="dlt_promo_code_btn">{{$cartLabels['YES']}}</button>
			</div>
		</div>
	</div>
</div>
<!-- Modal Over -->
<!-- Modal Start -->
<div class="modal fade bd-example-modal-sm" id="applyingPromoCodeModel" tabindex="-1" role="dialog" aria-labelledby="applyingPromoCodeModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="applyingPromoCodeModelLabel">{{$cartLabels['MESSAGE']}}</h5>
				<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button> -->
			</div>
			<div class="modal-body">
				<!-- <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				<input type="hidden" name="cart_master_id" id="cart_master_id">                     -->
				<p class="mb-0">{{$cartLabels['APPLYINGPROMOCODE']}}</p>
			</div>
			<!-- <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$cartLabels['NO']}}</button>
				<button type="button" class="btn btn-primary" id="dlt_promo_code_btn">{{$cartLabels['YES']}}</button>
			</div> -->
		</div>
	</div>
</div>
<!-- Modal Over -->
<!-- Modal Start -->
<div class="modal fade bd-example-modal-sm" id="removingPromoCodeModel" tabindex="-1" role="dialog" aria-labelledby="removingPromoCodeModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="removingPromoCodeModelLabel">{{$cartLabels['MESSAGE']}}</h5>
				<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button> -->
			</div>
			<div class="modal-body">
				<!-- <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				<input type="hidden" name="cart_master_id" id="cart_master_id">                     -->
				<p class="mb-0">{{$cartLabels['REMOVEPROMOCODE']}}</p>
			</div>
			<!-- <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{$cartLabels['NO']}}</button>
				<button type="button" class="btn btn-primary" id="dlt_promo_code_btn">{{$cartLabels['YES']}}</button>
			</div> -->
		</div>
	</div>
</div>
<!-- Modal Over -->

@endsection
@push('scripts')
<script type="text/javascript">
(function($) {
    $(document).ready(function(){
        localStorage.setItem('LUMISE-CART-DATA', '');
    });
})(jQuery);
</script>
<script src="{{asset('public/assets/frontend/js/shopping-cart/shopping_cart.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
@endpush
