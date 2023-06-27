@extends('frontend.layouts.master')
<title>@if(!empty($product->meta_title)) {{ $product->meta_title }} - {{ $product->title }} @else {{ $pageName }} @endif| {{ $projectName}}</title>
<meta name="description" content="@if(!empty($product->meta_description)) <?php echo $product->meta_description; echo " - "; echo $product->title; ?> @endif">
<meta name="keywords" content="@if(!empty($product->meta_keyword)) <?php echo $product->meta_keyword; echo " - "; echo $product->title; ?> @endif">
<?php
$resolution_msg['width']='[MINWIDTH]';
$resolution_msg['height']='[MINHEIGHT]';
$resolution_msg['maxwidth']='[MAXWIDTH]';
$resolution_msg['maxheight']='[MAXHEIGHT]';
$resolution_maxupload['max_upload']='[MAXUPLOAD]';
$productimg_msg['width']=$product->image_min_width;
$productimg_msg['height']=$product->image_min_height;
$productimg_msg['maxwidth']=$product->image_max_width;
$productimg_msg['maxheight']=$product->image_max_height;
$productimg_max_upload['max_upload']=$product->max_images;
$resolutionmsg=$productDetailsLabels["IMAGERESOLUTIONMESSAGE"];
$resolutionmsg=str_replace($resolution_msg,$productimg_msg,$resolutionmsg);
$maxuploadmsg=$productDetailsLabels["MAXUPLOADNUMBER"];
$maxuploadmsg=str_replace($resolution_maxupload,$productimg_max_upload,$maxuploadmsg);
$grrpArr=array();
$grrpArrRadio=array();
if(!empty($DefaultData) && $DefaultData->attribute_ids){
foreach($arrAttributeGroups as $grps){
	if(!empty($grps) && ($grps['type']==1 || $grps['type']==4)){
	$grrpArr[]=str_replace(" ","",$grps['name']);
 }
 elseif(!empty($grps) && $grps['type']==2){
	 $grrpArrRadio[]=str_replace(" ","",$grps['name']);
 }
}
}

?>
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>;
	var slug = <?php echo json_encode($product->product_slug); ?>;
	var rate = <?php echo json_encode($rate); ?>;
	var langVisibility = <?php echo json_encode($langVisibility->visibility); ?>;
	var language_id = <?php echo json_encode($lang_id); ?>;
	var writemsg   =  <?php echo json_encode($productDetailsLabels["WRITEMESSAGEFORGIFT"]);?>;
	var amazmsg   =  <?php echo json_encode($productDetailsLabels["AMAZINGGIFTFORAMAZINGFAMILY"]);?>;
	var charlimit   =  <?php echo json_encode($productDetailsLabels["CHARLIMIT"]);?>;
	var uploadfile   =  <?php echo json_encode($productDetailsLabels["UPLOADFILE"]);?>;
	var filevalid   =  <?php echo json_encode($productDetailsLabels["FILEVALID"]);?>;
	var uploadLimit   =  <?php echo json_encode($maxuploadmsg);?>;
	var productBase   =  <?php echo json_encode($product->design_tool_product_id);?>;
	var cartMasterId   =  <?php echo json_encode(Session::get('cart_master_id'));?>;
	var customerId   =  <?php  echo json_encode(Session::get('customer_id'));?>;
	var imageMinWidth   =  <?php  echo json_encode($productimg_msg['width']);?>;
	var imageMinHeight   =  <?php  echo json_encode($productimg_msg['height']);?>;
	var imageMaxWidth   =  <?php  echo json_encode($productimg_msg['maxwidth']);?>;
	var imageMaxHeight   =  <?php  echo json_encode($productimg_msg['maxheight']);?>;
	var resolutionMsg   =  <?php  echo json_encode($resolutionmsg);?>;
	var invalidImages   =  <?php  echo json_encode($productDetailsLabels["INVALIDIMAGES"]);?>;
	var acceptedImages   =  <?php  echo json_encode($productDetailsLabels["ACCEPTEDIMAGES"]);?>;
	var addToCart   =  <?php  echo json_encode($productDetailsLabels["ADDTOCART"]);?>;
	var buyNow   =  <?php  echo json_encode($productDetailsLabels["BUYNOW"]);?>;
	var pleaseWait   =  <?php  echo json_encode($productDetailsLabels["PLEASEWAIT"]);?>;
	var pleaseWaitUpload   =  <?php  echo json_encode($productDetailsLabels["PLEASEWAITUPLOAD"]);?>;
	var grrpArr   =  <?php  echo json_encode($grrpArr);?>;
	var grrpArrRadio   =  <?php  echo json_encode($grrpArrRadio);?>;
	var maxImage   =  <?php  echo json_encode($product->max_images);;?>;

</script>
<style>

    /*set a border on the images to prevent shifting*/
#gallery_01 img{border:2px solid white;}
/*Change the colour*/
/*
.active img{border:2px solid #333 !important;}
*/

.clearfix {
    display: block;
    width: 100%;
    float: left;
}

