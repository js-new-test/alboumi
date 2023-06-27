@extends('admin.layouts.master')
<title>Enquiry Photos| Alboumi</title>

@section('content')
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
    @include('admin.include.header')
    <div class="app-main">
        @include('admin.include.sidebar')
        <input type="hidden" name="enqId" id="enqId" value="{{ $enqId }}">
        <div class="app-main__outer" style="width:100%;">
            <div class="app-main__inner">
                <div class="app-page-title app-page-title-simple">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div>
                                <div class="page-title-head center-elem">
                                    <span class="d-inline-block pr-2">
                                        <i class="pe-7s-note2 opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Photos</span>
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
                                                <a href="javascript:void(0);">Photos</a>
                                            </li>

                                            <li class="active breadcrumb-item" aria-current="page">
                                            Photos List
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <a href="{{url('/admin/eventEnq/photos/addPhotos/'. $enqId)}}">
                                    <button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm" id="addPhotos">
                                        <i aria-hidden="true" class="fa fa-plus"></i> Add Photos
                                    </button>
                                </a>
                                <button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm deleteMultiplePhotos" id="deletePhotos">
                                    <i aria-hidden="true" class="fa fa-trash"></i> Delete Photos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <table id="eventEnqPhotosListing"
                            class="table table-hover table-striped table-bordered nowrap w-100">
                            <thead>
                                <tr class="text-center">
                                    <th><input name="selectAllPhotos" value="1" id="selectAllPhotos" type="checkbox" /></th>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            @include('admin.include.footer')
        </div>
    </div>
    <div class="modal" id="photoDeleteModel" tabindex="-1" role="dialog" aria-labelledby="photoDeleteModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="photo_id" id="photo_id">
                    <p class="mb-0" id="message">Are you Sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="deletePhoto">Yes</button>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection

@push('scripts')
<script src="{{asset('public/assets/js/eventEnq/eventEnqPhotos.js')}}"></script>
@endpush
