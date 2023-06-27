<div class="row">
    <div class="col-md-12">
        
        <div class="mb-3 text-dark card-border card text-white">
            <div class="card-header  bg-light">
                <h6>Inventory</h6>
            </div>
            <div class="card-body">
                <form name="inventoryForm" id="inventoryForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="editPage" value="INVENTORY" readonly="true">
                    <input type="hidden" name="productId" id="productId">
                	
                    <div class="form-row">
                		<div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label for="exampleEmail" class=""> Low Stock Alert <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                                <select class="form-control" name="lowStockAlert" id="lowStockAlert">
                                    <option>Yes</option>
                                    <option>No</option>
                                </select>
                            </div>
                        </div>
                        

                        <div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label class=""> Low Stock <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                            	<input name="lowStockAlertQuantity" id="lowStockAlertQuantity" placeholder="Low Stock" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label class="">Maximum Quantity per Order</label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                            	<input name="maximumQuantityPerOrder" id="maximumQuantityPerOrder" placeholder="Maximum Quantity per Order" type="text" class="form-control">
                            </div>
                        </div>

                        <!-- <div class="ml-auto">
                            <button class="btn-wide btn-pill btn-shadow btn-hover-shine btn btn-primary btn-lg" type="button" id="inventoryDetailsSubmit">Submit</button>
                        </div> -->

                        <div class="offset-md-3 col-md-9">
                            <button type="button" class="btn btn-primary" id="inventoryDetailsSubmit">Submit</button>

                            <a href="{{ url('admin/products') }}">
                                <button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button>
                            </a>
                        </div>
                	</div>
                	
                </form>
            </div>
        </div>
    </div>
</div>
