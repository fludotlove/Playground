/**
 * Creates simple tabs.
 *
 * @author Nathan Marshall (FDL)
*/
(function($) {
    $.fn.tabs = function() {
        return this.each(function() {
            var self = $(this),
                openTab = $('.tabs li.active-tab a', self).attr('href');
            
            $('.tab-content', self).hide();
            $(openTab, self).show();
            
            $('.tabs li a', self).click(function(event) {
                var clickedTab = $(this);
                event.preventDefault();
    
                if(clickedTab.parent('li').hasClass('tab-unavailable')) {
                    return false;
                }
                        
                clickedTab.parent('li')
                    .addClass('active-tab')
                    .siblings('li.active-tab')
                    .removeClass('active-tab');
    
                $('.tab-content', self).hide();
                $(clickedTab.attr('href'), self).show();
    
                return false;
            });
        });
    };
})(jQuery);