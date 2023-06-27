// photographer product dropdown


const BASE_URL = "";

const $_SELECT_PICKER = $('.my-image-selectpicker');

$_SELECT_PICKER.find('option').each((idx, elem) => {
    const $OPTION = $(elem);
    const IMAGE_URL = $OPTION.attr('data-thumbnail');

    if (IMAGE_URL) {
        $OPTION.attr('data-content', "<img src='%i'/> %s".replace(/%i/, BASE_URL + IMAGE_URL).replace(/%s/, $OPTION.text()))
    }

    console.warn('option:', idx, $OPTION)
});

$_SELECT_PICKER.selectpicker();
// sticky navbar start

window.onscroll = function() {myFunction()};

var navbar = document.getElementsByClassName('sub-nav');
var stickyNavbar = navbar[0].offsetTop;

function myFunction() {
  if (window.pageYOffset >= stickyNavbar) {
    navbar[0].classList.add("stickyNavbar")
  } else {
    navbar[0].classList.remove("stickyNavbar");
  }
}

$(function(){
  var $ul   =   $('.sidebar-navigation > ul');

  $ul.find('li a').click(function(e){
    var $li = $(this).parent();

    if($li.find('ul').length > 0){
      e.preventDefault();

      if($li.hasClass('selected')){
        $li.removeClass('selected').find('li').removeClass('selected');
        $li.find('ul').slideUp(400);
        $li.find('a em').removeClass('mdi-flip-v');
      }else{

        if($li.parents('li.selected').length == 0){
          $ul.find('li').removeClass('selected');
          $ul.find('ul').slideUp(400);
          $ul.find('li a em').removeClass('mdi-flip-v');
        }else{
          $li.parent().find('li').removeClass('selected');
          $li.parent().find('> li ul').slideUp(400);
          $li.parent().find('> li a em').removeClass('mdi-flip-v');
        }

        $li.addClass('selected');
        $li.find('>ul').slideDown(400);
        $li.find('>a>em').addClass('mdi-flip-v');
      }
    }
  });


  // $('.sidebar-navigation > ul ul').each(function(i){
  //   if($(this).find('>li>ul').length > 0){
  //     // var paddingLeft = $(this).parent().parent().find('>li>a').css('padding-left');
  //     // var pIntPLeft   = parseInt(paddingLeft);
  //     // var result      = 16;

  //     // $(this).find('>li>a').css('padding-left',result);
  //   }else{
  //     // var paddingLeft = $(this).parent().parent().find('>li>a').css('padding-left');
  //     // var pIntPLeft   = parseInt(paddingLeft);
  //     // var result      = 16;

  //     $(this).find('>li>a').parent().addClass('selected--last');
  //   }
  // });

  var t = ' li > ul ';
  for(var i=1;i<=10;i++){
    $('.sidebar-navigation > ul > ' + t.repeat(i)).addClass('subMenuColor' + i);
  }

  var activeLi = $('li.selected');
  if(activeLi.length){
    opener(activeLi);
  }

  function opener(li){
    var ul = li.closest('ul');
    if(ul.length){

        li.addClass('selected');
        ul.addClass('open');
        li.find('>a>em').addClass('mdi-flip-v');

      if(ul.closest('li').length){
        opener(ul.closest('li'));
      }else{
        return false;
      }

    }
  }

});


$(document).ready(function(){
  $(".dropdowns").click(function(){
    $(this).toggleClass("active");
  })
  if($(".dropdowns")){
    $(".dropdowns").text($(".dropdowns-toggles .active").text())
  }
})


/*RTL not Design from here below*/

// Event Gallery


$(document).ready(function() {

  var $imageSrc;
  var $printImg;

  $('.gallery-grid img').click(function() {
      $imageSrc = $(this).data('bigimage');

      $("#image").attr('src', $imageSrc  );

      $(".modal-btn-header").show();

      $(".modal-upper-logo").hide();

  });

  $('.logo-with-click').click(function() {
      $imageSrc = $(this).data('bigimage');

      $("#image").attr('src', $imageSrc  );

      $(".modal-btn-header").hide();

      $(".modal-upper-logo").show();

  });


  $('#gallery-modal').on('shown.bs.modal', function (e) {

      $("#image").attr('src', $imageSrc  );

  })

  $('#gallery-modal').on('hide.bs.modal', function (e) {
      $("#image").attr('src','');
  })

});


/*RTL not Design from here above*/





// mobile search

$(document).ready(function(){
    $(".search-icon").click(function(){
    $(".menus").hide();
    $(".cart-icon").hide();
    $(".mobile-logo").hide();
    $(".cart-badge").hide();
    $('.login-icon').hide();

    $(".search-icon").addClass("search--animaton-add");
    $(".search-input").addClass("input--animation-add");
    $(".search-close").addClass("close--animation-add")
    })

    $(".search-close").click(function(){
    $(".menus").show();
    $(".cart-icon").show();
    $(".mobile-logo").show();
    $(".cart-badge").show();
    $('.login-icon').show();

    $(".search-icon").removeClass("search--animaton-add");
    $(".search-input").removeClass("input--animation-add");
    $(".search-close").removeClass("close--animation-add")
    })
})


// mobile search






$(document).ready(function(){
  var lv = $(".language-change .dropdown-item.active").text()
  $("#language").text(lv);

  $(".language-change .dropdown-item").click(function(e){
      $("#language").text(e.target.innerHTML)
  })
})



$(document).ready(function(){
  $(".choose-plan").click(function(){
    var a = $(this).parent('th').index();

    var b = a + 1;

    var allTD = $(".package-tbl tbody tr td");
    var allTH = $(".package-tbl thead tr th");

    for (var i = 1; i <= allTD.length; i++) {
      $(".package-tbl tbody td:nth-child(" + i + ")").removeClass("selected")
    }
    for (var i = 1; i <= allTH.length; i++) {
      $(".package-tbl thead th:nth-child(" + i + ")").removeClass("selected")
    }
    $(this).parent('th').addClass("selected");
    $(".package-tbl tbody td:nth-child(" + b + ")").addClass("selected")

  })
})



















// ===============
//     for RTL
// ===============











// menu color effect start

$(document).ready(function(){
  var a = "";
  if ($('.sub-nav .nav-item').hasClass('active')) {
      $(".sub-nav .nav-item").each(function(){
          $(this).addClass("pblur")
      })
  }
  $('.sub-nav .nav-item').hover(
    function(){
      a = $(".sub-nav .nav-item").index( this );
      $(".sub-nav .nav-item").each(function(){
          if($(".sub-nav .nav-item").index(this) == a){
            $( this ).removeClass("blur")
          }
          else{
            $( this ).addClass("blur")
          }
      })
    },
    function(){
      $('.sub-nav .nav-item').removeClass("blur")
    }
  )
})

// menu color effect end
