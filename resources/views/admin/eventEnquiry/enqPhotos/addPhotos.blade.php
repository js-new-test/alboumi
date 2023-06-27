@extends('admin.layouts.master')
<title>Add Photos | Alboumi</title>

@section('content')
<script type="text/javascript">
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
                                        <i class="lnr-cog opacity-6"></i>
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
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/event/list')}}">Photos List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Photos
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Add Photos</h5>

                        <div class="grid-x grid-padding-x">
                            <div class="small-10 small-offset-1 medium-8 medium-offset-2 cell">
                                <form id="uploadPhotosForm" action="{{ url('admin/eventEnq/photos/addPhotos') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="enqId" id="enqId" value="{{ $enqId }}">

                                    <p>
                                        <label for="upload_imgs" class="button hollow">Select Your Images +</label><br>
                                        <input class="show-for-sr" type="file" id="upload_imgs" name="upload_imgs[]"
                                            multiple />
                                    </p>
                                    <p class="text-center">
                                        <input class="button large expanded w-25" type="submit"
                                           id="uploadImagesBtn" />
                                    </p>
                                </form>
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
@push('scripts')
<script src="{{asset('public/assets/js/eventEnq/eventEnqPhotos.js')}}"></script>
@endpush
