// Taken from HTML
$(document).ready(function(){
	$(".c-plan").click(function(){
		var a = document.getElementsByClassName('package-ck');
		for(var i = 0; i < a.length; i++){
			a[i].checked = false
		}
		$(this).closest(".package-box").find(".package-ck").prop('checked', true);
	})
})

// for RTL


$(function(){
  var $ul   =   $('.additional-accordian > ul');
  
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
  
  
  var t = ' li > ul ';
  for(var i=1;i<=10;i++){
    $('.additional-accordian > ul > ' + t.repeat(i)).addClass('subMenuColor' + i);
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


$(document).on('ready', function(){
  
  $('.plusminus').numberPicker();
  
});

if(langVisibility == 0)
    rtl = false;
if(langVisibility == 1)
    rtl = true;

$(document).ready(function() {
	$('.owl-carousel.owl5').owlCarousel({
	  rtl : rtl,
    loop: true,
	  responsiveClass: true,
	  nav: false,
	  dots: true,
	  responsive: {
	    0: {
	      items: 1,
	    }   
	  }
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
