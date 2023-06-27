$(document).ready(function() {
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[0];

    var table = $('#language_list').DataTable({
        processing: true,
        serverSide: true,
        "initComplete": function (settings, json) {
            $("#language_list").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        order: [[ 3, "desc" ]],
        ajax: {
            "url": window.location.href,
            "type": "GET"
        },
        columns: [
            {data: 'lang_name', name: 'country_name'},
            {data: 'sortcode', name: 'sortcode'},
            {data: 'visibility', name: 'visibility',
                render: function (data,type,row) {
                    if(row.visibility == 0)
                    {
                        return "LTR"
                    }
                    if(row.visibility == 1)
                    {
                        return "RTL"
                    }

                }
            },
            { data: 'zone', name: 'zone',
                render: function (data,type,row) {
                    if(row.user_zone != null)
                    {
                        var z = row.user_zone
                        return moment.utc(row.lng_created_at).utcOffset(z.replace('.', "")).format('YYYY-MM-DD HH:mm:ss')
                    }
                    else
                    {
                        return "-----"
                    }

                }
            },
            {data: 'status', name: 'status',
                render: function (data, type, full, meta)
                {
                    var output = "";
                    var disabled='';
                    if(full.is_default==1)
                    var disabled='disabled';
                    if(data == 1)
                    {
                        output += '<div class="row">'
                        +'<div class="col-sm-5">'
                        +'<button type="button" '+disabled+' class="btn btn-sm btn-toggle active toggle-is-status-switch" data-toggle="button" aria-pressed="true" autocomplete="off">'
                        +'<div class="handle"></div>'
                        +'</button>'
                        +'</div>'
                    }
                    else
                    {
                        output += '<div class="row">'
                        +'<div class="col-sm-5">'
                        +'<button type="button" class="btn btn-sm btn-toggle toggle-is-status-switch" data-toggle="button" aria-pressed="false" autocomplete="off">'
                        +'<div class="handle"></div>'
                        +'</button>'
                        +'</div>'
                    }
                    return output;
                },
            },
            {data: 'is_default', name: 'is_default',
                render: function (data, type, full, meta)
                {
                    var output = "";
                    if(data == 1)
                    {
                        output += '<div class="row">'
                        +'<div class="col-sm-5">'
                        +'<button type="button" disabled class="btn btn-sm btn-toggle active toggle-is-active-switch" data-toggle="button" aria-pressed="true" autocomplete="off">'
                        +'<div class="handle"></div>'
                        +'</button>'
                        +'</div>'
                    }
                    else
                    {
                        output += '<div class="row">'
                        +'<div class="col-sm-5">'
                        +'<button type="button" class="btn btn-sm btn-toggle toggle-is-active-switch" data-toggle="button" aria-pressed="false" autocomplete="off">'
                        +'<div class="handle"></div>'
                        +'</button>'
                        +'</div>'
                    }
                    return output;
                },
            },
            {data: 'id', name: 'id',
                render: function(data, type, row)
                {
                    var output = "";
                    var disabled='';
                    if(row.is_default==1)
                    var disabled='anchor-disabled';
                    output += '<a href="'+window.location.href+'/../edit/'+row.id+'"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;'
                    output += '<a href="javascript:void(0);" class="language_delete text-danger '+disabled+'"><i class="fa fa-trash language_delete" aria-hidden="true"></i></a>'
                    return output;
                }
            },
        ],
    });

    $('#language_list').on('click', 'tbody .language_delete', function () {
        var data_row = table.row($(this).closest('tr')).data();
        var language_id = data_row.id;
        var message = "Are you sure ?";
        $('#languageDeleteModel').on('show.bs.modal', function(e){
            $('#language_id').val(language_id);
            $('#message').text(message);
        });
        $('#languageDeleteModel').modal('show');
    })

    $(document).on('click','#deleteLanguage', function(){
        var language_id = $('#language_id').val();
        $.ajax({
            url: window.location.href + '/../delete',
            method: "POST",
            data: {
                "_token": $('#token').val(),
                language_id: language_id,
            },
            success: function(response)
            {
                if(response.status == 'true')
                {
                    $('#languageDeleteModel').modal('hide')
                    table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#currencyDeleteModel').modal('hide')
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.error(response.msg);
                }
                setTimeout(function(){
                    toastr.clear();
                }, 5000);
            }
        });
    })

    $('#language_list').on('click', 'tbody .toggle-is-active-switch', function () {
        if($(this).attr('aria-pressed') == 'false')
        {
            $(this).addClass('active');
        }
        if($(this).attr('aria-pressed') == 'true')
        {
            $(this).removeClass('active');
        }
        var data_row = table.row($(this).closest('tr')).data();
        var is_default = data_row.is_default;
        var lang_id = data_row.id;
        var message = "Are you sure?";
        $('#languageDefaultModel').on('show.bs.modal', function(e){
            $('#lang_id').val(lang_id);
            $('#is_dflt').val(is_default);
            $('#default_message').text(message);
        });
        $('#languageDefaultModel').modal('show');
    })

    $(document).on('click','#languageIsDefault', function(){
        var lang_id = $('#lang_id').val();
        var is_default = $('#is_dflt').val();
        $.ajax({
            url: window.location.href + '/../default',
            method: "POST",
            data: {
                "_token": $('#token').val(),
                lang_id: lang_id,
                is_default: is_default,
            },
            success: function(response)
            {
                // console.log(response);
                if(response.status == 'true')
                {
                    $('#languageDefaultModel').modal('hide')
                    table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#languageDefaultModel').modal('hide')
                    // table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.error(response.msg);
                }
                setTimeout(function(){
                    toastr.clear();
                }, 5000);
            }
        });
    })
    // To active inactive status By Nivedita 21-05-2021 //
    $('#language_list').on('click', 'tbody .toggle-is-status-switch', function () {
        if($(this).attr('aria-pressed') == 'false')
        {
            $(this).addClass('active');
        }
        if($(this).attr('aria-pressed') == 'true')
        {
            $(this).removeClass('active');
        }
        var data_row = table.row($(this).closest('tr')).data();
        var is_active = data_row.status;
        var lang_id = data_row.id;
        var message = "Are you sure?";
        $('#languageStatusModel').on('show.bs.modal', function(e){
            $('#lang_id').val(lang_id);
            $('#is_active').val(is_active);
            $('#status_message').text(message);
        });
        $('#languageStatusModel').modal('show');
    })
    $(document).on('click','#languageIsActive', function(){
        var lang_id = $('#lang_id').val();
        var is_active = $('#is_active').val();
        $.ajax({
            url: window.location.href + '/../changestatus',
            method: "POST",
            data: {
                "_token": $('#token').val(),
                lang_id: lang_id,
                is_active: is_active,
            },
            success: function(response)
            {
                // console.log(response);
                if(response.status == 'true')
                {
                    $('#languageStatusModel').modal('hide')
                    table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                }
                else
                {
                    $('#languageStatusModel').modal('hide')
                    // table.ajax.reload();
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.error(response.msg);
                }
                setTimeout(function(){
                    toastr.clear();
                }, 5000);
            }
        });
    })
});
