/**
 * Yet another fancy tooltip plugin for jQuery.
 *
 * @author Nathan Marshall (FDL)
 */
(function($) {
    $.fn.hoverTooltip = function(settings) {
        var options = $.extend({}, {
            fade: false,
            fallbackText: 'Ooops, something went wrong.',
            gravity: "n",
            html: false,
            opacity: 0.8,
            tipText: 'title'
        }, settings);

        return this.each(function() {
            var opts = options;

            $(this).hover(function() {
                $.data(this, 'cancel.help-tip', true);
                var tip = $.data(this, 'active.help-tip');
                if(!tip) {
                    tip = $('<div class="help-tip"><div class="help-tip-inner"></div></div>').css({
                        position: 'absolute',
                        zindex: '9999'
                    });
                    $.data(this, 'active.help-tip', tip);
                }

                // Remove the title attribute (move it to tooltip-title) so default title 
                // browser behaviour is not triggered.
                if($(this).attr('title') || typeof($(this).attr('original-title')) != 'string') {
                    $(this).attr('original-title', $(this).attr('title') || '').removeAttr('title');
                }

                var tipText;
                if(typeof opts.tipText == 'string') {
                    tipText = $(this).attr(opts.tipText == 'title' ? 'original-title' : opts.tipText);
                } else if(typeof opts.tipText == 'function') {
                    tipText = opts.tipText.call(this);
                }

                tip.find('.help-tip-inner')[opts.html ? 'html' : 'text'](tipText || opts.fallbackText);
                var pos = $.extend({}, $(this).offset(), {
                    width: this.offsetWidth,
                    height: this.offsetHeight
                });

                tip.get(0).className = 'help-tip';
                tip.remove().css({
                    top: 0,
                    left: 0,
                    visibility: 'hidden',
                    display: 'block'
                }).appendTo(document.body);

                var actualWidth = tip[0].offsetWidth,
                    actualHeight = tip[0].offsetHeight,
                    gravity = (typeof opts.gravity == 'function') ? opts.gravity.call(this) : opts.gravity;

                switch(gravity.charAt(0)) {
                    case 'n':
                        tip.css({
                            top: pos.top + pos.height,
                            left: pos.left + pos.width / 2 - actualWidth / 2
                        }).addClass('help-tip-north');
                        break;
                    case 's':
                        tip.css({
                            top: pos.top - actualHeight,
                            left: pos.left + pos.width / 2 - actualWidth / 2
                        }).addClass('help-tip-south');
                        break;
                    case 'e':
                        tip.css({
                            top: pos.top + pos.height / 2 - actualHeight / 2,
                            left: pos.left - actualWidth
                        }).addClass('help-tip-east');
                        break;
                    case 'w':
                        tip.css({
                            top: pos.top + pos.height / 2 - actualHeight / 2,
                            left: pos.left + pos.width
                        }).addClass('help-tip-west');
                        break;
                }
                if(opts.fade) {
                    tip.css({
                        opacity: 0,
                        display: 'block',
                        visibility: 'visible'
                    }).animate({
                        opacity: opts.opacity
                    }, 100);
                } else {
                    tip.css({
                        visibility: 'visible'
                    });
                }
            }, function() {
                $.data(this, 'cancel.help-tip', false);
                var self = this;
                setTimeout(function() {
                    if($.data(this, 'cancel.help-tip')) {
                        return;
                    }

                    var tip = $.data(self, 'active.help-tip');
                    if(opts.fade) {
                        tip.stop().fadeOut(50, function() {
                            $(this).remove();
                        });
                    } else {
                        tip.remove();
                    }
                }, 100);
            });
        });
    };
})(jQuery);