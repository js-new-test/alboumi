@extends('frontend.layouts.master')

@php
	$metaDesc = $categoryDetails->meta_description;
	if($metaDesc == null)
		$metaDesc = '';

	$metaKeywords = $categoryDetails->meta_keywords;
	if($metaKeywords == null)
		$metaKeywords = '';
@endphp

@section('description', $metaDesc )
@section('keywords', $metaKeywords )

@section('content')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{asset('public/assets/frontend/css/loader-style.css')}}">

@php
    $langId = Session::get('language_id');
    $visibility = App\Models\GlobalLanguage::checkVisibility($langId);
@endphp
<style>
	.ui-widget
	{
		font-size:0.3em;
	}
	.ui-widget-header
	{
		background: #062D7A;
	}
	.ui-widget.ui-widget-content{
		background:#D6D7D9;
	}
	.ui-corner-all, .ui-corner-top, .ui-corner-right, .ui-corner-tr{
		border-radius:0;
	}
	.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active{
		background: #062D7A;
		color: #062D7A;
	}
	.ui-slider-horizontal .ui-slider-handle{
		top: -0.5em;
		margin :0%;
	}
	.ui-slider .ui-slider-handle{
		width: 16px;
		height: 16px;
		border-radius: 50%;
	}
	#slider-range,#mob-slider-range
	{
		margin-top: 3%;
		width:93%;
		margin-right: 7%;
	}
	#priceTab span > input{
		opacity :1;
	}
	#priceTab p{
		font-size: 13px;
		color:#000;
	}
	.text-right{
		text-align: right !important;
	}

	.attr_items > .item > .ck > .checkmark .active::after {
    	content: '' !important;
		position:'absolute' !important;
		display:'none' !important;
	}
	#shopNowBtn{
		height:40px !important;
	}
	@media only screen and (max-width: 768px) {
		.btn_mobile{
			width:50% !important;
		}
	}
	.btn_mobile{
		width:25%;
	}
	a:hover {
		color: #212121;
		text-decoration: none;
	}
</style>
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
	var conversionRate = <?php echo json_encode($conversionRate); ?>;
	var minPrice = <?php echo json_encode($minPrice); ?>;
	var maxPrice = <?php echo json_encode($maxPrice); ?>;
	var language_id = <?php echo json_encode($langId); ?>;
	var currencyCode = <?php echo json_encode($currencyCode); ?>;
	var pageName = "categoryProducts";
	var slug = <?php echo json_encode($slug); ?>;
    var searchVal = '';

</script>
<div id="ajax-loader">
  <div class="cv-spinner">
  	<img src="{{ asset('public/assets/frontend/img/Loader.svg') }}" class="spinner">
  </div>
</div>

<!-- option in mobile view -->
<div class="filter-header d-flex justify-content-center align-item-center">
	<div class="d-flex sortIcons justify-content-center align-item-center">
		<img src="{{ asset('public/assets/frontend/img/Sort-1.png') }}">
		<span>{{ $productSortLabels['SORT'] }}</span>
	</div>
	<div class="d-flex filterIcons justify-content-center align-item-center">
		<img src="{{ asset('public/assets/frontend/img/Filter-1.png') }}">
		<span>{{ $productSortLabels['FILTER'] }}</span>
	</div>
</div>
<div class="filter-space"></div>

