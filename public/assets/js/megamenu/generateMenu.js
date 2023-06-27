$(document).ready(function(){
    $('body').on('click', '.generate_menu', function () {
        $('#langId').val($(this).data('id'));
        $('#code').val($(this).data('code'));
    });

    $('body').on('click', '#confirmDelete', function () {
        ajaxCall.generateMenu()
    })
});

var ajaxCall = {
    generateMenu : function() {
        var origin = window.location.href;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: origin + '1',
                method: "get",    
                data: {
                    languageId: $('#langId').val(),
                    code :$('#code').val()
                },            
                success: function(response)
                {
                    if(response.status == 1)
                    {
                        $('#generateMenuConfirmation').modal('hide')
                        toastr.clear();
                        toastr.options.closeButton = true;
                        toastr.options.timeOut = 0;
                        toastr.success(response.msg);
                    }
                    else
                    {
                        $('#generateMenuConfirmation').modal('hide')                  
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
    }
}