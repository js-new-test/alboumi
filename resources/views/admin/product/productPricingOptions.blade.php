<div class="row">
    <div class="col-md-12">

        <div class="mb-3 text-dark card-border card text-white">
            <div class="card-header  bg-light">
                <h6>Variants</h6>
            </div>
            <div class="card-body">



            	<form enctype="multipart/form-data" id="specificationForm" name="specificationForm" method="post" action="{{$baseUrl}}/admin/product/editPricingOption">

                	@csrf
                	<input type="hidden" name="editPage" id="editPage" value="SPECIFICATION" readonly="true">
                	<input type="hidden" name="productId" id="productId" value="@if(!empty($productDetails)){{$productDetails['id']}}@endif">
                  <input type="hidden" name="categoryId" id="categoryId" value="@if(!empty($productDetails)){{$productDetails['product']['category_id']}}@endif">
                  <input type="hidden" name="optioncount" id="optioncount" value="@if(!empty($productDetails)){{count($pricingOption)}}@endif">
                  @if(!empty($pricingOption) && count($pricingOption)>0)
                  <?php $i=0;?>
                  @foreach($pricingOption as $option)

	            	<div class="text-dark card-border card text-white process_input mb-3" id="processOperands">
	            		<div class="card-header bg-light">
	            			<div class="col-md-3">
			                	<h6>Options</h6>
	            			</div>
	            			<div class="offset-md-7 col-md-2">
			                	<button type="button" class="btn btn-info addMoreAttribute" id="addMoreAttribute">+</button>
                        <a href="#" data-id="{{$option['id']}}" class="btn btn-danger delete" type="btn">X</a>
	            			</div>
			            </div>

		            	<div class="card-body">
		            		<!-- <form id="specificationForm" name="specificationForm"> -->
			                	<!-- <div id="processOperands" class="process_input" style="border: 1px black dotted"> -->
			                		<div class="form-row">
				                		<!-- <div id="testing"></div> -->
                                @if(!empty($categoryAttrData))
                                @foreach($categoryAttrData['attributeGroup'] as $key=>$value)
                                <div class="col-md-3">
                                  <div class="position-relative form-group">
                                    <label > {{ $value }} <span class="text-danger">*</span></label>
                                  </div>
                                </div>
                                <div class="col-md-9">
                                  <div class="position-relative form-group">
                                    <select class="form-control attribute {{ $value }}" id="{{ $value }}_{{ $i }}" name="{{ $key }}[]" data='0' attributeGroupId="{{ $key }}">
                                      <!-- <option>Select {{$value}}</option> -->
                                      @foreach($categoryAttrData['attributes'][$value] as $v)
                                      <?php $is_selected="";?>
                                      @if(in_array($v['id'], explode(',',$option['attribute_ids'])))
                                      <?php $is_selected="selected='selected'";?>
                                      @endif
                                      <option value="{{$v['id']}}" {{ $is_selected }}>{{$v['name']}}</option>
                                      @endforeach
                                    </select>
                                  </div>
                                </div>
                                @endforeach
                                @endif

				                		<!-- <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label>Size <span class="text-danger">*</span></label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<select class="form-control attribute Size" name="Size" id="Size_0">
				                            		<option value=" ">Select Size</option>
				                            		<option>1</option>
				                            		<option>2</option>
				                            	</select>
				                            </div>
				                        </div> -->


				                		<div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label>SKU <span class="text-danger">*</span></label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input name="sku[]" id="sku_{{ $i }}" placeholder="SKU" type="text" value="{{ $option['sku'] }}" class="form-control sku">
				                            </div>
				                        </div>


				                        <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> MRP </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="text" class="form-control mrp" name="mrp[]" placeholder="MRP" id="mrp_{{$i}}" value="{{$option['mrp']}}">
				                            </div>
				                        </div>

				                        <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Selling Price <span class="text-danger">*</span></label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="text" class="form-control sellingPrice" name="sellingPrice[]"  placeholder="Selling Price" id="sellingPrice_{{$i}}" value="{{$option['selling_price']}}">
				                            </div>
				                        </div>

				                        <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Offer Price </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="text" class="form-control offerPrice" name="offerPrice[]" placeholder="Offer Price" id="offerPrice_{{$i}}" value="{{$option['offer_price']}}">
				                            </div>
				                        </div>

                                <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Offer Start Date </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="text" class="form-control datepicker offerStartDate" name="offerStartDate[]" placeholder="Offer Start Date" id="offerStartDate_{{$i}}" value="@if(!empty($option['offer_start_date'])){{date('m/d/Y',strtotime($option['offer_start_date']))}}@endif">
				                            </div>
				                        </div>

                                <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Offer End Date </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group ">
				                            	<input type="text" class="form-control datepicker offerEndDate" name="offerEndDate[]" placeholder="Offer End Date" id="offerEndDate_{{$i}}" value="@if(!empty($option['offer_end_date'])){{date('m/d/Y',strtotime($option['offer_end_date']))}}@endif">
				                            </div>
				                        </div>

				                        <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Inventory <span class="text-danger">*</span></label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="text" class="form-control quantity" name="quantity[]" placeholder="Quantity" id="quantity_{{$i}}" value="{{$option['quantity']}}">
				                            </div>
				                        </div>
                                <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Image </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="file" enctype="multipart/form-data" class="form-control image" name="image[]" placeholder="Image" id="image_{{$i}}">
				                            </div>
                                    @if($option['image']!='')
                                    <div style="position: relative;width: 140px;height: 100px;float:left;">
                                        <img style="width: 130px;height: 85px;position: absolute;" src="{{asset('public/images/product/'.$productDetails['product_id'].'/pricingoption').'/'.$option['image']}}" alt="current_image">
                                    </div>
                                    <a href="#" data-id="{{$option['id']}}" class="delete-image text-danger" type="btn"><i class="fa fa-trash image_delete" aria-hidden="true" ></i></a>
                                    @endif
				                        </div>


				                        <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Is Default </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="radio" value="{{$option['id']}}" class="isDefault" name="isDefault" id="isDefault_{{$i}}" @if($option['is_default']==1) {{'checked'}}@endif/>
				                            </div>
				                        </div>
                                <input type="hidden"  class="pricingId" name="pricingId[]" id="pricingId_{{$i}}" value="{{$option['id']}}"/>

			                		</div>


			                	<!-- </div> -->

		            	</div>

	            	</div>
                <?php $i++;?>
                @endforeach
                @else
                <div class="text-dark card-border card text-white process_input mb-3" id="processOperands">
	            		<div class="card-header bg-light">
	            			<div class="col-md-3">
			                	<h6>Options</h6>
	            			</div>
	            			<div class="offset-md-7 col-md-2">
			                	<button type="button" class="btn btn-info addMoreAttribute" id="addMoreAttribute">+</button>
                        <a href="#" data-id="0" class="btn btn-danger delete" type="btn">X</a>
	            			</div>
			            </div>

		            	<div class="card-body">
		            		<!-- <form id="specificationForm" name="specificationForm"> -->
			                	<!-- <div id="processOperands" class="process_input" style="border: 1px black dotted"> -->
			                		<div class="form-row">
				                		<!-- <div id="testing"></div> -->
                                @if(!empty($categoryAttrData))
                                @foreach($categoryAttrData['attributeGroup'] as $key=>$value)
                                <div class="col-md-3">
                                  <div class="position-relative form-group">
                                    <label > {{ $value }} <span class="text-danger">*</span></label>
                                  </div>
                                </div>
                                <div class="col-md-9">
                                  <div class="position-relative form-group">
                                    <select class="form-control attribute {{ $value }}" id="{{ $value }}_0" name="{{ $key }}[]" data='0' attributeGroupId="{{ $key }}">
                                      <!-- <option>Select {{$value}}</option> -->
                                      @foreach($categoryAttrData['attributes'][$value] as $v)
                                      <option value="{{$v['id']}}" >{{$v['name']}}</option>
                                      @endforeach
                                    </select>
                                  </div>
                                </div>
                                @endforeach
                                @endif

				                		<!-- <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label>Size <span class="text-danger">*</span></label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<select class="form-control attribute Size" name="Size" id="Size_0">
				                            		<option value=" ">Select Size</option>
				                            		<option>1</option>
				                            		<option>2</option>
				                            	</select>
				                            </div>
				                        </div> -->


				                		<div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label>SKU <span class="text-danger">*</span></label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input name="sku[]" id="sku_0" placeholder="SKU" type="text" value="" class="form-control sku">
				                            </div>
				                        </div>


				                        <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> MRP </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="text" class="form-control mrp" name="mrp[]" placeholder="MRP" id="mrp_0" value="">
				                            </div>
				                        </div>

				                        <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Selling Price <span class="text-danger">*</span></label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="text" class="form-control sellingPrice" name="sellingPrice[]"  placeholder="Selling Price" id="sellingPrice_0" value="">
				                            </div>
				                        </div>

				                        <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Offer Price </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="text" class="form-control offerPrice" name="offerPrice[]" placeholder="Offer Price" id="offerPrice_0" value="">
				                            </div>
				                        </div>

                                <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Offer Start Date </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="text" class="form-control datepicker offerStartDate" name="offerStartDate[]" placeholder="Offer Start Date" id="offerStartDate_0" value="">
				                            </div>
				                        </div>

                                <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Offer End Date </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group ">
				                            	<input type="text" class="form-control datepicker offerEndDate" name="offerEndDate[]" placeholder="Offer End Date" id="offerEndDate_0" value="">
				                            </div>
				                        </div>

				                        <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Inventory <span class="text-danger">*</span></label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="text" class="form-control quantity" name="quantity[]" placeholder="Quantity" id="quantity_0" value="">
				                            </div>
				                        </div>
                                <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Image </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="file" class="form-control image" name="image[]" placeholder="Image" id="image_0">
				                            </div>
				                        </div>

				                        <div class="col-md-3">
				                            <div class="position-relative form-group">
				                            	<label> Is Default </label>
				                            </div>
				                        </div>
				                        <div class="col-md-9">
				                            <div class="position-relative form-group">
				                            	<input type="radio" value="0" class="isDefault" name="isDefault" id="isDefault_0" />
				                            </div>
				                        </div>
                                <input type="hidden"  class="pricingId" name="pricingId[]" id="pricingId_0" value="0"/>

			                		</div>


			                	<!-- </div> -->

		            	</div>

	            	</div>
                @endif
		                		<div id="Padditionalselects"></div>

		                        <div class="offset-md-3 col-md-9">
		                            <button type="submit" class="btn btn-primary" id="specificarionDetailsSubmit">Submit</button>
		                            <a href="{{ url('admin/products') }}">
		                                <button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button>
		                            </a>
		                        </div>


                </form>




            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{asset('/public/assets/js/vendors/form-components/datepicker.js')}}"></script>
@endpush
