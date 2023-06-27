@extends('admin.layouts.master')
<title>Multilanguage Email Templates | Alboumi</title>

@section('content')
<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
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
                                        <i class="pe-7s-config"></i>
                                    </span>
                                    <span class="d-inline-block">Multilanguage Email Templates</span>
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
                                                <a href="javascript:void(0);">Multilanguage Email Templates</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Multilanguage Email Templates List
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <a href="{{url('/admin/emailTemplates/addTemplate')}}"><button
                                        class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i
                                            class="fa fa-plus btn-icon-wrapper"> </i>New Template</button></a>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <table id="email_template_listing"
                            class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>Sr.No</th>
                                    <th>ID</th>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Variables</th>
                                    <th>Is Active</th>
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
    <!-- Modal for activating deactivating template -->
    <div class="modal fade" id="templateIsActiveModel" tabindex="-1" role="dialog"
        aria-labelledby="templateIsActiveModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="templateIsActiveModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="template_id" id="template_id">
                    <input type="hidden" name="is_active" id="is_active">
                    <p class="mb-0" id="message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="templateIsActive">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for delete template -->
    <div class="modal fade" id="templateDeleteModel" tabindex="-1" role="dialog" aria-labelledby="templateDeleteModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="templateDeleteModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="template_id" id="template_id">
                    <p class="mb-0" id="message_delete"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="deleteTemplate">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/email_templates/email_templates.js')}}"></script>
@endpush
