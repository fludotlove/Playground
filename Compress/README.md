Compress
========

Compresses CSS files using PHP.

CSS Variables
-------------
Compress can also allow use of variables within your CSS files.

Add a comment anywhere in your CSS file (at the top of your file usually makes sense) containing variables in the following format and use them in your CSS. On compression the variables are replaced in your code.

    /* $fontStack = 'Helvetica Neue', Helvetica, Arial, sans-serif;
     * $base = #fff;
     * $primary = #f00;
     * $secondary = #c00; */
     
    a.myClass {
        font: 1em/1.2em $fontStack;
        color: $primary;
        background-color: $base;
    }
    a.myClass:hover {
        background-color: $secondary;
    }
    
On compression the code becomes:

    a.myClass {font:1em/1.2em 'Helvetica Neue',Helvetica,Arial,sans-serif;color:#f00;background-color:#fff}
    a.myClass:hover{background-color:#c00}