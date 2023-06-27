<div class="row">
    <div class="col-md-12">
        
        <div class="mb-3 text-dark card-border card text-white">
            <div class="card-header  bg-light">
                <h6>Bulk Pricing</h6>
            </div>
            <div class="card-body">
            	<form id="formID">
	            	<table id="tblBulkPricing" class="table table-hover table-striped table-bordered" width="100%">
	            		<thead id="tblHeadBulkPricing">
	            			<tr id="trBulkPricing">
	            				
	            			</tr>
	            		</thead>
	            		<tbody id="tblBodyBulkPricing">
	            		</tbody>
	            	</table>

	            	<div class="offset-md-3 col-md-9">
                        <button type="button" class="btn btn-primary" id="bulkPricingSubmit">Submit</button>
                        <a href="{{ url('admin/products') }}">
                            <button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button>
                        </a>
                    </div>
            	</form>
            </div>
        </div>
    </div>
</div>