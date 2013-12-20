jQuery.clearDefault
===================

Clear default value from an input - fallback for HTML5 `placeholders` in older browsers.

For HTML5 just use the `placeholder` attribute!

Example Usage
-------------

Start with including the plugin in your HTML:

    <script type="text/javascript" src="clearDefault.js"></script>
    
Add an `<input>` to your HTML.

    <input name="search" type="text" />
    
Invoke the plugin on when the DOM is ready. Setting the default value using jQuery will also mean that users with JavaScript disabled don't have to manually delete the value before being able to type.

    $(function() {
        $('input[name=search]').val('Type something...').clearDefault();
    });