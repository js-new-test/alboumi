$(document).ready(function(){
    // $(".menuIcons").on("click", function(){
    //     $(".sideMenu").toggleClass("active");
    //     $("body").toggleClass("scroll-stop")
    // });
    // $(".closeIcons").on("click", function(){
    //     $(".sideMenu").toggleClass("active");
    //     $("body").toggleClass("scroll-stop")
    // });

    // carousel
    if ($(".space-flex").offset()) {
        var x = $(".space-flex").offset();
        var y = x.left;

        console.log(y)	

        if ($(".left-container")) {

            $(".left-container").css("width","calc(100% - " + y +"px)")

        }
    }
     
    if(langVisibility == 0)
        rtl = false;
    if(langVisibility == 1)
        rtl = true;
    // slider
    $('.owl-carousel').owlCarousel({
        rtl : rtl,
        loop: true,
        margin: 10,
        responsiveClass: true,
        nav: true,
        responsive: {
            0: {
                items: 2,
                margin: 16
            },
            575: {
                items: 2,
                margin: 20
            },
            992: {
                items: 3,
                margin: 24
            },
            1200: {
                items: 4,
                margin: 24
            }
        }
    })
});

window.addEventListener("load", event => {
    var image1 = document.querySelector('.desktop-img');
    var image2 = document.querySelector('.mobile-img');
    var isLoaded1 = image1.complete && image1.naturalHeight !== 0;
    var isLoaded2 = image2.complete && image2.naturalHeight !== 0;

    if(isLoaded1 || isLoaded2)
    {
       $(".overlay").show();
    }
});