@extends('admin.layouts.master')
<title>List Language | Alboumi</title>
<style>
a.anchor-disabled{
  pointer-events: none;
  cursor: default;
  color:grey !important;;
}
</style>
@section('content')
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
                                        <i class="lnr-cog opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Language</span>
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
                                                <a href="javascript:void(0);">Settings</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Language
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <a href="{{url('/admin/language/add')}}"><button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i class="fa fa-plus btn-icon-wrapper"> </i>Add Language</button></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <!-- <h5 class="card-title">Language List</h5>   -->
                        <table style="width:100%;" id="language_list" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Language</th>
                                    <th>Sort Code</th>
                                    <th>LTR/RTL</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th>Is Default</th>
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
    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-sm" id="languageDeleteModel" tabindex="-1" role="dialog" aria-labelledby="languageDeleteModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="languageDeleteModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="language_id" id="language_id">
                    <p class="mb-0" id="message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="deleteLanguage">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-sm" id="languageDefaultModel" tabindex="-1" role="dialog" aria-labelledby="languageDefaultModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="languageDefaultModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="lang_id" id="lang_id">
                    <input type="hidden" name="is_dflt" id="is_dflt">
                    <p class="mb-0" id="default_message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="languageIsDefault">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-sm" id="languageStatusModel" tabindex="-1" role="dialog" aria-labelledby="languageStatusModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="languageStatusModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="lang_id" id="lang_id">
                    <input type="hidden" name="is_active" id="is_active">
                    <p class="mb-0" id="status_message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="languageIsActive">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/custom/datatables/settings/language-list-datatable.js')}}"></script>
@endpush
