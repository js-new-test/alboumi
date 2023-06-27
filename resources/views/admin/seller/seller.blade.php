@extends('admin.layouts.master')
<title>{{$pageTitle }} | {{ $projectName }} </title>
@section('content')
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl);?>;
</script>
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-white closed-sidebar">
    @include('admin.include.header')
    <div class="app-main">
        @include('admin.include.sidebar')
        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="app-page-title app-page-title-simple">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div>
                                <div class="page-title-head center-elem">
                                    <span class="d-inline-block pr-2">
                                        <i class="pe-7s-note2 opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Best Sellers</span>
                                </div>
                                <div class="page-title-subheading opacity-10">
                                    <nav class="" aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a>
                                                    <i aria-hidden="true" class="fa fa-home"></i>
                                                </a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Best Sellers
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <a href="seller/addSeller">
                                    <button class="btn btn-square btn-primary btn-sm" > <i aria-hidden="true" class="fa fa-plus"></i> Add New </button>
                                </a>
                                @if($languages->count() >= 2)
                                <button class="btn btn-square btn-primary btn-sm" id="divFilterToggle"> <i aria-hidden="true" class="fa fa-filter"></i> Filter </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @if($languages->count() >= 2)
                <div class="main-card mb-3 card" id="FilterLangDiv">
                    <div class="card-body">
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i>  Filter</h5>
                        <form>
                            <!-- <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group"> -->
                            <div class="row">
                                <div class="col-sm-4">
                                    <label class="mr-sm-2">Languages</label>
                                    <select name="languageId" id="languageId" class="multiselect-dropdown form-control">
                                        @foreach($languages as $lang)
                                            <option value="{{$lang->id}}" {{($lang->is_default == 1) ? 'selected' : '' }}>{{$lang->langEN}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- </div> -->
                            <div class="row">
                                <div class="col-sm-4">
                                    <button type="button" id="filter_faq" class="btn btn-primary desktop-filter-search-btn mobile-filter-search-btn">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <table style="width:100%;" class="table table-hover table-striped table-bordered" id="tblSellers">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Price ({{ ($defaultCurrency->currency_code != '') ? $defaultCurrency->currency_code : '' }})</th>
                                    <th>Image</th>
                                    <th>Sort Order</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @include('admin.include.footer')
        </div>
    </div>
</div>

<!-- Modal Start -->
<div class="modal fade" id="sellerDeleteModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="sellerId" id="sellerId">
                <!-- <input type="hidden" name="promotionStatusForDelete" id="promotionStatusForDelete"> -->

                <p class="mb-0" id="message">Are you Sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmDelete">Yes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Over -->

<!-- Modal Start -->
<!-- <div class="modal fade" id="approvalModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="promotionIdForApprove" id="promotionIdForApprove">
                <input type="hidden" name="promotionApprove" id="promotionApprove">

                <p class="mb-0" id="message">Are you Sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmApprove">Yes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div> -->
<!-- Modal Over -->
@push('scripts')
    <script src="{{asset('public/assets/js/seller/seller.js')}}"></script>
@endpush
@endsection
