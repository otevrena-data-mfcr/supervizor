$(document).ready(function(){
    $(".about").fancybox({
      type:"iframe",
      href: $(this).attr('href'),
      width:600,
      height:600,
      padding:40
    });
});
			