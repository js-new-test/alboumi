@extends('admin.layouts.master')
<title>Import Customer</title>

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
                                        <i class="lnr-users opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Customer</span>
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
                                                <a href="javascript:void(0);">Customer</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                <a href="{{url('/admin/customer/list')}}">Customer List</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                Import Customer
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>                            
                        </div>
                        <div class="page-title-actions">                            
                            <div class="d-inline-block dropdown">                               
                                <!-- <a href="{{asset('/excel-sample/user-sample-file.xls')}}" download><button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i class="fa fa-fw" aria-hidden="true"></i> Download Sample File</button></a> -->
                            </div>                                                       
                        </div>                           
                    </div>
                </div>                                                       
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Customer Information</h5>                                                                               
                        <form id="importCustomer" class="col-md-6" method="post" action="{{url('admin/customer/import')}}" enctype="multipart/form-data">
                            @csrf                                                                                     
                            <div class="position-relative form-group">
                                <label for="exampleFile" class="">File</label>
                                <input name="import_customer_file" id="import_customer_file" type="file" class="form-control-file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                <small class="form-text text-muted"><i class="fa fa-fw" aria-hidden="true" title="Copy to use warning"></i> File must be 'xls' or 'xlsx' formated.</small>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="import" value="import">Import</button>
                                <a href="{{asset('public/excel-sample/customer-sample-file.xls')}}" download><button type="button" class="btn btn-primary"><i class="fa fa-fw" aria-hidden="true"></i> Download Sample File</button></a>                                
                            </div>
                        </form>
                    </div>
                </div>  
                <!-- Error Section -->
                <div class="main-card mb-3 card ScrollStyle">
                    <div class="card-body">
                        <h5 class="card-title">Logs</h5>                                       
                        <nav class="" aria-label="breadcrumb">                       
                                <?php 
                                    $errors = Session::get('msg');                                                       
                                    if(isset($errors) && !empty($errors)){
                                ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?php
                                    foreach($errors as $error)    
                                    {                                
                                ?>
                                    <i class="fa fa-times"></i> <?php echo $error ?><br/>  
                                    <?php
                                    }
                                ?>
                                </div>                                              
                                <?php
                                    }
                                ?>   
                                <?php
                                    $uploaded = Session::get('success'); 
                                    if(isset($uploaded) && !empty($uploaded))
                                    {
                                ?>  
                                    <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <?php if(count($uploaded) >= 1)
                                        {
                                        ?>
                                            <i class="fa fa-times"></i> <?php echo (count($uploaded) == 1) ? count($uploaded).' Row' : count($uploaded).' Rows' ; ?> uploaded successfully.<br/>                                                                    
                                        <?php
                                        }
                                        ?>                                        
                                    </div>                                             
                                <?php
                                    }                                
                                ?>
                                <?php
                                    $faile = Session::get('faile'); 
                                    if(isset($faile) && !empty($faile))
                                    {
                                ?>  
                                    <div class="alert alert-secondary alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <?php if(count($faile) >= 1)
                                        {
                                        ?>
                                            <i class="fa fa-times"></i> <?php echo (count($faile) == 1) ? count($faile).' Row' : count($faile).' Rows' ; ?> failed to upload.<br/>                                                                    
                                        <?php
                                        }
                                        ?>                                          
                                    </div>                                             
                                <?php
                                    }                                
                                ?>                            
                        </nav>                    
                    </div>
                </div>              
            </div>
            @include('admin.include.footer')
        </div>
    </div>
</div>
@endsection
@push('scripts')

<!-- <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/metismenu"></script> -->
<script>
$(document).ready(function(){
    
})
</script>
@endpush