<section class="category-banner">
	@if(Session::get('language_id') == $defaultLangId)
		@if(empty($categoryDetails->main_banner))
			<img src="{{asset('public/assets/frontend/img/Banner.jpg')}}" class="desktop-img">
		@else
			<img src="{{asset('public/assets/images/categories/banner/'.$categoryDetails->main_banner)}}" class="desktop-img">
		@endif
		@if(empty($categoryDetails->main_mb_banner))
			<img src="{{asset('public/assets/frontend/img/M_Banner.jpg')}}" class="mobile-img">
		@else
			<img src="{{asset('public/assets/images/categories/mobile_banner/'.$categoryDetails->main_mb_banner)}}" class="mobile-img">
		@endif
	@else
		@if(empty($categoryDetails->banner_image))
			@if(empty($categoryDetails->main_banner))
			    <img src="{{asset('public/assets/frontend/img/Banner.jpg')}}" class="desktop-img">
			@else
				<img src="{{asset('public/assets/images/categories/banner/'.$categoryDetails->main_banner)}}" class="desktop-img">
			@endif
		@else
			<img src="{{asset('public/assets/images/categories/banner/'.$categoryDetails->banner_image)}}" class="desktop-img">
		@endif
		@if(empty($categoryDetails->mobile_banner))
			@if(empty($categoryDetails->main_mb_banner))
			    <img src="{{asset('public/assets/frontend/img/M_Banner.jpg')}}" class="mobile-img">
			@else
				<img src="{{asset('public/assets/images/categories/mobile_banner/'.$categoryDetails->main_mb_banner)}}" class="mobile-img">
			@endif
		@else
			<img src="{{asset('public/assets/images/categories/mobile_banner/'.$categoryDetails->mobile_banner)}}" class="mobile-img">
		@endif
	@endif
	<div class="overlay">
		<div class="container">
			<div class="row">
				<div class="col-12 col-sm-12 col-md-6 col-lg-4">
					<input type = "hidden" name="categoryId" id="categoryId" value="{{ $categoryDetails->id }}">
					{{--<h4>{{ $categoryDetails->title }}</h4>--}}
					<p class="m-0">@if(!empty($categoryDetails->description)) {!! $categoryDetails->description !!} @endif</p>
				</div>
			</div>
		</div>
	</div>
</section>

{{--@if(!empty($childCatgories))
<section class="menu-section">
	<div class="container">
		<div class="row">
			@foreach($childCatgories as $childCatgory)
			<div class="col-6 col-sm-6 col-md-4 col-lg-3">
				<img src="{{ asset('public/assets/images/categories/'.$childCatgory['category_image']) }}">
				<a href="{{ url('category/'. $childCatgory['slug'] )}}"><h6 class="text-center">{{ $childCatgory['title'] }}</h6></a>
			</div>
			@endforeach
		</div>
	</div>
</section>
@endif--}}

