// Custom scripts
(function($){
    $('#featured-post-carousel').carousel({
        interval: 3000
    });
})(jQuery);

$(document).ready(function(){
    $('.animated-filter-icon').click(function(){
        $(this).toggleClass('open');
    });
});