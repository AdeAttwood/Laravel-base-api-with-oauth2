# Laravel PHP Framework Data API
This is a standers laravel app that I have tweaked to make it a better start for data api's.
Changes
-------
The auth is handled with [this oauth2 library](http://bshaffer.github.io/oauth2-server-php-docs/) by bshaffer. The wrapper is found at /app/Extensions/Oauth2.php this is where all the auth functions are found.

I have also split the route files  to make it more manageable

Code coverage is also available in the index.php file . This creates a .cov file for every request, then we can join then using phpcov to give us better results when testing with guzzel.
