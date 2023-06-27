@extends('admin.layouts.master')
<title>Update Photographer | Alboumi</title>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
@endpush

@section('content')
<script type="text/javascript">
	var nonDefaultLanguage = <?php echo json_encode($nonDefaultLanguage);?>;
    var defaultLanguageId = <?php echo json_encode($defaultLanguageId);?>;
    var profile_pic = <?php echo json_encode($photographerDetails['profile_pic']);?>;
    var conver_photo = <?php echo json_encode($photographerDetails['cover_photo']);?>;
		var photographer_id = <?php echo json_encode($photographerDetails['id']);?>;
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-gray closed-sidebar">
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
                                        <i class="pe-7s-photo opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Photographers</span>
                                </div>
                                <div class="page-title-subheading opacity-10">
                                    <nav class="" aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a>
                                                    <i aria-hidden="true" class="fa fa-home"></i>
                                                </a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="javascript:void(0);">Photographers</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/photgraphers')}}">Photographers List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Update Photographer
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

								<div class="main-card mb-3 card">
	                    <div class="card-header">
	                    	Update Photographer
	                    	<hr/>
	                    </div>
	                    <div class="card-body">

	                        <ul class="nav nav-tabs">
	                            <li class="nav-item">
	                            	<a data-toggle="tab" href="#tabEditGeneralInfo" class="active nav-link">Photographer Details</a>
	                            </li>

	                            <li class="nav-item commonElement">
	                            	<a data-toggle="tab" href="#tabProtfolio" class="nav-link">Portfolio</a>
	                            </li>

	                        </ul>
	                        <div class="tab-content">
	                            <div class="tab-pane active" id="tabEditGeneralInfo" role="tabpanel">
																@include('admin.bahrainPhotographers.editGeneralInfo')
	                            </div>
	                            <div class="tab-pane" id="tabProtfolio" role="tabpanel">
																@include('admin.bahrainPhotographers.portfolioList')
	                            </div>
	                        </div>
	                    </div>
	                </div>
            </div>
            @include('admin.include.footer')
        </div>
    </div>
</div>
@endsection
<!-- Portfilio modal -->
<div class="modal fade" id="addPortfolioModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Portfolio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="addPortfolioForm" id="addPortfolioForm" enctype="multipart/form-data">
									@csrf
									<input type="hidden" name="editPage" value="PORTFOLIO" readonly="true">
									<input type="hidden" name="photographerId" id="photographerId" value="{{$photographerDetails['id']}}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Image</label><span class="text-danger">*</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
															<input type="file" class="form-control" id="image" name="image" onchange="_showPortfolioImgDimensions(this)">
															<small class="form-text text-muted">Image width should be 392 px.</small>
															<small class="form-text text-muted">width = <small id="portfolio_width"></small></small>
															<input type="hidden" name="portfolio_image_width" id="portfolio_image_width">
                            </div>
                        </div>
                    </div>
										<div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Select Product</label><span class="text-danger">*</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <select class="form-control multiselect-dropdown" name="product_id" id="product_id">
                                </select>
                            </div>
                        </div>
                    </div>
										<div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Sort Order</label><span class="text-danger">*</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <input type="text" class="form-control" id="sort_order" name="sort_order" />
                            </div>
                        </div>
                    </div>
										<div class="row">
												<div class="col-md-4">
														<div class="form-group position-relative">
																<label for="status">Status</label>
																<span class="text-danger">*</span>
														</div>
													</div>
													<div class="col-md-8">
															<select class="form-control" name="status" id="status">
																<option value="1" selected>Active</option>
																<option value="0">Inactive</option>
															</select>
													</div>
										</div>
										<div>
												<button class="btn btn-info" type="submit" id="savePortfolio">Save</button>
										</div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Over -->
<!-- Edit Portfilio modal -->
<div class="modal fade" id="editPortfolioModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Portfolio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="editPortfolioForm" id="editPortfolioForm" enctype="multipart/form-data">
									@csrf
									<input type="hidden" name="editPage" value="PORTFOLIO" readonly="true">
									<input type="hidden" name="photographerId" id="photographerId" value="{{$photographerDetails['id']}}">
									<input type="hidden" name="portfolioId" id="portfolioId" value="">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Image</label><span class="text-danger">*</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
															<input type="file" class="form-control" id="image" name="image" onchange="_showPortfolioImgEditDimensions(this)">
															<small class="form-text text-muted">Image width should be 392 px.</small>
															<small class="form-text text-muted">width = <small id="portfolio_width_edit"></small></small>
															<input type="hidden" name="portfolio_image_width" id="portfolio_image_width_edit">
                            </div>
														<div style="position: relative;width: 100px;height: 100px;">
																<img id="prodfolioImage" style="width: 130px;height: 85px;position: absolute;" src="" alt="current_image">
														</div>
                        </div>
                    </div>
										<div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Select Product</label><span class="text-danger">*</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <select class="form-control multiselect-dropdown" name="product_id" id="productId">
                                </select>
                            </div>
                        </div>
                    </div>
										<div class="row">
                        <div class="col-md-4">
                            <div class="position-relative form-group">
                                <label for="examplePassword" class="">Sort Order</label><span class="text-danger">*</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="position-relative form-group">
                                <input type="text" class="form-control" id="sort_order" name="sort_order" />
                            </div>
                        </div>
                    </div>
										<div class="row">
												<div class="col-md-4">
														<div class="form-group position-relative">
																<label for="status">Status</label>
																<span class="text-danger">*</span>
														</div>
													</div>
													<div class="col-md-8">
															<select class="form-control" name="status" id="statusEdit">
																<option value="1" selected>Active</option>
																<option value="0">Inactive</option>
															</select>
													</div>
										</div>
										<div>
												<button class="btn btn-info" type="submit" id="editPortfolio">Save</button>
										</div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Over -->
<!-- Modal Delete Portfolio -->
<div class="modal fade bd-example-modal-sm" id="PortfolioDeleteModel" tabindex="-1" role="dialog" aria-labelledby="portfolioDeleteModelLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
						<div class="modal-header">
								<h5 class="modal-title" id="portfolioDeleteModelLabel">Confirmation</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
								</button>
						</div>
						<div class="modal-body">
								<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
								<input type="hidden" name="portfolio_id" id="portfolio_id">
								<!-- <input type="hidden" name="is_active" id="is_active"> -->
								<p class="mb-0" id="message"></p>
						</div>
						<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
								<button type="button" class="btn btn-primary" id="deletePortfolio">Yes</button>
						</div>
				</div>
		</div>
</div>
<!-- Modal Over -->
@push('scripts')
<script src="{{asset('public/assets/js/photographers/photographers.js')}}"></script>
<script src="{{asset('public/assets/js/photographers/editPhotographer.js')}}"></script>
@endpush
