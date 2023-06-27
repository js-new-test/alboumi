<div class="row">
    <div class="col-md-12">
        
        <div class="mb-3 text-dark card-border card text-white">
            <div class="card-header  bg-light">
                <h6>COD Pincode</h6>
            </div>
            <div class="card-body">
                <form method="post" action="editProduct" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="editPage" value="CODPINCODE" readonly="true">
                    <input type="hidden" name="productId" id="productId">
                	<div class="form-row">
                		<div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label for="exampleEmail" class=""><span class="text-danger">*</span> Include State</label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                            	<input name="state[]" id="autocomplete_txt" placeholder="States" type="text" class="form-control autocomplete_txt">
                            </div>
                        </div>
                        

                        <div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label for="examplePassword" class=""> Exclude Pincodes</label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                            	<input name="codPincodeFile" id="codPincodeFile" placeholder="Meta Title" type="file" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                                <a href="#" class="pincodeFileDownload" part="null">Download Sample File</a> |
                                <a href="#" class="pincodeFileDownload" part="CODPINCODE">Export Existing Pincodes</a>
                            </div>
                        </div>

                        <div class="ml-auto">
                            <button class="btn-wide btn-pill btn-shadow btn-hover-shine btn btn-primary btn-lg" type="Submit">Submit</button>
                        </div>
                	</div>
                	
                </form>
            </div>
        </div>
    </div>
</div>
