// filter menu - mobile view - added from HTML
$(document).ready(function(){
    prodIds = [];

    // load more button
    var _totalCurrentResult=$(".product-box").length;
    var _totalResult=parseInt($(".load-more").attr('data-totalResult'));
    if(_totalCurrentResult==_totalResult){
        $(".load-more").addClass('d-none');
    }else{
        $(".load-more").html('Load More');
    }

    // filter
    $(".filterIcons").on("click", function(){
      $(".filterMenu").toggleClass("active");
      $("body").toggleClass("scroll-stop")
    });
    $(".closeIcons3").on("click", function(){
      $(".filterMenu").toggleClass("active");
      $("body").toggleClass("scroll-stop")
    });

    // sort
    $(".sortIcons").on("click", function(){
        $(".sortMenu").toggleClass("active");
        $("body").toggleClass("scroll-stop")
    });
    $(".closeIcons2").on("click", function(){
        $(".sortMenu").toggleClass("active");
        $("body").toggleClass("scroll-stop")
    });

    var width = $(window).width();
    if (width > 767)
    {
        $(':checkbox[name=catCheckbox]').on('change', function() {
            prodIds = [];
            if($('input[name="catCheckbox"]:checked').length > 0)
            {
                $("input[name='prodIds[]']").each(function() {
                    prodIds.push($(this).val());
                });
            }
            createFilterArray('Category',prodIds);
        });

        $(':checkbox[name=brandCheckbox]').on('change', function() {
            prodIds = [];
            if($('input[name="brandCheckbox"]:checked').length > 0)
            {
                $("input[name='prodIds[]']").each(function() {
                    prodIds.push($(this).val());
                });
            }
            createFilterArray('Brands',prodIds);
        });

        $("input[name='attrCheckbox']").click(function() {
            prodIds = [];
            if($('input[name="attrCheckbox"]:checked').length > 0)
            {
                $("input[name='prodIds[]']").each(function() {
                    prodIds.push($(this).val());
                });
            }
            var id = this.id.substr(this.id.indexOf("_") + 1);
            createFilterArray(id,prodIds);

        });
    }
    else
    {
        $(':checkbox[name=catCheckbox]').on('change', function() {
            prodIds = [];
            if($('input[name="catCheckbox"]:checked').length > 0)
            {
                $("input[name='prodIds[]']").each(function() {
                    prodIds.push($(this).val());
                });
            }
            createFilterArray('Category',prodIds);
        });

        $(':checkbox[name=brandCheckbox]').on('change', function() {
            prodIds = [];
            if($('input[name="brandCheckbox"]:checked').length > 0)
            {
                $("input[name='prodIds[]']").each(function() {
                    prodIds.push($(this).val());
                });
            }
            createFilterArray('Brands',prodIds);
        });

        $(':checkbox[name=attrCheckbox]').on('change', function() {
            prodIds = [];
            if($('input[name="attrCheckbox"]:checked').length > 0)
            {
                $("input[name='prodIds[]']").each(function() {
                    prodIds.push($(this).val());
                });
            }
            var id = this.id.substr(this.id.indexOf("_") + 1);
            createFilterArray(id,prodIds);
        });
    }
    $("#ajax-loader").hide();
});

