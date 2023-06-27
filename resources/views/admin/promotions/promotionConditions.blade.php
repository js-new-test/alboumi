@push('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
@endpush
<div class="tab-pane" id="promotionConditions" role="tabpanel">
    <div class="main-card mb-3 card">
        <div class="card-body">
        	<form method="post" action="{{url('/admin/promotions/promotionConditions')}}" name="promotionConditions">
        		@csrf
        		<input type="hidden" name="promotionId" id="promotionId" value="{{ $promotion->id}}" readonly="true">
				@if(!($conditions->isEmpty()))
				<?php
				$i = 0;
				$len = count($conditions->toArray());
				?>
				@foreach($conditions as $condition)
        		<div id="process_operands" class="process_input">
        			<div class="row">
            			<div class="col-md-3">
	                        <div class="position-relative form-group">
	                            <select class="form-control promotionOn" name="promotionOn[]" id="promotionOn_0" data='0'>
	                        		<option value="">Select Promotion On</option>
	                        		<option value="Brand" {{ $condition->promotion_on == 'Brand' ? 'selected' : ''}}>Brand</option>
	                        		<option value="Category" {{ $condition->promotion_on == 'Category' ? 'selected' : ''}}>Category</option>
	                        		<option value="Product" {{ $condition->promotion_on == 'Product' ? 'selected' : ''}}>Product</option>
	                        		<option value="Grand_Total" {{ $condition->promotion_on == 'Grand_Total' ? 'selected' : ''}}>Grand Total</option>
	                        	</select>
	                        </div>
	                    </div>
	                    <div class="col-md-3">
	                        <div class="position-relative form-group">
	                        	<select class="form-control conditionType" name="conditionType[]">
	                        		<option value="">Select Condition Type</option>
	                        		<option {{ $condition->condition_type == 'Equals To' ? 'selected' : ''}}>Equals To</option>
	                        		<option {{ $condition->condition_type == 'Not Equals To' ? 'selected' : ''}}>Not Equals To</option>
	                        	</select>
	                        </div>
	                    </div>

	                    <div class="col-md-3">
	                        <div class="position-relative form-group">
	                            <input name="promotionValue[]" type="text" class="form-control" id="promotionValue_0" value="{{ $condition->promotion_on_value }}">
	                        </div>
	                    </div>

	                    <div class="col-md-1">
	                        <div class="position-relative form-group">
	                        	<button type="button" class="btn loadModal" data='0'><i style='color:green' class='fa fa-copy'></i></button>
	                            
	                        </div>
	                    </div>
	                </div>
					@if($len == 1 && $i != 0)
						<a class="delete d-none" href="#">Remove</a>
					@endif
					@if ($i > 0) 
						<a class="delete" href="#">Remove</a>
					@endif
					<?php $i++; ?>
				</div>
				@endforeach
				@else
				<div id="process_operand" class="process_input">
        			<div class="row">
            			<div class="col-md-3">
	                        <div class="position-relative form-group">
	                            <select class="form-control promotionOn" name="promotionOn[]" id="promotionOn_0" data='0'>
	                        		<option value="">Select Promotion On</option>
	                        		<option value="Brand">Brand</option>
	                        		<option value="Category">Category</option>
	                        		<option value="Product">Product</option>
	                        		<option value="Grand_Total">Grand Total</option>
	                        	</select>
	                        </div>
	                    </div>
	                    <div class="col-md-3">
	                        <div class="position-relative form-group">
	                        	<select class="form-control conditionType" name="conditionType[]">
	                        		<option value="">Select Condition Type</option>
	                        		<option>Equals To</option>
	                        		<option>Not Equals To</option>
	                        	</select>
	                        </div>
	                    </div>

	                    <div class="col-md-3">
	                        <div class="position-relative form-group">
	                            <input name="promotionValue[]" type="text" class="form-control" id="promotionValue_0">
	                        </div>
	                    </div>

	                    <div class="col-md-1">
	                        <div class="position-relative form-group">
	                        	<button type="button" class="btn loadModal" data='0'><i style='color:green' class='fa fa-copy'></i></button>
	                            
	                        </div>
	                    </div>
	                </div>
				</div>
				@endif
				<div id="Padditionalselects"></div>
				<!-- <div id="Padditionalselects"></div>
				<p class="add_PO">Add more</p> -->
				<div>
					<button class="btn btn-success add_PO" type="button">+</button>
				</div>

        		<div class="offset-md-3 col-md-9">
                    <button type="submit" class="btn btn-primary" id="submitPromotionCondition">Save</button>
                    <!-- <a href="{{url('/admin/promotions')}}"> <button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button> </a> -->
                </div>
        	</form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{asset('public/assets/js/promotions/promotionConditions.js')}}"></script>
@endpush
