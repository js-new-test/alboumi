$( document ).ready(function() {
  init.handler()
  var filterData = {'languageId': $('#filterProduct').val(), 'categoryId': $('#filterCategory').val(),
           'brandId': $('#filterBrand').val(), 'status': $('#filterStatus').val(),
           'stock': $('#filterStock').val(),'customize': $('#filterCustomize').val()}

ajaxCalls.loadAllProducts(filterData);
ajaxCalls.loadActiveProducts(filterData);
ajaxCalls.loadInActiveProducts(filterData);
//	ajaxCalls.loadRejectedProducts(filterData);
//ajaxCalls.loadOutOfStockProducts(filterData);

ajaxCalls.getBrands($('#filterProduct').val())
ajaxCalls.getCategories($('#filterProduct').val())

$("#FilterLangDiv").hide();
});

var init = {
handler: function () {
  $('body').on('click', '#divFilterToggle', function () {
          $("#FilterLangDiv").slideToggle('slow');
      });

      $('body').on('click', '#btnFilterProduct', function () {
        filterData = {'languageId': $('#filterProduct').val(), 'categoryId': $('#filterCategory').val(),
           'brandId': $('#filterBrand').val(), 'status': $('#filterStatus').val(),
           'stock': $('#filterStock').val(),'customize': $('#filterCustomize').val()}

        ajaxCalls.loadAllProducts(filterData);
        ajaxCalls.loadActiveProducts(filterData);
    ajaxCalls.loadInActiveProducts(filterData);
  //	ajaxCalls.loadRejectedProducts(filterData);
  //	ajaxCalls.loadOutOfStockProducts(filterData);
      });

      $('body').on('click', ".deleteProduct", function () {
        var productId = $(this).attr('data');
          $('#productIdForDelete').val(productId)
          $('#productDeleteModel').modal('show');
      })

      $('body').on('click', '#confirmDelete', function () {
          filterData = {'languageId': $('#filterProduct').val(), 'categoryId': $('#filterCategory').val(),
                   'brandId': $('#filterBrand').val(), 'status': $('#filterStatus').val(),
                   'stock': $('#filterStock').val()};

        ajaxCalls.deleteProduct($('#productIdForDelete').val(), $('#filterProduct').val(), filterData);
      })

}
};

