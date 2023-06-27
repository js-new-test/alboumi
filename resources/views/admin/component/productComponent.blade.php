<div class="modal mt-5 fade" id="productComponentModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Product</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="productCounterValue" id="productCounterValue" readonly="true">
                <div class="page-title-actions mb-3">
                    <div class="d-inline-block dropdown">
                        <a href="#divFilter" class="btn btn-square btn-primary btn-sm"data-toggle="collapse"> <i aria-hidden="true" class="fa fa-filter"></i> Filter </a>
                    </div>
                </div>

                <div class="main-card mb-3 card collapse" id="divFilter">
                    <div class="card-body">
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i> Product Filter</h5>
                        <form id="productFilter">
                            @csrf
                            <input type="hidden" name="productCounterValue" id="productCounterValue" readonly="true">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">Product Name <span class="text-danger">*</span></label>
                                        <div>
                                            <input type="text" class="form-control" id="productName" name="productName" placeholder="Enter product name"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="timezone_offset">Category</label> 
                                        <select name="selectCategory" id="selectCategory" class="multiselect-dropdown form-control">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <div>
                                            <select name="status" id="status" class="multiselect-dropdown form-control">
                                            <option>Pending</option>
                                            <option>Hidden</option>
                                            <option>Rejected</option>
                                            <option>Active</option>
                                            <option>Inactive</option>
                                            <option>Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Brand <span class="text-danger">*</span></label>
                                        <div>
                                            <select name="brandName" id="brandNameId" class="multiselect-dropdown form-control">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" id="searchProduct">Search</button>
                                <button type="button" class="btn btn-light" name="cancel" value="Cancel">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <table id="tableProducts" class="table table-hover table-striped table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="productCheckAll" id="productCheckAll"></th>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        <div class="float-right mt-3">
                            <button type="button" class="btn btn-primary" id="saveProducts">Save</button>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
</div>
