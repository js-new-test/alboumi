$(document).ready(function(){
    ajaxCalls.getFooterData();
});
var ajaxCalls = {
    getFooterData : function() {
        $.ajax({
            type: "get",
            url:baseUrl +'/getFooterLinks',
            dataType: 'json',
            success: function (response)
            {
                $('#fb').attr('href',response.socialLinks.fb_link);
                $('#fb').attr('target','_blank');

                $('#insta').attr('href',response.socialLinks.insta_link);
                $('#insta').attr('target','_blank');

                $('#youtube').attr('href',response.socialLinks.youtube_link);
                $('#youtube').attr('target','_blank');

                $('#twitter').attr('href',response.socialLinks.twitter_link);
                $('#twitter').attr('target','_blank');

                for(i = 0; i< response.footerData.length ;i++)
                {
                    $('.footerTitle' + i + '> p').html(response.footerData[i].footer_group);
                    $.each(response.footerData[i].children,function(k,v) {
                        $('.footerTitle' + i + '> ul').append('<li><a href="'+ v.link +'">'+ v.name +'</a></li>');
                    });
                }
                $.each(response.footerLabels, function(i, v) {
                    if(i == "FOLLOWUS")
                        $('.localLabels0 > p').html(v);
                    else
                        $('.localLabels1 > p').html(v);
                })
            }
        });
    }
}
$(document).ready(function(){
    $(".footer-menu > .s1").click(function(){
      $(this).toggleClass("footerToggle");
    })
  })