var table;
var ajaxCalls = {
loadAllProducts : function (filterData) {
  $('#tableAllProduct').DataTable().destroy();
   table = $('#tableAllProduct').DataTable({
          processing: true,
          serverSide: true,
          "initComplete": function (settings, json) {
              $("#tableAllProduct").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
          },
          "ajax": {
              'type': 'get',
              'url': baseUrl+'/admin/product/list',
              'data': filterData
          },
          'columnDefs': [
                {
                    "targets": [2],
                    "visible": false
                }
          ],
          // ajax: baseUrl+'/admin/product/list',
          columns: [{
            "target": 0,
            "visible":false,
            "data":'productDetailsId'
          },{
              "target": 1,
              "bSortable": false,
              "order":false,
              "render": function ( data, type, row ) {
                if(row['imageName'])
                  return "<img src='" + baseUrl + "/public/images/product/"+row['id']+"/"+row['imageName']+"' class='rounded' style='height:50px; width:50px;'>";
                else
                  return "<img src='" + baseUrl + "/public/assets/images/no_image.png' class='rounded' style='height:50px; width:50px;'>";
              }
          },
          {
          	"target": 2,
          	"data":'id'
          },
          {
              "target": 3,
              "render": function ( data, type, row ) {
                if(permission==1){
                    if(row['is_customized']==1)
                      return "<a href='#' data="+row['design_tool_product_id']+" class='editProductdt'>"+row['title']+"</a> <i class='fa pe-7s-tools' aria-hidden='true'></i>";
                    else
                      return row['title'];
                }
                else{
                    return row['title'];
                }
              }
          },
          
          // {
          // 	"target": 3,
          // 	"data":'title'
          // },
          {
            "target": 4,
            "data":'product_slug'
          },{
            "target": 5,
            "data":'sku'
          },{
            "target": 6,
            "data":'categoryTitle'
          },
          // {
          // 	"target": 7,
          // 	"data":'brandName'
          // },
          {
            "target": 8,
          //	"data":'sellingPrice'
            "render": function(data, type, row) {
              if(row['sellingPrice'])
              return parseFloat(row['sellingPrice']).toFixed(2);
              else
              return '-';
            }
          },{
            "target": 9,
            "data":'productCreatedDate'
          },{
              "target": -1,
              "bSortable": false,
              "order":false,
            "render": function ( data, type, row ) 
            {
                html = '';
                   html += "<a href='product/editProduct/"+ row['productDetailsId'] +"'><i class='fas fa-edit'></i></a> &nbsp &nbsp";
                   html += "<a href='#' data="+row['productDetailsId']+" class='text-danger deleteProduct'><i class='fas fa-trash'></i></a> &nbsp &nbsp";
                   html += "<a href='product/addProduct?page=anotherLanguage&productId="+row['id']+"' class='btn btn-primary' title='Add In Another Language'><i class='fas fa-plus'></i></a> &nbsp; &nbsp;";
                   if(copyProduct == 1)
                      // html += "<a onClick='copySelectedProduct("+row['id']+"); return false;' href= '' class='btn btn-primary' title='Copy this Product'><i class='fas fa-copy'></i></a>";
                      html+= "<a href='#' class='btn btn-primary' id='copyProd' title='Copy this Product'><i class='fas fa-copy'></i></a>"
                  return html;
                  
            },
          }]
      });
},

//ajax for Active products
loadActiveProducts : function(filterData){
  $('#tableActiveProduct').DataTable().destroy();
  $('#tableActiveProduct').DataTable({
    processing: true,
    serverSide: true,
    "initComplete": function (settings, json) {
      $("#tableActiveProduct").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    },
    "ajax": {
              'type': 'get',
              'url': baseUrl+'/admin/product/activeList',
              'data': filterData
          },
    // ajax: baseUrl+"/admin/product/activeList",
    columns: [{
            "target": 0,
            "visible":false,
            "data":'productDetailsId'
          },{
              "target": 1,
              "bSortable": false,
              "order":false,
              "render": function ( data, type, row ) {
                if(row['imageName'])
                  return "<img src='" + baseUrl + "/public/images/product/"+row['id']+"/"+row['imageName']+"' class='rounded' style='height:50px; width:50px;'>";
                else
                  return "<img src='" + baseUrl + "/public/assets/images/no_image.png' class='rounded' style='height:50px; width:50px;'>";
              }
          },
          // {
          // 	"target": 2,
          // 	"visible":false,
          // 	"data":'id'
          // },
          {
              "target": 3,
              "render": function ( data, type, row ) {
                if(permission==1){
                    if(row['is_customized']==1)
                      return "<a href='#' data="+row['design_tool_product_id']+" class='editProductdt'>"+row['title']+"</a> <i class='fa pe-7s-tools' aria-hidden='true'></i>";
                    else
                      return row['title'];
                }
                else{
                    return row['title'];
                }
              }
          },
          // {
          // 	"target": 3,
          // 	"data":'title'
          // },
          {
            "target": 4,
            "data":'product_slug'
          },{
            "target": 5,
            "data":'sku'
          },{
            "target": 6,
            "data":'categoryTitle'
          },
          // {
          // 	"target": 7,
          // 	"data":'brandName'
          // },
          {
            "target": 8,
            "render": function(data, type, row) {
              if(row['sellingPrice'])
              return parseFloat(row['sellingPrice']).toFixed(2);
              else
              return '-';
            }
          },{
            "target": 9,
            "data":'productCreatedDate'
          },{
              "target": -1,
              "bSortable": false,
              "order":false,
            "render": function ( data, type, row ) {
                  return "<a href='product/editProduct/"+ row['productDetailsId'] +"'><i class='fas fa-edit'></i></a> &nbsp &nbsp"+
                   "<a href='#' id='deleteBrand' data="+row['productDetailsId']+"><i class='fas fa-trash'></i></a> &nbsp &nbsp"+
                   "<a href='product/addProduct?page=anotherLanguage&productId="+row['id']+"' class='btn btn-primary' title='Add In Another Language'><i class='fas fa-plus'></i></a>";
              },
          }]
  });
},

loadInActiveProducts : function(filterData){
  $('#tableInActiveProduct').DataTable().destroy();
  $('#tableInActiveProduct').DataTable({
    processing: true,
    serverSide: true,
    "initComplete": function (settings, json) {
      $("#tableInActiveProduct").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    },
    "ajax": {
              'type': 'get',
              'url': baseUrl+'/admin/product/inactiveList',
              'data': filterData
          },
    // ajax: baseUrl+"/admin/product/inactiveList",
    columns:[{
            "target": 0,
            "visible":false,
            "data":'productDetailsId'
          },{
              "target": 1,
              "bSortable": false,
              "order":false,
              "render": function ( data, type, row ) {
                if(row['imageName'])
                  return "<img src='" + baseUrl + "/public/images/product/"+row['id']+"/"+row['imageName']+"' class='rounded' style='height:50px; width:50px;'>";
                else
                  return "<img src='" + baseUrl + "/public/assets/images/no_image.png' class='rounded' style='height:50px; width:50px;'>";
              }
          },
          // {
          // 	"target": 2,
          // 	"visible":false,
          // 	"data":'id'
          // },
          {
              "target": 3,
              "render": function ( data, type, row ) {
                if(permission==1){
                    if(row['is_customized']==1)
                      return "<a href='#' data="+row['design_tool_product_id']+" class='editProductdt'>"+row['title']+"</a> <i class='fa pe-7s-tools' aria-hidden='true'></i>";
                    else
                      return row['title'];
                }
                else{
                    return row['title'];
                }
              }
          },
          // {
          // 	"target": 3,
          // 	"data":'title'
          // },
          {
            "target": 4,
            "data":'sku'
          },{
            "target": 5,
            "data":'product_slug'
          },{
            "target": 6,
            "data":'categoryTitle'
          },
          // {
          // 	"target": 7,
          // 	"data":'brandName'
          // },
          {
            "target": 8,
            "render": function(data, type, row) {
              if(row['sellingPrice'])
              return parseFloat(row['sellingPrice']).toFixed(2);
              else
              return '-';
            }
          },{
            "target": 9,
            "data":'productCreatedDate'
          },{
              "target": -1,
              "bSortable": false,
              "order":false,
            "render": function ( data, type, row ) {
                  return "<a href='product/editProduct/"+ row['productDetailsId'] +"'><i class='fas fa-edit'></i></a> &nbsp &nbsp"+
                   "<a href='#' id='deleteBrand' data="+row['productDetailsId']+"><i class='fas fa-trash'></i></a> &nbsp &nbsp"+
                   "<a href='product/addProduct?page=anotherLanguage&productId="+row['id']+"' class='btn btn-primary' title='Add In Another Language'><i class='fas fa-plus'></i></a>";
              },
          }]
  });
},

//load rejected products
loadRejectedProducts: function(filterData){
  $('#tableRejectedProduct').DataTable().destroy();
  $('#tableRejectedProduct').DataTable({
    processing: true,
    serverSide: true,
    "initComplete": function (settings, json) {
      $("#tableRejectedProduct").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    },
    "ajax": {
              'type': 'get',
              'url': baseUrl+'/admin/product/rejectedList',
              'data': filterData
          },
    // ajax: baseUrl+"/admin/product/rejectedList",
    columns:[{
            "target": 0,
            "visible":false,
            "data":'productDetailsId'
          },{
              "target": 1,
              "bSortable": false,
              "order":false,
              "render": function ( data, type, row ) {
                  return "<img src='" + baseUrl + "/public/images/product/"+row['id']+"/"+row['imageName']+"' class='rounded' style='height:50px; width:50px;'>";
              }
          },{
            "target": 2,
            "visible":false,
            "data":'id'
          },{
            "target": 3,
            "data":'title'
          },{
            "target": 4,
            "data":'sku'
          },{
            "target": 5,
            "data":'categoryTitle'
          },{
            "target": 6,
            "data":'brandName'
          },{
            "target": 7,
            "render": function(data, type, row) {
              return "-"
            }
          },{
            "target": 8,
            "data":'productCreatedDate'
          },{
              "target": -1,
              "bSortable": false,
              "order":false,
            "render": function ( data, type, row ) {
                  return "<a href='"+ row['id'] +"/editProduct'><i class='fas fa-edit'></i></a> &nbsp &nbsp"+
                   "<a href='#' id='deleteBrand' data="+row['productDetailsId']+"><i class='fas fa-trash'></i></a> &nbsp &nbsp"+
                   "<a href='product/addProduct?page=anotherLanguage&productId="+row['id']+"' class='btn btn-primary' title='Add In Another Language'><i class='fas fa-plus'></i></a>";
              },
          }]
  });
},

//load out of stock product
loadOutOfStockProducts: function(filterData){
  $('#tableOutofStockProduct').DataTable().destroy();
  $('#tableOutofStockProduct').DataTable({
    processing: true,
          serverSide: true,
          "initComplete": function (settings, json) {
            $("#tableOutofStockProduct").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
          },
          "ajax": {
              'type': 'get',
              'url': baseUrl+'/admin/product/outOfStockList',
              'data': filterData
          },
          // ajax: baseUrl+'/admin/product/list',
          columns: [{
            "target": 0,
            "visible":false,
            "data":'productDetailsId'
          },{
              "target": 1,
              "bSortable": false,
              "order":false,
              "render": function ( data, type, row ) {
                  return "<img src='" + baseUrl + "/public/images/product/"+row['id']+"/"+row['imageName']+"' class='rounded' style='height:50px; width:50px;'>";
              }
          },{
            "target": 2,
            "data":'id'
          },{
            "target": 3,
            "data":'title'
          },{
            "target": 4,
            "data":'sku'
          },{
            "target": 5,
            "data":'categoryTitle'
          },{
            "target": 6,
            "data":'brandName'
          },{
            "target": 7,
            "data":'categoryTitle'
          },{
            "target": 8,
            "data":'productCreatedDate'
          },{
              "target": -1,
              "bSortable": false,
              "order":false,
            "render": function ( data, type, row ) {
                  return "<a href='product/editProduct/"+ row['productDetailsId'] +"'><i class='fas fa-edit'></i></a> &nbsp &nbsp"+
                   "<a href='#' data="+row['productDetailsId']+" class='text-danger deleteProduct'><i class='fas fa-trash'></i></a> &nbsp &nbsp"+
                   "<a href='product/addProduct?page=anotherLanguage&productId="+row['id']+"' class='btn btn-primary' title='Add In Another Language'><i class='fas fa-plus'></i></a>";
              },
          }]
  })
},

deleteProduct(productDetailId, languageId, filterData){
  $.ajax({
          type: "get",
          url:'product/deleteProduct',
          data:{'productDetailId':productDetailId, 'languageId': languageId},
          success: function (response) {
              if (response['success'] == true) {
                  // $('#brandLanguageDeleteModel').modal('hide');
                  $('#productDeleteModel').modal('hide');
                  var table = $('#tableAllProduct').DataTable(); 
                  table.ajax.reload( null, false );
                  toastr.success(response.message);
                  // ajaxCalls.loadAllProducts(filterData);
                  // ajaxCalls.loadActiveProducts(filterData);
                  // ajaxCalls.loadInActiveProducts(filterData);
                  //ajaxCalls.loadRejectedProducts(filterData);
                //  ajaxCalls.loadOutOfStockProducts(filterData);
                  // ajaxCalls.loadDataTable($('#filter_brands').val());
              }
          }
      });
},

getBrands : function (languageId) {
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
          type: 'get',
          url: baseUrl +'/admin/product/brands',
          data:{'languageId':languageId},
          beforeSend: function() {
               $('#loaderimage').css("display", "block");
               $('#loadingorverlay').css("display", "block");
          },
          success: function (response) {
            appendHtml.appendBrandDropDown(response);
          }
      });
},

