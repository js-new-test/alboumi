$(document).ready(function(){
    $(".menuIcons").on("click", function(){
        $(".sideMenu").toggleClass("active");
        $("body").toggleClass("scroll-stop")
    });
    $(".closeIcons").on("click", function(){
        $(".sideMenu").toggleClass("active");
        $("body").toggleClass("scroll-stop")
    });
    ajaxCall.getAllLanguages();
    ajaxCall.getAllCurrency();
});
var ajaxCall = {
    getAllLanguages: function () {
        $.ajax({
            type: "get",
            url:baseUrl +'/api/v1/getAllLanguages',
            success: function (response) {
                if(response.status == "OK")
                {
                    for (var item in response.languageData)
                    {
                        var _menu = "";
                        var data = response.languageData[item];
                        var active = (item == 0) ? 'active' : '';
                        _menu = "<a class='dropdown-item "+active+"' onclick=setLang('" + data['id'] + "','" + data['text'] + "','" + data['Code']+"') id='"+data['id'] +"'> " + data['text'] + " </a>";

                        $('.languages').append(_menu);
                        $('.selectedLang > a').html(langName);
                        $('.selectedLang > a').attr("id",langId);

                        // $('#selectedLang > ul').append("<li><a class='s1' onclick=setLang('" + data['id'] + "','" + data['text'] + "','" + data['Code']+"')>"+ data['text'] +'</a></li>');

                        if(response.languageData.length==1)
                        {
                            $('.language-change').hide();
                            $('.track-order').removeClass('center-line');
                        }
                    }
                };
            }
        });
    },

    getAllCurrency: function () {
        $.ajax({
            type: "get",
            url:baseUrl +'/api/v1/getAllCurrency',
            success: function (response) {
                if(response.status == "OK")
                {
                    for (var item in response.currencyData)
                    {
                        var _menu = "";
                        var data = response.currencyData[item];

                        _menu = "<a class='dropdown-item' onclick=setCurr('" + data['id'] + "','" + data['currency_symbol'] + "') id='"+data['id'] +"'> " + data['currency_symbol'] + " </a>";

                        $('#currencies').append(_menu);
                        $('#selectedCurr > a').html(currSymbol);
                        $('#selectedCurr > a').attr("id",currId);

                    }
                };
            }
        });
    }
}
//code commented for lang code in url
function setLang(id,text,code)
{
    // console.log(code);
    $('#selectedLang > a').html(text);
    $('#selectedLang > a').attr("id",id);
    // var prev_url = window.location.href;
    // prev_url = prev_url.slice(0, prev_url.lastIndexOf('/'));
    // url = prev_url + '/getLangSpecificData';
    $.ajaxSetup({
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $.ajax({
        method: "POST",
        url: baseUrl+'/getLangSpecificData',
        data:{
            langId : id,
            code : code,
            text : text,
            decimal_number : decimalNumber,
            decimal_separator : decimalSeparator,
            thousand_separator : thousandSeparator,
        },
        success: function (response)
        {
          if(response.status == true)
            {
                location.reload();

                // if(defaultLangId == id)
                // {
                //     console.log('in if');
                //     window.location.href = prev_url;

                // }
                // else
                // {
                //     console.log('in else');
                //     window.location.href = prev_url + '/' + code;
                // }
            }
        },

    });
};

function setCurr(id,currency_symbol)
{
    console.log(currency_symbol);
    $('#selectedCurr > a').html(currency_symbol);
    $('#selectedCurr > a').attr("id",id);
    $.ajaxSetup({
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $.ajax({
        method: "POST",
        url: baseUrl+'/getCurrSpecificData',
        data:{
            currId : id,
            currency_symbol : currency_symbol,
        },
        success: function (response)
        {
          if(response.status == true)
            {
                location.reload();
            }
        },
    });
};


// Search product validation
$("#prodSearchForm").validate({
    ignore: [], // ignore NOTHING
    rules: {
        "searchVal": {
            required: true,
            minlength:4
        }
    },
    messages: {
        "searchVal": {
            required: searchErrorMsg
        }
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter(element)
    },
    submitHandler: function(form)
    {
        form.submit();
    }
});