// load more
$(".load-more").on('click',function(){
    var productIds = [];
    $(".product-box").each(function(){
        productIds.push($(this).attr('data-prod-ids'));
    });

    options = [];
    var assignedTo = $(':checkbox[id=attrCheckbox_' + $('.attr_tabs_class').attr('data-prod-ids') + ']:checked').map(function() {
        return this.value;
    }).get();

    options.push(JSON.stringify( assignedTo ));
    if(assignedTo.length > 0)
    {
        options = options.filter(function(elem) {
            filterQuery.push({
                attribute_id : $('.attr_tabs_class').attr('data-prod-ids'),
                option_id : elem,
            })
        });
    }

    assignedTo = $(':checkbox[name=catCheckbox]:checked').map(function() {
        return this.value;
    }).get();
    options.push(JSON.stringify( assignedTo ));

    if(assignedTo.length > 0)
    {
        options = options.filter(function(elem) {
            filterQuery.push({
                attribute_id : 'Category',
                option_id : elem,
            })
        });
    }

    assignedTo = $(':checkbox[name=brandCheckbox]:checked').map(function() {
        return this.value;
    }).get();
    options.push(JSON.stringify( assignedTo ));

    if(assignedTo.length > 0)
    {
        options = options.filter(function(elem) {
            filterQuery.push({
                attribute_id : 'Brands',
                option_id : elem,
            })
        });
    }

    if(pageName == 'categoryProducts')
    {
        url = baseUrl + '/category/' + slug + '?skip=' +  JSON.stringify(productIds) + '&ajax="ajax"',
        method = "get"
    }
    if(pageName == 'searchResultFilter')
    {
        url = baseUrl + '/search?searchVal=' + searchVal + '&skip=' + JSON.stringify(productIds) + '&ajax="ajax"',
        method = "post"
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ajaxSend(function() {
        $("#ajax-loader").fadeIn(300);　
    });
    $.ajax({
        url: url,
        type:method,
        dataType:'json',
        data :{
            'filterQuery' : filterQuery,
            'sortBy' : $('#sortDropdown').find(":selected").val()
        },

        success:function(response)
        {
            setTimeout(function(){
                $("#ajax-loader").fadeOut(300);
            },500);

            $.each(response.productsList, function(index,value)
            {
                $.each(value, function(i,v)
                {
                    html = '<div class="col-6 col-sm-6 col-md-6 col-lg-4 text-center product-box" data-prod-ids="'+ v.id +'">'
                    if(v.flagInstock == 0)
                    {
                        html += '<div class="stock-label">'
                        html += '<img src="'+ baseUrl + '/public/assets/frontend/img/stock-banner.png">'
                        html += '<p>Out Of Stock</p></div>'
                    }
                    html+= '<div class="content">'
                    html+= '<div class="content-overlay"></div>'
                    html+= '<img src="' + baseUrl + '/public/images/product/' + v.id + '/' + v.image + '">'
                    html+= '<div class="content-details fadeIn-left">'
                    html+= '<a href="'+ baseUrl + '/product/' + v.slug +'" class="blue-border-btn"> '+ response.productSortLabels.EXPLORE + '</a> </div> </div>'
                    html+= '<a href="'+ baseUrl + '/product/' + v.slug +'" class="s1" id="prodName">'+ v.title + '</a></br>';
                    if(v.discountedPrice != '' && v.discountedPrice != null)
                        html+= '<span> '+ response.productSortLabels.FROM + currencyCode + ' ' + '<strike>' + v.price.replace(/,/g, '') + '</strike><span class="text-danger prodPrice" data-price='+ currencyCode + ' ' + v.discountedPrice.replace(/,/g, '') +'> ' + v.discountedPrice.replace(/,/g, '') + '</span></span>';
                    else if(v.group_price != '' && v.group_price != null)
                        html+= '<span> '+ response.productSortLabels.FROM + currencyCode + ' ' + '<strike>' + v.price.replace(/,/g, '') + '</strike><span class="text-danger prodPrice" data-price='+ currencyCode + ' ' + v.group_price.replace(/,/g, '') +'> ' + v.group_price.replace(/,/g, '') + '</span></span>';
                    else
                        html+= '<span class="prodPrice"data-price='+ currencyCode + v.price.replace(/,/g, '') +'> '+ response.productSortLabels.FROM + ' ' + currencyCode + ' ' + v.price.replace(/,/g, '') + '</span>';
                    html += "</div></div>"
                    
                    $('#productListing').append(html);
                    $('#noProds').addClass('d-none');
                });
            });
            var sortingMethod = $('#sortDropdown').find(":selected").val()
            if(sortingMethod == 3)
            {
                sortProductsPriceAscending();
            }
            else if(sortingMethod == 4)
            {
                sortProductsPriceDescending();
            }
            // Change Load More When No Further result
            var _totalCurrentResult=$(".product-box").length;
            var _totalResult=parseInt($(".load-more").attr('data-totalResult'));
            // console.log(_totalCurrentResult,_totalResult);
            if(_totalCurrentResult==_totalResult){
                $(".load-more").addClass('d-none');
            }else{
                $(".load-more").html('Load More');
            }

        }
    });
});

function sortProductsPriceAscending()
{
    var products = $('.product-box');
    products.sort(function(a, b) {
        return $('.prodPrice', a).data("price") - $('.prodPrice', b).data("price");
    });
    $("#productListing").html('');
    $("#productListing").append(products);

}

function sortProductsPriceDescending()
{
    var products = $('.product-box');
    products.sort(function(a, b) {
        return $('.prodPrice', b).data("price") - $('.prodPrice', a).data("price");
    });
    $("#productListing").html('');
    $("#productListing").append(products);

}

// clear filters
$('#clearFilterBtn').on('click',function(){
    $('.checkmark').append('<style>.checkmark:after{display:none !important;}</style>');
    window.location.reload()
})

// Price range slider - added by Pallavi (Feb 15,2021) - desktop view
prodIds = [];
$("input[name='prodIds[]']").each(function() {
    prodIds.push($(this).val());
});
var minValue, maxValue = 0
if(minPrice != 0)
    minValue = minPrice.replace(/,/g, '');
if(maxPrice != 0)
    maxValue = maxPrice.replace(/,/g, '');

if(minValue == maxValue)
    minValue = 0;

var options = {
    range: true,
    min: 0,
    max: maxValue,
    values: [minValue, maxValue],
    slide: function(event, ui)
    {
        prodIds = [];
        if($('input[name="brandCheckbox"]:checked').length > 0)
        {
            $("input[name='prodIds[]']").each(function() {
                prodIds.push($(this).val());
            });
        }

        var min = ui.values[0],
            max = ui.values[1];
        
        $( "#minAmount" ).val( currencyCode + " " + min);
        $( "#maxAmount" ).val( currencyCode + " " + max);

        $( "#mobMinAmount" ).val( currencyCode + " " + min);
        $( "#mobMaxAmount" ).val( currencyCode + " " + max);
        width = $(window).width();
        if (width > 767)
        {
            createFilterArray('Price',prodIds);
        }
        else
        {
            createFilterArrayForMobile('Price',prodIds);
        }
    },
};

$(".slider-range").slider(options);
$( "#minAmount" ).val( currencyCode + " " + $( "#slider-range" ).slider( "values", 0 ));
$( "#maxAmount" ).val( currencyCode + " " + $( "#slider-range" ).slider( "values", 1 ));

$( "#mobMinAmount" ).val( currencyCode + " " + $( "#slider-range" ).slider( "values", 0 ));
$( "#mobMaxAmount" ).val( currencyCode + " " + $( "#slider-range" ).slider( "values", 1 ));

// sort By - Mobile
function setSortByMobileValue(selectedSortByOpt)
{
    prodIds = [];
    if($('input[name="brandCheckbox"]:checked').length > 0)
    {
        $("input[name='prodIds[]']").each(function() {
            prodIds.push($(this).val());
        });
    }
    $('#selectedSortByMobile').val(selectedSortByOpt);
    createFilterArrayForMobile(selectedSortByOpt,prodIds)
}

// Filter products - Added by Pallavi (Feb 12,2021)
filterQuery = [];

function createFilterArray(changedValue,prodIds)
{
    if(changedValue == "Category")
    {
        options = [];
        var assignedTo = $(':checkbox[name=catCheckbox]:checked').map(function() {
            return this.value;
        }).get();
        
        options.push(JSON.stringify( assignedTo ));

        for(var i = 0; i< filterQuery.length; i++)
        {
            $.each(filterQuery, function(i,v)
            {
                if(v != undefined)
                {
                    if(v.attribute_id == changedValue)
                    {
                        filterQuery.splice(i,1);
                    }
                }
            });
        }
        
        if(assignedTo.length > 0)
        {
            options = options.filter(function(elem) {
                filterQuery.push({
                    attribute_id : changedValue,
                    option_id : elem,
                })
            });
        }
    }

    else if(changedValue == "Brands")
    {
        options = [];
        var assignedTo = $(':checkbox[name=brandCheckbox]:checked').map(function() {
            return this.value;
        }).get();
        
        options.push(JSON.stringify( assignedTo ));

        for(var i = 0; i< filterQuery.length; i++)
        {
            $.each(filterQuery, function(i,v)
            {
                if(v != undefined)
                {
                    if(v.attribute_id == changedValue)
                    {
                        filterQuery.splice(i,1);
                    }
                }
            });
        }
        
        if(assignedTo.length > 0)
        {
            options = options.filter(function(elem) {
                filterQuery.push({
                    attribute_id : changedValue,
                    option_id : elem,
                })
            });
        }
    }
    
    else if(changedValue == "Price")
    {
        options = [];
        var minPrice = $("#minAmount").val();
        minPrice = minPrice.substring(minPrice.indexOf(" ") + 1);
        var maxPrice = $("#maxAmount").val();
        maxPrice = maxPrice.substring(maxPrice.indexOf(" ") + 1);

        options.push({
            'min': (minPrice/conversionRate).toFixed(2),
            'max': (maxPrice/conversionRate).toFixed(2),
        });

        // if(minPrice != Math.round(minValue) || maxPrice != Math.round(maxValue))
        // {
            filterQuery.push({
                attribute_id : changedValue,
                option_id : options
            });
        // }
    }

    else
    {
        options = [];
        var assignedTo = $(':checkbox[id=attrCheckbox_' + changedValue + ']:checked').map(function() {
            return this.value;
        }).get();

        options.push(JSON.stringify( assignedTo ));

        for(var i = 0; i< filterQuery.length; i++)
        {
            $.each(filterQuery, function(i,v)
            {
                if(v !== undefined)
                {
                    if(v.attribute_id == changedValue)
                    {
                        filterQuery.splice(i,1);
                    }
                }

            });
        }
        if(assignedTo.length > 0)
        {
            options = options.filter(function(elem) {

                filterQuery.push({
                    attribute_id : changedValue,
                    option_id : elem
                })
            })
        }
    }
    
    $('#selectedVal').val(changedValue);

    var category_id = $('#categoryId').val();
    var sort_by = $('#sortDropdown').find(":selected").val();
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ajaxSend(function() {
        $("#ajax-loader").fadeIn(300);　
    });
    $.ajax({
        url: baseUrl + '/product/applyFilter',
        method: "POST",
        data:{
            'filterQuery' : filterQuery,
            'language_id' : language_id,
            'category_id' : category_id,
            'sort_by' : sort_by,
            'pageName' : pageName,
            'prodIds' : prodIds,
            'searchVal' : searchVal
        },
        success: function(response)
        {
            if(response.status == true)
            {
                $('.load-more').attr('data-totalResult',response.totalFilteredProductsCount)
                setTimeout(function(){
                    $("#ajax-loader").fadeOut(300);
                },500);
                $('#productListing').show();
                $('#productListing').empty();

                if(response.resultArr.length == response.totalFilteredProductsCount)
                    $('.load-more').addClass('d-none');
                else
                    $('.load-more').removeClass('d-none');
                
                $.each(response.resultArr, function(i,v)
                {
                    html= '<div class="col-6 col-sm-6 col-md-6 col-lg-4 text-center product-box" data-prod-ids="'+ v.id +'">'
                    html+= '<div class="stock-detail-parent">'
                    if(v.flagInstock == 0)
                    {
                        html += '<div class="stock-label">'
                        html += '<img src="'+ baseUrl + '/public/assets/frontend/img/stock-banner.png">'
                        html += '<p>'+response.labels['OOS']+'</p></div>'
                    }
                    html+= '<div class="content">'
                    html+= '<div class="content-overlay"></div>'
                    html+= '<img src="'+ v.image + '">'
                    html+= '<div class="content-details fadeIn-left">'
                    html+= '<a href="'+ baseUrl + '/product/' + v.slug +'" class="blue-border-btn"> '+ response.labels['EXPLORE'] + '</a> </div> </div>'
                    html+= '<a href="'+ baseUrl + '/product/' + v.slug +'" class="s1" id="prodName">'+ v.title + '</a></br>';
                    html+= '<input type="hidden" id="prodIds" name="prodIds[]" value="'+ v.id +'"></input>';
                    if(v.discountedPrice != '')
                        html+= '<span> '+ response.labels['FROM'] + currencyCode + ' ' + '<strike>' + v.price.replace(/,/g, '') + '</strike><span class="text-danger prodPrice" data-price='+ ' ' + v.discountedPrice.replace(/,/g, '') +'> ' + v.discountedPrice.replace(/,/g, '') + '</span></span>';
                    else if(v.group_price != '')
                        html+= '<span> '+ response.labels['FROM'] + currencyCode + ' ' + '<strike>' + v.price.replace(/,/g, '') + '</strike><span class="text-danger prodPrice" data-price='+ ' ' + v.group_price.replace(/,/g, '') +'> ' + v.group_price.replace(/,/g, '') + '</span></span>';
                    else
                        html+= '<span class="prodPrice" data-price='+ v.price.replace(/,/g, '') +'> '+ response.labels['FROM'] + ' ' + currencyCode + ' ' + v.price.replace(/,/g, '') + '</span>';
                    html += '</div></div>';
                    $('#productListing').append(html);
                    $('#noProds').css('display','none');
                });

                // Replace filter options
                // if($('#selectedVal').val() == "Category")
                // {
                //     replaceBrand(response.sideBarOptions.brands);
                //     replaceAttributes(response.sideBarOptions.attributeGroups,response.selectedOptions);
                //     replacePrice(response.minPrice,response.maxPrice);
                // }

                // else if($('#selectedVal').val() == "Brands")
                // {
                //     replaceCategory(response.sideBarOptions.category);
                //     replaceAttributes(response.sideBarOptions.attributeGroups,response.selectedOptions);
                //     replacePrice(response.minPrice,response.maxPrice);
                // }

                // else if($('#selectedVal').val() == "Price")
                // {
                //     replaceCategory(response.sideBarOptions.category);
                //     replaceBrand(response.sideBarOptions.brands);
                //     replaceAttributes(response.sideBarOptions.attributeGroups,response.selectedOptions);
                // }

                // else
                // {
                //     replaceBrand(response.sideBarOptions.brands);
                //     replaceRemainingAttributes(response.sideBarOptions.attributeGroups,response.selectedOptions);
                //     replaceCategory(response.sideBarOptions.category);
                //     replacePrice(response.minPrice,response.maxPrice);
                // }
            }
            if(response.status == false)
            {
                setTimeout(function(){
                    $("#ajax-loader").fadeOut(300);
                },500);

                $('#noProds').removeClass('d-none');
                $('#noProds').css('display','block');
                $('#productListing').hide();
            }
        }
    })
}

// ignore for now
function replaceBrand(brands)
{
    console.log(brands);
    prodIds = [];
    // $("input[name='prodIds[]']").each(function() {
    //     prodIds.push($(this).val());
    // });
    $('#brandOptions > .acco-tab-content > .item').remove();
    $.each(brands, function(i,v) {
        var changedValue = 'Brands';
        html = '<div class="item">';
        html += '<label class="ck">' + v.title;
        if(v.flagSelected == 1)
            html += '<input type="checkbox" id="brandCheckbox_'+ v.id +'" name="brandCheckbox" value="'+ v.id +'" onchange="createFilterArray( \''+ changedValue + '\',[' + prodIds +'])" checked>';
        else
            html += '<input type="checkbox" id="brandCheckbox_'+ v.id +'" name="brandCheckbox" value="'+ v.id +'" onchange="createFilterArray( \''+ changedValue + '\',[' + prodIds +'])">'
        html += '<span class="checkmark"></span></label>';

        $('#brandOptions > .acco-tab-content').append(html);
    });
}

function replaceAttributes(attributeGroups,selectedOptions)
{
    prodIds = [];
    // $("input[name='prodIds[]']").each(function() {
    //     prodIds.push($(this).val());
    // });
    if($('#attrGroups').val() != undefined)
    {
        var attrGroups = $('#attrGroups').val().split(',');

        $.each(attributeGroups, function(i,v) {

            for (var i = 0; i < attrGroups.length; i++)
            {
                if (attrGroups[i] == v.filterId)
                {
                    attrGroups.splice(i,1);
                }
            }

            if(selectedOptions == null)
            {
                $('#attributeTab_'+ v.filterId).show();
            }

            $('#attributeTab_'+ v.filterId + '> .acco-tab-content').remove();

            html = '<div class="acco-tab-content items color-items">';
            $.each(v.data, function(i,val)
            {
                html += '<div class="item" id="attr_'+ val.id +'">';

                html += '<label class="ck">'
                if(val.color != '')
                {
                    html += '<div class="d-flex justify-content-start align-items-center">';
                    html += '<div class="s-color" style="background:'+ val.color + '"></div>'+ val.title +'</div>';
                }
                else
                {
                    html += val.title;
                }
                if(val.flagSelected == 1)
                {

                    html += '<input type="checkbox" id="attrCheckbox_'+ v.filterId +'" name="attrCheckbox" value="'+ val.id +'" onchange="createFilterArray('+v.filterId + ',['+prodIds+'])" checked>';
                }
                else
                {
                    html += '<input type="checkbox" id="attrCheckbox_'+ v.filterId +'" name="attrCheckbox" value="'+ val.id +'" onchange="createFilterArray('+v.filterId + ',['+prodIds+'])">';
                }
                html += '<span class="checkmark"></span>'
                html += '</label></div>';
            });

            html += '</div>';
            $('#attributeTab_' + v.filterId).append(html);
        });

        $('.attr_tabs_class > .acco-tab-content').each(function(){
            $('.acco-tab-content').css('padding',0);
        });

        $.each(attrGroups, function(i,v){
            $('#attributeTab_' + v).hide();
        });
    }
}

function replaceCategory(category)
{
    prodIds = [];
    // $("input[name='prodIds[]']").each(function() {
    //     prodIds.push($(this).val());
    // });
    $('#CategoryOptions > .acco-tab-content > .item').remove();
    $.each(category, function(i,v) {
        var changedValue = 'Category';
        html = '<div class="item">';
        html += '<label class="ck">' + v.title;
        if(v.flagSelected == 1)
        {

            html += '<input type="checkbox" id="catCheckbox_'+ v.id +'" name="catCheckbox" value="'+ v.id +'" onchange="createFilterArray( \''+ changedValue + '\',[' + prodIds +'])" checked>'
        }
        else
        {
            html += '<input type="checkbox" id="catCheckbox_'+ v.id +'" name="catCheckbox" value="'+ v.id +'" onchange="createFilterArray( \''+ changedValue + '\',[' + prodIds +'])">'
        }
        html += '<span class="checkmark"></span></label>';

        $('#CategoryOptions > .acco-tab-content').append(html);
    });
}

function replacePrice(minPrice,maxPrice)
{
    prodIds = [];

    // $("input[name='prodIds[]']").each(function() {
    //     prodIds.push($(this).val());
    // });

    var minimum, maximum = 0;
    if(minPrice != 0)
        minimum = minPrice.replace(/,/g, '');
    if(maxPrice != 0)
        maximum = maxPrice.replace(/,/g, '');

    if(minimum == maximum)
    {
        minimum = 0;
    }

    options = {
        range: true,
        min: 0,
        max: maximum,
        values: [minimum, maximum],
        slide: function(event, ui)
        {
            var min = ui.values[0],
                max = ui.values[1];

            $( "#minAmount" ).val( currencyCode + " " + min);
            $( "#maxAmount" ).val( currencyCode + " " + max);
            width = $(window).width();
            if (width > 767)
            {
                createFilterArray('Price',prodIds);
            }
            else
            {
                createFilterArrayForMobile('Price',prodIds);
            }
        },
    };

    $("#slider-range").slider(options);
    $( "#minAmount" ).val( currencyCode + " " + $( "#slider-range" ).slider( "values", 0 ));
    $( "#maxAmount" ).val( currencyCode + " " + $( "#slider-range" ).slider( "values", 1 ));
}

function replaceRemainingAttributes(attributeGroups,selectedOptions)
{
    prodIds = [];
    $("input[name='prodIds[]']").each(function() {
        prodIds.push($(this).val());
    });

    if($('#attrGroups').val() != undefined)
    {
        var attrGroups = $('#attrGroups').val().split(',');
        $.each(attributeGroups, function(i,v) {

            for (var i = 0; i < attrGroups.length; i++)
            {
                if (attrGroups[i] == v.filterId)
                {
                    attrGroups.splice(i,1);
                }
            }
            if($('#selectedVal').val() == v.filterId && selectedOptions != null)
            {
                return true;
            }
            else
            {
                if(selectedOptions == null)
                {
                    $('#attributeTab_'+ v.filterId).show();
                }

                $('#attributeTab_'+ v.filterId + ' .acco-tab-content').empty();

                html = '<div class="acco-tab-content items color-items" id="attributeTabContent_'+ v.filterId + '">';
                $.each(v.data, function(i,val) {
                    html += '<div class="item">';
                    html += '<label class="ck">'
                    if(val.color != '')
                    {
                        html += '<div class="d-flex justify-content-start align-items-center">';
                        html += '<div class="s-color" style="background:'+ val.color + '"></div>'+ val.title +'</div>';
                    }
                    else
                    {
                        html += val.title;
                    }
                    if(val.flagSelected == 1)
                    {

                        html += '<input type="checkbox" id="attrCheckbox_'+ v.filterId +'" name="attrCheckbox" value="'+ val.id +'" onchange="createFilterArray('+ v.filterId + ',[' + prodIds+'])" checked>';
                    }
                    else
                    {
                        html += '<input type="checkbox" id="attrCheckbox_'+ v.filterId +'" name="attrCheckbox" value="'+ val.id +'" onchange="createFilterArray('+ v.filterId + ',[' + prodIds +'])">';
                    }
                    html += '<span class="checkmark"></span>'

                    html += '</label></div>';
                });

                html += '</div>';
                $('#attributeTab_'+ v.filterId).append(html);
            }
        });

        $('.attr_tabs_class > .acco-tab-content').each(function(){
            $('.acco-tab-content').css('padding',0)

        });

        $.each(attrGroups, function(i,v){
            $('#attributeTab_' + v).hide();
        });

        
    }
}

function createFilterArrayForMobile(changedValue,prodIds)
{
    if(changedValue == "Category")
    {
        options = [];
        var assignedTo = $(':checkbox[name=catCheckbox]:checked').map(function() {
            return this.value;
        }).get();

        options.push(JSON.stringify( assignedTo ));

        for(var i = 0; i< filterQuery.length; i++)
        {
            $.each(filterQuery, function(i,v)
            {
                if(v != undefined)
                {
                    if(v.attribute_id == changedValue)
                    {
                        filterQuery.splice(i,1);
                    }
                }
            });
        }

        if(assignedTo.length > 0)
        {
            options = options.filter(function(elem) {
                filterQuery.push({
                    attribute_id : changedValue,
                    option_id : elem,
                })
            });
        }
    }

    if(changedValue == "Brands")
    {
        options = [];
        var assignedTo = $(':checkbox[name=brandCheckbox]:checked').map(function() {
            return this.value;
        }).get();

        options.push(JSON.stringify( assignedTo ));

        for(var i = 0; i< filterQuery.length; i++)
        {
            $.each(filterQuery, function(i,v)
            {
                if(v != undefined)
                {
                    if(v.attribute_id == changedValue)
                    {
                        filterQuery.splice(i,1);
                    }
                }
            });
        }

        if(assignedTo.length > 0)
        {
            options = options.filter(function(elem) {
                filterQuery.push({
                    attribute_id : changedValue,
                    option_id : elem,
                })
            });
        }
    }

    else if(changedValue == "Price")
    {
        options = [];
        var minPrice = $("#minAmount").val();
        minPrice = minPrice.substring(minPrice.indexOf(" ") + 1);
        var maxPrice = $("#maxAmount").val();
        maxPrice = maxPrice.substring(maxPrice.indexOf(" ") + 1);

        options.push({
            'min': (minPrice/conversionRate).toFixed(2),
            'max': (maxPrice/conversionRate).toFixed(2),
        });
        if(minPrice != Math.round(minValue) || maxPrice != Math.round(maxValue))
        {
            filterQuery.push({
                attribute_id : changedValue,
                option_id : options
            });
        }
    }

    else
    {
        options = [];
        var assignedTo = $(':checkbox[id=attrCheckbox_' + changedValue + ']:checked').map(function() {
            return this.value;
        }).get();

        options.push(JSON.stringify( assignedTo ));

        for(var i = 0; i< filterQuery.length; i++)
        {
            $.each(filterQuery, function(i,v)
            {
                if(v !== undefined)
                {
                    if(v.attribute_id == changedValue)
                    {
                        filterQuery.splice(i,1);
                    }
                }

            });
        }
        if(assignedTo.length > 0)
        {
            options = options.filter(function(elem) {

                filterQuery.push({
                    attribute_id : changedValue,
                    option_id : elem
                })
            })
        }
    }

    $('#selectedValM').val(changedValue);

    var category_id = $('#categoryId').val();
    var sort_by = $('#selectedSortByMobile').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ajaxSend(function() {
        $("#ajax-loader").fadeIn(300);　
    });
    $.ajax({
        url: baseUrl + '/product/applyFilter',
        method: "POST",
        data:{
            'filterQuery' : filterQuery,
            'language_id' : language_id,
            'category_id' : category_id,
            'sort_by' : sort_by,
            'pageName' : pageName,
            'prodIds' : prodIds,
            'searchVal' : searchVal
        },
        success: function(response)
        {
            if(response.status == true)
            {
                $('.load-more').attr('data-totalResult',response.totalFilteredProductsCount)

                setTimeout(function(){
                    $("#ajax-loader").fadeOut(300);
                },500);

                $('#productListing').html('');
                if(response.resultArr.length == response.totalFilteredProductsCount)
                    $('.load-more').addClass('d-none');
                else
                    $('.load-more').removeClass('d-none');
                $.each(response.resultArr, function(i,v)
                {
                    html = '<div class="col-6 col-sm-6 col-md-6 col-lg-4 text-center product-box" data-prod-ids="'+ v.id +'">'
                    html+= '<div class="stock-detail-parent">'
                    if(v.flagInstock == 0)
                    {
                        html += '<div class="stock-label">'
                        html += '<img src="'+ baseUrl + '/public/assets/frontend/img/stock-banner.png">'
                        html += '<p>'+response.labels['OOS']+'</p></div>'
                    }
                    html+= '<div class="content">'
                    html+= '<div class="content-overlay"></div>'
                    html+= '<img src="'+ v.image + '">'
                    html+= '<div class="content-details fadeIn-left">'
                    html+= '<a href="'+ baseUrl + '/product/' + v.slug +'" class="blue-border-btn"> '+ response.labels['EXPLORE'] + '</a> </div> </div>'
                    html+= '<p class="s1" id="prodName">'+ v.title + '</p>';
                    if(v.discountedPrice != '' && v.discountedPrice != null)
                        html+= '<span> '+ response.labels['FROM'] + ' ' + '<strike>' + v.price.replace(/,/g, '') + '</strike><span class="text-danger prodPrice" data-price='+ v.discountedPrice.replace(/,/g, '') +'> ' + v.discountedPrice.replace(/,/g, '') + '</span></span>';
                    else if(v.group_price != '' && v.group_price != null)
                        html+= '<span> '+ response.labels['FROM'] + ' ' + '<strike>' + v.price.replace(/,/g, '') + '</strike><span class="text-danger prodPrice" data-price='+ v.group_price.replace(/,/g, '') +'> ' + v.group_price.replace(/,/g, '') + '</span></span>';
                    else
                        if(v.price != null)
                            html+= '<span class="prodPrice" data-price='+ v.price.replace(/,/g, '') +'> '+ response.labels['FROM'] + ' ' + v.price.replace(/,/g, '') + '</span> </div>';
                    html += "</div></div>";
                    $('#productListing').append(html);
                    $('#noProds').addClass('d-none');
                });

                // Replace filter options
                // if ($('#selectedValM').val() == "Category")
                // {
                //     var attrGroups = $('#attrGroups').val().split(',');
                //     $.each(response.sideBarOptions.attributeGroups, function (i, v) {

                //         for (var i = 0; i < attrGroups.length; i++)
                //         {
                //             if (attrGroups[i] == v.filterId)
                //             {
                //                 attrGroups.splice(i,1);
                //             }
                //         }

                //         if(response.selectedOptions == null)
                //         {
                //             $('#attributeTab_'+ v.filterId).show();
                //         }


                //         $('#attributeTabM_' + v.filterId + '> .acco-tab-content').remove();

                //         html = '<div class="acco-tab-content items2 color-items">';

                //         $.each(v.data, function (i, val)
                //         {
                //             html += '<div class="item2" id="attr_' + val.id + '">';

                //             html += '<label class="ck">'
                //             if (val.color != '')
                //             {
                //                 html += '<div class="d-flex justify-content-start align-items-center">';
                //                 html += '<div class="s-color" style="background:' + val.color + '"></div>' + val.title + '</div>';
                //             }
                //             else
                //             {
                //                 html += val.title;
                //             }
                //             if (val.flagSelected == 1)
                //             {
                //                 html += '<input type="checkbox" id="attrCheckbox_' + v.filterId + '" name="attrCheckbox" value="' + val.id + '" onchange="createFilterArrayForMobile(' + v.filterId + ',[' + prodIds + '])" checked>';
                //             }
                //             else
                //             {
                //                 html += '<input type="checkbox" id="attrCheckbox_' + v.filterId + '" name="attrCheckbox" value="' + val.id + '" onchange="createFilterArrayForMobile(' + v.filterId + ',[' + prodIds + '])">';
                //             }
                //             html += '<span class="checkmark"></span>'

                //             html += '</label></div>';
                //         });
                //         html += '</div>';
                //         $('#attributeTabM_' + v.filterId).append(html);
                //     });

                //     $('.attr_tabs_class > .acco-tab-content').each(function(){
                //         $('.acco-tab-content').css('padding',0);
                //     });

                //     $.each(attrGroups, function(i,v){
                //         $('#attributeTabM_' + v).hide();
                //     });

                //     var minimum, maximum = 0;
                //     if (response.minPrice != 0)
                //         minimum = response.minPrice.replace(/,/g, '');
                //     if (response.maxPrice != 0)
                //         maximum = response.maxPrice.replace(/,/g, '');

                //     if(minimum == maximum)
                //     {
                //         minimum = 0;
                //     }
                //     options = {
                //         range: true,
                //         min: 0,
                //         max: maximum,
                //         values: [minimum, maximum],
                //         slide: function (event, ui) {
                //             var min = ui.values[0],
                //                 max = ui.values[1];

                //             $("#mobMinAmount").val(currencyCode + " " + min);
                //             $("#mobMaxAmount").val(currencyCode + " " + max);
                //             width = $(window).width();
                //             if (width > 767)
                //             {
                //                 createFilterArray('Price',prodIds);
                //             }
                //             else
                //             {
                //                 createFilterArrayForMobile('Price',prodIds);
                //             }
                //         },
                //     };

                //     $("#slider-range").slider(options);
                //     $("#mobMinAmount").val(currencyCode + " " + $("#slider-range").slider("values", 0));
                //     $("#mobMaxAmount").val(currencyCode + " " + $("#slider-range").slider("values", 1));
                // }

                // else if($('#selectedValM').val() == "Price")
                // {
                //     $('#CategoryOptions > .acco-tab-content > .item').remove();
                //     $.each(response.sideBarOptions.category, function(i,v) {
                //         var changedValue = 'Category';
                //         html = '<div class="item">';
                //         html += '<label class="ck">' + v.title;
                //         if(v.flagSelected == 1)
                //         {
                //             html += '<input type="checkbox" id="catCheckbox_'+ v.id +'" name="catCheckbox" value="'+ v.id +'" onchange="createFilterArrayForMobile( \''+ changedValue + '\',[' + prodIds +'])" checked>'
                //         }
                //         else
                //         {
                //             html += '<input type="checkbox" id="catCheckbox_'+ v.id +'" name="catCheckbox" value="'+ v.id +'" onchange="createFilterArrayForMobile( \''+ changedValue  + '\',[' + prodIds+'])">'
                //         }
                //         html += '<span class="checkmark"></span></label>';

                //         $('#CategoryOptions > .acco-tab-content').append(html);
                //     });

                //     var attrGroups = $('#attrGroups').val().split(',');

                //     $.each(response.sideBarOptions.attributeGroups, function(i,v) {

                //         for (var i = 0; i < attrGroups.length; i++)
                //         {
                //             if (attrGroups[i] == v.filterId)
                //             {
                //                 attrGroups.splice(i,1);
                //             }
                //         }

                //         $('#attributeTabM_'+ v.filterId + '> .acco-tab-content > .item').remove();

                //         html = '<div class="acco-tab-content items color-items attr_items">';
                //         html += '<div class="acco-tab-content items2 color-items">';

                //         $.each(v.data, function(i,val) {
                //             html += '<div class="item2" id="attr_'+ val.id +'">';
                //             html += '<label class="ck">'
                //             if(val.color != '')
                //             {
                //                 html += '<div class="d-flex justify-content-start align-items-center">';
                //                 html += '<div class="s-color" style="background:'+ val.color + '"></div>'+ val.title +'</div>';
                //             }
                //             else
                //             {
                //                 html += val.title;
                //             }
                //             if(val.flagSelected == 1)
                //             {
                //                 html += '<input type="checkbox" id="attrCheckbox_'+ v.filterId +'" name="attrCheckbox" value="'+ val.id +'" onchange="createFilterArrayForMobile('+ v.filterId + ',[' + prodIds +'])" checked>';
                //             }
                //             else
                //             {
                //                 html += '<input type="checkbox" id="attrCheckbox_'+ v.filterId +'" name="attrCheckbox" value="'+ val.id +'" onchange="createFilterArrayForMobile('+ v.filterId + ',[' + prodIds +'])">';
                //             }
                //             html += '<span class="checkmark"></span>'

                //             html += '</label></div>';
                //         });

                //         html += '</div>';
                //         $('#attributeOptionsM').append(html);
                //     });
                //     $.each(attrGroups, function(i,v){
                //         $('#attributeTabM_'+ v).remove();
                //     });
                // }

                // else
                // {
                //     $('#CategoryOptions > .acco-tab-content > .item').remove();
                //     $.each(response.sideBarOptions.category, function(i,v) {
                //         var changedValue = 'Category';
                //         html = '<div class="item">';
                //         html += '<label class="ck">' + v.title;
                //         if(v.flagSelected == 1)
                //         {
                //             html += '<input type="checkbox" id="catCheckbox_'+ v.id +'" name="catCheckbox" value="'+ v.id +'" onchange="createFilterArrayForMobile( \''+ changedValue + '\',[' + prodIds +'])" checked>'
                //         }
                //         else
                //         {
                //             html += '<input type="checkbox" id="catCheckbox_'+ v.id +'" name="catCheckbox" value="'+ v.id +'" onchange="createFilterArrayForMobile( \''+ changedValue + '\',[' + prodIds +'])">'
                //         }
                //         html += '<span class="checkmark"></span></label>';

                //         $('#CategoryOptions > .acco-tab-content').append(html);
                //     });
                //     if($('#attrGroups').val() != undefined)
                //     {
                //         var attrGroups = $('#attrGroups').val().split(',');
                //         $.each(response.sideBarOptions.attributeGroups, function(i,v) {
    
                //             for (var i = 0; i < attrGroups.length; i++)
                //             {
                //                 if (attrGroups[i] == v.filterId)
                //                 {
                //                     attrGroups.splice(i,1);
                //                 }
                //             }
                //             if($('#selectedValM').val() == v.filterId && response.selectedOptions != null)
                //             {
                //                 return true;
                //             }
                //             else
                //             {
                //                 if(response.selectedOptions == null)
                //                 {
                //                     $('#attributeTabM_'+ v.filterId).show();
                //                 }
                //                 $('#attributeTabM_'+ v.filterId + ' .acco-tab-content').empty();
    
                //                 html = '<div class="acco-tab-content items2 color-items">';
                //                 $.each(v.data, function(i,val) {
                //                     html += '<div class="item2">';
                //                     html += '<label class="ck">'
                //                     if(val.color != '')
                //                     {
                //                         html += '<div class="d-flex justify-content-start align-items-center">';
                //                         html += '<div class="s-color" style="background:'+ val.color + '"></div>'+ val.title +'</div>';
                //                     }
                //                     else
                //                     {
                //                         html += val.title;
                //                     }
                //                     if(val.flagSelected == 1)
                //                     {
                //                         html += '<input type="checkbox" id="attrCheckbox_'+ v.filterId +'" name="attrCheckbox" value="'+ val.id +'" onchange="createFilterArrayForMobile('+ v.filterId + ',[' + prodIds +'])" checked>';
                //                     }
                //                     else
                //                     {
                //                         html += '<input type="checkbox" id="attrCheckbox_'+ v.filterId +'" name="attrCheckbox" value="'+ val.id +'" onchange="createFilterArrayForMobile('+ v.filterId + ',[' + prodIds +'])">';
                //                     }
                //                     html += '<span class="checkmark"></span>';
                //                     html += '</label></div>';
                //                 });
    
                //                 html += '</div>';
                //                 $('#attributeTabM_'+ v.filterId).append(html);
                //             }
                //         });
    
                //         $('.attr_tabs_class > .acco-tab-content').each(function(){
                //             $('.acco-tab-content').css('padding',0)
    
                //         });
    
                //         $.each(attrGroups, function(i,v){
                //             $('#attributeTab_' + v).hide();
                //         });
    
                //         var minimum, maximum = 0;
                //         if(response.minPrice != 0)
                //             minimum = response.minPrice.replace(/,/g, '');
                //         if(response.maxPrice != 0)
                //             maximum = response.maxPrice.replace(/,/g, '');
    
                //         if(minimum == maximum)
                //         {
                //             minimum = 0;
                //         }
    
                //         options = {
                //             range: true,
                //             min: 0,
                //             max: maximum,
                //             values: [minimum, maximum],
                //             slide: function(event, ui)
                //             {
                //                 var min = ui.values[0],
                //                     max = ui.values[1];
    
                //                 $( "#mobMinAmount" ).val( currencyCode + " " + min);
                //                 $( "#mobMaxAmount" ).val( currencyCode + " " + max);
                //                 width = $(window).width();
                //                 if (width > 767)
                //                 {
                //                     createFilterArray('Price',prodIds);
                //                 }
                //                 else
                //                 {
                //                     createFilterArrayForMobile('Price',prodIds);
                //                 }
                //             },
                //         };
    
                //         $("#slider-range").slider(options);
                //         $( "#mobMinAmount" ).val( currencyCode + " " + $( "#slider-range" ).slider( "values", 0 ));
                //         $( "#mobMaxAmount" ).val( currencyCode + " " + $( "#slider-range" ).slider( "values", 1 ));
                //     }
                    
                // }

            }
            else
            {
                setTimeout(function(){
                    $("#ajax-loader").fadeOut(300);
                },500);

                $('#noProds').removeClass('d-none');
                $('#productListing').html('');
            }
        }
    })
}
