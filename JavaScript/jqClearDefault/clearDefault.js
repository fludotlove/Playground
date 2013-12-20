/**
 * Clear default value from an input.
 *
 * @author Nathan Marshall (FDL)
 */
(function($) {
    $.fn.clearDefault = function() {
        return this.each(function() {
            var value = $(this).val();

            $(this).focus(function() {
                if($(this).val() == value) {
                    $(this).val('');
                }
            }).blur(function() {
                if($(this).val() == '') {
                    $(this).val(value);
                }
            });
        });
    };
})(jQuery);