@extends('admin.layouts.master')
<title>{{$pageTitle }} | {{ $projectName }} </title>

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <style type="text/css">
        .ui-autocomplete {
            z-index: 100;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var language = <?php echo json_encode($language); ?>;
    var defaultLanguageId = <?php echo json_encode($defaultLanguageId); ?>;
    var productDetails = <?php echo json_encode($productDetails); ?>;
    var quantityMatrix = <?php echo json_encode($quantityMatrix); ?>;
</script>
<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
    @include('admin.include.header')
	<div class="app-main">
        @include('admin.include.sidebar')
	    <div class="app-main__outer">
	        <div class="app-main__inner">
	        	<div class="app-page-title">
                    <div class="page-title-wrapper">

                        <div class="page-title-heading">
                            <div>
                                <div class="page-title-head center-elem">
                                    <span class="d-inline-block pr-2">
                                        <i class="active_icon metismenu-icon pe-7s-cart"></i>
                                    </span>
                                    <span class="d-inline-block">Product</span>
                                </div>
                                <div class="page-title-subheading opacity-10">
                                    <nav class="" aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="javascript:void(0);">Product</a></li>
                                            <li class="active breadcrumb-item" aria-current="page"><a href="{{url('/admin/products')}}">Product List</a></li>
                                            <li class="active breadcrumb-item" aria-current="page">Edit Product</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


            	<div class="main-card mb-3 card">
                    <div class="card-header">
                    	Ony by One Product Upload
                    	<hr/>
                    </div>
                    <div class="card-body">

                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                            	<a data-toggle="tab" href="#tabAddProductGeneralInfo" class="active nav-link">General</a>
                            </li>

                            <li class="nav-item commonElement">
                            	<a data-toggle="tab" href="#tabAddProductImageVideo" class="nav-link">Image/Video</a>
                            </li>
                            <!-- <li class="nav-item commonElement">
                            	<a data-toggle="tab" href="#tabAddProductInventory" class="nav-link">Inventory</a>
                            </li> -->

                            <li class="nav-item commonElement">
                                <a data-toggle="tab" href="#tabProductPricingOptions" class="nav-link">Pricing Options</a>
                            </li>
                            <li class="nav-item commonElement">
                                <a data-toggle="tab" href="#tabAdvancePricing" class="nav-link">Advance Pricing</a>
                            </li>
                            <li class="nav-item commonElement" id="bulkPricing">
                                <a data-toggle="tab" href="#tabBulkPricing" class="nav-link">Bulk Pricing</a>
                            </li>
                            <li class="nav-item d-none">
                                <a data-toggle="tab" href="#tabProductSpecification" class="nav-link">Specification</a>
                            </li>
                            <li class="nav-item commonElement">
                                <a data-toggle="tab" href="#tabRelatedRecomendedProduct" class="nav-link">Related/Recomended Products</a>
                            </li>
                            <li class="nav-item d-none">
                                <a data-toggle="tab" href="#tabProductHistory" class="nav-link">History</a>
                            </li>
                            <li class="nav-item d-none">
                                <a data-toggle="tab" href="#tabProductReview" class="nav-link">Review</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tabAddProductGeneralInfo" role="tabpanel">
                            	@include('admin.product.editProductGeneralInfo')
                            </div>
                            <div class="tab-pane" id="tabAddProductImageVideo" role="tabpanel">
                            	@include('admin.product.addProductImageVideo')
                            </div>
                            <div class="tab-pane" id="tabAdvancePricing" role="tabpanel">
                              @include('admin.product.advancePricing')
                            </div>
                            <div class="tab-pane" id="tabAddProductInventory" role="tabpanel">
                            	@include('admin.product.addProductInventory')
                            </div>
                            <div class="tab-pane" id="tabProductPricingOptions" role="tabpanel">
                                @include('admin.product.productPricingOptions')
                            </div>
                            <div class="tab-pane" id="tabBulkPricing" role="tabpanel">
                                @include('admin.product.productBulkPricing')
                            </div>
                            <div class="tab-pane" id="tabProductSpecification" role="tabpanel">
                                @include('admin.product.productSpecification')
                            </div>
                            <div class="tab-pane" id="tabRelatedRecomendedProduct" role="tabpanel">
                                @include('admin.product.relatedRecomendedProduct')
                            </div>
                            <div class="tab-pane" id="tabProductHistory" role="tabpanel">
                                @include('admin.product.productHistory')
                            </div>
                            <div class="tab-pane" id="tabProductReview" role="tabpanel">
                                @include('admin.product.productReview')
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Related product modal -->
<div class="modal fade" id="addRelatedProductModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Related Products</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form class="">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Select Category</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <select class="form-control multiselect-dropdown" name="selectCategory" id="selectCategory">
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="productId" id="productIdddd">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Select Brand</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <select class="form-control multiselect-dropdown" name="selectBrandForRelated" id="selectBrandForRelated">
                                </select>
                            </div>
                        </div>
                    </div>
                </form>

                <div>
                    <button class="btn btn-info" type="button" id="saveRelatedProducts">Save</button>
                </div>
                <br>

                <table class="table table-hover table-striped table-bordered" id="relatedData">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="relatedProductCheckAll" id="relatedProductCheckAll"> </th>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recomended product modal -->
<div class="modal fade" id="addRecomendedProductModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Recomended Products</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Select Category</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <select class="form-control multiselect-dropdown" name="selectCategoryForRecomended" id="selectCategoryForRecomended">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Select Brand</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <select class="form-control multiselect-dropdown" name="selectBrandForRecomended" id="selectBrandForRecomended">
                                </select>
                            </div>
                        </div>
                    </div>
                </form>

                <div>
                    <button class="btn btn-info" type="button" id="saveRecomendedProducts">Save</button>
                </div>
                <br>

                <table class="table table-hover table-striped table-bordered" id="recomendedData">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="recommendedProductCheckAll" id="recommendedProductCheckAll"> </th>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Recomended product modal -->
<!-- <div class="modal fade" id="deleteImagesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Aru you sure?</p>
                <input type="text" name="selectedImages" readonly="true">
            </div>
        </div>
    </div>
</div> -->

<div class="modal" id="deleteImagesModal" tabindex="-1" role="dialog"
        aria-labelledby="imageDeleteModelLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Image </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="selectedImages" id="selectedImages">
                <p class="mb-0" id="message">Are you Sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmDeleteImage">Yes</button>
            </div>
        </div>
    </div>
</div>
 <!-- delete related products modal - Added by Pallavi (March 9, 2021) -->
 <div class="modal fade" id="relatedProductDeleteModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Related Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="relatedProductIdForDelete" id="relatedProductIdForDelete" readonly="true">
                <p class="mb-0" id="message">Are you Sure?</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmDelete">Yes</button>
            </div>
        </div>
    </div>
</div>

<!-- delete pricing data modal - Added by Nivedita (April 1, 2021) -->
<div class="modal fade" id="pricingDataDeleteModel" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog" role="document">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title">Delete Pricing option</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div class="modal-body">
               <input type="hidden" name="optionIdForDelete" id="optionIdForDelete" readonly="true">
               <p class="mb-0" id="message">Are you Sure?</p>

           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
               <button type="button" class="btn btn-primary" id="deleteOption">Yes</button>
           </div>
       </div>
   </div>
</div>
<!-- delete pricing data image modal - Added by Nivedita (june 23, 2021) -->
<div class="modal fade" id="pricingImageDeleteModel" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog" role="document">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title">Delete Pricing option image</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div class="modal-body">
               <input type="hidden" name="optionIdForImageDelete" id="optionIdForImageDelete" readonly="true">
               <p class="mb-0" id="message">Are you Sure?</p>

           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
               <button type="button" class="btn btn-primary" id="deleteOptionImage">Yes</button>
           </div>
       </div>
   </div>
</div>

 <!-- delete recommended products modal - Added by Pallavi (March 9, 2021) -->
 <div class="modal fade" id="recommendedProductDeleteModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Recommended Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="recommendedProductIdForDelete" id="recommendedProductIdForDelete" readonly="true">
                <p class="mb-0" id="message">Are you Sure?</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmDeleteRecom">Yes</button>
            </div>
        </div>
    </div>
</div>
<!-- Advance Pricing modal -->
<div class="modal fade" id="addAdvancePricingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Advance Pricing</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form class="" id="advancePricingForm" name="advancePricingForm" >
                    <div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Select Group</label><span class="text-danger">*</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <select class="form-control multiselect-dropdown" name="selectGroup" id="selectGroup">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Price</label><span class="text-danger">*</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <input name="price" id="price" min="1" placeholder="Price" type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="productId" id="productIdAp" >
                    <div>
                        <button class="btn btn-info" type="submit" id="saveAdvancePricing">Save</button>
                    </div>
                </form>


                <br>

            </div>
        </div>
    </div>
</div>
<!-- delete Advance pricing -->
<div class="modal fade" id="advancePriceDeleteModel" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog" role="document">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title">Delete Advance Price</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div class="modal-body">
               <input type="hidden" name="advancePriceForDelete" id="advancePriceForDelete" readonly="true">
               <p class="mb-0" id="message">Are you Sure?</p>

           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
               <button type="button" class="btn btn-primary" id="confirmAdvanceDelete">Yes</button>
           </div>
       </div>
   </div>
</div>
<!-- Edit Pricing modal -->
<div class="modal fade" id="advancePriceEditModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Advance Pricing</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form class="" id="editAdvancePricingForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Select Group</label><span class="text-danger">*</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                              <lable><span id="selectGroupEdit"></span></label>
                                <!-- <select class="form-control multiselect-dropdown" name="selectGroupEdit" id="selectGroupEdit">
                                </select> -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Price</label><span class="text-danger">*</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <input name="priceEdit" id="priceEdit" min="1" placeholder="Price" type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="productId" id="productIdApEdit" >
                    <input type="hidden" name="pricingId" id="pricingId" >

                </form>

                <div>
                    <button class="btn btn-info" type="button" id="updateAdvancePricing">Update</button>
                </div>
                <br>

            </div>
        </div>
    </div>
</div>

@push('scripts')
	<script src="{{asset('public/assets/js/product/editProduct.js')}}"></script>
@endpush
@endsection
