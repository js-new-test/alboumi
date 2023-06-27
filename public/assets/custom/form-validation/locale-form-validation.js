$(document).ready(function(){
    $("#LocaleTab li>a:first").addClass("active").show(); //Activate first tab on load
    $(".tab_content:first").addClass("active").show();
});

// Set tab active on click
$('#LocaleTab li>a').click(function(e) {
    $($('#LocaleTab li>a').parent()).addClass("active").not(this.parentNode).removeClass("active");   
    e.preventDefault();
});

$("#localeForm").validate( {
    ignore: [], // ignore NOTHING
    rules: {
        code: "required",
        title: "required",            
    },
    messages: {
        code: "Code is required",
        title: "Title is required",                     
    },
    errorPlacement: function ( error, element ) {

        error.insertAfter( element );
    },
} );

$(".locale_textarea").each(function(){
    $(this).rules("add", { 
        required:true,
        messages:{required:'Please enter locale details'}
    });
});

