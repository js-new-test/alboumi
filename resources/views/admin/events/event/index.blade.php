@extends('admin.layouts.master')
<title>Events | Alboumi</title>

@section('content')
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
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
                                        <i class="lnr-cog opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Events</span>
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
                                                <a href="javascript:void(0);">Events</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Event List
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <a href="{{url('/admin/event/addEvent')}}"><button
                                        class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i
                                            class="fa fa-plus btn-icon-wrapper"> </i>New Event</button></a>
                                <!-- <button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"
                                    id="exportEvents"><i class="fa fa-download btn-icon-wrapper"></i>Export</button> -->
                                @if($languages->count() >= 2)
                                <a href="javascript:void(0);" class="expand_collapse_filter"><button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm">
                                    <i aria-hidden="true" class="fa fa-filter"></i> Filter
                                </button></a>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
                @if($languages->count() >= 2)
                <div class="main-card mb-3 card expand_filter" id="FilterLangDiv">
                    <div class="card-body">
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i>  Filter</h5>
                        <form method="post">  
                            @csrf
                            <!-- <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group"> -->
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="filter_event_lang" class="mr-sm-2">Languages</label>
                                    <select name="filter_event_lang" id="filter_event_lang" class="multiselect-dropdown form-control">
                                        @foreach($languages as $lang)
                                            <option value="{{$lang->id}}" {{($lang->is_default == 1) ? 'selected' : '' }}>{{$lang->langEN}}</option>
                                        @endforeach 
                                        <option value="all">All</option>
                                    </select>
                                </div>
                            </div>
                            <!-- </div> -->
                            <div class="row">
                                <div class="col-sm-4">
                                    <button type="button" id="filter_event" class="btn btn-primary desktop-filter-search-btn mobile-filter-search-btn">Search</button>
                                </div>
                            </div>
                        </form>   
                    </div>
                </div>
                @endif
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <table style="width: 100%;" id="event_listing"
                            class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>Sr.No</th>
                                    <th>ID</th>
                                    <th>Event Name</th>
                                    <!-- <th>Event Description</th> -->
                                    <th>Event Image</th>
                                    <th>Created At</th>
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
    <!-- Modal for activating deactivating event -->
    <div class="modal fade" id="eventIsActiveModel" tabindex="-1" role="dialog"
        aria-labelledby="eventIsActiveModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventIsActiveModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="event_id" id="event_id">
                    <input type="hidden" name="is_active" id="is_active">
                    <p class="mb-0" id="message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="eventIsActive">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for delete event -->
    <div class="modal fade" id="eventDeleteModel" tabindex="-1" role="dialog" aria-labelledby="eventDeleteModelLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDeleteModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="event_id" id="event_id">
                    <p class="mb-0" id="message_delete"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="deleteevent">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/events/event.js')}}"></script>
<script>
$('.expand_collapse_filter').on('click', function(){
    $(".expand_filter").slideToggle('slow');
})    
</script>
@endpush
