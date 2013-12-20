Dot
===

Class and functions for using [dot-notation](http://www.dev-archive.net/articles/js-dot-notation/) to access arrays in PHP.

Example Usage
-------------

First include the functions (`functions.php`) **or** class (`Dot.php`) into your project. **Important:** there is no need to include both in your code.

    // Uncomment as required to include functions or class.
    require '../path/to/functions.php';
    //require '../path/to/Dot.php';
    
Now you're ready to use the functions.

### Set a key/value pair using dot-notation.
To set a key/value pair using dot-notation use the `array_set` or `Dot::set` function/method.

    $user = [];
    array_set($user, 'forename', 'Nathan');
    array_set($user, 'contact.email', 'nathan@fludotlove.com');

Results in the following:

    Array(
        'forename' => 'Nathan'
        'contact' => Array(
            'email' => 'nathan@fludotlove.com'
        )
    )
    
### Get a value using dot-notation.
To get a value from an array using dot-notation use the `array_get` or `Dot::get` function/method.

    $email = array_get($user, 'contact.email'); // nathan@fludotlove.com

It's also possible to pass a default value for times when the array key doesn't exist. This is passed as a 3rd parameter.

    $phone = array_get($user, 'contact.phone', 'No phone number provided.');

### Remove an array key using dot-notation.
To remove a key from an array using dot-notation use the `array_forget` or `Dot::forget` function/method.

    array_forget($user, 'contact.email');
    
### Check if an array has a key using dot-notation.
To determine if an array has a key using dot-notation use the `array_has` or `Dot::has` function/method.

    if(array_has($user, 'contact.email')) {
        // Send an email.
    }