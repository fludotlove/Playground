(function($) {
    $.fn.newsReel = function(articles, options) {
        var blink,
            currentLength = 0,
            currentArticle = -1,
            reel = articles,
            reelLength = Object.keys(articles).length,
            settings = $.extend({}, {
                blinkSpeed: 0.3,
                leadString: 'Breaking News: ',
                articleTimeout: 5,
                characterTimeout: 0.06
            }, options),
            ticker = $(this),
            timeout;

        function runNewsReel() {
            if(currentLength == 0) {
              currentArticle++;
              currentArticle = currentArticle % reelLength;
            }

            articleSubject = reel[currentArticle].title;
            ticker.html('<span class="news-reel-lead">' + settings.leadString + '</span>' + articleSubject.substring(0, currentLength) + '<span class="news-reel-cursor' + (currentLength == articleSubject.length ? '-blink' : '') + '">|</span>')
                  .attr('href', reel[currentArticle].link);

            if(currentLength != articleSubject.length) {
                currentLength++;
                timeout = settings.characterTimeout * 1000;
            } else {
                currentLength = 0;

                clearInterval(blink);
                blink = setInterval(function() {
                    $('.news-reel-cursor-blink', ticker).css('display', ($('.news-reel-cursor-blink', ticker).css('display') == 'inline' ? 'none' : 'inline'));
                }, settings.blinkSpeed * 1000);

                timeout = settings.articleTimeout * 1000;
            }

            setTimeout(runNewsReel, timeout);
        }

        runNewsReel();
    };
})(jQuery);