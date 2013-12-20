jQuery.tabs
===========

Creates simple tabs.

Example Usage
-------------

Include the plugin in your HTML:

    <script type="text/javascript" src="tabs.js"></script>

Add some HTML for your tabs, this basic example consists of a container `<div>`, a `<ul>` for the tabs, and a bunch of nested `<div>` tags for the content of the tabs.

    <div class="tabs-container">
        <ul class="tabs">
            <li class="active-tab"><a href="#tab_a">Tab A</a></li>
            <li><a href="#tab_b">Tab B</a></li>
            <li class="tab-unavailable"><a href="#tab_c" title="Currently unavailable">Tab C</a></li>
            <li class="tab-unavailable"><a href="#tab_d" title="Currently unavailable">Tab D</a></li>
            <li><a href="#tab_e">Tab E</a>
            </li>
        </ul>
        <div id="tab_a" class="tab-content">
            <p>Content for A</p>
        </div>
        <div id="tab_b" class="tab-content">
            <p>Content for B</p>
        </div>
        <div id="tab_e" class="tab-content">
            <p>Content for E</p>
        </div>
    </div>
    
Note that tabs with the `tab-unavailable` class do not trigger the tab behaviour.

Now invoke the plugin (after DOM is ready):

    $(function() {
        $('.tabs-container').tabs();
    });
    
You can style the tabs however you like, a default stylesheet is included in the repository.