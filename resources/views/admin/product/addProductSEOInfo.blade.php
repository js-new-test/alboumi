<div class="row">
    <div class="col-md-12">
        
        <div class="mb-3 text-dark card-border card text-white">
            <div class="card-header  bg-light">
                <h6>SEO</h6>
            </div>
            <div class="card-body">
                <form id="seoForm" name="seoForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="editPage" id="editPage" value="SEO" readonly="true">
                    <input type="hidden" name="productId" id="productId">
                	<div class="form-row">
                		<div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label for="exampleEmail" class=""><span class="text-danger">*</span> Meta Title</label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                            	<input name="metaTitle" id="metaTitle" placeholder="Meta Title" type="text" class="form-control">
                            </div>
                        </div>
                        

                        <div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label for="examplePassword" class=""> Meta Keyword</label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                            	<textarea name="metaKeyword" id="metaKeyword" placeholder="Meta Keyword" type="text" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label for="examplePassword" class="">Meta Description</label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                            	<textarea name="metaDescription" id="metaDescription" placeholder="Meta Description" type="text" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="ml-auto">
                            <button class="btn-wide btn-pill btn-shadow btn-hover-shine btn btn-primary btn-lg" type="button" id="seoDetailsSubmit">Submit</button>
                        </div>
                	</div>
                	
                </form>
            </div>
        </div>
    </div>
</div>
