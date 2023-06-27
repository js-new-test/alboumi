<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">Enter Information about the product you want to sell</h5>
        <form class="col-md-12 mx-auto" name="generalDetails" id="generalDetails" enctype="multipart/form-data">
            @csrf

            <div class="row">
                @if(!empty($otherLanguages))

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleEmail">Language </label>

                        <input type="hidden" name="page" id="page" value="{{$page}}" readonly="true">
                        @if($page != 'anotherLanguage')
                            <div>
                                <div class="position-relative form-group">
                                    <label for="exampleEmail"> {{ $defaultLanguage }} </label>
                                    <input type="hidden" name="defaultLanguage" id="defaultLanguage" value="{{ $defaultLanguageId }}" readonly="true">
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="productId" id="productId" value="{{$productId}}" readonly="true">
                            <div>
                                <div class="position-relative form-group">
                                  <select class="form-control multiselect-dropdown" name="defaultLanguage" id="defaultLanguage">
                                  </select>
                                </div>
                            </div>
                        @endIf
                    @else
                        <input type="hidden" name="defaultLanguageId" value="{{ $defaultLanguageId }}">
                    @endif
                    </div>
                </div>


                <div class="col-md-6">
                    @if($page != 'anotherLanguage')
                        <div class="form-group">
                            <label for="price" class="font-weight-bold">Brand Name <span class="text-danger">*</span></label>
                            <div>
                                <select class="form-control multiselect-dropdown" name="brandName" id="brandName">
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @if($page != 'anotherLanguage')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Category <span class="text-danger">*</span></label>
                            <div>
                                <select class="form-control multiselect-dropdown" name="categoryName" id="categoryName">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Title </label>
                        <div>
                            <input name="title" id="title" placeholder="Title" type="text" class="form-control" onInput="generateSlug()">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @if($page != 'anotherLanguage')
                    <div class="form-group">
                        <label for="is_active" class="font-weight-bold">Product Slug
                            <span class="text-danger">*</span>
                        </label>
                        <div>
                            <input name="productSlug" id="productSlug" placeholder="Product Slug" type="text" class="form-control">
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Key Features </label>
                        <div>
                            <textarea name="description" id="description" placeholder="Description" type="text" class="form-control ckeditor"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Description </label>
                        <div>
                            <textarea name="keyFeatures" id="keyFeatures" placeholder="Description" type="text" class="form-control ckeditor"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            @if($page != 'anotherLanguage')
            <div class="row">
              <div class="col-md-6 ">
                  <div class="form-group">
                      <label class="font-weight-bold">Can Gift Wrap </label>
                      <div>
                          <select class="form-control" name="canGiftWrap" id="canGiftWrap">
                              <option>No</option>
                              <option>Yes</option>
                          </select>
                      </div>
                  </div>
              </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active" class="font-weight-bold">Length (Including packaging)(in Cm) <span class="text-danger">*</span>
                        </label>
                        <div>
                            <input class="form-control input-mask-trigger" name="length" id="length">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                      <label class="font-weight-bold">Width (Including packaging)(in Cm)<span class="text-danger">*</span> </label>
                      <div>
                          <input class="form-control input-mask-trigger" name="width" id="width">
                      </div>
                  </div>
              </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active" class="font-weight-bold">Height (Including packaging)(in Cm)<span class="text-danger">*</span>
                        </label>
                        <div>
                            <input class="form-control input-mask-trigger" name="height" id="height">
                        </div>
                    </div>
                </div>

            </div>
            @endif
            <div class="row">
                @if($page != 'anotherLanguage')
                  <div class="col-md-6">
                      <div class="form-group">
                          <label for="is_active" class="font-weight-bold">Weight (in Kg) <span class="text-danger">*</span>
                          </label>
                          <div>
                              <input class="form-control input-mask-trigger" name="weight" id="weight">
                          </div>
                      </div>
                  </div>
                @endif

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Product Type / Comments</label><span><a href="#" data-toggle="tooltip" title="Type of product to pass in Aramex"><i class="fa fa-info"></i></a><span>
                        <div>
                            <input name="product_type" id="product_type" placeholder="Product Type / Comments" type="text" class="form-control">
                        </div>
                    </div>
                </div>
          </div>

            @if($page != 'anotherLanguage')
            <div class="row d-none ImageRes">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Min Image Width (in px) <span class="text-danger">*</span> </label>
                        <div>
                            <input class="form-control input-mask-trigger" name="image_min_width" id="image_min_width">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active" class="font-weight-bold">Min Image Height (in px) <span class="text-danger">*</span>
                        </label>
                        <div>
                            <input class="form-control input-mask-trigger" name="image_min_height" id="image_min_height">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Max Image Width (in px) <span class="text-danger">*</span> </label>
                        <div>
                            <input class="form-control input-mask-trigger" name="image_max_width" id="image_max_width">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active" class="font-weight-bold">Max Image Height (in px) <span class="text-danger">*</span>
                        </label>
                        <div>
                            <input class="form-control input-mask-trigger" name="image_max_height" id="image_max_height">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active" class="font-weight-bold">Max Images <span class="text-danger">*</span>
                        </label>
                        <div>
                            <input class="form-control input-mask-trigger" name="max_images" id="max_images">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
              @if($page != 'anotherLanguage')

              <div class="col-md-6">

              <div class="form-group">
                  <!-- <div class="position-relative form-check"> -->
                      <label class="font-weight-bold">Status</label>
                  <!-- </div> -->
                  <div>
                      <select class="form-control" id="status" name="status">
                          <!-- <option value="Pending">Pending</option>
                          <option value="Hidden">Hidden</option>
                          <option value="Approved">Approved</option>
                          <option value="Rejected">Rejected</option> -->
                          <option value="Active">Active</option>
                          <option value="Inactive">Inactive</option>
                          <!-- <option value="Deleted">Deleted</option> -->
                      </select>
                  </div>
              </div>

          </div>
          <div class="col-md-6">

          <div class="form-group">
              <!-- <div class="position-relative form-check"> -->
                  <label class="font-weight-bold">Tax Class</label>  <span class="text-danger">*</span>
              <!-- </div> -->
              <div>
                  <select class="form-control" id="tax_class_id" name="tax_class_id">

                  </select>
              </div>
          </div>

        </div>
        @endif
        </div>
        <div class="row">
            @if($page != 'anotherLanguage')
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Flexmedia Code</label>
                        <div>
                            <input name="flexmedia_code" id="flexmedia_code" placeholder="Flexmedia Code" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                  @endif
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active" class="font-weight-bold">Meta Title <span class="text-danger">*</span>
                        </label>
                        <div>
                            <input name="metaTitle" id="metaTitle" placeholder="Meta Title" type="text" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Meta Keyword <span class="text-danger">*</span> </label>
                        <div>
                            <textarea name="metaKeyword" id="metaKeyword" placeholder="Meta Keyword" type="text" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Meta Description </label>
                        <div>
                            <textarea name="metaDescription" id="metaDescription" placeholder="Meta Description" type="text" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            @if($page != 'anotherLanguage')
                <div class="row">
                <div class="col-md-6 ml-2per" >
                    <div class="form-group">
                        <!-- <div class="position-relative form-check"> -->
                            <label class="font-weight-bold">
                                <input type="checkbox" class="form-check-input" name="isCustomized" id="isCustomized"> Is Customized Product?
                            </label>
                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <!-- <div class="position-relative form-check"> -->
                            <label class="font-weight-bold">
                                <input type="checkbox" class="form-check-input" name="flag_deliverydate" id="flag_deliverydate" checked> Display Delivery Date?
                            </label>
                        <!-- </div> -->
                    </div>
                </div>

                <div class="col-md-6 d-none lumiseproduct">
                        <div class="form-group">
                            <label for="price" class="font-weight-bold">Design Tool Product</label>
                            <div>
                                <select class="form-control multiselect-dropdown" name="lumise_product_id" id="lumise_product_id">
                                </select>
                            </div>
                        </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="printing_product" class="font-weight-bold mr-3">Printing Product</label>
                        <div style="display:unset">
                            <div class="custom-radio custom-control custom-control-inline">
                                <input class="custom-control-input" type="radio" id="printing_product_no" name="printing_product" value="0" checked>
                                <label class="custom-control-label" for="printing_product_no">No</label>
                            </div>
                            <div class="custom-radio custom-control custom-control-inline">
                                <input class="custom-control-input" type="radio" id="printing_product_yes" name="printing_product" value="1">
                                <label class="custom-control-label" for="printing_product_yes">Yes</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2 offset-md-4">
                                <button class="btn btn-primary" type="button" id="generalInfo">Add Product</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ url('admin/products') }}">
                                    <button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    CKEDITOR.replace('description', {
        filebrowserUploadUrl: "{{route('ckeditor.upload_prod_image', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });
    CKEDITOR.replace('keyFeatures', {
        filebrowserUploadUrl: "{{route('ckeditor.upload_prod_image', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });
</script>
@endpush
