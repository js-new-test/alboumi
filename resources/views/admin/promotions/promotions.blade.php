@extends('admin.layouts.master')
<title>{{ $projectName }} | {{$pageTitle }}</title>
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
                                        <i class="pe-7s-cart opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Promotions</span>
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
                                                Promotions
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <a href="promotions/addPromotion">
                                    <button class="btn btn-square btn-primary btn-sm" > <i aria-hidden="true" class="fa fa-plus"></i> Add New </button>
                                </a>

                                <button class="btn btn-square btn-primary btn-sm" id="divFilterToggle"> <i aria-hidden="true" class="fa fa-filter"></i> Filter </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main-card mb-3 card" id="divFilter">
                    <div class="card-body">
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i>  Filter</h5>
                        <form class="form-inline">
                            <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group">
                                <!-- <label for="filter_date" class="mr-sm-2">Select Date Range</label> -->
                                <input type="text" class="form-control" name="promotionTitle" id="promotionTitle" placeholder="Promotion Title">&nbsp
                                <input type="text" class="form-control" name="promotionCode" id="promotionCode" placeholder="Promotion Code">&nbsp
                                <select class="form-control" name="promotionStatus" id="promotionStatus">
                                    <option value=" ">Select Status</option>
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>
                            <button type="button" id="searchPromotionData" class="btn btn-primary">Search</button>
                            <!-- <button type="button" id="resetDate" class="btn btn-warning">Reset</button> -->
                        </form>
                    </div>
                </div>
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <!-- <h5 class="card-title">Promotions</h5> -->
                        <table style="width:100%;" class="table table-hover table-striped table-bordered" id="tblPromotions">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Coupon Code</th>
                                    <th>Coupon Usage Limit</th>
                                    <!-- <th>Coupon Used Count</th> -->
                                    <th> Discount Type</th>
                                    <th>Discount Amount</th>
                                    <th>Coupon Use Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Is Active</th>
                                    <!-- <th>Is Admin Approved</th> -->
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
<div class="modal fade" id="confirmationModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="promotionId" id="promotionId">
                <input type="hidden" name="promotionStatusForDelete" id="promotionStatusForDelete">

                <p class="mb-0" id="message">Are you Sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmStatus">Yes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Over -->

<!-- Modal Start -->
<div class="modal fade" id="approvalModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel" aria-hidden="true">
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
</div>
<!-- Modal Over -->

<!-- Modal Start -->
<div class="modal fade" id="promotionDeleteModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Promotion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="promotionIdForDelete" id="promotionIdForDelete">
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
@push('scripts')
    <script src="{{asset('public/assets/js/promotions/promotions.js')}}"></script>
@endpush
@endsection
