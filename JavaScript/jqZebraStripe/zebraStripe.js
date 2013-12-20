/**
 * jQuery.zebraStripe
 * 
 * A jQuery plugin to add zebra stripe classes to tables in browsers with
 * no support for the :nth-child pseudo selector.
 * 
 * @author Nathan Marshall (FDL)
 */
(function($) {
    $.fn.zebraStripe = function(options) {
        var settings = $.extend({}, {
                parity: 'odd',
                className: 'odd'
            }, options);

        return this.each(function() {
            $('tr:nth-child(' + settings.parity + ')', $(this)).addClass(settings.className);
        });
    };
})(jQuery);