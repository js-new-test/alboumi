(function ($) {
    $.fn.numberPicker = function() {
      var dis = 'disabled';
      return this.each(function() {
        var picker = $(this),
            p = picker.find('button:last-child'),
            m = picker.find('button:first-child'),
            input = picker.find('input'),                 
            min = parseInt(input.attr('min'), 10),
            max = parseInt(input.attr('max'), 10),
            inputFunc = function(picker) {
              var i = parseInt(input.val(), 10);
              if ( (i <= min) || (!i) ) {
                input.val(min);
                p.prop(dis, false);
                m.prop(dis, true);
              } else if (i >= max) {
                input.val(max);
                p.prop(dis, true); 
                m.prop(dis, false);
              } else {
                p.prop(dis, false);
                m.prop(dis, false);
              }
            },
            changeFunc = function(picker, qty) {
              var q = parseInt(qty, 10),
                  i = parseInt(input.val(), 10);
              if ((i < max && (q > 0)) || (i > min && !(q > 0))) {
                input.val(i + q);
                inputFunc(picker);
              }
            };
        m.on('click', function(){changeFunc(picker,-1);});
        p.on('click', function(){changeFunc(picker,1);});
        input.on('change', function(){inputFunc(picker);});
        inputFunc(picker); //init
      });
    };
  }(jQuery));
  
  $("#ajax-loader").hide();
  $(document).on('ready', function(){
    
    $('.plusminus').numberPicker();  
   
  });

  // side bar js
  
  // $(document).ready(function(){
  //   $(".menuIcons").on("click", function(){
  //     $(".sideMenu").toggleClass("active");
  //     $("body").toggleClass("scroll-stop")
  //   });
  //   $(".closeIcons").on("click", function(){
  //     $(".sideMenu").toggleClass("active");
  //     $("body").toggleClass("scroll-stop")
  //   });
  // });
  
  var remove_obj;
  $('.remove_qty').on('click', function(){    
      remove_obj = $(this);
      var remove_qty = parseInt($(this).closest(".plusminus").find(".productQty").val()) - parseInt(1);    
      var cart_id = $(this).closest(".quantity").find('.cart_id').val();    
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url: baseUrl + '/add-remove-qty',
          method: 'POST',
          data: {
              "_token": $('#token').val(),
              remove_qty: remove_qty,
              cart_id: cart_id
          },
          success: function(response){
              if(response.status == 'true')
              {
                //   remove_obj.closest(".plusminus").find(".productQty").val(response.qty);
                location.reload();
              }
          }
      })    
  });
  
  var add_obj;
  $('.add_qty').on('click', function(){
      add_obj = $(this);
      var add_qty = parseInt($(this).closest(".plusminus").find(".productQty").val()) + parseInt(1);    
      var cart_id = $(this).closest(".quantity").find('.cart_id').val();
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url: baseUrl + '/add-remove-qty',
          method: 'POST',
          data: {
              "_token": $('#token').val(),
              add_qty: add_qty,
              cart_id: cart_id
          },
          success: function(response){
              if(response.status == 'true')
              {
                //   add_obj.closest(".plusminus").find(".productQty").val(response.qty);
                location.reload();
              }
          }
      })
  })
  
  var current_rmv_obj;
  $('.remove_product').on('click', function() {
      var cart_id = $(this).attr('data-cart-id');
      current_rmv_obj = $(this);
      $('#deleteCartProductModel').on('show.bs.modal', function(e){
          $('#cart_id').val(cart_id);        
      });
      $('#deleteCartProductModel').modal('show');
  });
  
  $('#dlt_prod_from_cart').on('click', function(){
      var cart_id = $('#cart_id').val();
      var output = '';
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
          url: baseUrl + '/remove-product-from-cart',
          method: 'POST',
          data: {
              "_token": $('#token').val(),            
              cart_id: cart_id
          },
          success: function(response){              
              if(response.status == 'true')
              {
                  if(response.Cart_Count > 0)
                  {
                    $('#deleteCartProductModel').modal('hide');
                    current_rmv_obj.closest(".dynamic_table_section_remove").remove();
                    $('#sub_total').text(response.sub_total)
                    $('#discount').text(response.discount)
                    $('#net').text(response.net)
                    $('#shipping_cost').text(response.shipping_cost)
                    $('#grand_total').text(response.grand_total)
                    $('#vat').text(response.vat)
                    $('.badge-icon').text(response.Cart_Count);
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                  }
                  else
                  {
                    $('.badge-icon').text(response.Cart_Count);
                    $('#deleteCartProductModel').modal('hide');
                    $(".dynamic_section_remove").remove();
                    output += '<section class="shopping-cart">';
                    output += '<div class="container">';
                    output += '<h4>'+response.cart_empty_msg+'</h4>';
                    output += '</div>';
                    output += '</section>';                    
                    $('#shopping_cart_empty').html(output)

                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success(response.msg);
                  }                  
                  // location.reload();
              }
          }
      })
  })

  $(document).on('click', '#remove_coupon_code_btn',function(){
    var cart_id = $(this).attr('data-cart-id');
    $('#removePromoCodeModel').on('show.bs.modal', function(e){
        $('#cart_master_id').val(cart_id);        
    });
    $('#removePromoCodeModel').modal('show');
  })

  $(document).on('click', '#dlt_promo_code_btn', function(){
    $('#removePromoCodeModel').modal('hide');
    var cart_master_id = $('#cart_master_id').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
      url: baseUrl + '/remove-promo-code',
      method: 'POST',
      data: {
        "_token": $('#token').val(),
        cart_master_id: cart_master_id
      },
      beforeSend: function() {
        $("#ajax-loader").fadeIn();
      },
      success: function(response){
        if(response.status == 'true')
        {

          // $('#removePromoCodeModel').modal('hide');
          // $('.promo_code_apply_input').attr('value', '');
          // $('.promo_code_apply_input').attr('placeholder', response.Placeholder);
          // $('.promo_code_apply_btn').attr('id', 'apply_coupon_code_btn');
          // $('.promo_code_apply_btn').html(response.Apply);
          // toastr.clear();
          toastr.options.closeButton = true;
          toastr.options.timeOut = 0;
          toastr.success(response.msg);
          setTimeout(function() { 
            // $("#removingPromoCodeModel").modal('hide'); 
            toastr.clear();            
          }, 2000);
          setTimeout(function(){   
            $("#ajax-loader").fadeOut();

            location.reload();              
          }, 2000);
        }
      }      
    })
  })

  $(document).on('click', '#apply_coupon_code_btn', function(e){
    var coupon_code = $('#coupon_code').val();
    var enter_promo_code_label = $('#enter_promo_code_label').val();
    var cart_master_id = $(this).attr('data-cart-id');
    if(coupon_code == '')
    {
      // $('.promo_code_input_error').html('<p>'+enter_promo_code_label+'</p>');
      toastr.clear();
      toastr.options.closeButton = true;
      toastr.options.timeOut = 0;
      toastr.error(enter_promo_code_label);
      return false;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: baseUrl + '/apply-promo-code',
        method: 'POST',
        data: {
          "_token": $('#token').val(),
          coupon_code: coupon_code,
          cart_master_id: cart_master_id,
        },
        beforeSend: function() {
          $("#ajax-loader").fadeIn();
        },
        success: function(response){                
          if(response.status == 'Success')
          {                                    
            toastr.options.closeButton = true;
            toastr.options.timeOut = 0;
            $("#ajax-loader").fadeOut();
            toastr.success(response.message);   
            /*setTimeout(function(){                
                toastr.clear();                
            }, 2000);*/
            setTimeout(function(){ 
              //$("#ajax-loader").fadeOut();                             
              location.reload();              
            }, 1500);
          }
          else
          {                          
              toastr.options.closeButton = true;
              toastr.options.timeOut = 0;
              toastr.error(response.message);        
              $("#ajax-loader").fadeOut();
              setTimeout(function(){                
                  toastr.clear();                
              }, 3000);
              /*setTimeout(function(){ 
                $("#ajax-loader").fadeOut();                                           
              }, 3000);*/
          }
        } 
    })
  })