.zoom-left {
    max-width: 412px;
}
.carousel-control-prev-icon {
 background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E") !important;
}
.carousel-control-next-icon {
  background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E") !important;
}
</style>
@section('content')
    @if($product)
    <div class="thumb-nav tb-11">
        <div class="container">
            <a href="{{ $baseUrl }}">{{$productDetailsLabels["CONTACTUSPAGELABEL3"]}}</a>
            @foreach($category_details as $category_detail)
                <a href="{{ $baseUrl }}/category/{{ $category_detail->slug }}">{{$category_detail->title}}</a>
            @endforeach
            <span>{{$product->title}}</span>
        </div>
    </div>
		@if(!empty($PhotoBooks) && count($PhotoBooks)>0)
    <section class="wedding-book">
		@else
		<section>
		@endif
        <div class="container">
            <div class="row img-customize">
                <div class="col-12 col-sm-12 col-md-6 col-lg-6 offset-xl-1 col-xl-5">
                    <div class="web-view">
                        <div class="big-img">
                            @if(!empty($productImages) && count($productImages)!=0)
                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="false">
                                <div class="carousel-inner">
                                    @php $count = 0; @endphp
                                    @foreach($productImages as $imagesDef)
                                    @php $count++; @endphp
                                        <div class="carousel-item {{($count == 1) ? 'active' : ''}} big_image_{{$count}}">
                                            <img id="bigImage" src="{{asset('/public/images/product/'.$product->id).'/'.$imagesDef->product_image}}" class="openImgInGallery" data-id="{{asset('/public/images/product/'.$product->id).'/'.$imagesDef->product_image}}" data-toggle="modal" data-bigimage="{{asset('/public/images/product/'.$product->id).'/'.$imagesDef->product_image}}" data-target="#gallery-modal">
                                        </div>
                                    @endforeach
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                            @else
                                <img id="bigImage" src="{{asset('/public/assets/images/no_image.png')}}" class="openImgInGallery" data-id="{{asset('/public/assets/images/no_image.png')}}" data-toggle="modal" data-bigimage="{{asset('/public/assets/images/no_image.png')}}" data-target="#gallery-modal">
                            @endif
                        </div>

                        <div class="small-img" style="justify-content:flex-start !important;">
                            @if(!empty($productImages))
                            <div class="owl-carousel loopwise-owl-carousel">
                                @foreach($productImages as $image)
                                    <div class="small-border" style="width: calc(70% - 19.2px);">
                                        <img src="{{asset('/public/images/product/'.$product->id).'/'.$image->product_image}}" class="smallImageHover">
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- <div class="zoom-left web-view">
                        <img style="border:1px solid #e8e8e6;" id="zoom_04" src="https://www.elevateweb.co.uk/wp-content/themes/radial/zoom/images/small/image3.png"
                        data-zoom-image="https://www.elevateweb.co.uk/wp-content/themes/radial/zoom/images/large/image3.jpg"
                        width="411"  /> </div> -->

                    <div class="mobile-view">
                        <div class="owl-carousel owl4 slider-img m-remove owl-theme our-service-carasoul">
													@if(!empty($productImages))
													@php $count = 0; @endphp
													@foreach($productImages as $image)
													@php $count++; @endphp
                            <div class="item text-center">
                                <div class="big-img {{($count == 1) ? 'active' : ''}} big_image_mobile_{{$count}}">
                                    <img src="{{asset('/public/images/product/'.$product->id).'/'.$image->product_image}} ">
                                </div>
                            </div>
														@endforeach
														@endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 t21767">

                    <div class="row">
                        <div class="col-10">
                            <h5 class="page-titles">{{$product->title}}</h5>
                                                        @if(!empty($DefaultData))
                                                        <input type="hidden" name="selected_option_id" id="selected_option_id" value="{{ $DefaultData->id }}">
                                                        @endif
                                                        <input type="hidden" name="product_id" id="product_id" value="{{ $product->id }}">
                        </div>
                        <div class="col-2 text-right"><?php
                            if(false)
                            {
                                ?><a href="#" class="share-icon">
                                    <img src="{{asset('/public/assets/frontend/img/PShare.png')}}">
                                </a><?php
                            }
                            ?><a href="https://www.facebook.com/sharer.php?u=<?php echo $baseUrl . '/product/' . $product->product_slug?>" class="share-icon">
                                <img src="{{asset('/public/assets/frontend/img/Facebook1.svg')}}">
                            </a>
                            <a href="https://twitter.com/share?url=<?php echo $baseUrl . '/product/' . $product->product_slug?>&text=<?php echo $product->title ?>" class="share-icon">
                                <img src="{{asset('/public/assets/frontend/img/Twitter.svg')}}">
                            </a>
                        </div>
                    </div>
										<!-- <div class="s1 mb13">BHD <span class="original_price" style="text-decoration:line-through;">11.000</span>
					<span class="selling_price" style="color:red;margin-left: 5px;">9.000</span>
					<span style="margin-left: 5px;">(Inc all taxes)</span></div> -->
                    @if($GroupPrice==0)
                        @if(!empty($DefaultData->offer_price) && (date("Y-m-d",strtotime($DefaultData->offer_start_date)) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($DefaultData->offer_end_date))))
                        <p class="s1 mb13">{{$Currency_symbol}}
                            <span class="pricespan">
                                <span class="original_price" style="text-decoration:line-through;">
                                {{ number_format( $DefaultData->selling_price * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</span><span class="selling_price" style="color:red;margin-left: 5px;">@if(!empty($DefaultData->offer_price))
                                {{ number_format($DefaultData->offer_price * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</span>
                            </span>
                            ({{ $productDetailsLabels['INCLOFVAT']}})@endif
                        </p>
                        @else
                        <p class="s1 mb13">{{$Currency_symbol}}
                            <span class="pricespan">
                                <span class="selling_price">@if(!empty($DefaultData->selling_price))
                                {{ number_format( $DefaultData->selling_price * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</span>
                            </span> ( {{$productDetailsLabels['INCLOFVAT']}} )@endif
                        </p>
                        @endif
                    @else
                    <p class="s1 mb13">{{$Currency_symbol}}
                        <span class="pricespan">
                            <span class="original_price" style="text-decoration:line-through;">{{ number_format( $DefaultData->selling_price * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</span><span class="selling_price" style="color:red;margin-left: 5px;">@if(!empty($GroupPrice)){{ number_format( $GroupPrice * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</span>
                        </span> ({{ $productDetailsLabels['INCLOFVAT']}})@endif
                    </p>
                    @endif

                    <span class="blurColor mb30">{!! $product->description !!}</span>

                    <!-- <div class="dividers"></div> -->

                    @if($category->lady_operator == 1)
                    <div class="prefer">
                        <label class="ck">{{ $productDetailsLabels['LADYOPERATORTEXT'] }}
                        <input type="checkbox" name="ladyoperator" id="ladyoperator">
                        <span class="checkmark"></span>
                        </label>
                    </div>
                    @endif
                    @if(!empty($DefaultData) && $DefaultData->attribute_ids)
										@foreach($arrAttributeGroups as $grps)
										@if(!empty($grps) && ($grps['type']==1 || $grps['type']==4))
										<div class="size-i">
											<p class="s1">{{$grps['display_name']}}</p>
											<p class="{{str_replace(" ","",$grps['name'])}}"></p>
											<a id="size-preview"></a>
										</div>
										@if($grps['type']==1)
										<select class="select s-bold w256" id="{{str_replace(" ","",$grps['name'])}}" onchange="getAttributeGroupData();">
													@foreach($grps['attributes'] as $attr)
													@if(in_array($attr['attribute_id'], $arrProductAttributes))
													<?php   $is_selected=''; ?>
													@if(in_array($attr['attribute_id'], $arrDefaultSelected))
													<?php $is_selected='selected';?>
													@endif
													<option value="{{ $attr['attribute_id'] }}" {{ $is_selected }}>{{ $attr['display_name'] }}</option>
													@endif
													@endforeach
												</select>
										@elseif($grps['type']==4)
										<div class="b-custome-select w256">
										<select class="my-image-selectpicker" id="{{str_replace(" ","",$grps['name'])}}" onchange="getAttributeGroupData();">
											@foreach($grps['attributes'] as $attr)
											@if(in_array($attr['attribute_id'], $arrProductAttributes))
											<?php   $is_selected=''; ?>
											@if(in_array($attr['attribute_id'], $arrDefaultSelected))
											<?php $is_selected='selected';?>
											@endif
											<option value="{{ $attr['attribute_id'] }}" data-thumbnail="{{asset('/public/assets/images/attributes/').'/'.$attr['image']}}" {{$is_selected}}>{{ $attr['display_name'] }}</option>
											@endif
											@endforeach
										</select>
									</div>
										@endif
												@elseif(!empty($grps) && $grps['type']==2)
												<div class="finish size-i">
													<p class="s1">{{$grps['display_name']}}</p>
													<p class="{{str_replace(" ","",$grps['name'])}}_Name"></p>
												</div>
												<div class="finishType">
													@foreach($grps['attributes'] as $attr)
													@if(in_array($attr['attribute_id'], $arrProductAttributes))
													<?php   $is_checked=''; ?>
													@if(in_array($attr['attribute_id'], $arrDefaultSelected))
													<?php $is_checked='checked';?>
													@endif
													<label class="rd">{{ $attr['display_name']}}
														<input id="{{str_replace(" ","",$grps['name'])}}" type="radio" value="{{ $attr['attribute_id'] }}" data-name="{{ $attr['display_name'] }}" {{ $is_checked }} name="{{str_replace(" ","",$grps['name'])}}" onchange="getAttributeGroupData();">
														<span class="rd-checkmark"></span>
													</label>
													@endif
													@endforeach
												</div>
												@elseif(!empty($grps) && $grps['type']==3)
												<div class="colors size-i">
													<p class="s1">{{$grps['display_name']}}</p>
													<p class="colorName"></p>
												</div>
												<div class="chooseColor">
													@foreach($grps['attributes'] as $attr)
													@if(in_array($attr['attribute_id'], $arrProductAttributes))
													@if(in_array($attr['attribute_id'], $arrDefaultSelected))
													<input type="hidden" value="{{ $attr['attribute_id']}}" name="color_id" id="color_id"/>
													<input type="hidden" value="{{ $attr['display_name']}}" name="colornamefield" id="colornamefield"/>
													@endif
													<div class="forBorder">
														<div id="{{ $attr['attribute_id']}}" class="{{ $attr['display_name']}}" style="background:{{ $attr['color']}};height: 100%;width:100%;border-radius: 100%;"></div>
													</div>
													@endif
													@endforeach
												</div>
												@endif
												@endforeach
											  @endif

                    <!-- write text -->
                    <!-- <p class="s1 mb12">Write Text Here</p>
                    <input type="text" name="textmsg" id="textmsg" class="input wth" value="" placeholder="Write message here"> -->
                    <!-- write text -->
                    <form id="ImageUpload" name="ImageUpload">
                    @if($category->photo_upload==1 )
										@if($category->upload_is_multiple==1)
                    <div class="prefer">
                        <label class="ck">{{ $productDetailsLabels['MULTIPLEIMAGEUPLOAD'] }}
                        <input type="checkbox" name="multipleimage" id="multipleimage">
                        <span class="checkmark"></span>
                        </label>
                    </div>
										@endif
										<input type="hidden" name="socialImgCount" value="" id="socialImgCount"/>
                        <div class="dynamic_images"></div>

                    <!-- upload file -->
                        <!-- <p class="s1 mb12 mt12">Upload Image</p>
                                            <div id="uploaadimgdiv">
                            <input type="file" id="real-file" hidden="hidden"  />
                                            </div>
                        <button type="button" id="upload-file" ><img src="{{asset('/public/assets/frontend/img/Image-upload.png')}}"> Choose File</button>
                        <p id="upload-text">No file choosen</p> -->
                                            <div class="dropdown choose-photo mt12">
                                              <button id="UploadPhoto" type="button" class="dropdown-toggle" data-toggle="dropdown">
                                               <img src="{{asset('/public/assets/frontend/img/Image-upload.png')}}"> {{ $productDetailsLabels['CHOOSEPHOTO']}}
                                              </button>
																							<a class="image-counts" style="display: none;"></a>
                                              <a class="clear-images" style="display: none;" href="javascript:void(0)">{{ $productDetailsLabels['CLEAR']}}</a>
                                              <div class="dropdown-menu">
                                                <a class="dropdown-item" >
                                                    <img src="{{asset('/public/assets/frontend/img/My-Computer.png')}}">{{ $productDetailsLabels['MYCOMPUTER']}}
                                                            <div id="uploaadimgdiv">
                                                        <input type="file" class="hidden-my-computer" name="real-file" id="real-file" >
                                                          </div>
                                                </a>
                                                <a class="dropdown-item openpopup" route="{{ route('gpredirect') }}" href="javascript:void(0)"><img src="{{asset('public/assets/frontend/img/Google-Photos.png')}}">{{ $productDetailsLabels['GOOGLEPHOTOS']}}</a>
                                                <a class="dropdown-item openpopup" route="{{ route('fbredirect') }}" href="javascript:void(0)"><img src="{{asset('public/assets/frontend/img/Facebook.png')}}">{{ $productDetailsLabels['FACEBOOK']}}</a>
                                                <a class="dropdown-item openpopup" route="{{ route('igredirect') }}" href="javascript:void(0)"><img src="{{asset('public/assets/frontend/img/Instagram.png')}}">{{ $productDetailsLabels['INSTAGRAM']}}</a>
                                              </div>
                                            </div>


                                        <div class="printStaffMsg">
                                            <p class="s1 mb12">{{$productDetailsLabels["WRITEHERE"]}}</p>
                                            <input type="text" name="printstaffmsg" id="printstaffmsg" maxlength="100" class="input wth" value="" placeholder='{{$productDetailsLabels["MSGPRINTSTAFF"]}}'>
                                        </div>
                    <!-- upload file -->
                                        @endif
                                    </form>


                    <p class="s1 mb12">{{$productDetailsLabels["QUANTITY"]}}</p>
                    <div class="plusminus horiz">
                    <button class="changeQty" disabled><img src="{{asset('/public/assets/frontend/img/Minus1.png')}}"></button>
                                        @if($category->photo_upload==0)
                                        @if(!empty($DefaultData))
                    <input type="number" id="productQty" name="productQty" value="1" min="1" max="{{ $DefaultData->quantity }}">
                                        @endif
                                        @else
                                        <input type="number" id="productQty" name="productQty" value="1" min="1" max="999999">
                                        @endif
                    <button class="changeQty"><img src="{{asset('/public/assets/frontend/img/Plus1.png')}}"></button>
                    </div>
                                        @if($category->photo_upload==0)
                                        @if(!empty($DefaultData))
                    <input type="hidden" name="available_qty" id="available_qty" value='{{ $DefaultData->quantity }}' >
                                        @endif
                                        @else
                                        <input type="hidden" name="available_qty" id="available_qty" value="999999" >
                                        @endif




                    <div class="delivery-by <?php if($product->flag_deliverydate==0) echo "d-none";?>">
                        <p>{{$productDetailsLabels["DELIVERYBY"]}}:</p>
                        <p class="s1" id="deliverydate">{{$delevery_date}}</p>
                    </div>

                    @if($product->can_giftwrap == 'Yes')
                    <div class="gift-wrap">
                        <label class="ck"><img src="{{asset('/public/assets/frontend/img/Gift.png')}}"> {{$productDetailsLabels["GIFTWRAPTHISITEM"]}}
                        <input type="checkbox" id="forMSG" name="forMSG">
                        <span class="checkmark"></span>
                        </label>
                                                <div class="showWhenChecked">

                                                </div>
                    </div>
                    @endif
										@if(!empty($PhotoBooks) && count($PhotoBooks)>0)
										<p class="s1 mb12">{{$productDetailsLabels["WRITECAPTIONBOOK"]}}</p>
										<input type="text" name="photobook_caption" id="photobook_caption" class="input wth" value="" placeholder="{{$productDetailsLabels['PHOTOBOOKPLACEHOLDER']}}">
										<div class="note">
											<p class="s2">{{$productDetailsLabels["NOTE"]}}:</p>
											<span class="blurColor">{{$productDetailsLabels["BOOKNOTE"]}}.</span>
									  </div>
										@endif
                    <div class="row">
											<?php
                      $disabled='';$class='';
											if(isset($DefaultData) && (($DefaultData->quantity==0 && $category->photo_upload==0) || $attributesActive==0)){
											$disabled='disabled';$class='fill-btn-disabled';}?>
                        <div class="col-6">
                            <button class="border-btn w100 addToCart {{$class}}" {{$disabled}}>{{$productDetailsLabels["ADDTOCART"]}}</button>
                        </div>
						@if($product->is_customized==0)
	                        <div class="col-6">
	                            <button class="fill-btn w100 buyNow {{$class}}" {{$disabled}}>{{$productDetailsLabels["BUYNOW"]}}</button>
	                        </div>
												@else
													<div class="col-6">
	                            <button class="fill-btn w100 designTool {{$class}}" {{$disabled}}>{{$productDetailsLabels["CUSTOMIZE"]}}</button>
	                        </div>
						@endif
                    </div>
										<br/><a class="accept-images" style="display: none;color:green;"></a>
										<br/><a class="invalid-images" style="display: none;color:red;"></a>
                </div>
            </div>
        </div>
    </section>



    @if(!empty($recommended_products) && count($recommended_products)>0)
    <section>
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 offset-xl-1 col-xl-10">
                    <div class="dividers"></div>
                    <div class="frequently-together">
                        <h4>{{$productDetailsLabels["FREQUENTLYBOUGHTTOGETHER"]}}</h4>

                        <div class="d-flex justify-content-start align-items-center drop-in-mobile">
                                                    <?php $totalprice=0;$i=1;?>
                                                    @foreach($recommended_products as $recpro)
                                                    <?php $totalprice=$totalprice+$recpro->price;?>
                            <div class="img-plus">
                                <img src="{{asset('/public/images/product/'.$recpro->id).'/'.$recpro->product_image}}">
                                                                @if($i < count($recommended_products))
                                <img src="{{asset('/public/assets/frontend/img/Plus1.png')}}" class="plus-img">
                                                                @endif
                                <!-- <img src="img/ProductListing/PF8.png"> -->
                                                                <?php $i++;?>
                            </div>
                                                    @endforeach
                            <div class="total-price">
                                <div class="tp d-flex justify-content-start align-items-center">
                                    <p>{{$productDetailsLabels["TOTALPRICE"]}}:</p>
                                    <p class="s1">{{$Currency_symbol}} {{ number_format($totalprice*$rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</p>
                                </div>
                                <button class="border-btn addtocartrecommended" >{{$productDetailsLabels["ADDTOCART"]}}</button>
                            </div>
                        </div>
                    </div>
                    <div class="dividers"></div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <section class="pricing-tabbing web-view">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 offset-xl-1 col-xl-10">

                    <div class="tab1">
                    @if($pricing_tab)
                    <button class="tablinks1" onclick="openTab(event, 'pricing')" id="defaultOpen">{{$productDetailsLabels["PRICING"]}}</button>
                    @endif
                                        @if($product->key_features)
                    <button class="tablinks1" onclick="openTab(event, 'descripation')" id="defaultOpen">{{$productDetailsLabels["PRODUCTDETAILSDESC"]}}</button>
                                        @endif
                    </div>

                    @if($pricing_tab)
                    <div id="pricing" class="tabcontent1">
                        <div class="table-responsive">
                            <table class="pricing-table">
                                <tr>
                                    <th><span>{{$productDetailsLabels["VARIATION"]}}</span></th>
                                                                        @if(!empty($pricing_tab_qtyrng))
                                                                        @foreach($pricing_tab_qtyrng as $range)
                                    <th><span>{{$range['from_quantity']}} @if($range['to_quantity']==0) {{ '+' }} @else {{ '- '.$range['to_quantity'] }} @endif {{$productDetailsLabels["QTY"]}}</span> ({{$productDetailsLabels["EACH"]}})</th>
                                                                        @endforeach
                                                                        @endif
                                </tr>
                                                                @if($pricing_tab_variations)
                                                                @foreach($pricing_tab_variations as $variants)
                                <tr>
                                    <td>{{$variants["displayname"]}}</td>
                                                                        @foreach($variants['pricedata'] as $prices)
                                          <td>{{$Currency_symbol}} {{ number_format($prices['price'] * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</td>
                                                                        @endforeach
                                </tr>
                                                                @endforeach
                                                                @endif
                            </table>
                        </div>
                    </div>
                    @endif

                    <div id="descripation" class="tabcontent1 descripation">
                        <p>{!! $product->key_features !!}</p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="pricing-description mobile-view">
        <div class="container">
            <div class="acco-tabs">
            <div class="acco-tab">
                <input type="checkbox" id="pricing-acco">
                                @if($pricing_tab)
                <label class="acco-tab-label" for="pricing-acco">{{$productDetailsLabels["PRICING"]}}</label>
                <div class="acco-tab-content all-size-tables">
                    <table class="sizeTables">
                                            <tr>
                                                    <th>{{$productDetailsLabels["VARIATION"]}}</th>
                                                    @if($pricing_tab_qtyrng)
                                                    @foreach($pricing_tab_qtyrng as $range)
                                                    <th>{{$range['from_quantity']}} @if($range['to_quantity']==0) {{ '+' }} @else {{ '- '.$range['to_quantity'] }} @endif {{$productDetailsLabels["QTY"]}} ({{$productDetailsLabels["EACH"]}})</th>
                                                    @endforeach
                                                    @endif
                                            </tr>
                                            @if($pricing_tab_variations)
                                            @foreach($pricing_tab_variations as $variants)
                                            <tr>
                                                    <td>{{$variants["displayname"]}}</td>
                                                    @foreach($variants['pricedata'] as $prices)
                                                        <td>{{$Currency_symbol}} {{ number_format($prices['price'] * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</td>
                                                    @endforeach
                                            </tr>
                                            @endforeach
                                            @endif
                    </table>
                    <!-- <table class="sizeTables">
                        <tr>
                            <th>Size (Inches)</th>
                            <th>6”x4”</th>
                        </tr>
                        <tr>
                            <td>1-10 Prints (each)</td>
                            <td>{{Session::get('currency_symbol')}} 0.150</td>
                        </tr>
                        <tr>
                            <td>11-49 Prints (each)</td>
                            <td>{{Session::get('currency_symbol')}} 0.125</td>
                        </tr>
                        <tr>
                            <td>50-99 Prints (each)</td>
                            <td>{{Session::get('currency_symbol')}} 0.100</td>
                        </tr>
                        <tr>
                            <td>100-199 Prints (each)</td>
                            <td>{{Session::get('currency_symbol')}} 0.080</td>
                        </tr>
                        <tr>
                            <td>200+ Prints (each)</td>
                            <td>{{Session::get('currency_symbol')}} 0.075</td>
                        </tr>
                    </table>
                    <table class="sizeTables">
                        <tr>
                            <th>Size (Inches)</th>
                            <th>6”x4”</th>
                        </tr>
                        <tr>
                            <td>1-10 Prints (each)</td>
                            <td>{{Session::get('currency_symbol')}} 0.150</td>
                        </tr>
                        <tr>
                            <td>11-49 Prints (each)</td>
                            <td>{{Session::get('currency_symbol')}} 0.125</td>
                        </tr>
                        <tr>
                            <td>50-99 Prints (each)</td>
                            <td>{{Session::get('currency_symbol')}} 0.100</td>
                        </tr>
                        <tr>
                            <td>100-199 Prints (each)</td>
                            <td>{{Session::get('currency_symbol')}} 0.080</td>
                        </tr>
                        <tr>
                            <td>200+ Prints (each)</td>
                            <td>{{Session::get('currency_symbol')}} 0.075</td>
                        </tr>
                    </table> -->
                </div>
                                @endif
            </div>
            <div class="acco-tab">
                <input type="checkbox" id="Description-acco">
                <label class="acco-tab-label" for="Description-acco">{{$productDetailsLabels["PRODUCTDETAILSDESC"]}}</label>
                <div class="acco-tab-content descripation">
                    {!!$product->key_features!!}
                </div>
            </div>
            </div>
        </div>
    </section>

    @if(!empty($product->flexmedia_code))
    <section>
        <div class="container">
            <div class="col-md-12">
                <div id="flixmedia-loading">Loading...</div>
                <script language="javascript" type="text/javascript">
                    window.onload = function(){ document.getElementById("flixmedia-loading").style.display = "none" }
                </script>

                <div id="flix-inpage"></div>

                <script
                    type="text/javascript"
                    src="https://media.flixfacts.com/js/loader.js"
                    data-flix-distributor="15752"
                    data-flix-language="<?php echo Session::get('language_code') ?>"
                    data-flix-brand="Sony"
                    data-flix-mpn="<?php echo $product->flexmedia_code; ?>"
                    data-flix-ean="<?php echo $product->flexmedia_code; ?>"
                    data-flix-flexmedia_code="<?php echo $product->flexmedia_code; ?>"
                    data-flix-button=""
                    data-flix-inpage="flix-inpage"
                    data-flix-button-image=""
                    data-flix-price=""
                    data-flix-fallback-language="c1"
                    async>
                </script>
            </div>
        </div>
    </section>
    @endif

    @if(!empty($related_products) && $related_products->count() > 0)
    <section class="you-may">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 offset-xl-1 col-xl-10">
                    <h4>{{$productDetailsLabels["YOUMAYLIKE"]}}</h4>
                    <div class="owl-carousel owl2 owl-theme our-service-carasoul show-right-arrow">
                        @foreach($related_products as $related_product)
                        <div class="item text-center">
													@if($related_product->flagInstock==0)
													<div class="stock-detail-parent">
										  			<div class="stock-label">
										  				<img src="{{asset('public/assets/frontend/img/stock-banner.png')}}">
										  				<p>{{$productDetailsLabels["OOS"]}}</p>
										  			</div>
														@endif
                            <div class="content">
                                <div class="content-overlay"></div>
                                <img src="{{asset('/public/images/product/'.$related_product->id).'/'.$related_product->product_image}}" style="border-radius:0% !important;">
                                <div class="content-details fadeIn-left">
                                <a href="{{$explore_link.'/'.$related_product->product_slug}}" class="blue-border-btn">{{$productDetailsLabels["EXPLORE"]}}</a>
                                </div>
                            </div>
                            <p class="s1">{{$related_product->title}}</p>
                            <span>{{$productDetailsLabels["FROM"]}} {{$Currency_symbol}}</span>
                            @if(empty($related_product['group_price']))
                                @if(!empty($related_product->offer_price) && (date("Y-m-d",strtotime($related_product->offer_start_date)) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($related_product->offer_end_date))))
                                    <strike>  {{ number_format($related_product->selling_price*$rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</strike>
                                    <span class="text-danger"> {{ number_format($related_product->offer_price*$rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</span>
                                @else
                                    <span> {{ number_format($related_product->selling_price*$rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</span>
                                @endif
                            @else
                                @if(!empty($related_product['selling_price']))
                                    <strike>{{ number_format($related_product['selling_price'] * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</strike>
                                @endif
                                <span class="text-danger">{{ number_format($related_product->group_price * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</span>
                            @endif
														@if($related_product->flagInstock==0)
														<!-- add html for stock label -->
												  		</div>
													  @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    @if($recent_viewed_products->count() > 0)
    <section class="recent-view">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 offset-xl-1 col-xl-10">
                    <h4>{{$productDetailsLabels["RECENTLYVIEWED"]}}</h4>
                    <div class="owl-carousel owl3 owl-theme our-service-carasoul show-right-arrow">
                        @foreach($recent_viewed_products as $recent_viewed_product)
                        <div class="item text-center">
													@if($recent_viewed_product->flagInstock==0)
														<!-- add html for stock label -->
													<div class="stock-detail-parent">
										  			<div class="stock-label">
										  				<img src="{{asset('public/assets/frontend/img/stock-banner.png')}}">
										  				<p>{{$productDetailsLabels["OOS"]}}</p>
										  			</div>
														@endif
                            <div class="content">
                                <div class="content-overlay"></div>
                                                                @if($recent_viewed_product->product_image)
                                <img src="{{asset('/public/images/product/'.$recent_viewed_product->id).'/'.$recent_viewed_product->product_image}}" style="border-radius:0% !important;">
                                                                @else
                                                                <img src="{{asset('/public/assets/images/no_image.png')}}" style="border-radius:0% !important;">
                                                                @endif
                                <div class="content-details fadeIn-left">
                                <a href="{{$explore_link.'/'.$recent_viewed_product->product_slug}}" class="blue-border-btn">{{$productDetailsLabels["EXPLORE"]}}</a>
                                </div>
                            </div>
                            <p class="s1">{{$recent_viewed_product->title}}</p>
                            <span>{{$productDetailsLabels["FROM"]}} {{$Currency_symbol}}</span>
                            @if(empty($recent_viewed_product['group_price']))
                                @if(!empty($recent_viewed_product->offer_price) && (date("Y-m-d",strtotime($recent_viewed_product->offer_start_date)) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($recent_viewed_product->offer_end_date))))
                                    <strike>  {{ number_format($recent_viewed_product->selling_price*$rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</strike>
                                    <span class="text-danger"> {{ number_format($recent_viewed_product->offer_price*$rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</span>
                                @else
                                    <span> {{ number_format($recent_viewed_product->selling_price*$rate,$decimalNumber, $decimalSeparator, $thousandSeparator)}}</span>
                                @endif
                            @else
                                @if(!empty($recent_viewed_product['selling_price']))
                                    <strike>{{ number_format($recent_viewed_product['selling_price'] * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</strike>
                                @endif
                                <span class="text-danger">{{ number_format($recent_viewed_product->group_price * $rate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</span>
                            @endif
														@if($recent_viewed_product->flagInstock==0)
														<!-- add html for stock label -->
												  		</div>
														@endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif


    @else
        <p>Product Not Found.</p>
    @endif

     <!-- show image popup -->
     <div class="modal fade" id="gallery-modal" tabindex="-1" role="dialog" aria-labelledby="galleryModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
							<button type="button" class="gallery-close-btn gallery-close-btn-prod-details" data-dismiss="modal" aria-label="Close">
									<img src="{{ asset('public/assets/frontend/img/Event-Gallery/Close.png')}}">
							</button>
                <div class="modal-body">
                    <!-- <img src="" alt="" id="galleryImage" class="img-fluid"> -->
										<div class="big-img">
												@if(!empty($productImages) && count($productImages)!=0)
												<div id="carouselExampleControls_popup" class="carousel slide" data-ride="carousel" data-interval="false">
														<div class="carousel-inner">
																@php $count = 0; @endphp
																@foreach($productImages as $imagesDef)
																@php $count++; @endphp
																		<div class="carousel-item {{($count == 1) ? 'active' : ''}}">
																				<img id="galleryImage" src="{{asset('/public/images/product/'.$product->id).'/'.$imagesDef->product_image}}" class="openImgInGallery" >
																		</div>
																@endforeach
														</div>
														<a class="carousel-control-prev" href="#carouselExampleControls_popup" role="button" data-slide="prev">
																<span class="carousel-control-prev-icon" aria-hidden="true"></span>
																<span class="sr-only">Previous</span>
														</a>
														<a class="carousel-control-next" href="#carouselExampleControls_popup" role="button" data-slide="next">
																<span class="carousel-control-next-icon" aria-hidden="true"></span>
																<span class="sr-only">Next</span>
														</a>
												</div>
												@else
														<img id="bigImage" src="{{asset('/public/assets/images/no_image.png')}}" class="openImgInGallery" data-id="{{asset('/public/assets/images/no_image.png')}}" data-toggle="modal" data-bigimage="{{asset('/public/assets/images/no_image.png')}}" data-target="#gallery-modal">
												@endif
										</div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/products/productdetails.js')}}"></script>
<script src="https://www.elevateweb.co.uk/wp-content/themes/radial/jquery.elevatezoom.min.js"></script>
<script>
(function ($) {
  $.fn.numberPicker = function() {
    var dis = 'disabled';
    return this.each(function() {
      var picker = $(this),
          p = picker.find('button:last-child'),
          m = picker.find('button:first-child'),
          input = picker.find('input'),
          min = parseInt(input.attr('min'), 10),
          max = parseInt(input.attr('max'), 10),
          inputFunc = function(picker) {
            var i = parseInt(input.val(), 10);
            if ( (i <= min) || (!i) ) {
              input.attr('value', min);
              p.prop(dis, false);
              m.prop(dis, true);
            } else if (i >= max) {
              input.attr('value', max);
              p.prop(dis, true);
              m.prop(dis, false);
            } else {
                            input.attr('value', i);
              p.prop(dis, false);
              m.prop(dis, false);
            }
          },
          changeFunc = function(picker, qty) {
            var q = parseInt(qty, 10),
                i = parseInt(input.val(), 10);
            if ((i < max && (q > 0)) || (i > min && !(q > 0))) {
              input.val(i + q);
              inputFunc(picker);
            }
          };
      m.on('click', function(){changeFunc(picker,-1);});
      p.on('click', function(){changeFunc(picker,1);});
      input.on('change', function(){inputFunc(picker);});
      inputFunc(picker); //init
    });
  };
}(jQuery));

$(document).ready(function() {

  $('.plusminus').numberPicker();
  $('.carousel').carousel({
        pause: 'hover'
    });
		if(langVisibility == 0)
						rtl = false;
				if(langVisibility == 1)
						rtl = true;
    $(".loopwise-owl-carousel").owlCarousel({
			  rtl : rtl,
        items:5,
        loop:false,
        margin:-50,
        autoplay:false,
        autoplayTimeout:3000,
        autoplayHoverPause:true
    });

});

function HandlePopupResult(result) {
    // console.log("result of popup is: ");
    // console.log(result);
    var MULTIPLE_IMAGE_COUNT = '{{config('app.MULTIPLE_IMAGE_COUNT')}}'
    var htm = "";
    $.each(result, function( index, value ) {
        // console.log(index)
        if(index<MULTIPLE_IMAGE_COUNT){
            htm += '<input type="hidden" name="socialimage['+index+']" id="socialimage_'+index+'" value="'+value+'" >';
        }
				$('#socialImgCount').val(index+1);
    });
    if(htm){
        $('.dynamic_images').html(htm);
        $('.clear-images').show();

    }
}
$(document).on('click','.clear-images',function(){
    if(confirm('Are you sure to remove all images')){
        $('.dynamic_images').html("");
        $('.clear-images').hide();
    }
});

$(document).on('click','.openpopup',function(){
    var route = $(this).attr('route');
    var selectType = $('#multipleimage').prop('checked');
    if(selectType){
        route +='/'+maxImage+'/multiple';
    }else{
        route +='/1/single';
    }
    window.open(route, '_blank', 'location=yes,height='+screen.availHeight+',width='+screen.availWidth+',scrollbars=yes,status=yes');
})
</script>
@endpush
