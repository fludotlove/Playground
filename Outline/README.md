Outline
=======

A simple and easy to use view rendering class.

The class allows for view caching - see examples for how to enable view caching.

Example Usage
-------------

Start by creating a directory in your project (where you will store your views), `chmod` the directory to `0777`. If you want to make use of view caching, create another directory within your view directory called `cache`, and `chmod` that also.

Finally, include the class in your code with `require '../path/to/Outline.php';` and you're ready to go.

**Example A** - the most basic use scenario.

    $outline = new Outline('../path/to/your/views');
    $outline->displayView('myViewName'); // loads path/to/your/views/myViewName.php
    
**Example B** - adding variables.

    $outline = new Outline('../path/to/your/views');
    $outline->displayView('myViewName', ['forename' => 'Nathan']);
    
Now in your view (myViewName) you can use `<?=$this->forename;?>` to output 'Nathan'.
    
**Example C** - caching output from a view.

To cache a view you need to enable the caching by setting a cache time (in hours) using the `setCacheLimit` method or by passing the number of hours in the second argument of the constructor. Examples of both methods are shown.

    // Set using constructor method.
    $outline = new Outline('../path/to/your/views', 24); // Set cache time to 24 hours.
    
    // Set using the setCacheLimit method.
    $outline = new Outline('../path/to/your/views');
    $outline->setCacheLimit(24);

Now when you call the `renderView` or `displayView` methods enable caching using the 3rd parameter.

    $outline->renderView('myViewName', ['forename' => 'Nathan'], true);
    
Your evaluated view is now cached for 24 hours.