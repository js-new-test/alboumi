<div class="row">
    <div class="float-right">
        <!-- <button class="btn-primary">Delete All</button>
        <button class="btn-primary">Save</button> -->
        <!-- <button class="btn-primary">+ Select Files</button> -->
        <!-- <button class="btn-primary">+ Select from URL</button> -->
        <!-- <button class="btn-primary" id="uploadImages">Upload Files</button> -->
    </div>
</div>

<form enctype="multipart/form-data" name="imageForm" id="imageForm">
   {{csrf_field()}}
   <div class="form-row">
        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="exampleEmail" class=""> Select Image(s) </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative form-group">
                <input type="file" id="image-upload" accept="image/*" name="image_upload[]" enctype="multipart/form-data" multiple class="form-control">
                <small class="form-text text-muted">Image size should be {{config('app.products.width')}} X {{config('app.products.height')}} px.</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="position-relative form-group">
                <button type="submit" id="uploadImages" class="btn btn-primary">save Images</button>
                &nbsp
                <button type="button" class="btn btn-danger" id="deleteImages">Delete Image(s)</button>
            </div>
        </div>
    </div>
</form>

<div class="row mt-5">
    <div class="col-md-12">
        <table id="tableProductImage" class="table table-hover table-striped table-bordered" width="100%">
            <thead>
                <tr>
                    <th> <input type="checkbox" name="" id="imagesCheckAll"> </th>
                    <th>Image</th>
                    <th>Sort Order</th>
                    <th>Default Image</th>
                    <!-- <th>Base Image</th>
                    <th>Small Image</th>
                    <th>Thumbnail</th>
                    <th>Is Visible</th>
                    <th>Is Default</th>
                    <th>Action</th> -->
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-header">
                Video
            </div>
            <div class="card-body">
                <form name="videoForm" id="videoForm" enctype="multipart/form-data">
                    <input type="hidden" name="editPage" value="VIDEO" readonly="true">
                    <input type="hidden" name="productId" id="productId">
                    @csrf
                	<div class="form-row">
                		<div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label for="exampleEmail" class=""> Title <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                            	<input name="videoTitle" id="videoTitle" placeholder="Video Title" type="text" class="form-control">
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label> Type <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                                <select class="form-control" name="videoType" id="videoType">
                                    <option value="">Select</option>
                                    <option value="Youtube">Youtube</option>
                                    <option value="Vimeo">Vimeo</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="position-relative form-group">
                            	<label> Video URL <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                            	<input name="videoURL" id="videoURL" placeholder="Video URL" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="position-relative form-group">
                                <label> Status</label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="position-relative form-group">
                                <select class="form-control" name="videoStatus" id="videoStatus">
                                    <option value="Inactive">Inactive</option>
                                    <option value="Active">Active</option>
                                </select>
                            </div>
                        </div>

                        <div class="offset-md-3 col-md-9">
                            <button type="button" class="btn btn-primary" id="videoDetailsSubmit">Submit</button>
                            <a href="{{ url('admin/products') }}">
                                <button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button>
                            </a>
                        </div>

                        <!-- <div class="ml-auto">
                            <button class="btn-wide btn-pill btn-shadow btn-hover-shine btn btn-primary btn-lg" type="button" id="videoDetailsSubmit">Submit</button>
                        </div> -->
                	</div>

                </form>
            </div>
        </div>
    </div>
</div>
