        <script src="js/jquery.js"></script>
        <script src="js/tblsorter.jquery.js"></script>
        <script src="js/timeago.jquery.js" type="text/javascript"></script>
        <script type="text/javascript">
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
                        $('tr', $(this)).removeClass(settings.className);
                        $('tr:nth-child(' + settings.parity + ')', $(this)).addClass(settings.className);
                    });
                };
            })(jQuery);

            $(document).ready(function() {
                $.tablesorter.addParser({
                    id: 'dt',
                    is: function(s)
                    {
                        return false;
                    },
                    format: function(s)
                    {
                        return parseInt(s.replace(/[ :-]/g, ''), 10);
                    },
                    type: 'numeric'
                });
                $.tablesorter.addParser({
                    id: 'prio',
                    is: function(s)
                    {
                        return false;
                    },
                    format: function(s)
                    {
                        return s.replace('Very High', 1)
                                .replace('Very Low', 5)
                                .replace('High', 2)
                                .replace('Medium', 3)
                                .replace('Low', 4);
                    },
                    type: 'numeric'
                });

                $(".tablesorter").tablesorter({
                    sortList: [[1,1]],
                    headers: {
                        1: {
                            sorter: 'dt'
                        },
                        2: {
                            sorter: 'prio'
                        },
                        3: { 
                            sorter: false
                        }
                    }
                }).bind("sortEnd",function() {
                    $(this).zebraStripe(); 
                }).zebraStripe();
                $("abbr.timeago").timeago();

                $("#preview-button").click(function() {
                    $.ajax({
                        type: "POST",
                        url: 'preview.php',
                        data: {
                            item: $("textarea[name=item_description]").val()
                        },
                        success: function(data) {
                            $("#preview-area").html(data);
                        }
                    });
                });
            });
        </script>
    </body>
</html>