@if(count($productsList) > 0 && !empty($productsList[0]))
<input type="hidden" name="selectedVal" id="selectedVal">
<section>
	<div class="container">
	  <div class="row">
	  	<div class="col-md-4 col-lg-3 d-not-767">
	  		<div class="margin-top-73">
			  <p class="s1">{{ $productSortLabels['FILTERBY'] }} :</p>
			    <div class="acco-tabs">
					@if(!empty($resultFilterArr['category']))
					<div class="acco-tab" id="CategoryOptions">
						<input type="checkbox" id="chck1">
						<label class="acco-tab-label" for="chck1">{{ $productSortLabels['CATEGORIES'] }}</label>
						<div class="acco-tab-content">
							@foreach($resultFilterArr['category'] as $cat)
							<div class="item" id="onLoadCategory">
								<label class="ck">{{ $cat['title'] }}
								<input type="checkbox" id="catCheckbox_{{ $cat['id'] }}" name="catCheckbox" value="{{ $cat['id'] }}">
								<span class="checkmark"></span>
								</label>
							</div>
							@endforeach
						</div>
					</div>
					@endif
					@if(!empty($resultFilterArr['brands']))
					<div class="acco-tab" id="brandOptions">
						<input type="checkbox" id="chck3">
						<label class="acco-tab-label" for="chck3">{{ $productSortLabels['BRAND'] }}</label>
						<div class="acco-tab-content">
							@foreach($resultFilterArr['brands'] as $brand)
							<div class="item" id="onLoadBrand">
								<label class="ck">{{ $brand['name'] }}
								<input type="checkbox" id="brandCheckbox_{{ $brand['id'] }}" name="brandCheckbox" value="{{ $brand['id'] }}">
								<span class="checkmark"></span>
								</label>
							</div>
							@endforeach
						</div>
					</div>
					@endif
					@if(!empty($resultFilterArr['attributeGroups']))
					@foreach($resultFilterArr['attributeGroups'] as $group)
					<?php $attrGroups[] = $group['filterId']; ?>
					<div class="acco-tab attrTab_{{ $group['filterId'] }} attr_tabs_class" id="attributeTab_{{ $group['filterId'] }}" data-prod-ids="{{ $group['filterId'] }}">
						<input type="checkbox" id="chck2_{{ $group['filterId'] }}">
						<input type ="hidden" name="attribute_group_name" id="attribute_group_name" value="{{ $group['filterTypeName'] }}">
						<label class="acco-tab-label" for="chck2_{{ $group['filterId'] }}">{{ $group['filterTypeName'] }}</label>
						<div class="acco-tab-content items color-items" id="attributeTabContent_{{ $group['filterId'] }}">
							@foreach($group['data'] as $attribute)
							<div class="item">
								<label class="ck">
									@if($group['filterTypeName'] == $group['groupType'])
										<div class="d-flex justify-content-start align-items-center">
											<div class="s-color" style="background: {{ $attribute['color'] }}"></div>
											{{ $attribute['title'] }}
										</div>
									@else
										{{ $attribute['title'] }}
									@endif
								<input type="checkbox" id="attrCheckbox_{{ $group['filterId'] }}" name="attrCheckbox" value="{{ $attribute['id'] }}">
								<span class="checkmark"></span>
								</label>
							</div>
							@endforeach
						</div>
					</div>
					@endforeach
					<?php $attrGroupNames = implode(',',$attrGroups); ?>

					<input class="attrGroups" type ="hidden" name="attrGroups" id="attrGroups" value="{{ $attrGroupNames }}">
					@endif

					@if(count($resultFilterArr['price']) != 0)
						<div class="h-100 acco-tab mb-5" id="priceTab">
							<input type="checkbox" id="chck7">
							<label class="acco-tab-label" for="chck7">{{ $productSortLabels['PRICE'] }}</label>
							<div class="acco-tab-content">
								<div class="row">
									<div class="col-md-12">
										<div id="slider-range" class="slider-range"></div>
										<div class="row mt-2">
											@if($visibility->visibility == 0)
											<div class="col-md-6">
												<p class="font-weight-bold"> {{ $productSortLabels['MIN'] }} :
													<span><input type="text" id="minAmount" readonly style="border:0; color:#000;"></span>
												</p>
											</div>
											<div class="col-md-6">
												<p class="font-weight-bold"> {{ $productSortLabels['MAX'] }} :
													<span><input type="text" id="maxAmount" readonly style="border:0; color:#000;" class="w-100"></span>
												</p>
											</div>
											@endif
											@if($visibility->visibility == 1)
											<div class="col-md-6 text-right">
												<p class="font-weight-bold"> {{ $productSortLabels['MIN'] }} :
													<span><input type="text" id="minAmount" readonly style="border:0; color:#000;"></span>
												</p>
											</div>
											<div class="col-md-6 text-right p-0">
												<p class="font-weight-bold"> {{ $productSortLabels['MAX'] }} :
													<span><input type="text" id="maxAmount" readonly style="border:0; color:#000;" class="w-100"></span>
												</p>
											</div>
											@endif
										</div>
									</div>
								</div>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	  	<div class="col-md-8 col-lg-9 pad-top-40 pad-bottom-64">
			<div class="row listing-head">
				<div class="col-12 col-sm-12 col-md-6 mt-4">
					<h4>{{ $categoryDetails->title }}</h4>
				</div>
				<div class="col-12 col-sm-12 col-md-6 text-right d-not-767">
					<span>{{ $productSortLabels['SORTBY'] }}</span>
					<select class="select sort-select" name="sortDropdown" id="sortDropdown" onchange = "createFilterArray(this.value)">
						<option value="1">{{ $productSortLabels['MOSTRECENT'] }}</option>
						<option value="2">{{ $productSortLabels['ONSALE'] }}</option>
						<option value="3">{{ $productSortLabels['PRICELTOH'] }}</option>
						<option value="4">{{ $productSortLabels['PRICEHTOL'] }}</option>
					</select>
				</div>
			</div>
			<div class="row" id="productListing">
				@if(count($productsList) > 0 && !empty($productsList[0]))
				@foreach($productsList as $products)
				@foreach($products as $product)
				<div class="col-6 col-sm-6 col-md-6 col-lg-4 text-center product-box" id="prodList" data-prod-ids="{{$product['id']}}">
					<div class="stock-detail-parent">
						@if($product['flagInstock'] == 0)
						<div class="stock-label">
							<img src="{{ asset('public/assets/frontend/img/stock-banner.png')}}">
							<p>{{ $productSortLabels['OOS'] }}</p>
						</div>
						@endif
						<div class="content">
							<div class="content-overlay"></div>
								@if($product['image'] != null)
									<img src="{{ asset('public/images/product/'.$product['id'].'/'.$product['image']) }}">
								@else
									<img src="{{ asset('public/assets/images/no_image.png') }}">
								@endif
							<div class="content-details fadeIn-left">
							<a href="{{ url('product/'.$product['slug']) }}" class="blue-border-btn">{{ $productSortLabels['EXPLORE'] }}</a>
							</div>
						</div>
						<a href="{{ url('product/'.$product['slug']) }}" class="s1" id="prodName">{{ $product['title'] }}</a></br>
						<input type="hidden" id="prodIds" name="prodIds[]" value="{{  $product['id'] }}"></input>

						<span>{{ $productSortLabels['FROM'] }} {{ $currencyCode }}
							@if(empty($product['group_price']))
								@if(!empty($product['discountedPrice']) && (date("Y-m-d",strtotime($product['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($product['offer_end_date']))))
									@if(!empty($product['price']))
										<strike>{{ number_format($product['price'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</strike>
									@endif
									<span class="text-danger">{{ number_format($product['discountedPrice'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</span>
								@else
									<span>{{ number_format($product['price'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</span>
								@endif
							@else
								@if(!empty($product['price']))
									<strike>{{ number_format($product['price'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</strike>
								@endif
								<span class="text-danger">{{ number_format($product['group_price'] * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</span>
							@endif
						</span>
					</div>
				</div>
				@endforeach
				@endforeach
				@endif
			</div>
			
			@if(count($productsList)>0)
				<div class="text-center">
					<button class="load-more fill-btn btn_mobile" data-last-id="{{ $product['id'] }}" data-totalResult="{{ $totalProductsCount }}">{{ $productSortLabels['LOADMORE'] }}</button>
				</div>
    		@endif
			<div class="row d-none my-5" id="noProds">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-6 offset-md-3 text-center">
							<h4> {{ $productSortLabels['NOPRODAVAILABLE'] }} </h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	   </div>
	</div>
</section>
@else
<div class="row my-5">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-6 offset-md-3 text-center">
				<h4> {{ $productSortLabels['NOPRODAVAILABLE'] }} </h4>
			</div>
		</div>
	</div>
</div>
@endif

<!-- for mobile view -->
<!-- sort menu -->
<div class="sortMenu">
  <p class="s1">{{ $productSortLabels['SORTBY'] }}</p>
  <img src="{{ asset('public/assets/frontend/img/Close.svg') }}" class="closeIcons2 fixed-right" alt="close">
  <div class="sortbar-navigation">
	  <ul>
	      <li><a onClick="setSortByMobileValue(1)">{{ $productSortLabels['MOSTRECENT'] }}</a></li>
	      <li><a onClick="setSortByMobileValue(2)">{{ $productSortLabels['ONSALE'] }}</a></li>
	      <li><a onClick="setSortByMobileValue(3)">{{ $productSortLabels['PRICELTOH'] }}</a></li>
	      <li><a onClick="setSortByMobileValue(4)">{{ $productSortLabels['PRICEHTOL'] }}</a></li>
	  </ul>
	</div>
	<input type="hidden" name="selectedSortByMobile" id="selectedSortByMobile">
</div>

<!-- filter menu -->
<div class="filterMenu">
  <p class="s1">{{ $productSortLabels['FILTERBY'] }} :</p>
  <img src="{{ asset('public/assets/frontend/img/Close.svg') }}" class="closeIcons3 fixed-right" alt="close">

  <div class="row btn-section">
  	<div class="col-6">
  		<button class="border-btn" id="clearFilterBtn">{{ $productSortLabels['CLEAR_ALL'] }}</button>
  	</div>
  	<div class="col-6">
  		<button class="fill-btn closeIcons3" id="shopNowBtn">{{ $productSortLabels['SHOPNOW'] }}</button>
  	</div>
  </div>

  <div class="filter-divider"></div>
  <input type="hidden" name="selectedValM" id="selectedValM">

  <div class="acco-tabs">
		@if(!empty($resultFilterArr['category']))
			<div class="acco-tab" id="CategoryOptions">
				<input type="checkbox" id="f1">
				<label class="acco-tab-label" for="f1">{{ $productSortLabels['CATEGORIES'] }}</label>
				<div class="acco-tab-content">
				<input class="filterTitle" type ="hidden" name="attribute_id" id="attribute_id" value="Category">
					@foreach($resultFilterArr['category'] as $cat)
					<div class="item">
						<label class="ck">{{ $cat['title'] }}
						<input type="checkbox" id="catCheckbox_{{ $cat['id'] }}" name="catCheckbox" value="{{ $cat['id'] }}">
							<span class="checkmark"></span>
						</label>
					</div>
					@endforeach
				</div>
			</div>
		@endif
		@if(!empty($resultFilterArr['brands']))
			<div class="acco-tab" id="brandOptions">
				<input type="checkbox" id="f3">
				<label class="acco-tab-label" for="f3">{{ $productSortLabels['BRAND'] }}</label>
				<div class="acco-tab-content">
					<input class="filterTitle" type ="hidden" name="attribute_id" id="attribute_id" value="Brands">
					@foreach($resultFilterArr['brands'] as $brand)
					<div class="item" id="onLoadBrand">
						<label class="ck">{{ $brand['name'] }}
						<input type="checkbox" id="brandCheckbox_{{ $brand['id'] }}" name="brandCheckbox" value="{{ $brand['id'] }}">
						<span class="checkmark"></span>
						</label>
					</div>
					@endforeach
				</div>
			</div>
		@endif
		@if(!empty($resultFilterArr['attributeGroups']))
			@foreach($resultFilterArr['attributeGroups'] as $group)
			<?php $attrGroups[] = $group['filterId']; ?>
				<div class="acco-tab attrTabM_{{ $group['filterId'] }} attr_tabs_classM" id="attributeTabM_{{ $group['filterId'] }}">
					<input type="checkbox" id="f2_{{ $group['filterId'] }}">
					<input type ="hidden" name="attribute_group_name" id="attribute_group_nameM" value="{{ $group['filterTypeName'] }}">
					<label class="acco-tab-label" for="f2_{{ $group['filterId'] }}">{{ $group['filterTypeName'] }}</label>
					<div class="acco-tab-content items2 color-items">
						@foreach($group['data'] as $attribute)
						<div class="item2">
							<label class="ck">
								@if($group['filterTypeName'] == $group['groupType'])
									<div class="d-flex justify-content-start align-items-center">
										<div class="s-color" style="background: {{ $attribute['color'] }}"></div>
										{{ $attribute['title'] }}
									</div>
								@else
									{{ $attribute['title'] }}
								@endif
								<input type="checkbox" id="attrCheckbox_{{ $group['filterId'] }}" name="attrCheckbox" value="{{ $attribute['id'] }}">
								<span class="checkmark"></span>
							</label>
						</div>
						@endforeach
					</div>
				</div>
			@endforeach
			<?php $attrGroupNames = implode(',',$attrGroups); ?>

			<input class="attrGroups" type ="hidden" name="attrGroups" id="attrGroups" value="{{ $attrGroupNames }}">
		@endif

		@if(count($resultFilterArr['price']) != 0)
		<div class="h-100 acco-tab mb-5" id="priceTab">
			<input type="checkbox" id="f7">
			<label class="acco-tab-label" for="f7">{{ $productSortLabels['PRICE'] }}</label>
			<input class="filterTitle" type ="hidden" name="attribute_id" id="attribute_id" value="Price">
			<div class="acco-tab-content">
				<div class="row">
					<div class="col-md-12">
						<div id="slider-range" class="slider-range"></div>
						<div class="row mt-2">
                        @if($visibility->visibility == 0)
                            <div class="col-md-6">
                                <p class="font-weight-bold"> {{ $productSortLabels['MIN'] }} :
                                    <span><input type="text" id="mobMinAmount" readonly style="border:0; color:#000;"></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="font-weight-bold"> {{ $productSortLabels['MAX'] }} :
                                    <span><input type="text" id="mobMaxAmount" readonly style="border:0; color:#000;" class="w-100"></span>
                                </p>
                            </div>
                        @endif
                        @if($visibility->visibility == 1)
                            <div class="col-md-6 text-right">
                                <p class="font-weight-bold"> {{ $productSortLabels['MIN'] }} :
                                    <span><input type="text" id="mobMinAmount" readonly style="border:0; color:#000;"></span>
                                </p>
                            </div>
                            <div class="col-md-6 text-right p-0">
                                <p class="font-weight-bold"> {{ $productSortLabels['MAX'] }} :
                                    <span><input type="text" id="mobMaxAmount" readonly style="border:0; color:#000;" class="w-100"></span>
                                </p>
                            </div>
                        @endif
						</div>
					</div>
				</div>
			</div>
		</div>
		@endif
    </div>
</div>
@endsection
@push('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/category/category.js')}}"></script>
@endpush
