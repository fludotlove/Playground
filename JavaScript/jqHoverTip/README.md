jQuery.hoverTooltip
===============

Yet another fancy tooltip plugin for jQuery.

Example Usage
-------------

Include the plugin and stylesheet in your HTML:

    <script type="text/javascript" src="hoverTooltip.js"></script>
    <link rel="stylesheet" type="text/css" href="hoverTooltip.css" />

Now invoke the plugin on DOM ready. 

    $(function() {
        $('*[title]').hoverTooltip();
    });

This example binds all elements with the `data-tooltip` attribute.

    $(function() {
        $('*[data-tooltip]').hoverTooltip({
            tipText: function() {
                return this.getAttribute('data-tooltip');
            },
            gravity: 'n',
            fade: true,
            html: false
        });
    });
    
Now add the `data-tooltip` attribute to your tags to use the tooltip functionality.

    <span data-tooltip="This is a tooltip!">Hover over me.</span>
    
### Options
Have a play with the options available. Each option is documented below:
- **fade** _boolean_ - whether the tooltip fades, or shows immediately. Default is `false`.
- **fallbackText** _string_ - fallback text for when a tooltip fails. Default is `Ooops, something went wrong.`.
- **gravity** _string_ - direction of the tooltip gravity. Default is `n`. Available options are:
  - `n` or `north` (displays below)
  - `e` or `east` (displays on the left)
  - `s` or `south` (displays above)
  - `w` or `west` (displays on the right)
- **html** _boolean_ - enable HTML within the tooltip. Default is `true`.
- **opacity** _float_ - opacity of the tooltip. Default is `0.8`.
- **tipText** _string_|_callback_ - attribute where the tooltip text should be retrieved from. You can use an anonymous function here for additional flexibility. Default behaviour is to use the `title` attribute.