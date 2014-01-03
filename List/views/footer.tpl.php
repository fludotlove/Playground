        <script src="js/jquery.js"></script>
        <script src="js/tblsorter.jquery.js"></script>
        <script src="js/timeago.jquery.js" type="text/javascript"></script>
        <script type="text/javascript">
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

                $(".tablesorter").tablesorter({
                    sortList: [[1,1]],
                    headers: {
                        1: {
                            sorter: 'dt'
                        },
                        2: { 
                            sorter: false
                        }
                    }
                });
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