getCategories : function(languageId){
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $.ajax({
          type: 'get',
          // url: baseUrl +'/admin/categories',
          url: baseUrl +'/admin/product/categories',
          data:{'languageId':languageId},
          beforeSend: function() {
               $('#loaderimage').css("display", "block");
               $('#loadingorverlay').css("display", "block");
          },
          success: function (response) {
            appendHtml.appendCategoryDropDown(response);
          }
      });
}
}

var appendHtml = {
//append brands dropdown
appendBrandDropDown : function (brands, elementId) {
      $('#filterBrand').empty();
      var brandId = document.getElementById('filterBrand');
      var value = "all";
      var text = "All";
      var o = new Option(text, value);
      brandId.append(o);

  $.each(brands, function(index, item) {
    var value = item['id'];
    var text = item['brandName'];
    var o = new Option(text, value);
    brandId.append(o);
  });
},

//append brands dropdown
appendCategoryDropDown : function (categories) {
  $('#filterCategory').empty();
  var selectCategory = document.getElementById('filterCategory');
  var value = "all";
  var text = "All";
  var o = new Option(text, value);
  selectCategory.append(o);
  $.each(categories, function(index, item) {
    var value = item['id'];
    var text = item['category'];
    var o = new Option(text, value);
    selectCategory.append(o);
  });
},
}
//To direct login to Design-Tool
$(".loginLumise").click(function(){
    $.ajaxSetup({
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $.ajax({
      type: "post",
      url: baseUrl +'/design-tool/admin.php?login',
      data:{"email":'admin@gmail.com',
            "password":'12345678',
            "nonce":'nonce',
            "action":'login',
            },
      success: function (response)
      {
          url = baseUrl+'/design-tool/admin.php?lumise-page=products';
          window.open(url);
      }
  });
});

//move to edit product in Design-Tool
$('body').on('click', ".editProductdt", function () {
  var productId = $(this).attr('data');
      $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
      });
      $.ajax({
        type: "post",
        url: baseUrl +'/design-tool/admin.php?login',
        data:{"email":'admin@gmail.com',
              "password":'12345678',
              "nonce":'nonce',
              "action":'login',
              },
        success: function (response)
        {
            url = baseUrl+'/design-tool/admin.php?lumise-page=product&id='+productId;
            window.open(url);
        }
    });
  });

  $('body').on('click','#resetFilter',function(){
      document.getElementById("myForm1").reset();
      var filterData = {'languageId': $('#filterProduct').val(), 'categoryId': $('#filterCategory').val(),
               'brandId': $('#filterBrand').val(), 'status': $('#filterStatus').val(),
               'stock': $('#filterStock').val(),'customize': $('#filterCustomize').val()}
      ajaxCalls.loadAllProducts(filterData);
      ajaxCalls.loadActiveProducts(filterData);
      ajaxCalls.loadInActiveProducts(filterData);
  });


  // Copy product (Pallavi : July 29, 2021)
// change event enq status
$(document).on('click', '#copyProd', function(){
    var id = table.row($(this).closest('tr')).data();
    
    $('#productCopyModel').on('show.bs.modal', function(e){
      $('#productIdForCopyModel').val(id.id);
    });
    $('#productCopyModel').modal('show');
})

// function copySelectedProduct(prodId)
$(document).on('click','#confirmProdCopy', function()
{
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    $('#productCopyModel').modal('hide');

    $(document).ajaxSend(function() {
        $("#ajax-loader").fadeIn(300);　
    });

    $.ajax({
        type: "POST",
        url: baseUrl +'/admin/product/copyProduct',
        data:{
            prodId : $('#productIdForCopyModel').val()
        },
        success: function (response)
        {
            // $("#ajax-loader").hide();
            setTimeout(function(){
                $("#ajax-loader").hide();
            },500);

            
            if(response.status == true)
            {

                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.success(response.msg);

            }
            else
            {
                toastr.clear();
                toastr.options.closeButton = true;
                toastr.options.timeOut = 0;
                toastr.error(response.msg);
            }

            setTimeout(function(){
                toastr.clear();
            }, 2000);
            // $('#tableAllProduct').DataTable().ajax.reload()

            window.location.reload();
        }
    });
})